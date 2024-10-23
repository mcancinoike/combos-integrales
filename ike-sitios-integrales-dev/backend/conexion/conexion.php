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

    function __construct()
    {
        $data = $this->getConexion();
        $this->user = $data["userDB"];
        $this->passDB = $data["passDB"];
        $this->serverDBEscritura = $data["serverDBEscritura"];
        $this->serverDBLectura = $data["serverDBLectura"];
        $this->database = $data["dataBaseDB"];
        $this->ambiente = $data["ambiente"];

        try {
            $this->conexion = new PDO("mysql:host=" . $this->serverDBEscritura . ";dbname=" . $this->database, $this->user, $this->passDB, array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            
        }
        if ($this->ambiente != 2) {
            try {
                $this->conexionLectura = new PDO("mysql:host=" . $this->serverDBLectura . ";dbname=" . $this->database, $this->user, $this->passDB, array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
                $this->conexionLectura->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                
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
     * @return true retorna un booleano true si el insert fue exitoso
     */
    public function insertData($sqlstr, $sqlArray = null)
    {
        try {
            $results = $this->conexion->prepare($sqlstr);
            $results->execute($sqlArray);
            if (!$results->rowCount()) {
                return false;
            } else {
                return true;
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
    function xssClean($data)
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
}