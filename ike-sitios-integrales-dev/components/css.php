<?php
    require_once $_SESSION["relativePath"] . "backend/conexion/conexion.php";
    $conexion = new conexion;
    define("VERSION", $conexion->VERSION);
?>
<link rel="stylesheet" type="text/css" href="<?php echo $_SESSION["relativePath"]?>css/bootstrap/bootstrap.css">
<link rel="stylesheet" type="text/css" href="<?php echo $_SESSION["relativePath"]?>css/select2-4.1.0.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo $_SESSION["relativePath"]?>css/style.css?v=<?php echo VERSION?>">
<link rel="stylesheet" href="<?php echo $_SESSION["relativePath"]?>css/toastr/toastr.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo $_SESSION["relativePath"]?>css/fonts.css">
