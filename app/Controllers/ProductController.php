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
        $product = $this->product_model->findAll();

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
