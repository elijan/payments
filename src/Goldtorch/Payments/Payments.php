<?php

namespace Goldtorch\Payments;

//@TOO create intereface for comon methods



use Elijan\PaypalAdaptive\PaypalAdaptive;

class Payments
{

    private $payment_method;
    private $payment_object;


    public function create($type, $transaction_id=null)
    {
        $this->payment_method = $type;

        $this->payment_object = $this->getPaymentMethodInstance($this->payment_method);

        $this->payment_object->createOrder($transaction_id);

    }

    private function getPaymentMethodInstance($method)
    {

        switch ($method) {

            case "paypal":
                return new PaypalMethod();

                break;
            case "schedule":

             break;

            case "paypal-adaptive":
                return new AdaptivePayments();
            break;


        }


    }

    public function addItems($items)
    {

        $this->payment_object->addItems($items);

    }

    public function process($transaction)
    {

        $this->payment_object->process($transaction);

    }

    public function getData()
    {

        return $this->payment_object->getData();
    }

    public function getLink()
    {
        return $this->payment_object->getLink();
    }

    public function getTransaction($payerId = null)
    {
        return $this->payment_object->getTransaction($payerId);
    }

    public function getStatus($payPalId)
    {
        return $this->payment_object->getStatus($payPalId);

    }

    public function getPayer($payPalId)
    {

        return $this->payment_object->getPayer($payPalId);

    }

    public function getTotal()
    {
        return $this->payment_object->getTotal();
    }

    public function getReturnUrl($payKey=null){


        return $this->payment_object->getReturnUrl($payKey);
    }

    public function getState($payKey=null){


        return $this->payment_object->getState($payKey);
    }


    public function execute($payPalId, $payerID=null)
    {
        return $this->payment_object->execute($payPalId, $payerID);
    }

    public function getErrorMessage(){


        return $this->payment_object->getErrorMessage();
    }



}

?>