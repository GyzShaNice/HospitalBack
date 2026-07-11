<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Api\ResponseTrait;
use App\Models\StockModel;

class StockController extends BaseController
{
    use ResponseTrait;

    protected $stock;

    function __construct(){
        $this->stock = new StockModel();
    }

    public function indexStock()
    {
        $db = \Config\Database::connect();

        $stock = $db->table('Stock')
            ->select('Stock.id_stock,Stock.name_stock,Stock.expiry_date,Stock.quantity_available,Product.minimum_quantity,')
            ->join('StockMovement','StockMovement.id_stock = Stock.id_stock')
            ->join('Product','Product.id_product = StockMovement.id_product')
            ->where('Stock.deleted_at',null)
            ->groupBy('Stock.id_stock')
            ->get()
            ->getResultArray();

        $response = [
            "message"=>count($stock)>0? "stock found":"stock not found",
            "success"=>count($stock)>0,
            "data"=>$stock
        ];

        return $this->respond($response);
    }

    public function showStock($idStock = null){
        $SpecificStock = $this->stock->find($idStock);
            $response = [
                "message"=>$SpecificStock? "stock ok":"stock not found",
                "success"=>(bool)$SpecificStock,
                "data"=>$SpecificStock
            ];

            return $this->respond($response);
    }

    public function createStock(){
        helper(['form']);

        $rules = [
            'name_stock'=> 'required|max_length[255]|min_length[3]',
            'expiry_date'=>'required|valid_date',
            'quantity_available'=>'required|max_length[255]|min_length[1]',
        ];

            if(!$this->validate($rules)){
                return $this->fail([
                    "error"=>$this->validator->getErrors(),
                    "message"=>"could not create stock",
                    "success"=>false
                ],ResponseInterface::HTTP_BAD_REQUEST);
            }

            $dataStock = [
                'name_stock'=>$this->request->getVar('name_stock'),
                'expiry_date'=>$this->request->getVar('expiry_date'),
                'quantity_available'=>$this->request->getVar('quantity_available'),

            ];

            $StockID = $this->stock->insert($dataStock);
                $response = [
                    "message"=>$StockID? "stock created":"stock not created",
                    "success"=>(bool)$StockID,
                    "data"=>$StockID
                ];

                if(!$StockID){
                    return $this->response->setJSON($this->stock->errors());
                }
                return $this->respondCreated($response);
    }

    public function updateStock($idStock = null){
        helper(['form']);

        $rules = [
            'name_stock'=> 'required|max_length[255]|min_length[3]',
            'expiry_date'=>'required|valid_date',
            'quantity_available'=>'required|max_length[255]|min_length[1]',
        ];

        if(!$this->validate($rules)){
            $response = [
                'errors'=>$this->validator->getErrors(),
                'message'=>'invalid information',
                'success'=>false
            ];
          
            return $this->fail($response);
        }

        $name_stock = $this->request->getVar('name_stock');

        $dataStock = [
            'name_stock'=>$this->request->getVar('name_stock'),
            'expiry_date'=>$this->request->getVar('expiry_date'),
            'quantity_available'=>$this->request->getVar('quantity_available'),
        ];

        $stock = $this->stock->update($idStock,$dataStock);

        $response = [
            "message"=>$stock? "stock updated":"stock not created",
            "success"=>(bool)$stock,
            "data"=>array_merge(['id_stock'=>$idStock],$dataStock)
        ];

        return $this->respond($response);
    }

    public function deleteStock($idStock = null){
        $stock_exist = $this->stock->find($idStock);

        if(!$stock_exist){
            return $this->failNotFound('delete impossible');
        }

        $delete = $this->stock->delete($idStock);

        $response = [
            "message"=>$delete? "delete ok":"delete fail",
            "success"=>(bool) $delete,
            "data" =>['id_stock'=>$idStock]
        ];
        return $this->respond($response);
    }
}
