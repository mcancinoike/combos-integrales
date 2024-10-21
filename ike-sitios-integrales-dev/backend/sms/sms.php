<?php

    class Sms {
        private $telephone;
        private $plan_telephone;
        private $folio;
        private $date;
        private $conexion;
        private $renovation;
        private $validity;
        public $error;

        public function __construct($telephone, $plan_telephone, $folio, $date, $validity, $renovation,$conexion){
            $this->telephone = $telephone;
            $this->plan_telephone = $plan_telephone;
            $this->folio = $folio;
            $this->date = $date;
            $this->conexion = $conexion;
            $this->validity = $validity;
            $this->renovation = $renovation;
        }

        public function send(){
            if(!$this->sms()){
                return false;
            }
            return true;
        }

        public function senDomicilia(){
            if(!$this->smsDomicilia()){
                return false;
            }
            return true;
        }

        public function sendCashi(){
            if(!$this->smsCashi()){
                return false;
            }
            return true;
        }

        public function sendProductRegister(){
            if(!$this->smsProductRegister()){
                return false;
            }
            return true;
        }

        public function sms(){
            try {
                $tokenSms = $this->conexion->getTokenSms();
                if(!$tokenSms){
                    throw new Exception("Error al obtener envio SMS");
                    return false;
                }
                if($this->renovation == 1){
                    $message = "Su plan salud folio ".$this->folio." se activo y esta vigente del ".$this->date. " al ".$this->validity." Para solicitar servicio o hacer valido su check up llame al ". $this->plan_telephone;
                }else{
                    $message = "Su plan salud folio ".$this->folio." se activo y esta vigente del ".$this->date. " al ".$this->validity." Para solicitar servicio llame al ". $this->plan_telephone;
                }

                $token = $tokenSms['access_token'];
                $tokenType = $tokenSms['token_type'];
                $data = [
                    "identifier" => [
                        "tenants" => "adff7f6a-e97d-11eb-9a03-0242ac130003",
                        "app" => "Membresias Salud Walmart E-commerce"
                    ],
                    "petition" => [
                        "message" => $message,
                        "phone" => $this->telephone
                    ]
                ];
                $sendSms = $this->conexion->startCulr($this->conexion->urlSendSms, "sms", $token, $data);
                if($sendSms["message"] != "OK"){
                    throw new Exception("Error al enviar SMS");
                    error_log("Error al enviar SMS");
                    return false;
                }
                return true;
            } catch (\Exception $e) {
                $this->error = $e->getMessage();
            }
        }

        public function smsDomicilia(){
            try {
                $tokenSms = $this->conexion->getTokenSms();
                if(!$tokenSms){
                    throw new Exception("Error al obtener envio SMS");
                    return false;
                }
                $message = "Su plan ha sido domiciliada exitosamente. Para solicitar servicio o hacer valido su check up llame al ". $this->plan_telephone;
                
                $token = $tokenSms['access_token'];
                $tokenType = $tokenSms['token_type'];
                $data = [
                    "identifier" => [
                        "tenants" => "adff7f6a-e97d-11eb-9a03-0242ac130003",
                        "app" => "Membresias Salud Walmart E-commerce"
                    ],
                    "petition" => [
                        "message" => $message,
                        "phone" => $this->telephone
                    ]
                ];
                $sendSms = $this->conexion->startCulr($this->conexion->urlSendSms, "sms", $token, $data);
                if($sendSms["message"] != "OK"){
                    throw new Exception("Error al enviar SMS");
                    error_log("Error al enviar SMS");
                    return false;
                }
                return true;
            } catch (\Exception $e) {
                $this->error = $e->getMessage();
            }
        }

        public function smsCashi(){
            try {
                $tokenSms = $this->conexion->getTokenSms();
                if(!$tokenSms){
                    throw new Exception("Error al obtener envio SMS");
                    return false;
                }
                $message = "Su plan salud con folio ".$this->folio." se activo y está vigente del ".$this->date. " al ".$this->validity." Para solicitar servicio solo llame al ". $this->plan_telephone;
                
                $token = $tokenSms['access_token'];
                $tokenType = $tokenSms['token_type'];
                $data = [
                    "identifier" => [
                        "tenants" => "adff7f6a-e97d-11eb-9a03-0242ac130003",
                        "app" => "Membresias Salud"
                    ],
                    "petition" => [
                        "message" => $message,
                        "phone" => $this->telephone
                    ]
                ];
                $sendSms = $this->conexion->startCulr($this->conexion->urlSendSms, "sms", $token, $data);
                if($sendSms["message"] != "OK" || empty($sendSms["message"])){
                    throw new Exception("Error al enviar SMS");
                    error_log("Error al enviar SMS");
                    return false;
                }
                return true;
            } catch (\Exception $e) {
                $this->error = $e->getMessage();
            }
        }

        public function smsProductRegister(){
            try {
                $tokenSms = $this->conexion->getTokenSms();
                if(!$tokenSms){
                    throw new Exception("Error al obtener envio SMS");
                    return false;
                }
                //$fechaFin =  date("d-m-Y",strtotime($this->date."+ " . $this->validity ." days")); 
                
                $message = "Su plan salud con folio ".$this->folio." se activo y está vigente del ".$this->date. " al ". $this->validity ." Para solicitar servicio solo llame al ". $this->plan_telephone;
                
                $token = $tokenSms['access_token'];
                $tokenType = $tokenSms['token_type'];
                $data = [
                    "identifier" => [
                        "tenants" => "adff7f6a-e97d-11eb-9a03-0242ac130003",
                        "app" => "Membresias Salud"
                    ],
                    "petition" => [
                        "message" => $message,
                        "phone" => $this->telephone
                    ]
                ];
                $sendSms = $this->conexion->startCulr($this->conexion->urlSendSms, "sms", $token, $data);
                if($sendSms["message"] != "OK" || empty($sendSms['message'])){
                    throw new Exception("Error al enviar SMS");
                    error_log("Error al enviar SMS");
                    return false;
                }
                return true;
            } catch (\Exception $e) {
                $this->error = $e->getMessage();
            }
        }

        public function smsVerify(){
            try {
                
                $code = $this->generarCodigo();

                if(!$this->registerCode($code)){
                    throw new Exception("Error al guardar SMS");
                    return false;
                };

                $tokenSms = $this->conexion->getTokenSms();
                if(!$tokenSms){
                    throw new Exception("Error al obtener envio SMS");
                    return false;
                }
                $message = "Tu codigo de verificación para ingresar a MEMBRESIA SALUD es " . $code;
                
                $token = $tokenSms['access_token'];
                $tokenType = $tokenSms['token_type'];
                $data = [
                    "identifier" => [
                        "tenants" => "adff7f6a-e97d-11eb-9a03-0242ac130003",
                        "app" => "Membresias Salud"
                    ],
                    "petition" => [
                        "message" => $message,
                        "phone" => $this->telephone
                    ]
                ];
                $sendSms = $this->conexion->startCulr($this->conexion->urlSendSms, "sms", $token, $data);
                if($sendSms["message"] != "OK" || empty($sendSms["message"])){
                    throw new Exception("Error al enviar SMS");
                    error_log("Error al enviar SMS");
                    return false;
                }
                return true;
            } catch (\Exception $e) {
                $this->error = $e->getMessage();
            }
        }

        public function registerCode($code){
            try {
                $query = "UPDATE orders_walmart SET sms_code = :code WHERE telephone = :telephone";

                $queryArray = [
                    "code" => $code,
                    "telephone"=>$this->telephone
                ];
    
                if(!$this->conexion->insertData($query, $queryArray)){
                    $this->error = 'Hubo problemas al guardar tu codigo. Por favor intenta más tarde';
                    throw new Exception("Error al realizar la activación");
                    return false;
                }

                return true;
            } catch (\Exception $e) {
                $this->error = $e->getMessage();
                return false;
            }
        }

        

        public function generarCodigo() {
            $codigo = '';
            for ($i = 0; $i < 6; $i++) {
                $codigo .= mt_rand(0, 9); // Genera un dígito aleatorio y lo agrega al código
            }
            return $codigo;
        }

       
        



    }
?>