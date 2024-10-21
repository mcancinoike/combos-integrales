<?php
    session_start();
    extract($_REQUEST);
    require_once "Payment.php";
    require_once "../conexion/conexion.php";
    include_once ('./CSRF.php');

    $_CSRF = new CSRF;
    $_CSRF->valiarTokenCSRF();

    $_conexion = new Conexion;
    $productos = $_SESSION["session"];
    $query = "SELECT clave_plan, name_plan, price_plan, price_annual FROM plan_account WHERE cl_account = :cl_account";
    $queryArray = [
        'cl_account' => $cl_account
    ];
    $datos = $_conexion->getData($query, $queryArray)[0];
    $total = $forma_pago == 7 ? $datos["price_plan"] : $datos["price_annual"];
    $plan = $datos["name_plan"];
    $account = $datos["clave_plan"];
    $empresa=isset($empresa)?$empresa : "";
    $oPayment= new Payment($conektaTokenId,$tarjetaHabiente,$correo,$telefono,$tarjeta,$total,
    $plan,$forma_pago,$tipo_pago,$account,$cl_account,$renovacion,$empresa,$productos,$_conexion, $_conexion->rutaPrincipal . "confirmacion.php");

    $apiKey = "";
    if ($cl_account == 2508){
        $apiKey = $_conexion->apiKeyConektaPayF;
    }else{
        $apiKey = $_conexion->apiKeyConektaPayI;
    }
    $oPayment->setPrivates($apiKey,$_conexion->versionConekta);

    if($oPayment->pay()){
        $_SESSION["orderId"]=$oPayment->order_id;
        die(json_encode(array(
    		"respuesta" => true,
    		"mensaje" => "Guardado con éxito",
            'three_ds_url' => $oPayment->three_ds_url,
            "error" => ""
    	)));
    }else{
        die(json_encode(array(
    		"respuesta" => false,
    		"mensaje" => "",
            "error" => $oPayment->error
    	)));
    }

?>
