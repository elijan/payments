<?php

namespace Goldtorch\Payments;



class PaypalMethod {

    private $_ClientId = 'AfXoiRCDR5d159HAhePs6wO1BxncrzaNF4n3rA5SKfgfKlTFFHa96r8exHx_';
    private $_ClientSecret = 'EHVEGRDk6PWvfbDqW_0mKSKLjyIFg4ex2trwOMz32ljEFK_baclLQ2XUDm8D';

    private $_apiContext;

    private $total = 0;
    private $amount;
    private $payment = null;
    private $response;

    private $items = [];
    private $item_list;


    public function __construct()
    {
        $this->_apiContext = PaypalPayment:: ApiContext(
            PaypalPayment::OAuthTokenCredential(
                $this->_ClientId,
                $this->_ClientSecret
            )
        );


    }


    public function getTotal(){


        return $this->total;

    }

    public function addItems($offer_items){

        $this->item_list= PaypalPayment::ItemList();

        foreach($offer_items['items'] as $offer_item){

        if(($offer_item['type']=='Package')){

            foreach($offer_item['children'] as $package){



                $this->total+=$package['item_price'] * $package['sessions'];

                $item = PaypalPayment::Item();
                $item->setQuantity("".$package['sessions']);
                $item->setName($package['item_name']);
                $item->setPrice("".$package['item_price']);
                $item->setCurrency('AUD');
                $item->setSku("".$package['id']);

                $this->items[]  =$item;
            }

        }else{



            if(($offer_item['type']=='Service')){

                $this->total+=$offer_item['sessions'] *  $offer_item['session_price'];
                $item = PaypalPayment::Item();
                $item->setQuantity("".$offer_item['sessions']);
                $item->setName($offer_item['item_name']);
                $item->setPrice(number_format($offer_item['session_price'],2));
                $item->setCurrency('AUD');
                $item->setSku("".$offer_item['id']);

                $this->items[]  =$item;

                if($offer_item['travel_time']){

                    $this->total+=$offer_item['trip_price'];
                    $item = PaypalPayment::Item();
                    $item->setQuantity("1");
                    $item->setName("Travel Time for". $offer_item['item_name']);
                    $item->setPrice(number_format($offer_item['trip_price'],2));
                    $item->setCurrency('AUD');
                    $item->setSku("".$offer_item['id']);

                    $this->items[]  =$item;


                }
            }else{


                $this->total+=$offer_item['item_price'];
                $item = PaypalPayment::Item();
                $item->setQuantity("1");
                $item->setName($offer_item['item_name']);
                $item->setPrice(number_format($offer_item['item_price'],2));
                $item->setCurrency('AUD');
                $item->setSku("".$offer_item['id']);

                $this->items[]  =$item;


                if($offer_item['shipping_id']!=0){

                    $this->total+=$offer_item['shipping_price'];

                    $item = PaypalPayment::Item();
                    $item->setQuantity("1");
                    $item->setName($offer_item['shipping_name']);
                    $item->setPrice(number_format($offer_item['shipping_price'],2));
                    $item->setCurrency('AUD');
                    $item->setSku("shipping_".$offer_item['id']);

                    $this->items[]  =$item;

                }else{

                    /*$item = PaypalPayment::Item();
                    $item->setQuantity("1");
                    $item->setName("Pickup");
                    $item->setPrice("0.00");
                    $item->setCurrency('AUD');
                    $item->setSku("shipping_".$offer_item['id']);*/
                }


            }

        }
        }

        $this->item_list->setItems($this->items);;

        //set amount
        $this->setAmount(number_format($this->total,2));

    }



