<?php
header('Content-type:application/json;charset=utf-8');
require_once "conexion/conexion.php";
$conexion = new Conexion();

$action = $_POST['action'];

function saveClient($conexion, $nombre, $segundoNombre, $apellidoPaterno, $apellidoMaterno, $fechaNac, $email, $telefono, $code, $idPrima, $asistencias)
{
    $fecha_alta = date("Y-m-d H:i:s");

    $query = "INSERT INTO clientes_hsbc (id, client_type, name, middle_name, pater_surname, mater_surname, cell_phone, code, confirm_code, email, date_birth, id_prima, active, created_at, updated_at, deleted_at) VALUES('', 'ap', '$nombre', '$segundoNombre', '$apellidoPaterno', '$apellidoMaterno', '$telefono', $code, 0, '$email', '$fechaNac', '$idPrima', 1, '$fecha_alta', '0000-00-00 00:00:00', '0000-00-00 00:00:00');";
    $idCliente = $conexion->insertData($query);
    if(!$idCliente){
        $result = array("mensaje" => "Ha ocurrido un error!");
    }else{
        $asist = explode("|", $asistencias);   
        foreach ($asist as $val2) {
            if($val2 != ""){
                $query2 = "INSERT INTO hsbc_cliente_assistance (id, id_cliente, id_assistance, active, created_at, updated_at, deleted_at) VALUES('', '$idCliente', '$val2', 1, '$fecha_alta', '0000-00-00 00:00:00', '0000-00-00 00:00:00');";
                $conexion->insertData($query2);
            }  
        }     
        $result = array("mensaje" => "Se creó el cliente, con éxito!", "idCliente" => $idCliente);       
    }
    
    return $result;
}

function saveBeneficiare($conexion, $idCliente, $parentesco, $nombre, $segundoNombre, $apellidoPaterno, $apellidoMaterno, $estadoCivil, $sexo, $fechaNac, $rfc, $nacionalidad, $actividad, $residencia)
{
    $fecha_alta = date("Y-m-d H:i:s");    

    $query = "INSERT INTO beneficiaries_hsbc (id, id_cliente, relationship, name, middle_name, pater_surname, mater_surname, marital_status, sex, date_birth, rfc, nationality, economic_activity, residence, percentage, active, created_at, updated_at, deleted_at) VALUES('', '$idCliente', '$parentesco', '$nombre', '$segundoNombre', '$apellidoPaterno', '$apellidoMaterno', '$estadoCivil', '$sexo', '$fechaNac', '$rfc', '$nacionalidad', '$actividad', '$residencia', 0, 1, '$fecha_alta', '0000-00-00 00:00:00', '0000-00-00 00:00:00');";
    
    if(!$conexion->insertData($query)){
        $result = array("mensaje" => "Ha ocurrido un error!");
    }else{
        $result = array("mensaje" => "Se creó el beneficiario, con éxito!");       
    }
    
    echo json_encode($result);
}

function genCode()
{
    $code = '';
    for ($i = 0; $i < 6; $i++) {
        $code .= mt_rand(0, 9);
    }
    return $code;
}

/**
 * @throws Exception
 */
function sendCodeCell($code, $cellPhone, $conexion)
{
    $tokenSms = $conexion->getTokenSms();
    if(!$tokenSms)
        throw new Exception("Error al obtener envio SMS");

    $data = [
        "identifier" => [
            "tenants" => "adff7f6a-e97d-11eb-9a03-0242ac130003",
            "app" => "HSBC"
        ],
        "petition" => [
            "message" => "Tu código de verificación de HSBC es $code",
            "phone" => $cellPhone
        ]
    ];

   return $conexion->postCurl($tokenSms['access_token'], $data);
}

function verifyCode($idCliente, $code, $conexion)
{
    $query = "SELECT * FROM clientes_hsbc WHERE id = :idCliente AND code = :code";
    $data = ["idCliente" => $idCliente, "code" => $code];
    $rows = $conexion->getData($query, $data);
    if(count($rows))
        return array("isValid" => true);
    else
        return array("isValid" => false);
}

function updateCodeClient($conexion, $idCliente, $code)
{
    $query = "UPDATE clientes_hsbc SET code = $code, updated_at = NOW() WHERE id = :idCliente";
    $data = ["idCliente" => $idCliente];

    return $conexion->insertData($query, $data);
}

function getCellClient($conexion, $idCliente)
{
    $query = "SELECT cell_phone FROM clientes_hsbc WHERE id = :idCliente";
    $data = ["idCliente" => $idCliente];

    $rows = $conexion->getData($query, $data);

    if(count($rows)){
        return $rows[0]['cell_phone'];
    } else {
        return null;
    }
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
        $code = genCode();
        $result = saveClient($conexion, $nombre, $segundoNombre, $apellidoPaterno, $apellidoMaterno, $fechaNac, $email, $telefono, $code, $idPrima, $asistencias);

        // if (isset($result["idCliente"]))
        //     $result["msgCode"] = sendCodeCell($code, $telefono, $conexion);

        echo json_encode($result);
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
        $rfc = $_POST['rfc'];
        $nacionalidad = $_POST['nacionalidad'];
        $actividad = $_POST['actividad'];
        $residencia = $_POST['residencia'];
        saveBeneficiare($conexion, $idCliente, $parentesco, $nombre, $segundoNombre, $apellidoPaterno, $apellidoMaterno, $estadoCivil, $sexo, $fechaNac, $rfc, $nacionalidad, $actividad, $residencia);
        break;

    case 'verifyCode':
        $idCliente = $_POST['idCliente'];
        $code = $_POST['code'];

        echo json_encode(verifyCode($idCliente, $code, $conexion));
        break;

    case 'sendNewCode':
        $idCliente = $_POST['idCliente'];
        $code = genCode();

        if(!updateCodeClient($conexion, $idCliente, $code)) {
            $telefono = getCellClient($conexion, $idCliente);

            try {
                sendCodeCell($code, $telefono, $conexion);
                echo json_encode(array("status" => "ok"));

            } catch (Exception $e) {
                echo json_encode(array("status" => "Error inesperado al enviar SMS, intente más tarde por favor"));
            }
        } else {
            echo json_encode(array("status" => "Error al actualizar código"));
        }
        break;

endswitch;