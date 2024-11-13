<?php
header('Content-type:application/json;charset=utf-8');

require 'email/src/Exception.php';
require 'email/src/PHPMailer.php';
require 'email/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "conexion/conexion.php";
$conexion = new Conexion();

$action = $_POST['action'];

function getSumaAsegurada($conexion, $fechaNac, $sexo)
{
    $cumpleanos = new DateTime($fechaNac);
    $hoy = new DateTime();
    $anios = $hoy->diff($cumpleanos);
    $edad = $anios->y;
    
    $campos = "";
    if($sexo == 'hombre'){
        $campos .= "id, edad, hombre as pago_mensual, suma_asegurada";        
    }else{
        $campos .= "id, edad, mujer as pago_mensual, suma_asegurada";
    }

    $where = " AND edad = $edad";

    $select = "<label>Elige una suma asegurada</label>";
    $select .= "<select name='sumaAsegurada' class='frm__control' id='sumaAseguradaS1' style='width:100%'>";
    $select .= "<option vaue=''>Seleccione</option>";
    $query = "SELECT $campos FROM hsbc_prima_ap WHERE active = 1 $where";
    foreach($conexion->getData($query) as $val){  
        $select .= '<option value='. $val['suma_asegurada'] . ' data-id='. $val['id'] .' data-mensual= '. $val['pago_mensual'] .'>$'. formatoMoneda($val['suma_asegurada']) .'</option>';
    }
    $select .= '</select>';

    echo json_encode($select);       
}

