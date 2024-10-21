<?php

    class Conexion {
        private $serverDBEscritura;
        private $serverDBLectura;
        private $user;
        private $password;
        private $database;
        private $conexion;
        private $config;
        private $authorizationOauth;
        private $urlTokenSms;
        private $usernameTokenSms;
        private $passwordTokenSms;
        private $urlTokenConsulta;
        public $puertoEmail;
        public $ambiente;
        public $keyString;
        public $arrayAES;
        public $urlSendSms;
        public $urlOauth;
        public $urlApiAfiliados;
        public $rutaPrincipal;
        public $urlCancelar;
        public $urlServicios;
        public $urlAvisoP;
        public $apiKeyConekta;
        public $apiKeyConektaPay;
        public $versionConekta;
        public $urlConsulta;
        public $conexionLectura;
        public $captcha;
        
        function __construct(){
            $data = $this->getConexion();            
            $this->serverDBEscritura = $data["serverDBEscritura"];
            $this->serverDBLectura = $data["serverDBLectura"];
            $this->user = $data["userDB"];
            $this->password = $data["passDB"];
            $this->database = $data["dataBaseDB"];
            $this->puertoEmail = $data["puertoEmail"];
            $this->ambiente = $data["ambiente"];
            $this->keyString = $data["keyString"];
            $this->arrayAES = $data["arrayAES"];
            $this->authorizationOauth = $data["authorizationOauth"];
            $this->urlTokenSms = $data["urlTokenSms"];
            $this->usernameTokenSms = $data["usernameTokenSms"];
            $this->passwordTokenSms = $data["passwordTokenSms"];
            $this->urlSendSms = $data["urlSendSms"];
            $this->urlOauth = $data["urlOauth"];
            $this->urlApiAfiliados = $data["urlApiAfiliados"];
            $this->rutaPrincipal = $data["rutaPrincipal"];
            $this->urlCancelar = $data["urlCancelar"];
            $this->urlServicios = $data["urlServicios"];
            $this->urlAvisoP = $data["urlAvisoP"];
            $this->apiKeyConekta = $data["apiKeyConekta"];
            $this->apiKeyConektaPay = $data["apiKeyConektaPay"];
            $this->versionConekta = $data["versionConekta"];
            $this->urlTokenConsulta = $data["urlTokenConsulta"];
            $this->urlConsulta = $data["urlConsulta"];
            $this->captcha = $data["captcha"];
            try {
                 $this->conexion = new PDO("mysql:host=".$this->serverDBEscritura.";dbname=".$this->database,$this->user,$this->password,array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
                 $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                error_log( 'Falló la conexión: ' . $e->getMessage());
            }
            if($this->ambiente != 2){
                try {
                     $this->conexionLectura = new PDO("mysql:host=".$this->serverDBLectura.";dbname=".$this->database,$this->user,$this->password,array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
                     $this->conexionLectura->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    error_log( 'Falló la conexión de Lectura: ' . $e->getMessage());
                }
            }else{
                $this->conexionLectura = $this->conexion;
            }
        }

        private function getConexion(){
            $this->config = parse_ini_file("config.ini");
            return $this->config;
        }

        private function convertUTF8($array){
            array_walk_recursive($array, function(&$item, $key){
                if(!mb_detect_encoding($item,'utf-8', true)){
                    $item = utf8_encode($item);
                }
            });
            return $array;
        }

         /**
         * Iniciar transaccion
         */
        public function beginTransaction(){
            $this->conexion->beginTransaction();
        }

        /**
         * Hacer rollback
         */
        public function rollback(){
            $this->conexion->rollback();
        }

        /**
         * Guardar datos
         */
        public function commit(){
            $this->conexion->commit();
        }

        /**
         * Obtiene los datos de la BD mediante un query
         * @param sqlstr Parametro que contiene un query para la base de datos
         * @return return devuelve la busqueda del query
         */
        public function getData($sqlstr, $sqlArray = null){
            try {
                $results = $this->conexionLectura->prepare($sqlstr);
                $results->execute($sqlArray);
                if(!$results->rowCount()){
                    return [];
                }else{
                    foreach ($results as $key) {
                        $resultArray[] = $key;
                    }
                    return $this->convertUTF8($resultArray);
                }
            } catch (PDOException $e) {
                error_log( 'Algo ha salido mal: ' . $e->getMessage());
                return [];
            }
        }
        
        /**
         * Inserta en la BD
         * @param sqlstr Parametro que contiene un query
         * @param sqlArray Parametro que contiene un array de valores para insertar, puede ser null
         * @return true retorna un booleano true si el insert fue exitoso
         */
        public function insertData($sqlstr, $sqlArray = null){
            try {
                $results = $this->conexion->prepare($sqlstr);
                $results->execute($sqlArray);
                if(!$results->rowCount()){
                    return false;
                }else{
                    return true;
                }
            } catch (PDOException $e) {
                error_log( 'Algo ha salido mal: ' . $e->getMessage());
                return false;
            }
        }

        /**
         * Encriptado de tarjeta
         * @param card Parametro a encriptar
         * @return encryptCard Retorna la tarjeta encriptada
         */
        public function encryptCard($card){
            try {
                if($card == null || $card == ""){
                    return null;
                }
                $query = "SET block_encryption_mode = 'aes-256-cbc';";
                $this->conexionLectura->query($query);
                $query = "SELECT AES_ENCRYPT('$card',SHA2('$this->keyString',512),'$this->arrayAES') as card";
                $encryptCard = $this->conexionLectura->query($query);
                return $encryptCard->fetchColumn();
            } catch (PDOException $e) {
                error_log( 'Algo ha salido mal al encriptar: ' . $e->getMessage());
            }
        }

        /**
         * Desencriptado de tarjeta
         * @return return Retorna un SET SQL para desencriptar la tarjeta
         */
        public function decryptCard(){
            $query = "SET block_encryption_mode = 'aes-256-cbc';";
            return $this->conexionLectura->query($query);
        }

       /**
         * Obtiene la fecha actual en formato "9 de Julio de 2021"
         * @param intervalo_anual Parametro para un intervalo anual, puede ser null
         * @param intervalo_mensual Parametro para intervalo mensual, puede ser null
         * @return date Fecha requerida
         */
        public function currentDate($intervalo_anual = null, $intervalo_mensual = null){  
            date_default_timezone_set('America/mexico_city');
            $week_days = array ("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado");  
            $months = array ("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");  
            $month_now = date ("n");
            $year_now = date ("Y");
            if($month_now + $intervalo_mensual > 12){
                $month_now = 0;
                if($intervalo_anual == null || $intervalo_anual == 0){
                    $intervalo_anual = 1;
                }
            }
            $intervalo_anual == null ? $year_now : $year_now = $year_now + $intervalo_anual;
            $intervalo_mensual == null ? $month_now : $month_now = $month_now + $intervalo_mensual;  
            $day_now = date ("j");  
            $date = $day_now . " de " . $months[$month_now] . " de " . $year_now;   
            return $date;    
        }

        /**
         * Obtiene la fecha actual en formato "1995-06-14"
         * Formatea fechas para insertar a la BD correctamente y en API afiliados
         * @param date Parametro a formatear, puede ser null
         * @return day Retorna la fecha con milisegundos o sin milisegundos dependiendo que se necesite
         */
        public function formatDay($date = null){
            if($date != null){
                $day = new DateTime($date);
                return $day->format('Y-m-d');
            }else{
                $day = new DateTime();
                $timeZone = new DateTimeZone('America/Mexico_City');
                $day->setTimezone($timeZone);
                return $day->format('Y-m-d H:i:s');
            }
        }

        public function formatDay2($date = null){
            $day = new DateTime($date);
            return $day->format('d-m-Y');
        }

        public function formatPaidAt(){
            $day = new DateTime();
            $timeZone = new DateTimeZone('America/Mexico_City');
            $day->setTimezone($timeZone);
            return $day->format('Y-m-d');
        }

        public function compareDate($date){
            $day = new DateTime();
            $timeZone = new DateTimeZone('America/Mexico_City');
            $day->setTimezone($timeZone);
            $dateVigencia = new DateTime($date);

            if($day > $dateVigencia){
                return false;
            }
            return true; 
        }


        public function compareTwoDates($date,$dateTwo){
            $dateVigenciaUno = new DateTime($date);
;
            $dateVigenciaDos = new DateTime($dateTwo);

            if($dateVigenciaUno > $dateVigenciaDos){
                return true;
            }
            return false; 
        }
        public function formatDate($date = null, $validity = null){
            $setDate = new DateTime($date);
            $timeZone = new DateTimeZone('America/Mexico_City');
            $setDate->setTimezone($timeZone);
            if($validity != null){
                $setDate = $setDate->modify('+ '.$validity.' days');
            }
            $setDate = $setDate->format('d-m-Y');
            return $setDate;
        }

       /**
         * Obtiene el ultimo ID que se inserto en la tabla ORDERS que usaremos como Folio identificador
         * @return return Retorna el folio para los beneficiarios 
         */
        public function getFolio($order_id){
            $query = "SELECT LAST_INSERT_ID() AS id FROM orders_walmart WHERE order_id = '$order_id' limit 1";
            $response = $this->conexion->query($query);
            return $response->fetchColumn();
        }

        /**
         * Conexión mediante CURL para API´S
         * Obtiene un token de autenticación para la API de afiliados
         * Inserta los valores que le enviamos a la API de afiliados
         * @param url Parametro con la url con la que necesitamos inciar el CURL
         * @param tokenType Tipo de token de autorización para el envio de datos a la API
         * @param token Token de autorización para el envio de datos a la API
         * @param data_array Array con los valores a insertar en la API.
         * @return data Retorna la informacion obtenida en casa de exito, en caso de error, retorna un booleano false
         */
        public function startCulr($url, $tokenType = "", $token = "", $data_array = ""){
            try {
                    # Iniciamos una instancia de curl
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
                # Verificamos si estan enviando datos para la API de afiliados o si estan obteniendo un token

                if($tokenType == "sms" && $token != ""){
                    curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($data_array));
                    $array = array(
                        "authorization: ".$token ,
                        "content-type: application/json");
                    $errorType = "/Load/Single";
                }
                else if($tokenType == "api"){
                    curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($data_array));
                    $array = array(
                        "Accesstoken: ".$token ,
                        "content-type: application/json");
                    $errorType = "/Load/Single";
                }
                else if($token != "" && $tokenType != ""){
                    curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($data_array));
                    $array = array(
                        "authorization: ".$tokenType." ".$token ,
                        "content-type: application/json");
                    $errorType = "/Load/Single";
                }
                # Respuesta en caso de busqueda de token
               else{
                    $array = array(
                        "authorization: ".$this->authorizationOauth,
                        "content-type: application/json");
                    $errorType = "oauth/token";
                }
                
                curl_setopt($curl, CURLOPT_HTTPHEADER, $array);
                # Guardamos la captura de informacion en data
                curl_setopt($curl, CURLINFO_HEADER_OUT, true);
                $data = curl_exec($curl);
                $error = curl_error($curl);
                #Si existe un error lo guardamos en logs_api
                if($error){
                    $query = "INSERT INTO logs_api (api_response,date_created,error)
                    VALUES('Error: ".$errorType." ','".$this->formatDay()."','".$error."')";
                    $this->insertData($query);
                    # Cerramos el Curl y liberamos recursos del sistema
                    curl_close($curl);
                    return false;
                }
                # Si no hay errores retornamos la respuesta en formato JSON
                else{
                    # Cerramos el Curl y liberamos recursos del sistema
                    curl_close($curl);
                    return json_decode($data, true);
                }
           } catch (\Exception $e) {
               $this->error = $e->getMessage();
               return false;
           }
        }

        public function sessionFilter($sesiones, $busqueda, $index){
            $modelos = array_filter($sesiones, function($e) use ($busqueda, $index){
                if (array_key_exists($index, $e)) {
                    return $e[$index] == $busqueda;
                }else{
                    return array();
                }
            });
            
            return $modelos;
        }

        public function getTokenSms(){
            try {
                $ch = curl_init($this->urlTokenSms);
                $data = json_encode(array(
                    'user_name' => $this->usernameTokenSms,
                    'password' => $this->passwordTokenSms
                ));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);
                return json_decode($result, true);
            } catch (\Exception $e) {
                return false;
            }
            
        }
        
        public function getTokenConsulta(){
            try {
                $ch = curl_init($this->urlTokenConsulta);
                $data = json_encode(array(
                    'login' => array(
                        'user' => $this->usernameTokenSms,
                        'password' => $this->passwordTokenSms
                    )
                ));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);
                return json_decode($result, true);
            } catch (\Exception $e) {
                return false;
            }
            
        }

    }
?>