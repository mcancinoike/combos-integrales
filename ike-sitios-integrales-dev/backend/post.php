<?php
session_start();
require_once "../backend/conexion/conexion.php";
$conexion = new conexion;
$captchaPublic = $conexion->captchaPublic;

function formatoMoneda($numero)
{
   return number_format(floor(($numero*100))/100, 2);
}

?>