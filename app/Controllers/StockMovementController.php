<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Api\ResponseTrait;
use App\Models\StockMovementModel;
use App\Models\ProductModel;
use App\Models\StockModel;


class StockMovementController extends BaseController
{   use ResponseTrait;
    protected $stock;
    protected $stockMvt;
    protected $product_model;

    function __construct(){
        $this->stock = new StockModel();
        $this->stockMvt = new StockMovementModel();
        $this->product_model = new ProductModel();
    }
    public function indexStockMvt()
{
    $db = \Config\Database::connect();

    $movementType = strtolower((string) $this->request->getGet('movement_type'));
    $idStock = $this->request->getGet('id_stock');
    $idProduct = $this->request->getGet('id_product');
    $expiryBefore = $this->request->getGet('expiry_before');

    if (!empty($movementType) && !in_array($movementType, ['entree', 'sortie'], true)) {
        return $this->fail([
            "message" => "movement_type must be entree or sortie",
            "success" => false
        ], ResponseInterface::HTTP_BAD_REQUEST);
    }

    $query = $db->table('StockMovement')
        ->select('StockMovement.id_stockMvt, StockMovement.id_stock, StockMovement.id_product, StockMovement.movement_type, StockMovement.quantity, StockMovement.movement_date, Product.name_product, Stock.name_stock, Stock.expiry_date, Stock.quantity_available')
        ->join('Product', 'Product.id_product = StockMovement.id_product')
        ->join('Stock', 'Stock.id_stock = StockMovement.id_stock')
        ->where('StockMovement.deleted_at', null)
        ->where('Stock.deleted_at', null)
        ->where('Product.deleted_at', null);

    if (!empty($movementType)) {
        $query->where('LOWER(StockMovement.movement_type)', $movementType);
    }

    if (!empty($idStock)) {
        $query->where('StockMovement.id_stock', (int) $idStock);
    }

    if (!empty($idProduct)) {
        $query->where('StockMovement.id_product', (int) $idProduct);
    }

    if (!empty($expiryBefore)) {
        $query->where('Stock.expiry_date <=', $expiryBefore);
    }

    $movements = $query
        ->orderBy('StockMovement.movement_date', 'DESC')
        ->orderBy('StockMovement.id_stockMvt', 'DESC')
        ->get()
        ->getResultArray();

    $response = [
        "message" => count($movements) > 0 ? "movements found" : "movement not found",
        "success" => count($movements) > 0,
        "data" => $movements
    ];

    return $this->respond($response);
}

    public function showStockMvt($idStockMvt = null){
         $Specmovement = $this->stockMvt->find($idStockMvt);

        $response = [
            "message"=>count($Specmovement)? "movements found":"movement not found",
            "success"=>(bool)$Specmovement,
            "data"=>$Specmovement
        ];

        return $this->respond($response);
    }

    public function createStockMvt(){
        helper(['form']);

        $rules = [
            'id_product'=>'required|is_natural_no_zero',
            'name_stock'=>'required|max_length[20]|min_length[2]',
            'expiry_date'   => 'required|valid_date',
            'quantity'      => 'required|is_natural_no_zero',
            'movement_date' => 'required|valid_date',
        ];

        if(!$this->validate($rules)){
            return $this->fail([
                "error"=>$this->validator->getErrors(),
                "message"=>"could not create movement",
                "success"=>false
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $idProduct = $this->request->getVar('id_product');

        $productExists = $this->product_model->find($idProduct);

        if(!$productExists){
            return $this->fail([
                "message"=>"product does not exists",
                "success"=>false
            ],ResponseInterface::HTTP_BAD_REQUEST);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // cree le nouveau stock
        $idStock = $this->stock->insert([
            'name_stock' => $this->request->getVar('name_stock'),
            'expiry_date' => $this->request->getVar('expiry_date'),
            'quantity_available' => $this->request->getVar('quantity'),
        ]);

        $movementID = $this->stockMvt->insert([
            'id_product' => $idProduct,
            'id_stock' => $idStock,
            'movement_type' => 'entree',
            'quantity' => $this->request->getVar('quantity'),
            'movement_date'=> $this->request->getVar('movement_date'),
        ]);

        $db->transComplete();

        if($db->transStatus() === false){
            return $this->fail([
                "message"=>"transaction failed, movement not recorded",
                "success"=>false
            ]);
        }

        return $this->respondCreated([
            "message"=>"movement recorded",
            "success"=>true,
            "data"=> [
                'id_stockMvt'=>$movementID,
                'id_stock'=>$idStock,
            ]
        ]);
    }

    public function deleteStockMvt($idStockMvt = null){
        $movement_exist = $this->stockMvt->find($idStockMvt);

        if (!$movement_exist) {
            return $this->failNotFound('delete impossible');
        }

        $delete = $this->stockMvt->delete($idStockMvt);

        $response = [
            "message" => $delete ? "delete ok" : "delete fail",
            "success" => (bool) $delete,
            "data" => ['id_stockMvt' => $idStockMvt]
        ];

        return $this->respond($response);
    }
}