function saveClient($conexion, $nombre, $segundoNombre, $apellidoPaterno, $apellidoMaterno, $fechaNac, $rfc, $email, $telefono, $code, $idPrima, $asistencias, $sexo)
{
    $fecha_alta = date("Y-m-d H:i:s");

    $query = "INSERT INTO clientes_hsbc (id, client_type, name, middle_name, pater_surname, mater_surname, cell_phone, code, confirm_code, email, date_birth, rfc, card, id_prima, sexo, active, created_at, updated_at, deleted_at) VALUES('', 'ap', '$nombre', '$segundoNombre', '$apellidoPaterno', '$apellidoMaterno', '$telefono', $code, 0, '$email', '$fechaNac', '$rfc', 0, '$idPrima', '$sexo', 1, '$fecha_alta', '0000-00-00 00:00:00', '0000-00-00 00:00:00');";
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

function updateBeneficiare($conexion, $idBeneficiario, $parentesco, $nombre, $segundoNombre, $apellidoPaterno, $apellidoMaterno, $estadoCivil, $sexo, $fechaNac, $rfc, $nacionalidad, $actividad, $residencia)
{
    $fecha_modif = date("Y-m-d H:i:s");    

    $query = "UPDATE beneficiaries_hsbc SET relationship = '$parentesco', name = '$nombre', middle_name = '$segundoNombre', pater_surname = '$apellidoPaterno', mater_surname = '$apellidoMaterno', marital_status = '$estadoCivil', sex = '$sexo', date_birth = '$fechaNac', rfc = '$rfc', nationality = '$nacionalidad', residence = '$residencia', updated_at = '$fecha_modif' WHERE id = '$idBeneficiario'";
    
    if(!$conexion->insertData($query)){
        $result = array("mensaje" => "Ha ocurrido un error!");
    }else{
        $result = array("mensaje" => "Se actualizaron los beneficiario, con éxito!");       
    }
    
    echo json_encode($result);
}

function deleteBeneficiare($conexion, $idBeneficiario)
{
    $fecha_delete = date("Y-m-d H:i:s");    

    $query = "DELETE FROM beneficiaries_hsbc WHERE id = '$idBeneficiario'";
    
    if(!$conexion->insertData($query)){
        $result = array("mensaje" => "Ha ocurrido un error!");
    }else{
        $result = array("mensaje" => "Se elimino el beneficiario, con éxito!");       
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

function verifyCard($conexion, $idCliente, $numeroTarjeta)
{
    $bin = substr($numeroTarjeta, 0, 6);

    $query = "SELECT * FROM bin_account WHERE bin = :bin";
    $data = ["bin" => $bin];
    $rows = $conexion->getData($query, $data);
    if(count($rows)){
        $query2 = "UPDATE clientes_hsbc SET card = '$numeroTarjeta' WHERE id = '$idCliente'";
        if(!$conexion->insertData($query2)){
            apiAfiliados($conexion, $idCliente);
            sendMail($conexion, $idCliente);
            $result = array("mensaje" => "Se actualizo la tarjeta del cliente, con éxito!");  
        }else{
            $result = array("mensaje" => "Ha ocurrido un error!");
        } 
    }else{
        $result = array("mensaje" => "Tarjeta incorrecta");
    }
    echo json_encode($result);     
}

function apiAfiliados($conexion, $idCliente){
    #oauth/token
    $urlOauth = $conexion->urlOauth;
    $curlOauth = $conexion->startCurl($urlOauth);
    // var_dump($curlOauth);
    if($curlOauth != false){
        #OBTENEMOS UN TOKEN PARA LA API DE AFILIADOS
        $token = $curlOauth['access_token'];
        $tokenType = $curlOauth['token_type'];

        $queryTit = "SELECT rfc as clave, created_at as fecha_inicio, card, concat(name, ' ', middle_name, ' ', pater_surname, ' ', mater_surname) as nombre_titular, date_birth as fecha_nacimiento, cell_phone as celular, email as correo FROM clientes_hsbc WHERE id = '$idCliente';";
        foreach($conexion->getData($queryTit) as $valTit){ 
            $clave = $valTit['clave'];
            $fecha_inicio = date("Y-m-d", strtotime($valTit['fecha_inicio']));
            $fecha_fin = date("Y-m-d", strtotime($fecha_inicio . "+ 1 year"));
            $card = $valTit['card'];
            $ultimosTDC = "************" . substr($card, 12, 16);
            $nombre_titular = $valTit['nombre_titular'];
            $fecha_nacimiento = date("Y-m-d", strtotime($valTit['fecha_nacimiento']));
            $fecha_venta = date("Y-m-d", strtotime($valTit['fecha_inicio']));
            $celular = $valTit['celular'];
            $correo = $valTit['correo'];
        }

        $cumpleanos = new DateTime($fecha_nacimiento);
        $hoy = new DateTime();
        $anios = $hoy->diff($cumpleanos);
        $edad = $anios->y;

        $queryAsist = "select ass.assistance as producto, ass.cuenta_ike, ass.clProyecto from hsbc_cliente_assistance ca INNER JOIN hsbc_assistance ass ON ca.id_assistance = ass.id WHERE ca.id_cliente = '$idCliente';";
        foreach($conexion->getData($queryAsist) as $valAs){

            $data_ben = [
                "Cuenta_IKE" => $valAs['cuenta_ike'],
                "Movimiento_IKE" => "2",
                "Clave" => $clave,
                "Fecha_Inicio" => $fecha_inicio,
                "Fecha_Fin" => $fecha_fin,
                "card" => $card,
                "Tipo_Cobro" => "2",
                "Canal_Venta" => "B2C",
                "Tipo_Tarjeta" => "debit",
                "clProyecto" => $valAs['clProyecto'],
                "Nombre_Titular" => $nombre_titular,
                "Fecha_Nacimiento" => $fecha_nacimiento,
                "Celular" => $celular,
                "Correo" => $correo,
                "UltimosTDC" => $ultimosTDC,
                "Edad" => $edad,
                "Producto" => "Apoyo por hospitalización",
                "Programa" => $valAs['producto'],
                "Suma_Asegurada" => "0",
                "Fecha_Venta" => $fecha_venta
            ];

            $i = 1;
            $queryBenf = "SELECT concat(name, ' ', middle_name) as nombreB, pater_surname as paternoB, mater_surname as maternoB, date_birth as fechaNacB, relationship as civilB, percentage as porcentajeB, sex as sexoB, rfc as rfcB, relationship as parentescoB, nationality as nacionalidadB, residence as residenciaB, economic_activity as actividadB  FROM hsbc.beneficiaries_hsbc WHERE id_cliente = '$idCliente';";   
            foreach($conexion->getData($queryBenf) as $valBn){
                $nombreB = $valBn['nombreB'];
                $paternoB = $valBn['paternoB'];
                $maternoB = $valBn['maternoB'];
                $fechaNacB = date("Y-m-d", strtotime($valBn['fechaNacB']));
                $civilB = $valBn['civilB'];
                $porcentajeB = $valBn['porcentajeB'];
                $sexoB = $valBn['sexoB'];
                $rfcB = $valBn['rfcB'];
                $parentescoB = $valBn['parentescoB'];
                $nacionalidadB = $valBn['nacionalidadB'];
                $residenciaB = $valBn['residenciaB'];
                $actividadB = $valBn['actividadB'];
                
                $k = ($i == 1 ? "" : $i);
                $data_ben["Nombre_B$k"] = $nombreB;
                $data_ben["Apellido_Paterno_B$k"] = $paternoB;
                $data_ben["Apellido_Materno_B$k"] = $maternoB;
                $data_ben["Fecha_Nacimiento_B$k"] = $fechaNacB;
                $data_ben["Estado_Civil_B$k"] = $civilB;
                $data_ben["Porcentaje_B$k"] = "0";
                $data_ben["Sexo_B$k"] = $sexoB;
                $data_ben["Rfc_B$k"] = $rfcB;
                $data_ben["Parentesco_B$k"] = $parentescoB;
                $data_ben["Nacionalidad_B$k"] = $nacionalidadB;
                $data_ben["Residencia_B$k"] = $residenciaB;
                $data_ben["Actividad_B$k"] = $actividadB;

                $i++;
            }
            // $conexion->insertData($insertar, $data_ben);
            $urlAfiliados = $conexion->urlApiAfiliados;
            // #Enviamos mediante Curl la informacion a la API AFILIADOS
            $curlAfiliados = $conexion->startCurl($urlAfiliados, $tokenType, $token, $data_ben);
            // var_dump($curlAfiliados);
            if($curlAfiliados != false){
                $errorApi =  $curlAfiliados['code']  =='200' ? 'OK':json_encode($curlAfiliados['error']); 
                $log_alta = "INSERT INTO logs_api (Movimiento_IKE, id_key, cl_Account, titular, api_response, id_event, type_procces, date_created, order_id, error) ";
                $log_alta .= "VALUES('2','NO','". $valAs['producto'] ."','". $nombre_titular ."','". $curlAfiliados['code'] ."','NO','NO','". date("Y-m-d H:i:s") ."','NO','".$errorApi."')";
                $alta  = $conexion->insertData($log_alta);
            }
        } 
        // var_dump($data_ben);
    }
}


function sendMail($conexion, $idCliente){
    $query = "SELECT * FROM clientes_hsbc WHERE id = '$idCliente';";

    foreach($conexion->getData($query) as $val){    
        $nameClient = $val['name'] . " " . $val['middle_name'] . " " . $val['pater_surname'] . " " . $val['mater_surname'];
        $mailClient = $val['email'];
        $idPrima = $val['id_prima'];
        $sexo = $val['sexo'];
    }

    $prima = 0;
    $campos = ($sexo == 'hombre' ? 'hombre as prima_mensual' : 'mujer as prima_mensual');
    $query = "SELECT $campos FROM hsbc.hsbc_prima_ap WHERE id = '$idPrima';";
    foreach($conexion->getData($query) as $val){
        $prima = formatoMoneda($prima + $val['prima_mensual']);
    }

    $asistencia = 0;
    $query = "SELECT price FROM hsbc.hsbc_cliente_assistance cl INNER JOIN hsbc_assistance ass ON cl.id_assistance = ass.id WHERE cl.id_cliente = '$idCliente';";
    foreach($conexion->getData($query) as $val){
        $asistencia = formatoMoneda($asistencia + $val['price']);
    }

    $plan = "HSBC Apoyo Por Hospitalización";
    $total = ($prima + $asistencia);

    $mail = new PHPMailer(true);
    $body = file_get_contents('email/confirmationMail.html', dirname(__FILE__));
    $body = str_replace('clienteVar', $nameClient, $body);
    $body = str_replace('fechaVar', date('d-m-Y H:i:s'), $body);
    $productos = '<tr>
    <th width="25%" align="center" style="border: 2px solid #929292;color: #014c82;">'.$nameClient.'</th>
    <th width="25%" align="center" style="border: 2px solid #929292;color: #014c82;">'.$plan.'</th>              
    <th width="25%" align="center" style="border: 2px solid #929292;color: #014c82;">$'. formatoMoneda($total) .'</th>
    </tr>';
    $body = str_replace('<tableProductos></tableProductos>', $productos, $body);
    $mail->isSMTP();
    $mail->Host = 'email-smtp.us-east-1.amazonaws.com';
    $mail->SMTPAuth = true; 
    $mail->AddEmbeddedImage('../img/headerMail.png', 'headerMail', 'headerMail.png');
    $mail->AddEmbeddedImage('../img/footerMail.png', 'footerMail', 'footerMail.png');
    $mail->Username = 'AKIASWTWHVISO27R4S7R';
    $mail->Password = 'BIWnz2eaTb7DgUA1VdNWw3NX1jvlPjDoPPETy4CZIR01';
    $mail->Port = 587;     
    $mail->ClearAllRecipients();

    $mail->CharSet = 'UTF-8';
    $mail->MsgHTML($body);
    $mail->IsHTML(true);

    $mail->SetFrom('Notificaciones@ikeasistencia.com', 'HSBC Apoyo Por Hospitalización');
    $mail->Subject = 'Kit de Bienvenida y Términos & Condiciones HSBC Apoyo Por Hospitalización';
    $mail->addAddress($mailClient, $nameClient);

    $query = "SELECT * FROM hsbc_cliente_assistance WHERE id_cliente = '$idCliente';";
    foreach($conexion->getData($query) as $val){    
        $asistencia = $val['id_assistance'];
        if($asistencia == 1)
            $mail->AddAttachment('../docs/WK_Papas.pdf');
        
        if($asistencia == 2)
            $mail->AddAttachment('../docs/WK_Senior.pdf');
        
        if($asistencia == 3)
            $mail->AddAttachment('../docs/WK_Jovenes.pdf');
        
        if($asistencia == 4)
            $mail->AddAttachment('../docs/WK_Mascotas.pdf');
    }

    $mail->send();
}

function formatoMoneda($numero)
{
   return number_format(floor(($numero*100))/100, 2);
}

switch ($action):
    case 'getSumaAsegurada':
        $fechaNac = $_POST['fechaNac'];   
        $sexo = $_POST['sexo'];   
        getSumaAsegurada($conexion, $fechaNac, $sexo);
        break;

    case 'saveClient':
        $asistencias = $_POST['asistencias'];
        $idPrima = $_POST['idPrima'];
        $nombre = $_POST['nombre'];
        $segundoNombre = $_POST['segundoNombre'];
        $apellidoPaterno = $_POST['apellidoPaterno'];
        $apellidoMaterno = $_POST['apellidoMaterno'];
        $fechaNac = $_POST['fechaNac'];
        $rfc = $_POST['rfc'];
        $email = $_POST['email'];
        $telefono = $_POST['telefono'];
        $sexo = $_POST['sexo'];
        $code = genCode();
        $result = saveClient($conexion, $nombre, $segundoNombre, $apellidoPaterno, $apellidoMaterno, $fechaNac, $rfc, $email, $telefono, $code, $idPrima, $asistencias, $sexo);

        if (isset($result["idCliente"]))
            $result["msgCode"] = sendCodeCell($code, $telefono, $conexion);

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
    
    case 'updateBeneficiare':
        $idBeneficiario = $_POST['idBeneficiario'];
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
        updateBeneficiare($conexion, $idBeneficiario, $parentesco, $nombre, $segundoNombre, $apellidoPaterno, $apellidoMaterno, $estadoCivil, $sexo, $fechaNac, $rfc, $nacionalidad, $actividad, $residencia);
        break;

    case 'deleteBeneficiare':
        $idBeneficiario = $_POST['idBeneficiario'];   
        deleteBeneficiare($conexion, $idBeneficiario);
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
        
    case 'verifyCard':
        $idCliente = $_POST['idCliente'];   
        $numeroTarjeta = $_POST['numeroTarjeta'];   
        verifyCard($conexion, $idCliente, $numeroTarjeta);
        break;
endswitch;