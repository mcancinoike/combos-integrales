<?php

include_once('./CSRF.php');
include_once('../conexion/conexion.php');

$_CSRF = new CSRF;
$_CSRF->valiarTokenCSRF();

$_CONEXION = new Conexion();

$ip = $_SERVER['REMOTE_ADDR'];
$captcha = $_POST['g-recaptcha-response'];
$secretKey = $_CONEXION->captcha;

$url = "https://www.google.com/recaptcha/api/siteverify";
$data = [
    'secret' => $secretKey,
    'response' => $captcha,
    'remoteip' => $ip
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$respuesta = curl_exec($ch);

curl_close($ch);

$atributos = json_decode($respuesta, true);
$errors = array();

if (!$atributos['success']) {
    $errors[] = 'El captcha es obligatorio.';
}

echo $respuesta;
?>
