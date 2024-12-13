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

function saveClient($conexion, $nombre, $segundoNombre, $apellidoPaterno, $apellidoMaterno, $fechaNac, $rfc, $email, $telefono, $code, $idPrima, $asistencias, $sexo, $clientType)
{

    $query = "INSERT INTO clientes_hsbc (client_type, name, middle_name, pater_surname, mater_surname, cell_phone, code_cell, email, date_birth, rfc, id_prima, sexo, updated_at) 
                     VALUES(:client_type, :name, :middle_name, :pater_surname, :mater_surname, :cell_phone, :code_cell, :email, :date_birth, :rfc, :id_prima, :sexo, :updated_at);";
    $data = [
      "client_type" => $clientType,
      "name" => $nombre,
      "middle_name" => $segundoNombre,
      "pater_surname" => $apellidoPaterno,
      "mater_surname" => $apellidoMaterno,
      "cell_phone" => $telefono,
      "code_cell" => $code,
      "email" => $email,
      "date_birth" => $fechaNac,
      "rfc" => $rfc,
      "id_prima" => $idPrima,
      "sexo" => $sexo,
      "updated_at" => '0000-00-00 00:00:00'
    ];

    $idCliente = $conexion->insertData($query, $data);
    if(!is_numeric($idCliente)){
        $result = array("code" => 400, "msg" => "Error al intentar guardar su información, asegúrese que sus datos son correctos e intente nuevamente por favor");
    }else{

        foreach ($asistencias as $val2) {

                $query2 = "INSERT INTO hsbc_cliente_assistance (id_cliente, id_assistance, updated_at) VALUES(:id_cliente, :id_assistance , '0000-00-00 00:00:00');";
                $conexion->insertData($query2, ["id_cliente" => $idCliente, "id_assistance" => $val2]);
        }     
        $result = array("code" => 200, "msg" => "Se creó el cliente, con éxito!", "idCliente" => $idCliente);
    }
    
    return $result;
}

function updateClient($conexion, $nombre, $segundoNombre, $apellidoPaterno, $apellidoMaterno, $fechaNac, $email, $idPrima, $asistencias, $sexo, $idCliente)
{

    $query = "UPDATE clientes_hsbc SET name = :name, middle_name = :middle_name, 
                                       pater_surname = :pater_surname, mater_surname = :mater_surname,
                                       email = :email, date_birth = :date_birth, 
                                       id_prima = :id_prima, sexo = :sexo WHERE id = :id;";
    $data = [
        "name" => $nombre,
        "middle_name" => $segundoNombre,
        "pater_surname" => $apellidoPaterno,
        "mater_surname" => $apellidoMaterno,
        "email" => $email,
        "date_birth" => $fechaNac,
        "id_prima" => $idPrima,
        "sexo" => $sexo,
        "id" => $idCliente,
    ];

    if(!is_numeric($conexion->insertData($query, $data))){
        $result = array("code" => 400, "msg" => "Error al intentar actualizar su información, asegúrese que sus datos son correctos e intente nuevamente por favor");
    }else{
        $conexion->beginTransaction();

        try {
            if (count($asistencias) > 0) {
                sort($asistencias);

                $query = "SELECT id_assistance FROM hsbc_cliente_assistance WHERE id_cliente = :id_cliente ORDER BY id ASC";
                $rowsClientAsis = $conexion->getData($query, ["id_cliente" => $idCliente]);

                if (count($rowsClientAsis) > 0) {

                    $asistenciasSave = [];
                    for ($i = 0; $i < count($rowsClientAsis); $i++)
                            $asistenciasSave[] = $rowsClientAsis[$i]["id_assistance"];

                    $query = "SELECT id FROM hsbc_assistance ORDER BY id ASC";
                    foreach ($conexion->getData($query) as $row) {

                        if (in_array($row["id"], $asistenciasSave) && !in_array($row["id"], $asistencias)) { // si la asistencias guardada existe y la nueva NO borramos

                            $query = "DELETE FROM hsbc_cliente_assistance WHERE id_cliente = :id_cliente AND id_assistance = :id_assistance";
                            $conexion->insertData($query, ['id_cliente' => $idCliente, "id_assistance" => $row["id"]]);


                        } else if (!in_array($row["id"], $asistenciasSave) && in_array($row["id"], $asistencias)) { // si la asistencias guardada NO existe y la nueva SI insertamos
                            $query = "INSERT INTO hsbc_cliente_assistance (id_cliente, id_assistance , updated_at) VALUES(:id_cliente, :id_assistance, '0000-00-00 00:00:00');";
                            $conexion->insertData($query, ["id_cliente" => $idCliente, "id_assistance" => $row["id"]]);
                        }
                    }

                } else {
                    foreach ($asistencias as $id_asistencia) {
                        $query = "INSERT INTO hsbc_cliente_assistance (id_cliente, id_assistance , updated_at) VALUES(:id_cliente, :id_assistance, '0000-00-00 00:00:00');";
                        $conexion->insertData($query, ["id_cliente" => $idCliente, "id_assistance" => $id_asistencia]);
                    }
                }

            } else {
                $query = "DELETE FROM hsbc_cliente_assistance WHERE id_cliente = :id_cliente";
                $conexion->insertData($query, ['id_cliente' => $idCliente]);

            }
            $conexion->commit();
            $result = array("code" => 200, "msg" => "Se actualizó el cliente, con éxito!");
        }catch (\Exception $exception) {
            $conexion->rollBack();
            $result = array("code" => 400, "msg" => "Ocurrió un error al tratar de actualizar asistencias, intente nuevamente por favor");
        }

    }

    return $result;
}

