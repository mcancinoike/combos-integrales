<?php
header('Content-type:application/json;charset=utf-8');
require_once "conexion/conexion.php";
$conexion = new conexion;

$action = $_POST['action'];

function saveClient($conexion, $nombre, $segundoNombre, $apellidoPaterno, $apellidoMaterno, $fechaNac, $email, $telefono, $idPrima, $asistencias)
{
    $fecha_alta = date("Y-m-d H:i:s");

    $queryCons = "SELECT MAX(id) + 1 as idCliente FROM clientes_hsbc";

    foreach ($conexion->getData($queryCons) as $valCons) {
        $idCliente = $valCons['idCliente'];
    }

    $query = "INSERT INTO clientes_hsbc (id, client_type, name, middle_name, pater_surname, mater_surname, cell_phone, code, confirm_code, email, date_birth, id_prima, active, created_at, updated_at, deleted_at) VALUES('', 'AH', '$nombre', '$segundoNombre', '$apellidoPaterno', '$apellidoMaterno', '$telefono', 0, 0, '$email', '$fechaNac', '$idPrima', 1, '$fecha_alta', '0000-00-00 00:00:00', '0000-00-00 00:00:00');";
    
    if(!$conexion->insertData($query)){
        $result = array("mensaje" => "Ha ocurrido un error!");
    }else{
        $asist = explode("|", $asistencias);   
        foreach ($asist as $val2) {
            $query2 = "INSERT INTO hsbc_cliente_assistance (id, id_cliente, id_assistance, active, created_at, updated_at, deleted_at) VALUES('', '$idCliente', '$val2', 1, '$fecha_alta', '0000-00-00 00:00:00', '0000-00-00 00:00:00');";
            $conexion->insertData($query2);  
        }     
        $result = array("mensaje" => "Se creó el cliente, con éxito!", "idCliente" => $idCliente);       
    }
    
    echo json_encode($result);
}

function saveBeneficiare($conexion, $idCliente, $parentesco, $nombre, $segundoNombre, $apellidoPaterno, $apellidoMaterno, $estadoCivil, $sexo, $fechaNac, $nacionalidad, $actividad, $residencia)
{
    $fecha_alta = date("Y-m-d H:i:s");    

    $query = "INSERT INTO beneficiaries_hsbc (id, id_cliente, relationship, name, middle_name, pater_surname, mater_surname, marital_status, sex, date_birth, rfc, nationality, economic_activity, residence, percentage, active, created_at, updated_at, deleted_at) VALUES('', '$idCliente', '$parentesco', '$nombre', '$segundoNombre', '$apellidoPaterno', '$apellidoMaterno', '$estadoCivil', '$sexo', '$fechaNac', '', '$nacionalidad', '$actividad', '$residencia', 0, 1, '$fecha_alta', '0000-00-00 00:00:00', '0000-00-00 00:00:00');";
    
    if(!$conexion->insertData($query)){
        $result = array("mensaje" => "Ha ocurrido un error!");
    }else{
        $result = array("mensaje" => "Se creó el beneficiario, con éxito!");       
    }
    
    echo json_encode($result);
}

switch ($action):
    case 'saveClient':
        $asistencias = $_POST['asistencias'];
        $idPrima = $_POST['idPrima'];
        $nombre = $_POST['nombre'];
        $segundoNombre = $_POST['segundoNombre'];
        $apellidoPaterno = $_POST['apellidoPaterno'];
        $apellidoMaterno = $_POST['apellidoMaterno'];
        $fechaNac = $_POST['fechaNac'];
        $email = $_POST['email'];
        $telefono = $_POST['telefono'];
        saveClient($conexion, $nombre, $segundoNombre, $apellidoPaterno, $apellidoMaterno, $fechaNac, $email, $telefono, $idPrima, $asistencias);
        break;
    case 'saveBeneficiare':
        $idCliente = $_POST['idCliente'];
        $parentesco = $_POST['parentesco'];
        $nombre = $_POST['nombre'];
        $segundoNombre = $_POST['segundoNombre'];
        $apellidoPaterno = $_POST['apellidoPaterno'];
        $apellidoMaterno = $_POST['apellidoMaterno'];
        $estadoCivil = $_POST['estadoCivil'];
        $sexo = $_POST['sexo'];
        $fechaNac = $_POST['fechaNac'];
        $nacionalidad = $_POST['nacionalidad'];
        $actividad = $_POST['actividad'];
        $residencia = $_POST['residencia'];
        saveBeneficiare($conexion, $idCliente, $parentesco, $nombre, $segundoNombre, $apellidoPaterno, $apellidoMaterno, $estadoCivil, $sexo, $fechaNac, $nacionalidad, $actividad, $residencia);
        break;
endswitch;