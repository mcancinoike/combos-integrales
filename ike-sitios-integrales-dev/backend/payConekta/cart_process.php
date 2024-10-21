<?php 
require_once "../conexion/conexion.php";
$_conexion = new Conexion;

if(isset($_POST["consulta"])){
	$cuenta = $_POST["cuenta"];
	$tipo = $_POST["tipo"];
	$query = "SELECT * FROM plan_account WHERE cl_account =  :cuenta";
    $queryArray = [
        'cuenta' => $cuenta
    ];
	$resultado = $_conexion->getData($query, $queryArray);
	$precio = 0;
	if($tipo == "1"){
		$precio = $resultado[0]["price_annual"];
	}else if($tipo == "7" || $tipo == "5"){
		$precio = $resultado[0]["price_plan"];
	}else if($tipo == "0"){
		$precio = 0;
	}

	die(json_encode(array(
		"success" => true,
		"precio" => $precio,
		"tipo" => $tipo
	)));
}

?>