function saveBeneficiare($conexion, $idCliente, $parentesco, $nombre, $segundoNombre, $apellidoPaterno, $apellidoMaterno, $estadoCivil, $sexo, $fechaNac, $rfc, $nacionalidad, $actividad, $residencia)
{

    $query = "INSERT INTO beneficiaries_hsbc (id_cliente, relationship, name, middle_name, pater_surname, mater_surname, marital_status, sex, date_birth, rfc, nationality, economic_activity, residence, updated_at) 
                         VALUES(:id_cliente, :relationship, :name, :middle_name, :pater_surname, :mater_surname, :marital_status, :sex, :date_birth, :rfc, :nationality, :economic_activity, :residence, :updated_at);";

    $data = [
        "id_cliente" => $idCliente,
        "relationship" => $parentesco,
        "name" => $nombre,
        "middle_name" => $segundoNombre,
        "pater_surname" => $apellidoPaterno,
        "mater_surname" => $apellidoMaterno,
        "marital_status" => $estadoCivil,
        "sex" => $sexo,
        "date_birth" => $fechaNac,
        "rfc" => $rfc,
        "nationality" => $nacionalidad,
        "economic_activity" => $actividad,
        "residence" => $residencia,
        "updated_at" => "0000-00-00 00:00:00"
    ];

    $idBeneficiario = $conexion->insertData($query, $data);

    if(!is_numeric($idBeneficiario))
        $result = array("code" => 200, "msg" => "Ha ocurrido un error!");
    else
        $result = array("code" => 400, "msg" => "Se creó el beneficiario, con éxito!", "idBeneficiario" => $idBeneficiario );

    echo json_encode($result);
}

