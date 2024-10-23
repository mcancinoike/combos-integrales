<?php
require_once "backend/conexion/conexion.php";
$conexion = new conexion;

$suma_asegurada = !isset($_POST['suma_asegurada']) ? "0.00" : $conexion->xssClean($_POST['suma_asegurada']);
$prima_anual = !isset($_POST['prima_anual']) ? "0.00" : $conexion->xssClean($_POST['prima_anual']);
$subtotal_mensual = !isset($_POST['subtotal_mensual']) ? "0.00" : $conexion->xssClean($_POST['subtotal_mensual']);
$subtotal_mensual_asistencia = !isset($_POST['subtotal_mensual_asistencia']) ? $subtotal_mensual : $conexion->xssClean($_POST['subtotal_mensual_asistencia']);
$idPrima = !isset($_POST['idPrima']) ? 0 : $conexion->xssClean($_POST['idPrima']);
$asistencias = !isset($_POST['asistencias']) ? [] : $_POST['asistencias'];
$idCliente = !isset($_POST['idCliente']) ? 0 : $conexion->xssClean($_POST['idCliente']);
$textAsistencia = "";
foreach ($asistencias as $val) {
    $textAsistencia .= $conexion->xssClean($val) . "|";
}

?>