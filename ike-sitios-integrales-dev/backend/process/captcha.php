<?php

    // include_once ('../process/CSRF.php');
    include_once ('../conexion/conexion.php');

    // $_CSRF = new CSRF;
    // $_CSRF->valiarTokenCSRF();

    $conexion = new Conexion();
    
    $ip = $_SERVER['REMOTE_ADDR'];
    $captcha = htmlentities($_POST['captcha_response'], ENT_QUOTES, 'UTF-8');
    $secretKey = $conexion->captchaSecret;
    $data = array(
        'secret'   => $secretKey,
        'response' => $captcha,
        'remoteip' => $ip
    );

    $verify = curl_init();
    curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($verify, CURLOPT_POST, true);
    curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
    $respuesta = curl_exec($verify);
    curl_close($verify);
    $atributos = json_decode($respuesta, true);
    $errors = array();
    if(!$atributos['success']){
        $errors[] = 'El captcha es obligatorio';
    }
    echo htmlspecialchars($respuesta, ENT_NOQUOTES, "UTF-8");
?>