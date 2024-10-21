<?php

require '../vendor/autoload.php';

class ConektaApi{

    
    private $webToken;
    private $customerId;
    private $orderId;
    private $CONEKTACNF;
    private $accept_language;
    private $three_ds_url;
    private $paymentStatus;


    function __construct($webToken,$ConektaApiKey){
        
        $this->accept_language = 'es';
        $this->webToken = $webToken;
        $this->customerId = null;
        $this->orderId = null;
        $this->CONEKTACNF = Conekta\Configuration::getDefaultConfiguration()->setAccessToken($ConektaApiKey);
    }

    

    public function createCustomer($email,$phone,$name){
        
        $apiCustomer = new Conekta\Api\CustomersApi(
            new GuzzleHttp\Client(),
            $this->CONEKTACNF
        );

        $customer_data = array(
            "email"           => $email,
            "phone"           => $phone,
            "name"            => $name,
            "payment_sources" => array(
                array(
                    "type"     => "card",
                    "token_id" => $this->webToken
                )
            )
        );

        $customer = new \Conekta\Model\Customer($customer_data);

        try{

            $apiConsume = $apiCustomer->createCustomer($customer,$this->accept_language);
            error_log(print_r($apiConsume,true));
            $this->customerId = $apiConsume->getId();
            
            return true;

        }catch(Exception $e){
            throw $e;
        }

        return false;
    }    

    public function createOrder($nombre_plan,$precio_plan,$metadata,$return_url){

        $apiOrders = new Conekta\Api\OrdersApi(
            new GuzzleHttp\Client(),
            $this->CONEKTACNF
        );

        $order_data = array(
            "three_ds_mode" => "strict",
            "return_url"    => $return_url,
            "currency"      => "MXN",
            "metadata"      => $metadata,
            "charges"       => array(
                array(
                    "amount"         => $precio_plan*100,
                    "payment_method" => array(
                        "type" => "default"
                    )
                )
            ),
            "customer_info" => array(
                "customer_id" => $this->customerId
            ),
            "line_items"    => array(
                array(
                    "name"       => $nombre_plan,
                    "unit_price" => $precio_plan * 100,
                    "quantity"   => 1
                )
            ),
            
        );

        $order_request = new \Conekta\Model\OrderRequest($order_data);

        try{
            $apiConsume = $apiOrders->createOrder($order_request,$this->accept_language);
            $this->orderId = $apiConsume->getId();
            $this->three_ds_url = $apiConsume->getNextAction()->getRedirectToUrl()->getUrl();
            $this->paymentStatus = $apiConsume->getPaymentStatus();
            return true;
        }catch(Exception $e){
            throw $e;
        }

        return false;
    }

    public function getOrderById($order_id){

        $apiOrders = new Conekta\Api\OrdersApi(
            new GuzzleHttp\Client(),
            $this->CONEKTACNF
        );

        try{
            $apiConsume = $apiOrders->getOrderById($order_id,$this->accept_language);
            $charges = $apiConsume->getCharges();
            $data = $charges->getData();
            $this->failureCode = $data[0]->getFailureCode();
            $this->failureMessage = $data[0]->getFailureMessage();
            return true;
        }catch(Exception $e){
            throw $e;
        }
    }

    public function getOrderId(){
        return $this->orderId;
    }

    public function getCustomerId(){
        return $this->customerId;
    }

    public function getThreeDsUrl(){
        return $this->three_ds_url;
    }
    public function getPaymentStatus(){
        return $this->paymentStatus;
    }
    public function getFailureCode(){
        return $this->failureCode;
    }
    public function getFailureMessage(){
        return $this->failureMessage;
    }
}

?>