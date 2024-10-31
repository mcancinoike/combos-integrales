<?php
require_once "../backend/conexion/conexion.php";
$conexion = new conexion;

function formatoMoneda($numero)
{
   return number_format(floor(($numero*100))/100, 2);
}

$suma_asegurada = !isset($_POST['suma_asegurada']) ? "0.00" : $conexion->xssClean($_POST['suma_asegurada']);
$suma_asegurada =  empty($suma_asegurada) ? 0 : $suma_asegurada;

$prima_anual = !isset($_POST['prima_anual']) ? "0.00" : $conexion->xssClean($_POST['prima_anual']);
$prima_anual = empty($prima_anual) ? 0 : ($prima_anual);

$subtotal_mensual = !isset($_POST['subtotal_mensual']) ? "0.00" : $conexion->xssClean($_POST['subtotal_mensual']);
$subtotal_mensual = empty($subtotal_mensual) ? 0 : $subtotal_mensual;

$subtotal_mensual_asistencia = !isset($_POST['subtotal_mensual_asistencia']) ? $subtotal_mensual : $conexion->xssClean($_POST['subtotal_mensual_asistencia']);
$subtotal_mensual_asistencia = empty($subtotal_mensual_asistencia) ? 0 : $subtotal_mensual_asistencia;

$idPrima = !isset($_POST['idPrima']) ? 0 : $conexion->xssClean($_POST['idPrima']);

$asistencias = !isset($_POST['asistencias']) ? [] : $_POST['asistencias'];

$idCliente = !isset($_POST['idCliente']) ? 0 : $conexion->xssClean($_POST['idCliente']);

$textAsistencia = "";
foreach ($asistencias as $val) {
    $textAsistencia .= $conexion->xssClean($val) . "|";
}

?>