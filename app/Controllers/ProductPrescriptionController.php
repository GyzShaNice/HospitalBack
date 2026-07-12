<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;
use App\Models\ProductPrescriptionModel;
use App\Models\PrescribtionModel;
use App\Models\ProductModel;

class ProductPrescriptionController extends BaseController
{
    use ResponseTrait;

    protected $productPresc_model;
    protected $presc_model;
    protected $product_model;

    function __construct(){
        $this->productPresc_model = new ProductPrescriptionModel();
        $this->presc_model = new PrescribtionModel();
        $this->product_model = new ProductModel();
    }

    public function indexProductPresc()
    {
        $lines = $this->productPresc_model
            ->select('productpres.id_ProductPres,productpres.id_presc,product.id_product,product.name_product,product.price')
            ->join('product', 'product.id_product = productpres.id_product')
            ->findAll();

        $response = [
            "message" => count($lines) > 0 ? "medicaments found" : "medicaments not found",
            "success" => count($lines) > 0,
            "data" => $lines
        ];

        return $this->respond($response);
    }

    public function showByPresc($idPresc = null)
    {
        $lines = $this->productPresc_model
            ->select('productpres.id_ProductPres,productpres.id_presc,product.id_product,product.name_product,product.price')
            ->join('product', 'product.id_product = productpres.id_product')
            ->where('productpres.id_presc', $idPresc)
            ->findAll();

        $response = [
            "message" => count($lines) > 0 ? "medicaments found" : "medicaments not found",
            "success" => count($lines) > 0,
            "data" => $lines
        ];

        return $this->respond($response);
    }

    public function createProductPresc(){
        helper(['form']);

        $rules = [
            'id_presc'   => 'required|integer',
            'id_product' => 'required|integer',
        ];

        if(!$this->validate($rules)){
            return $this->fail([
                'error'   => $this->validator->getErrors(),
                'message' => 'invalid information',
                'success' => false
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $idPresc = $this->request->getVar('id_presc');
        $idProduct = $this->request->getVar('id_product');

        $prescription = $this->presc_model->find($idPresc);

        if(!$prescription){
            return $this->fail([
                'message' => 'prescription not found',
                'success' => false
            ]);
        }

        $product = $this->product_model->find($idProduct);

        if(!$product){
            return $this->fail([
                'message' => 'product not found',
                'success' => false
            ]);
        }

        $id = $this->productPresc_model->insert([
            'id_presc'   => $idPresc,
            'id_product' => $idProduct,
        ]);

        return $this->respondCreated([
            "message" => $id ? "medicament added to prescription" : "could not add medicament",
            "success" => (bool) $id,
            "data" => [
                'id_ProductPres' => $id,
                'id_presc'       => $idPresc,
                'id_product'     => $idProduct,
                'name_product'   => $product['name_product'],
            ]
        ]);
    }

    public function deleteProductPresc($id = null){
        $line = $this->productPresc_model->find($id);

        if(!$line){
            return $this->failNotFound('deletion impossible');
        }

        $delete = $this->productPresc_model->delete($id);

        return $this->respond([
            "message" => $delete ? "deletion ok" : "deletion impossible",
            "success" => (bool) $delete,
            "data" => ['id_ProductPres' => $id]
        ]);
    }
}
