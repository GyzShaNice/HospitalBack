<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;
use App\Models\ProductModel;


class ProductController extends BaseController
{
    use ResponseTrait;

    protected $product_model;

    function __construct(){
        $this->product_model = new ProductModel();
    }

     public function indexProduct()
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        $inStockOnly = filter_var($this->request->getGet('in_stock_only'), FILTER_VALIDATE_BOOLEAN);
        $expiryBefore = $this->request->getGet('expiry_before');

        // available_quantity = somme des stocks (non supprimes) actuellement lies au produit,
        // en ne comptant chaque lot de stock qu'une seule fois (un lot peut avoir plusieurs
        // mouvements: une entree puis des sorties successives lors des ventes).
        $product = $db->query('
            SELECT
                product.*,
                COALESCE(SUM(stock_totals.quantity_available), 0) AS available_quantity,
                COALESCE(SUM(CASE
                    WHEN stock_totals.expiry_date IS NULL OR stock_totals.expiry_date >= ?
                    THEN stock_totals.quantity_available
                    ELSE 0
                END), 0) AS sellable_quantity,
                MIN(CASE
                    WHEN stock_totals.quantity_available > 0 THEN stock_totals.expiry_date
                    ELSE NULL
                END) AS nearest_expiry_date
            FROM product
            LEFT JOIN (
                SELECT DISTINCT sm.id_product, s.id_stock, s.quantity_available, s.expiry_date
                FROM stockmovement sm
                INNER JOIN stock s ON s.id_stock = sm.id_stock AND s.deleted_at IS NULL
                WHERE sm.deleted_at IS NULL
            ) AS stock_totals ON stock_totals.id_product = product.id_product
            WHERE product.deleted_at IS NULL
            GROUP BY product.id_product
        ', [$today])->getResultArray();

        if ($inStockOnly) {
            $product = array_values(array_filter($product, static function ($item) {
                return (int) ($item['sellable_quantity'] ?? 0) > 0;
            }));
        }

        if (!empty($expiryBefore)) {
            $product = array_values(array_filter($product, static function ($item) use ($expiryBefore) {
                return !empty($item['nearest_expiry_date']) && $item['nearest_expiry_date'] <= $expiryBefore;
            }));
        }

            $response = [
                "message"=>count($product)>0? "product found":"product not found",
                "success"=>count($product)>0,
                "data"=>$product
            ];

            return $this->respond($response);
    }

    public function showProduct($idProduct=null){
        $SpecificProduct = $this->product_model->find($idProduct);
                $response = [
                    "message"=>$SpecificProduct? "product done":"product not done",
                    "success"=>(bool)$SpecificProduct,
                    "data" => $SpecificProduct
                ];
                
                return $this->respond($response);
    }

    public function createProduct(){
        helper(['form']);

        $rules = [
            'name_product'=> 'required|max_length[254]|min_length[3]',
            'description'=>'required|max_length[254]|min_length[2]',
            'minimum_quantity'=>'required|max_length[254]|min_length[1]',
            'price'=>'required|max_length[254]|min_length[3]'
        ];

        if(!$this->validate($rules)){
            return $this->fail([
                "error"=>$this->validator->getErrors(),
                "message"=>"could not create product",
                "success"=>false
            ],ResponseInterface::HTTP_BAD_REQUEST);
        }

        $dataProduct = [
            'name_product'=>$this->request->getVar('name_product'),
            'description'=>$this->request->getVar('description'),
            'minimum_quantity'=>$this->request->getVar('minimum_quantity'),
            'price'=>$this->request->getVar('price'),
        ];

        $ProductID = $this->product_model->insert($dataProduct);
            $response = [
                "message"=>$ProductID? "product created":"product not created",
                "success"=>(bool)$ProductID,
                "data"=>$ProductID
            ];

            if(!$ProductID){
                return $this->response->setJSON($this->product_model->errors());
            }

            return $this->respondCreated($response);
    }

    public function updateProduct($idProduct=null){
        helper(['form']);

        $rules = [
            'name_product'=> 'required|max_length[254]|min_length[3]',
            'description'=>'required|max_length[254]|min_length[2]',
            'minimum_quantity'=>'required|max_length[254]|min_length[1]',
            'price'=>'required|max_length[254]|min_length[3]'
        ];

        if(!$this->validate($rules)){
            $response = [
                'errors'=>$this->validator->getErrors(),
                'message'=>'invalid information',
                'success'=>false
            ];

            return $this->fail($response);
        }

        $name_product = $this->request->getVar('name_product');

         $dataProduct = [
            'name_product'=>$this->request->getVar('name_product'),
            'description'=>$this->request->getVar('description'),
            'minimum_quantity'=>$this->request->getVar('minimum_quantity'),
            'price'=>$this->request->getVar('price'),
        ];

        $product = $this->product_model->update($idProduct,$dataProduct);

        $response = [
            "message"=>$product? "functin updated":"function not updated",
            "success"=>(bool)$product,
            "data"=>array_merge(['id_product'=>$idProduct],$dataProduct)
        ];

        return $this->respond($response);
    }

    public function deleteProduct($idProduct=null){
        $product_exist = $this->product_model->find($idProduct);

        if(!$product_exist){
            return $this->failNotFound('delete impossible');
        }

        $delete = $this->product_model->delete($idProduct);

        $response = [
            "message" => $delete ? "delete ok" : "delete fail",
                "success" => (bool) $delete,
                "data" => ['id_product'=>$idProduct]
        ];

        return $this->respond($response);
    }
}
