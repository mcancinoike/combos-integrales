<?php

class Conexion
{
    private $serverDBEscritura;
    private $serverDBLectura;
    private $user;
    private $passDB;
    private $database;
    private $conexion;
    private $conexionLectura;
    private $config;
    private $ambiente;
    private $urlTokenSms;
    private $usernameTokenSms;
    private $passwordTokenSms;
    private $urlSendSms;
    public $captchaPublic;
    public $captchaSecret;
    public $urlOauth;
    public $urlApiAfiliados;
    public $authorizationOauth;
    public $mailUser;
    public $mailPassword;
    public $VERSION;

    function __construct()
    {
        $data = $this->getConexion();
        $this->user = $data["userDB"];
        $this->passDB = $data["passDB"];
        $this->serverDBEscritura = $data["serverDBEscritura"];
        $this->serverDBLectura = $data["serverDBLectura"];
        $this->database = $data["dataBaseDB"];
        $this->ambiente = $data["ambiente"];
        $this->urlTokenSms = $data["urlTokenSms"];
        $this->usernameTokenSms = $data["usernameTokenSms"];
        $this->passwordTokenSms = $data["passwordTokenSms"];
        $this->urlSendSms = $data["urlSendSms"];
        $this->captchaSecret = $data["captchaSecret"];
        $this->captchaPublic = $data["captchaPublic"];
        $this->urlOauth = $data["urlOauth"];
        $this->authorizationOauth = $data["authorizationOauth"];
        $this->urlApiAfiliados = $data["urlApiAfiliados"];
        $this->mailUser = $data["mailUs"];
        $this->mailPassword = $data["mailPa"];
        $this->VERSION = $data["VERSION"];

        try {
            $this->conexion = new PDO("mysql:host=" . $this->serverDBEscritura . ";dbname=" . $this->database, $this->user, $this->passDB, array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log( 'Falló la conexión de Escritura: ' . $e->getMessage());
        }
        if ($this->ambiente != 2) {
            try {
                $this->conexionLectura = new PDO("mysql:host=" . $this->serverDBLectura . ";dbname=" . $this->database, $this->user, $this->passDB, array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
                $this->conexionLectura->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                error_log( 'Falló la conexión de Lectura: ' . $e->getMessage());
            }
        } else {
            $this->conexionLectura = $this->conexion;
        }
    }

    private function getConexion()
    {
        $this->config = parse_ini_file("config.ini");
        return $this->config;
    }

    private function convertUTF8($array)
    {
        array_walk_recursive($array, function (&$item, $key) {
            if (!mb_detect_encoding($item, 'utf-8', true)) {
                $item = utf8_encode($item);
            }
        });
        return $array;
    }

    /**
     * Iniciar transaccion
     */
    public function beginTransaction()
    {
        $this->conexion->beginTransaction();
    }

    /**
     * Hacer rollback
     */
    public function rollback()
    {
        $this->conexion->rollback();
    }

    /**
     * Guardar datos
     */
    public function commit()
    {
        $this->conexion->commit();
    }

    /**
     * Obtiene los datos de la BD mediante un query
     * @param sqlstr Parametro que contiene un query para la base de datos
     * @return return devuelve la busqueda del query
     */
    public function getData($sqlstr, $sqlArray = null)
    {
        try {
            $results = $this->conexionLectura->prepare($sqlstr);
            $results->execute($sqlArray);
            if (!$results->rowCount()) {
                return [];
            } else {
                foreach ($results as $key) {
                    $resultArray[] = $key;
                }
                return $this->convertUTF8($resultArray);
            }
        } catch (PDOException $e) {
            error_log('Algo ha salido mal: ' . $e->getMessage());
            return [];
        }
    }
    /**
     * Inserta en la BD
     * @param sqlstr Parametro que contiene un query
     * @param sqlArray Parametro que contiene un array de valores para insertar, puede ser null
     * @return bool|string
     */
    public function insertData($sqlstr, $sqlArray = null)
    {
        try {
            $results = $this->conexion->prepare($sqlstr);
            $results->execute($sqlArray);
            if (!$results->rowCount()) {
                return false;
            } else {
                return (int) $this->conexion->lastInsertId();
            }
        } catch (PDOException $e) {
            error_log('Algo ha salido mal: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * elimina Cross-site Scripting (XSS)
     * @param data evil data
     * @return cleanData retorna un string con o sin html sin XSS
    NOTA: La función quedó fuera de la clase conexión debido a que el analizador de vulnerabilidades SNYK no la dectecta como metodo
     */
    public function xssClean($data)
    {
        // Fix &entity\n;
        $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do
        {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        }
        while ($old_data !== $data);

        // we are done...
        return $data;
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

    public function postCurl($token, $data): string
    {
        try {
            $curl = curl_init($this->urlSendSms);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($data));
            $array = array(
                "authorization: " . $token ,
                "content-type: application/json");

            curl_setopt($curl, CURLOPT_HTTPHEADER, $array);
            curl_setopt($curl, CURLINFO_HEADER_OUT, true);
            $result = json_decode(curl_exec($curl));
            curl_close($curl);
             return $result->message;

        } catch (\Exception $e) {
            error_log("Error post curl sms -> " . $e->getMessage());
            return "Error al enviar código SMS";
        }
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
    public function startCurl($url, $tokenType = "", $token = "", $data_array = ""){
        try {
                # Iniciamos una instancia de curl
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            # Verificamos si estan enviando datos para la API de afiliados o si estan obteniendo un token
            if($token != "" && $tokenType != ""){
                curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($data_array));
                $array = array(
                    "authorization: ".$tokenType." ".$token ,
                    "content-type: application/json");
                $errorType = "/Load/Single";
            }
            #respuesta para solo envio de datos y token
            else if($token != "" && $data_array != ""){
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data_array));
                $array = array(
                    "X-Auth-token: ". $token,
                    "content-type: application/json");
                $errorType = "tokenizacion";
            }
            #respuesta para solo envio de datos
            else if($data_array != ""){
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data_array));
                $array = array(
                    "content-type: application/json");
                $errorType = "oauth/tokenizacion";
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
            $data = curl_exec($curl);
            $error = curl_error($curl);
            #Si existe un error lo guardamos en logs_api
            if($error){
                $query = "INSERT INTO logs_api (api_response,error)
                VALUES('Error: ".$errorType." ','".$error."')";
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
}