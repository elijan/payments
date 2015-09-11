<?php

namespace Goldtorch\Payments;

use Elijan\PaypalAdaptive;


class AdaptivePayments {


    private $total = 0;

    public function __construct($transaction_id=null)
    {

    }


    public function createOrder($transaction_id=null, $method="create")
    {

        \PaypalAdaptive::createOrder($transaction_id, $method);
    }


    public function getStatus($payKey){

        $response =  \PaypalAdaptive::getDetails($payKey);

        return $response->status;
    }

    public function getReturnUrl($payKey=null){

        if($payKey) {

            $response = \PaypalAdaptive::getDetails($payKey);

            return $response->returnUrl;
        }

    }

    public function getState($payKey=null){

        if($payKey) {

            $response = \PaypalAdaptive::getDetails($payKey);

            return $response->actionType;
        }

    }

    public function execute($payKey, $payerId){


        $response = \PaypalAdaptive::Execute($payKey);

        return $response;

    }

    public function getErrorMessage(){


       return \PaypalAdaptive::getError();

    }


    public function getTotal(){

        return $this->total;

    }

    /**
     * @param $offer_items
     */
    public function addItems($offer_items){

        $this->item_list= PaypalPayment::ItemList();

        foreach($offer_items as $offer_item){

            if(($offer_item['type']=='Package')){

                foreach($offer_item['children'] as $package){


                    $this->total+=$package['item_price'] * $package['sessions'];

                    \PaypalAdaptive::addItem(['name'=>$package['item_name'], 'price'=> $package['item_price'] * $package['sessions'],'itemPrice'=>$package['item_price'],'itemCount'=>$package['sessions']]);
                }

            }else{



                if(($offer_item['type']=='Service')){

                    $this->total+=$offer_item['sessions'] *  $offer_item['session_price'];


                    \PaypalAdaptive::addItem(['name'=>$offer_item['item_name'], 'price'=> $offer_item['sessions'] *  $offer_item['session_price'],'itemPrice'=>$offer_item['session_price'],'itemCount'=>$offer_item['sessions']]);



                    if($offer_item['travel_time']){

                        $this->total+=$offer_item['trip_price'];

                        \PaypalAdaptive::addItem(['name'=>"Travel Time for". $offer_item['item_name'], 'price'=> number_format($offer_item['trip_price'],2),'itemPrice'=>$offer_item['trip_price'],'itemCount'=>1]);



                    }
                }else{


                    $this->total+=$offer_item['item_price'];


                    \PaypalAdaptive::addItem(['name'=>$offer_item['item_name'], 'price'=> number_format($offer_item['item_price'],2),'itemPrice'=>$offer_item['item_price'],'itemCount'=>1]);



                    if($offer_item['shipping_id']!=0){

                        $this->total+=$offer_item['shipping_price'];


                        \PaypalAdaptive::addItem(['name'=>$offer_item['shipping_name'], 'price'=> number_format($offer_item['shipping_price'],2),'itemPrice'=>$offer_item['shipping_price'],'itemCount'=>1]);


                    }


                }

            }
        }

        //set item options

    }

}
