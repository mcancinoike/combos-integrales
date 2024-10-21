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
}