function updateBeneficiare($conexion, $idBeneficiario, $parentesco, $nombre, $segundoNombre, $apellidoPaterno, $apellidoMaterno, $estadoCivil, $sexo, $fechaNac, $rfc, $nacionalidad, $actividad, $residencia)
{
    $fecha_modif = date("Y-m-d H:i:s");    

    $query = "UPDATE beneficiaries_hsbc 
                     SET relationship = :relationship, name = :name, middle_name = :middle_name, pater_surname = :pater_surname, mater_surname = :mater_surname, marital_status = :marital_status, 
                         sex = :sex, date_birth = :date_birth , rfc = :rfc, nationality = :nationality, economic_activity = :economic_activity, residence = :residence, updated_at = :updated_at WHERE id = :id";

    $data = [
        "id" => $idBeneficiario,
        "relationship" => $parentesco,
        "name" => $nombre,
        "middle_name" => $segundoNombre,
        "pater_surname" => $apellidoPaterno,
        "mater_surname" => $apellidoMaterno,
        "marital_status" => $estadoCivil,
        "sex" => $sexo,
        "date_birth" => $fechaNac,
        "rfc" => $rfc,
        "nationality" => $nacionalidad,
        "economic_activity" => $actividad,
        "residence" => $residencia,
        "updated_at" => $fecha_modif
    ];

    if($conexion->insertData($query, $data) === 0){
        $result = array("code" => 200, "msg" => "Se actualizaron los beneficiario, con éxito!");
    }else{
        $result = array("code" => 400, "msg" => "Ha ocurrido un error!");
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

function deleteSeguro($conexion, $idCliente)
{

    try {
        $conexion->beginTransaction();
        $query = "UPDATE clientes_hsbc SET id_prima = 0 WHERE id = :idCliente";
        if($conexion->insertData($query, ["idCliente" => $idCliente]) === 0){
            $query = "DELETE FROM beneficiaries_hsbc WHERE id_cliente = :idCliente";
            if($conexion->insertData($query, ["idCliente" => $idCliente]) === 0){
                $conexion->commit();
                return true;
            }else {
                $conexion->rollback();
                return false;
            }
        }else {
            $conexion->rollBack();
            return false;
        }
    }catch (Exception $e){
        error_log("Error eliminar seguro. " . $e->getMessage());
        $conexion->rollBack();
        return false;
    }



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
    $query = "SELECT * FROM clientes_hsbc WHERE id = :idCliente AND code_cell = :code";
    $data = ["idCliente" => $idCliente, "code" => $code];
    $rows = $conexion->getData($query, $data);
    if(count($rows)){
        if (confirm("cell", $conexion, $idCliente) === 0)
                return array("isValid" => true);
        else
            return array("isValid" => false);

    } else
        return array("isValid" => false);
}

function confirm($campo, $conexion, $idCliente)
{
    $query = "UPDATE clientes_hsbc SET confirm_$campo = 1 WHERE id = :idCliente";
    $data = ["idCliente" => $idCliente];

    return $conexion->insertData($query, $data);
}


function updateCodeClient($conexion, $idCliente, $code)
{
    $query = "UPDATE clientes_hsbc SET code_cell = :code WHERE id = :idCliente";
    $data = ["idCliente" => $idCliente, "code" => $code];

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

function verifyCard($conexion, $idCliente, $numeroTarjeta, $clientType)
{     
    $bin = substr($numeroTarjeta, 0, 6);

    $query = "SELECT description FROM bin_account WHERE bin = :bin AND account_id = 999999";
    $data = ["bin" => $bin];
    $rows = $conexion->getData($query, $data);
    if(count($rows)){
        $cardType = str_contains($rows[0]["description"], "DÉBITO") ? "TDD" : "TDC";
         if (apiAfiliados($conexion, $idCliente, $cardType, $clientType, $numeroTarjeta)){
             if (sendMail($conexion, $idCliente))
                 if (confirm("email", $conexion, $idCliente) === 0)
                    $result = array("code" => 200, "msg" => "Se actualizo la tarjeta del cliente, con éxito!");
                 else
                     $result = array("code" => 400, "msg" => "Error al confirmar correo");
             else
                 $result = array("code" => 400, "msg" => "Error al enviar correo");
         }  else
             $result = array("code" => 400, "msg" => "Error al intentar enviar información API afiliados");

    }else{
        $result = array("code" => 400, "msg" => "La tarjeta ingresada es incorrecta");
    }
    return json_encode($result);
}

function apiAfiliados($conexion, $idCliente, $cardType, $clientType, $card){
    #oauth/token
    $urlOauth = $conexion->urlOauth;
    $curlOauth = $conexion->startCurl($urlOauth);
    // var_dump($curlOauth);
    if($curlOauth != false){
        #OBTENEMOS UN TOKEN PARA LA API DE AFILIADOS
        $token = $curlOauth['access_token'];
        $tokenType = $curlOauth['token_type'];

        $queryTit = "SELECT rfc as clave, ch.created_at as fecha_inicio, concat(name, ' ', middle_name, ' ', pater_surname, ' ', mater_surname) as nombre_titular, date_birth as fecha_nacimiento, 
                            cell_phone as celular, email as correo, IFNULL(suma_asegurada, 0) as suma_asegurada
                      FROM clientes_hsbc ch LEFT JOIN hsbc_prima_$clientType hp ON hp.id = ch.id_prima WHERE ch.id = '$idCliente';";

        foreach($conexion->getData($queryTit) as $valTit){ 
            $clave = $valTit['clave'];
            $fecha_inicio = date("Y-m-d", strtotime($valTit['fecha_inicio']));
            $fecha_fin = date("Y-m-d", strtotime($fecha_inicio . "+ 1 year"));
            $ultimosTDC = "************" . substr($card, 12, 16);
            $nombre_titular = $valTit['nombre_titular'];
            $fecha_nacimiento = date("Y-m-d", strtotime($valTit['fecha_nacimiento']));
            $fecha_venta = date("Y-m-d", strtotime($valTit['fecha_inicio']));
            $celular = $valTit['celular'];
            $correo = $valTit['correo'];
            $sumaAsegurada = $valTit['suma_asegurada'];
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
                "Tipo_Tarjeta" => $cardType,
                "clProyecto" => $valAs['clProyecto'],
                "Nombre_Titular" => $nombre_titular,
                "Fecha_Nacimiento" => $fecha_nacimiento,
                "Celular" => $celular,
                "Correo" => $correo,
                "UltimosTDC" => $ultimosTDC,
                "Edad" => $edad,
                "Producto" => $clientType === "ap" ? "Accidentes Personales" : "Apoyo por hospitalización",
                "Programa" => $valAs['producto'],
                "Suma_Asegurada" => $sumaAsegurada,
                "Fecha_Venta" => $fecha_venta
            ];

            $i = 1;
            $queryBenf = "SELECT concat(name, ' ', middle_name) as nombreB, pater_surname as paternoB, mater_surname as maternoB, date_birth as fechaNacB, relationship as civilB, percentage as porcentajeB, sex as sexoB, rfc as rfcB, relationship as parentescoB, nationality as nacionalidadB, residence as residenciaB, economic_activity as actividadB  FROM beneficiaries_hsbc WHERE id_cliente = '$idCliente';";
            foreach($conexion->getData($queryBenf) as $valBn){
                $nombreB = $valBn['nombreB'];
                $paternoB = $valBn['paternoB'];
                $maternoB = $valBn['maternoB'];
                $fechaNacB = date("Y-m-d", strtotime($valBn['fechaNacB']));
                $civilB = $valBn['civilB'];
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
                $data_ben["Porcentaje_B$k"] = $valBn['porcentajeB'];
                $data_ben["Sexo_B$k"] = $sexoB;
                $data_ben["Rfc_B$k"] = $rfcB;
                $data_ben["Parentesco_B$k"] = $parentescoB;
                $data_ben["Nacionalidad_B$k"] = $nacionalidadB;
                $data_ben["Residencia_B$k"] = $residenciaB;
                $data_ben["Actividad_B$k"] = str_replace(',', ' ',$actividadB);

                $i++;
            }

            $urlAfiliados = $conexion->urlApiAfiliados;
            // #Enviamos mediante Curl la informacion a la API AFILIADOS
            $curlAfiliados = $conexion->startCurl($urlAfiliados, $tokenType, $token, $data_ben);
            // var_dump($curlAfiliados);
            if($curlAfiliados != false){
                $errorApi =  $curlAfiliados['code']  =='200' ? 'OK':json_encode($curlAfiliados['error']); 
                $log_alta = "INSERT INTO logs_api (Movimiento_IKE, id_key, cl_Account, titular, api_response, id_event, type_procces, date_created, order_id, error) ";
                $log_alta .= "VALUES('2','NO','". $valAs['cuenta_ike'] ."','". $nombre_titular ."','". $curlAfiliados['code'] ."','NO','NO','". date("Y-m-d H:i:s") ."','NO','".$errorApi."')";
                $conexion->insertData($log_alta);
                if ($errorApi !== 'OK')
                    return false;
            } else
                return false;
        }
        return true;

    } else
        return false;
}

function sendMail($conexion, $idCliente){
    $query = "SELECT * FROM clientes_hsbc WHERE id = '$idCliente';";

    foreach($conexion->getData($query) as $val){    
        $nameClient = $val['name'] . " " . $val['middle_name'] . " " . $val['pater_surname'] . " " . $val['mater_surname'];
        $mailClient = $val['email'];
        $idPrima = $val['id_prima'];
        $clientType = $val['client_type'];
        $sexo = $val['sexo'];
    }

    $prima = 0;
    if($clientType === "ap")
        $campos = "prima_mensual";
    else
        $campos = ($sexo == 'hombre' ? 'hombre as prima_mensual' : 'mujer as prima_mensual');

    $query = "SELECT $campos FROM hsbc_prima_$clientType WHERE id = '$idPrima';";
    foreach($conexion->getData($query) as $val){
        $prima = formatoMoneda($prima + $val['prima_mensual']);
    }

    $asistencia = 0;
    $query = "SELECT price FROM hsbc_cliente_assistance cl INNER JOIN hsbc_assistance ass ON cl.id_assistance = ass.id WHERE cl.id_cliente = '$idCliente';";
    foreach($conexion->getData($query) as $val){
        $asistencia = formatoMoneda($asistencia + $val['price']);
    }
    $textAsis = $clientType === "ap" ? "Accidentes Personales" : "Apoyo Por Hospitalización";
    $plan = "HSBC $textAsis";
    $total = ($prima + $asistencia);

    $mail = new PHPMailer(true);
    $body = file_get_contents('email/confirmationMail.html', dirname(__FILE__));
    $body = str_replace('clienteVar', $nameClient, $body);
    $body = str_replace('fechaVar', date('d-m-Y H:i:s'), $body);
    $productos = '<tr>
    <th width="25%" align="center" style="border: 2px solid #929292;color: #014c82;">'.$nameClient.'</th>
    <th width="25%" align="center" style="border: 2px solid #929292;color: #014c82;">'.$plan.'</th>              
    <th width="25%" align="center" style="border: 2px solid #929292;color: #014c82;">$'. formatoMoneda($total).'</th>
    </tr>';
    $body = str_replace('<tableProductos></tableProductos>', $productos, $body);
    $mail->isSMTP();
    $mail->Host = 'email-smtp.us-east-1.amazonaws.com';
    $mail->SMTPAuth = true; 
    $mail->AddEmbeddedImage('../img/headerMail.png', 'headerMail', 'headerMail.png');
    $mail->AddEmbeddedImage('../img/footerMail.png', 'footerMail', 'footerMail.png');
    $mail->Username = $conexion->mailUser;
    $mail->Password = $conexion->mailPassword;
    $mail->Port = 587;
    $mail->SMTPSecure='TLS';
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->ClearAllRecipients();

    $mail->CharSet = 'UTF-8';
    $mail->MsgHTML($body);
    $mail->IsHTML(true);

    $mail->SetFrom('notificaciones@ikeasistencia.com', 'HSBC ' . $textAsis);
    $mail->Subject = 'Kit de Bienvenida y Términos & Condiciones HSBC ' . $textAsis;
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
    if($mail->send())
        return true;
    else {
        error_log('Error al enviar correo: ' . $mail->ErrorInfo);
        return false;
    }
}

function formatoMoneda($numero)
{
   return number_format(floor(($numero*100))/100, 2);
}

function updatePercentage($pBeneficiarios, $conexion)
{

    if (is_array($pBeneficiarios)) {
        $conexion->beginTransaction();
        $result = array("status" => true, "mensaje" => "Porcentajes actualizados correctamente");

        foreach ($pBeneficiarios as $pBeneficiario) {

            $data = ["id" => $pBeneficiario["idBeneficiario"], "percentage" => $pBeneficiario["porcentaje"]];
            $query = "UPDATE beneficiaries_hsbc SET percentage = :percentage WHERE id = :id";
            if($conexion->insertData($query, $data) === false){
                $conexion->rollBack();
                $result = array("status" => false, "msg" => "Ha ocurrido un error al intentar actualizar los porcentajes");
                break;
            }
        }
        if ($result["status"])
            $conexion->commit();

    } else {
        $result = array("status" => false, "msg" => "Los datos enviados no tienen el formato correcto");
    }

    return json_encode($result);
}

function getSumaAsegurada($conexion, $fechaNac, $sexo)
{
    $cumpleanos = new DateTime($fechaNac);
    $hoy = new DateTime();
    $anios = $hoy->diff($cumpleanos);
    $edad = $anios->y;

    if ($edad < 18)
       return json_encode(["code" => 400, "msg" => "Tu edad debe estar en un rango de 18 y 69 años"]);

    $campos = "";
    if($sexo == 'hombre'){
        $campos .= "id, edad, hombre as pago_mensual, suma_asegurada";
    }else{
        $campos .= "id, edad, mujer as pago_mensual, suma_asegurada";
    }

    $where = " AND edad = $edad";

    $select = "<label>Elige una suma asegurada</label>";
    $select .= "<select name='sumaAsegurada' class='frm__control' id='sumaAseguradaS1' style='width:100%'>";
    $select .= "<option value=''>Seleccione</option>";
    $query = "SELECT $campos FROM hsbc_prima_ah WHERE active = 1 $where";
    foreach($conexion->getData($query) as $val){
        $select .= '<option value="'. $val['id'] . '" data-pago-mensual="'. $val['pago_mensual'] . '" data-id-prima="'. $val['id'] . '" data-suma-asegurada="'. $val['suma_asegurada'] . '">$'. formatoMoneda($val['suma_asegurada']) .'</option>';
    }
    $select .= '</select>';

    return json_encode(["code" => 200, "msg" => "ok", "data" => $select]);
}

function getBeneficiaries($conexion, $idCliente, $path)
{

    $beneficiarios = "";
    $query = "SELECT * FROM beneficiaries_hsbc WHERE active = 1 AND id_cliente = $idCliente ORDER BY id ASC;";
    foreach ($conexion->getData($query) as $val) {
        $nombre = $val['name'] . " " . $val['middle_name'] . " " . $val['pater_surname'] . " " . $val['mater_surname'];
        $beneficiarios .= '<div class="box__row b1">';
        $beneficiarios .= '<div class="box__info"><div class="box__name">' . $nombre . '</div>';
        $beneficiarios .= '<div class="box__action">';
        $beneficiarios .= '<a class="cursor-pointer btn-e-ben" data-id="' . $val['id'] . '"><img src="' . $path .'img/icons/edit.svg"></a>';
        $beneficiarios .= '<a class="cursor-pointer btn-del-ben" data-id="' . $val['id'] . '"><img src="' . $path .'img/icons/delete.svg"></a>';
        $beneficiarios .= '</div></div>';
        $beneficiarios .= '<div class="box__percentage">Porcentaje';
        $porcentaje =  $val['percentage'] == 0 ? "" : $val['percentage'];
        $beneficiarios .= '<div class="box__percentage__input"><input type="text" data-idb="'.$val['id'].'" value="'. $porcentaje .'" class="onlyNumbers" maxlength="3" name="porcentaje[]"> %';
        $beneficiarios .= '</div></div></div><br>';
    }
    return $beneficiarios;
}

function resumSoli($conexion, $idCliente, $app)
{
    $seguro = "";
    $relativePath = $app === "ap" ? "" : "../";
    $count = 0;
    $select = $app === "ap" ? ", suma_asegurada, prima_mensual, prima_anual" : ", suma_asegurada, hombre, mujer, sexo";
    $query = "SELECT cl.id_prima $select FROM clientes_hsbc cl INNER JOIN hsbc_prima_$app hp ON cl.id_prima = hp.id WHERE cl.id = '$idCliente';";
    foreach ($conexion->getData($query) as $val) {
        $count++;
        $prima = $app === "ap" ? $val['prima_mensual'] : ($val['sexo'] == 'hombre' ? $val['hombre'] : $val['mujer']);
        $seguro .= '<div class="tbl">';
        $seguro .= '<div class="tbl__body">';
        $seguro .= '<div class="tbl__row">';
        $seguro .= '<div class="tbl__col left">';
        $seguro .= 'Suma asegurada:<br>$ ' . formatoMoneda($val['suma_asegurada']) . ' MXN';
        $seguro .= '</div>';
        if ($app === "ap")
            $seguro .= '<div class="tbl__col right">Prima anual:<br>$ ' . formatoMoneda($val['prima_anual']) . ' MXN';

        $seguro .= '</div></div><div class="tbl__row">';
        $seguro .= '<div class="tbl__col left subtotal">Subtotal mensual a pagar</div>';
        $seguro .= '<div class="tbl__col right subtotal2">$ ' . formatoMoneda($prima) . ' MXN</div>';
        $seguro .= '</div></div></div>';

    }
    if ($count == 0) {
        $cero = 0;
        $seguro .= '<div class="tbl">';
        $seguro .= '<div class="tbl__body">';
        $seguro .= '<div class="tbl__row">';
        $seguro .= '<div class="tbl__col left">';
        $seguro .= 'Suma asegurada:<br>$ ' . formatoMoneda($cero) . ' MXN';
        $seguro .= '</div>';
        $seguro .= '<div class="tbl__col right">Prima anual:<br>$ ' . formatoMoneda($cero) . ' MXN';
        $seguro .= '</div></div><div class="tbl__row">';
        $seguro .= '<div class="tbl__col left subtotal">Subtotal mensual a pagar</div>';
        $seguro .= '<div class="tbl__col right subtotal2">$ ' . formatoMoneda($cero) . ' MXN</div>';
        $seguro .= '</div></div></div>';
    }

    $asistencias = "";
    $price = 0;
    $query = "SELECT ass.assistance, ass.price FROM hsbc_cliente_assistance ca INNER JOIN hsbc_assistance ass ON ca.id_assistance = ass.id WHERE id_cliente = '$idCliente';";
    $asistencias .= '<div class="tbl"><div class="tbl__body">';
    foreach ($conexion->getData($query) as $val) {
        $price = $price + $val['price'];
        $asistencias .= '<div class="tbl__row">';
        $asistencias .= '<div class="tbl__col left">'. $val['assistance'] .'</div>';
        $asistencias .= '<div class="tbl__col right">+$'. formatoMoneda($val['price']) .' MXN</div></div>';
    }
    $asistencias .= '<div class="tbl__row"><div class="tbl__col left subtotal">Subtotal mensual a pagar</div>';
    $asistencias .= '<div class="tbl__col right subtotal2">$'. formatoMoneda($price) .' MXN</div></div>';
    $asistencias .= '<div class="tbl__note"><img src="' . $relativePath . 'img/icons/info.svg" class="info__icon">Tu primer mes de asistencias no tiene costo.</div>';

    $asistencias .= '</div></div>';

    $beneficiarios = "";
    $query = "SELECT * FROM beneficiaries_hsbc WHERE id_cliente = '$idCliente';";
    foreach ($conexion->getData($query) as $val) {
        $name = $val['name'] . " " . $val['middle_name'] . " " . $val['pater_surname'] . " " . $val['mater_surname'];
        $beneficiarios .= '<div class="tbl"><div class="tbl__body">';
        $beneficiarios .= '<div class="tbl__row"><div class="tbl__col left full">';
        $beneficiarios .= '<img src="' . $relativePath . 'img/icons/person2.svg">';
        $beneficiarios .= '<p> ' . $name . ' <br><span class="percentage">' . $val['percentage'] . '%</span></p>';
        $beneficiarios .= '</div></div></div></div>';
    }

    return $seguro . "___" . $asistencias . "___" .$beneficiarios;
}

function verifyRFC($conexion, $rfc, $verify,  $idBenORtypeClient, $idCliente = 0){

    switch ($verify) {
        case "cliente":
            $query = "SELECT id FROM clientes_hsbc WHERE rfc = :rfc AND active = 1 AND client_type = :client_type";
            $data =  ["rfc" => $rfc, "client_type" => $idBenORtypeClient];
            break;
        case "beneficiario":
            $query = "SELECT id, rfc FROM beneficiaries_hsbc WHERE rfc = :rfc AND active = 1 AND id_cliente = :idCliente";
            $data =  ["rfc" => $rfc, "idCliente" => $idCliente];
            break;
    }
    $rows = $conexion->getData($query, $data);

    if(count($rows)){
        if ($idBenORtypeClient !== 0 && $idCliente !== 0){
            foreach ($rows as $row) {
                if ($row["id"] != $idBenORtypeClient)
                    return true;
            }
            return false;
        } else
            return true;
    }
    else
        return false;
}

switch ($action):
    // ah
    case 'getSumaAsegurada':
        $fechaNac = $_POST['fechaNac'];
        $sexo = $_POST['sexo'];
        echo getSumaAsegurada($conexion, $fechaNac, $sexo);
        break;

    case 'saveClient':

        if (verifyRFC($conexion, $_POST['rfc'], "cliente", $_POST['clientType'])) {
           echo json_encode(array("code" => 400, "msg" => "Tus datos ya han sido registrados anteriormente"));
           exit();
        }

        $asistencias = isset($_POST['asistencias']) ? $_POST['asistencias'] : [];
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
        $clientType = $_POST['clientType'];
        $code = genCode();
        $result = saveClient($conexion, $nombre, $segundoNombre, $apellidoPaterno, $apellidoMaterno, $fechaNac, $rfc, $email, $telefono, $code, $idPrima, $asistencias, $sexo, $clientType);

        if (isset($result["idCliente"]))
            $result["msgCode"] = sendCodeCell($code, $telefono, $conexion);

        echo json_encode($result);
        break;

    case 'updateClient':
        $asistencias = isset($_POST['asistencias']) ? $_POST['asistencias'] : [];
        $idPrima = $_POST['idPrima'];
        $nombre = $_POST['nombre'];
        $segundoNombre = $_POST['segundoNombre'];
        $apellidoPaterno = $_POST['apellidoPaterno'];
        $apellidoMaterno = $_POST['apellidoMaterno'];
        $fechaNac = $_POST['fechaNac'];
        $email = $_POST['email'];
        $sexo = $_POST['sexo'];
        $clientType = $_POST['clientType'];
        $idCliente = $_POST['idCliente'];
        $result = updateClient($conexion, $nombre, $segundoNombre, $apellidoPaterno, $apellidoMaterno, $fechaNac, $email, $idPrima, $asistencias, $sexo, $idCliente);
        echo json_encode($result);
        break;

    case 'saveBeneficiare':
        if (verifyRFC($conexion, $_POST['rfc'], "beneficiario", 0, $_POST['idCliente'])) {
            echo json_encode(array("code" => 400, "msg" => "El beneficiario que intentas agregar ya ha sido registrado anteriormente"));
            exit();
        }
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
        if (verifyRFC($conexion, $_POST['rfc'], "beneficiario", $_POST['idBeneficiario'], $_POST['idCliente'])) {
            echo json_encode(array("code" => 400, "msg" => "El beneficiario que intentas actualizar ya ha sido registrado anteriormente"));
            exit();
        }
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

        if(updateCodeClient($conexion, $idCliente, $code) === 0) {
            $telefono = getCellClient($conexion, $idCliente);

            try {
                sendCodeCell($code, $telefono, $conexion);
                $response = array("code" => 200, "msg" => "ok");

            } catch (Exception $e) {
                $response = array("code" => 400, "msg" => "Error inesperado, intente más tarde por favor");
            }
        } else {
            $response = array("code" => 400, "msg" => "Error al actualizar código");
        }
        echo json_encode($response);
        break;

    case 'verifyCard':
        $idCliente = $_POST['idCliente'];   
        $numeroTarjeta = $_POST['numeroTarjeta'];   
        $clientType = $_POST['clientType'];
        echo verifyCard($conexion, $idCliente, $numeroTarjeta, $clientType);
        break;

    case 'updatePercentage':
        echo updatePercentage($_POST["pBeneficiarios"], $conexion);
        break;
    case 'getBeneficiaries':
        $idCliente = $_POST['idCliente'];
        $path = $_POST['path'];
        $data = getBeneficiaries($conexion, $idCliente, $path);
            echo json_encode(["code" => 200, "data" => $data]);
        break;
    case 'resumSoli':
        $idCliente = $_POST['idCliente'];
        $app = $_POST['app'];
        $data = resumSoli($conexion, $idCliente, $app);
        if ($data !== '')
            echo json_encode(["code" => 200, "data" => $data]);
        else
            echo json_encode(["code" => 400, "msg" => "Error al obtener Resumen de datos"]);
        break;
    case 'deleteSeguro':
        $idCliente = $_POST['idCliente'];

        if (deleteSeguro($conexion, $idCliente))
            echo json_encode(["code" => 200]);
        else
            echo json_encode(["code" => 400, "msg" => "Error al eliminar seguro"]);
        break;
endswitch;