    public function getAdaptivePaymentItems($offer_items){

        $this->item_list= PaypalPayment::ItemList();

        foreach($offer_items['items'] as $offer_item){

            if(($offer_item['type']=='Package')){

                foreach($offer_item['children'] as $package){



                    $this->total+=$package['item_price'] * $package['sessions'];

                    $item = PaypalPayment::Item();
                    $item->setQuantity("".$package['sessions']);
                    $item->setName($package['item_name']);
                    $item->setPrice("".$package['item_price']);
                    $item->setCurrency('AUD');
                    $item->setSku("".$package['id']);

                    $this->items[]  =$item;
                }

            }else{



                if(($offer_item['type']=='Service')){

                    $this->total+=$offer_item['sessions'] *  $offer_item['session_price'];
                    $item = PaypalPayment::Item();
                    $item->setQuantity("".$offer_item['sessions']);
                    $item->setName($offer_item['item_name']);
                    $item->setPrice(number_format($offer_item['session_price'],2));
                    $item->setCurrency('AUD');
                    $item->setSku("".$offer_item['id']);

                    $this->items[]  =$item;

                    if($offer_item['travel_time']){

                        $this->total+=$offer_item['trip_price'];
                        $item = PaypalPayment::Item();
                        $item->setQuantity("1");
                        $item->setName("Travel Time for". $offer_item['item_name']);
                        $item->setPrice(number_format($offer_item['trip_price'],2));
                        $item->setCurrency('AUD');
                        $item->setSku("".$offer_item['id']);

                        $this->items[]  =$item;


                    }
                }else{


                    $this->total+=$offer_item['item_price'];
                    $item = PaypalPayment::Item();
                    $item->setQuantity("1");
                    $item->setName($offer_item['item_name']);
                    $item->setPrice(number_format($offer_item['item_price'],2));
                    $item->setCurrency('AUD');
                    $item->setSku("".$offer_item['id']);

                    $this->items[]  =$item;


                    if($offer_item['shipping_id']!=0){

                        $this->total+=$offer_item['shipping_price'];

                        $item = PaypalPayment::Item();
                        $item->setQuantity("1");
                        $item->setName($offer_item['shipping_name']);
                        $item->setPrice(number_format($offer_item['shipping_price'],2));
                        $item->setCurrency('AUD');
                        $item->setSku("shipping_".$offer_item['id']);

                        $this->items[]  =$item;

                    }else{

                        /*$item = PaypalPayment::Item();
                        $item->setQuantity("1");
                        $item->setName("Pickup");
                        $item->setPrice("0.00");
                        $item->setCurrency('AUD');
                        $item->setSku("shipping_".$offer_item['id']);*/
                    }


                }

            }
        }

        $this->item_list->setItems($this->items);;

        //set amount
        $this->setAmount(number_format($this->total,2));

    }


    public function setAmount($price){

        $this->amount = PaypalPayment::Amount();
        $this->amount->setCurrency("AUD");
        $this->amount->setTotal($price);


    }



    public function process($transaction, $description="Payment Description ")
    {
        //do some checks here

        $payer = PaypalPayment::Payer();
        $payer->setPaymentMethod("paypal");


        $paypal_transaction =  PaypalPayment::Transaction();
        $paypal_transaction->setAmount($this->amount);

        $paypal_transaction->setItemList($this->item_list);
        $paypal_transaction->setDescription($description);

        $redirectUrls = PaypalPayment::RedirectUrls();
        $redirectUrls->setReturn_url(url('checkout/approved?transaction='.$transaction->transaction_id));
        $redirectUrls->setCancel_url(url('checkout/cancel/'.$transaction->transaction_id));


        $this->payment =  PaypalPayment::Payment();

        $this->payment->setIntent("sale");
        $this->payment->setPayer($payer);
        $this->payment->setRedirectUrls($redirectUrls);
        $this->payment->setTransactions(array($paypal_transaction));


        try {
            $this->payment->create($this->_apiContext);


        } catch (\PayPal\Exception\PPConnectionException $ex) {
            echo "Exception: " . $ex->getMessage() . PHP_EOL;
            var_dump($ex->getData());
           exit(1);
        }

    }



    public function getData(){

        return $this->payment->toArray();

    }

    public function getPayment($payPalId){

        if($this->payment && $payPalId==null){

            return $this->payment;

        }



        $this->payment = PaypalPayment::get($payPalId, $this->_apiContext);

        return  $this->payment;


    }


    public function getTransaction($payPalId){

            $payment= $this->getPayment($payPalId);

            return $payment->getTransactions()[0];


    }

    public function getPayer($payPalId){

        $payment= $this->getPayment($payPalId);

        //check status of the pauyer???

        if($payment){
            return $payment->getPayer();
        }

        return false;


    }
    public function execute($payPalId, $payerID=null){


        if($payerID==null){

            $payerID = $this->getPayer->getPayerInfo()->getPayerId();

        }


        if($this->getStatus($payPalId)=="created") {
            $payment = $this->getPayment($payPalId);

            $execution = PaypalPayment::PaymentExecution();


            $execution->setPayerId($payerID);
            try {
                $payment->execute($execution, $this->_apiContext);
            } catch (\PayPal\Exception\PPConnectionException $ex) {
                echo "Exception: " . $ex->getMessage() . PHP_EOL;
                dd($ex->getData());
                exit(1);
            }


        }
    }

    public function getLink(){


        //grab the itent
        //adn decide on response

        foreach($this->payment->getLinks() as $link) {
            if($link->getRel() == 'approval_url') {
               return $link->getHref();
            }

        }

        return false;

    }


    public function getStatus($payPalId = null){

        //check if the payment exist
        if($payPalId!=null){

            $this->payment = $this->getPayment($payPalId);
        }

        if($this->payment)
            return $this->payment->getState();



        return "error";

    }

    public function getResponse(){

        return $this->response;

    }



}
?>