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

    public function createPurchase(){
        $data = $this->request->getJSON(true);

        $db = \Config\Database::connect();

        $db->transStart();

        $this->purchaseModel->insert([
            'final_amount' => $data['final_amount']
        ]);

        $id_purchase = $this->purchaseModel->getInsertID();

        foreach($data['products'] as $product){
          $lastMovement = $this->stockMvt
                ->where(
                    'id_product',
                    $product['id_product']
                )
                ->orderBy(
                    'id_stockMvt',
                    'DESC'
                )
                ->first();


                if(!$lastMovement){

                    $db->transRollback();

                    return $this->fail([
                        "message"=>"No stock found for this product"
                    ]);

                }



            $stock = $this->stock
                ->find($lastMovement['id_stock']);


                if(!$stock){
                    $db->transRollback();

                    return $this->fail([
                        "message"=>"Stock not found for product"
                    ]);
                }

                $id_stockMvt = $this->stockMvt->insert([
                    'id_product'=>$product['id_product'],
                    'id_stock'=>$stock['id_stock'],
                    'movement_type'=> 'sortie',
                    'quantity'=>$product['quantity'],
                    'movement_date'=>date('Y-m-d')
                ]);
        

             $newQuantity =
            $stock['quantity_available']
            -
            $product['quantity'];



        if($newQuantity < 0)
        {

            $db->transRollback();

            return $this->fail([
                "message"=>"Not enough stock"
            ]);

        }

            $this->stock
                ->where(
                    'id_stock',
                    $stock['id_stock']
                )
                ->set([
                    'quantity_available'=>$newQuantity
                ])
                ->update();

                $this->purchaseLineModel->insert([

                    'id_product'=>$product['id_product'],

                    'id_purchase'=>$id_purchase,

                    'id_stockMvt'=>$id_stockMvt

                ]);
        }

        $db->transComplete();

        if($db->transStatus() === false)
        {
            return $this->fail([
                "message"=>"Purchase failed"
            ]);
        }






        return $this->respond([

            "message"=>"Purchase created successfully",

            "id_purchase"=>$id_purchase

        ]);


    }


    public function indexSale()
        {
            $sales = $this->saleModel
                ->orderBy('id_sale','DESC')
                ->findAll();

            return $this->respond($sales);
        }

    public function showPurchase($id)
    {

        $db = \Config\Database::connect();


        $sale = $this->purchaseModel

            ->find($id);



        if(!$sale)
        {

            return $this->failNotFound(
                "Sale not found"
            );

        }



        /*
        Get products inside the sale
        */


        $products = $db

            ->table('purchasel')

            ->select(
                '
                product.name_product,
                purchasel.id_product
                '
            )

            ->selectSum(
                'stockmovement.quantity',
                'quantity'
            )


            ->join(

                'product',

                'product.id_product=purchasel.id_product'

            )


            ->join(

                'stockmovement',

                'stockmovement.id_stockMvt=purchasel.id_stockMvt'

            )


            ->where(

                'purchasel.id_purchase',

                $id

            )


            ->groupBy(

                'purchasel.id_purchaseL'

            )


            ->get()

            ->getResult();



        return $this->respond([


            "sale"=>$sale,


            "products"=>$products


        ]);

    }






    /*
    |--------------------------------------------------------------------------
    | DELETE SALE
    |--------------------------------------------------------------------------
    */

    public function deletePurchase($id)
    {

        $sale = $this->purchaseModel->find($id);



        if(!$sale)
        {

            return $this->failNotFound(
                "Sale not found"
            );

        }



        $this->purchaseModel->delete($id);



        return $this->respond([

            "message"=>"Sale deleted"

        ]);

    }
}
