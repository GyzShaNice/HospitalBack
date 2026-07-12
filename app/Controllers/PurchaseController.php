<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Api\ResponseTrait;
use App\Models\PurchaseModel;
use App\Models\PurchaseLineModel;
use App\Models\ProductModel;
use App\Models\StockModel;
use App\Models\StockMovementModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class PurchaseController extends BaseController
{
  use ResponseTrait;

    protected $product_model;
    protected $purchaseModel;
    protected $purchaseLineModel;
    protected $stock;
    protected $stockMvt;

    function __construct(){
        $this->product_model = new ProductModel();
        $this->purchaseModel = new PurchaseModel();
        $this->purchaseLineModel = new PurchaseLineModel();
        $this->stock = new StockModel();
        $this->stockMvt = new StockMovementModel();
    }

    /**
     * Identifie le personnel connecte (caissier) a partir du token JWT,
     * en reprenant le payload encode par PersonnelController::connexionPerso().
     */
    private function getAuthenticatedPersonnelId(): ?int
    {
        $header = $this->request->getHeaderLine('Authorization');

        if (empty($header) || !preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return null;
        }

        try {
            $decoded = JWT::decode($matches[1], new Key(getenv('JWT_SECRET'), 'HS256'));

            return $decoded->data->id_personel ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function indexPurchase()
    {
        $purchases = $this->purchaseModel
            ->orderBy('id_purchase', 'DESC')
            ->findAll();

        $response = [
            "message" => count($purchases) > 0 ? "purchases found" : "purchases not found",
            "success" => count($purchases) > 0,
            "data" => $purchases
        ];

        return $this->respond($response);
    }

    public function createPurchase(){
        $data = $this->request->getJSON(true);

        if(empty($data['products']) || !is_array($data['products'])){
            return $this->fail([
                "message" => "No products provided",
                "success" => false
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $paymentMethod = $data['payment_method'] ?? null;

        $db = \Config\Database::connect();

        $db->transStart();

        // 1) On valide tout AVANT d'ecrire quoi que ce soit : produit existant,
        //    stock disponible, produit non perime. Le montant final est
        //    entierement recalcule cote serveur (jamais confie au client).
        $lines = [];
        $finalAmount = 0;
        $today = date('Y-m-d');

        foreach($data['products'] as $item){

            $product = $this->product_model->find($item['id_product'] ?? null);

            if(!$product){
                $db->transRollback();

                return $this->fail([
                    "message" => "Product not found"
                ]);
            }

            $quantity = (int) ($item['quantity'] ?? 0);

            if($quantity <= 0){
                $db->transRollback();

                return $this->fail([
                    "message" => "Invalid quantity for product " . $product['name_product']
                ]);
            }

            $availableStocks = $db->table('stock s')
                ->select('s.id_stock, s.quantity_available, s.expiry_date')
                ->join('stockmovement sm', 'sm.id_stock = s.id_stock')
                ->where('sm.id_product', $product['id_product'])
                ->where('s.deleted_at', null)
                ->where('sm.deleted_at', null)
                ->where('s.quantity_available >', 0)
                ->groupBy('s.id_stock')
                ->orderBy('CASE WHEN s.expiry_date IS NULL THEN 1 ELSE 0 END', 'ASC', false)
                ->orderBy('s.expiry_date', 'ASC')
                ->orderBy('s.id_stock', 'ASC')
                ->get()
                ->getResultArray();

            $eligibleStocks = array_filter($availableStocks, static function ($stock) use ($today) {
                return empty($stock['expiry_date']) || $stock['expiry_date'] >= $today;
            });

            if(empty($eligibleStocks)) {
                $db->transRollback();

                return $this->fail([
                    "message" => "No non-expired stock found for product " . $product['name_product']
                ]);
            }

            $remaining = $quantity;
            $allocations = [];

            foreach($eligibleStocks as $stock){
                if($remaining <= 0){
                    break;
                }

                $availableQty = (int) $stock['quantity_available'];
                $takenQty = min($availableQty, $remaining);

                if($takenQty <= 0){
                    continue;
                }

                $allocations[] = [
                    'id_stock' => (int) $stock['id_stock'],
                    'quantity' => $takenQty,
                ];

                $remaining -= $takenQty;
            }

            if($remaining > 0){
                $db->transRollback();

                return $this->fail([
                    "message" => "Not enough stock for product " . $product['name_product']
                ]);
            }

            $lines[] = [
                'id_product' => $product['id_product'],
                'unit_price' => $product['price'],
                'quantity'   => $quantity,
                'allocations' => $allocations,
            ];

            $finalAmount += $product['price'] * $quantity;
        }

        // 2) Le total etant connu et valide, on cree la vente puis les mouvements de stock.
        $id_personel = $this->getAuthenticatedPersonnelId();

        $id_purchase = $this->purchaseModel->insert([
            'final_amount'   => $finalAmount,
            'payment_method' => $paymentMethod,
            'id_personel'    => $id_personel,
        ]);

        foreach($lines as $line){
            foreach($line['allocations'] as $allocation){
                $id_stockMvt = $this->stockMvt->insert([
                    'id_product'    => $line['id_product'],
                    'id_stock'      => $allocation['id_stock'],
                    'movement_type' => 'sortie',
                    'quantity'      => $allocation['quantity'],
                    'movement_date' => date('Y-m-d')
                ]);

                $this->stock
                    ->where('id_stock', $allocation['id_stock'])
                    ->set(['quantity_available' => "quantity_available - {$allocation['quantity']}"], false)
                    ->update();

                $this->purchaseLineModel->insert([
                    'id_product'  => $line['id_product'],
                    'id_purchase' => $id_purchase,
                    'id_stockMvt' => $id_stockMvt,
                    'unit_price'  => $line['unit_price'],
                ]);
            }
        }

        $receiptNumber = 'REC-' . date('Ymd') . '-' . str_pad((string) $id_purchase, 6, '0', STR_PAD_LEFT);

        $this->purchaseModel->update($id_purchase, ['receipt_number' => $receiptNumber]);

        $db->transComplete();

        if($db->transStatus() === false){
            return $this->fail([
                "message" => "Purchase failed"
            ]);
        }

        return $this->respondCreated([
            "message"        => "Purchase created successfully",
            "success"        => true,
            "id_purchase"    => $id_purchase,
            "receipt_number" => $receiptNumber,
            "final_amount"   => $finalAmount,
        ]);
    }

    public function showPurchase($id)
    {
        $db = \Config\Database::connect();

        $sale = $this->purchaseModel->find($id);

        if(!$sale)
        {
            return $this->failNotFound("Sale not found");
        }

        $products = $db
            ->table('purchasel')
            ->select('
                product.name_product,
                purchasel.id_product,
                purchasel.unit_price
            ')
            ->selectSum('stockmovement.quantity', 'quantity')
            ->join('product', 'product.id_product=purchasel.id_product')
            ->join('stockmovement', 'stockmovement.id_stockMvt=purchasel.id_stockMvt')
            ->where('purchasel.id_purchase', $id)
            ->groupBy('purchasel.id_purchaseL')
            ->get()
            ->getResult();

        return $this->respond([
            "message"  => "Sale found",
            "success"  => true,
            "sale"     => $sale,
            "products" => $products
        ]);
    }

    public function showReceipt($id)
    {
        $db = \Config\Database::connect();

        $sale = $this->purchaseModel->find($id);

        if(!$sale)
        {
            return $this->failNotFound("Sale not found");
        }

        $products = $db
            ->table('purchasel')
            ->select('
                product.name_product,
                purchasel.unit_price
            ')
            ->selectSum('stockmovement.quantity', 'quantity')
            ->join('product', 'product.id_product=purchasel.id_product')
            ->join('stockmovement', 'stockmovement.id_stockMvt=purchasel.id_stockMvt')
            ->where('purchasel.id_purchase', $id)
            ->groupBy('purchasel.id_purchaseL')
            ->get()
            ->getResultArray();

        $cashier = null;

        if(!empty($sale['id_personel'])){
            $cashier = $db
                ->table('personel')
                ->select('users.name_user, users.surname_user')
                ->join('users', 'users.id_user = personel.id_user')
                ->where('personel.id_personel', $sale['id_personel'])
                ->get()
                ->getRowArray();
        }

        $html = view('purchase_receipt', [
            'sale'     => $sale,
            'products' => $products,
            'cashier'  => $cashier,
        ]);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A5', 'portrait');
        $dompdf->render();

        $filename = 'recu-' . ($sale['receipt_number'] ?? $id) . '.pdf';

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    public function deletePurchase($id)
    {
        $sale = $this->purchaseModel->find($id);

        if(!$sale)
        {
            return $this->failNotFound("Sale not found");
        }

        $this->purchaseModel->delete($id);

        return $this->respond([
            "message" => "Sale deleted"
        ]);
    }
}
