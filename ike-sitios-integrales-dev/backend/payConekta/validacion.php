<?php
    require_once '../conexion/conexion.php';
    include_once ('./CSRF.php');
    require "./ConektaApi.php";
    
    $_CSRF = new CSRF;
    $_CSRF->valiarTokenCSRF();
    
    $_POST['order_id'] = htmlentities($_POST['order_id'], ENT_QUOTES, 'UTF-8');

    if( !empty($_POST['order_id'])){

        $order_id = $_POST['order_id'];
        $conexion = new Conexion;

        $ConektaAPI = new ConektaApi('', $conexion->apiKeyConektaPayI);

        try{
            $ConektaAPI->getOrderById($order_id);

            $conexion->beginTransaction();
            $query = "UPDATE orders
                       SET status = :status 
                       WHERE order_id = :order_id";

            $queryArray = [
                "order_id"  => $order_id,
                "status"    => $ConektaAPI->getFailureCode()
            ];

            try {
                if(!$conexion->insertData($query, $queryArray)){
                    $conexion->rollback();
                    throw new Exception("Error al actualizar el status");
                }
                $response = array(
                    "status" => true,
                    "error"  => $ConektaAPI->getFailureMessage()
                );
                $conexion->commit();
            }catch (Exception $ex) {
                $conexion->rollback();
                $response = array(
                    "status" => $ex,
                    "error"  => ''
                );
            }
            echo json_encode($response);
        }catch (Exception $ex) {
            $conexion->rollback();
            echo json_encode(
                array(
                    "status"     => $ex ,
                    "order_id"   => null,
                    "phone_plan" => null
                )
            );
        }
    } else {
        $response = array(
            "status"     => 'Falta completar datos.',
            "order_id"   => null,
            "phone_plan" => null
        );

        echo json_encode($response);
    }
?>