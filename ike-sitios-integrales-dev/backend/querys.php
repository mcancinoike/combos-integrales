<?php
require 'email/src/Exception.php';
require 'email/src/PHPMailer.php';
require 'email/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "conexion/conexion.php";
$conexion = new Conexion();

$action = $_POST['action'];

function saveClient($conexion, $data)
{

    $query = "INSERT INTO clientes_hsbc (client_type, name, middle_name, pater_surname, mater_surname, cell_phone, code_cell, confirm_cell, email, date_birth, rfc, id_prima, sexo, updated_at) 
                     VALUES(:client_type, :name, :middle_name, :pater_surname, :mater_surname, :cell_phone, :code_cell, :confirm_cell, :email, :date_birth, :rfc, :id_prima, :sexo, :updated_at);";

    $data["updated_at"] = '0000-00-00 00:00:00';
    return $conexion->insertData($query, $data);
}

function saveAssistance($conexion, $asistencias, $idCliente)
{
    $return = 0;
    foreach ($asistencias as $val2) {

        $query2 = "INSERT INTO hsbc_cliente_assistance (id_cliente, id_assistance, updated_at) VALUES(:id_cliente, :id_assistance , '0000-00-00 00:00:00');";
        $return += $conexion->insertData($query2, ["id_cliente" => $idCliente, "id_assistance" => $val2["id_assistance"]]);
    }
    return $return;
}
function saveBeneficiares($conexion, $beneficiarios, $idCliente)
{

    $return = 0;
    foreach ($beneficiarios as $data) {

        $query = "INSERT INTO beneficiaries_hsbc (id_cliente, relationship, name, middle_name, pater_surname, mater_surname, marital_status, sex, date_birth, rfc, nationality, economic_activity, residence, percentage, updated_at) 
                             VALUES(:id_cliente, :relationship, :name, :middle_name, :pater_surname, :mater_surname, :marital_status, :sex, :date_birth, :rfc, :nationality, :economic_activity, :residence, :percentage, :updated_at);";

        $data["id_cliente"] = $idCliente;
        $data["updated_at"] = '0000-00-00 00:00:00';
        unset($data["preId"]);

        $return += $conexion->insertData($query, $data);

    }

    return $return;
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

function confirm($campo, $conexion, $idCliente)
{
    $query = "UPDATE clientes_hsbc SET confirm_$campo = 1 WHERE id = :idCliente";
    $data = ["idCliente" => $idCliente];

    return $conexion->insertData($query, $data);
}

function insertAllData($conexion, $allData)
{

    $bin = substr($allData["card"], 0, 6);

    $query = "SELECT description FROM bin_account WHERE bin = :bin AND account_id = 999999";
    $data = ["bin" => $bin];
    $rows = $conexion->getData($query, $data);

    if(count($rows)){

        try {
            $conexion->beginTransaction();

            $idCliente = saveClient($conexion, $allData["cliente"]);

            if (is_numeric($idCliente)) {

                $gate = true;

                // Programa de asistencias
                if (isset($allData["asistencias"])) {

                    $numInsertsAsistance = saveAssistance($conexion, $allData["asistencias"], $idCliente);

                    if ($numInsertsAsistance === 0) {
                        $gate = false;
                        $result = array("code" => 400, "msg" => "Error al intentar guardar sus Asistencias");

                    } else {
                        $cardType = str_contains($rows[0]["description"], "DÉBITO") ? "TDD" : "TDC";
                        $respAfiliados = apiAfiliados($conexion, $cardType, $allData);
                        if ($respAfiliados["code"] == 400) {
                            $gate = false;
                            $result = $respAfiliados;

                        } else {
                            if (sendMail($allData)) {
                                if (confirm("email", $conexion, $idCliente) !== 0) {
                                    $gate = false;
                                    $result = array("code" => 400, "msg" => "Error al confirmar correo");
                                }
                            } else {
                                $gate = false;
                                $result = array("code" => 400, "msg" => "Error al enviar correo");
                            }
                        }
                    }

                }

                // contrato de Seguro
                if (isset($allData["beneficiarios"]) && $gate) {

                    $numInsertBeneficiaries = saveBeneficiares($conexion, $allData["beneficiarios"], $idCliente);

                    if (is_numeric($numInsertBeneficiaries) && $numInsertBeneficiaries == 0) {
                        $gate = false;
                        $result = array("code" => 400, "msg" => "Error al intentar guardar sus beneficiarios");
                    }
                }

                //Todo salio bien en guardar Asistencias y/o Seguro
                if ($gate)
                    $result = array("code" => 200, "msg" => "Información guardada con éxito!");

            } else
                $result = array("code" => 400, "msg" => "Error al intentar guardar su información");

        } catch (Exception $e){
            error_log("Error en el proceso de inserción de datos: " . $e->getMessage());
            $result = array("code" => 400, "msg" => "Error en el proceso de inserción de datos");
        }

        if ($result["code"] == 200)
            $conexion->commit();
        else
            $conexion->rollback();

    } else
        $result = array("code" => 400, "msg" => "La tarjeta ingresada es incorrecta");

    return json_encode($result);
}

function apiAfiliados($conexion, $cardType, $data){
    #oauth/token
    $urlOauth = $conexion->urlOauth;
    $curlOauth = $conexion->startCurl($urlOauth);
    // var_dump($curlOauth);
    if($curlOauth != false){
        #OBTENEMOS UN TOKEN PARA LA API DE AFILIADOS
        $token = $curlOauth['access_token'];
        $tokenType = $curlOauth['token_type'];
        $clave = $data["cliente"]["rfc"];
        $fecha_inicio = date("Y-m-d");
        $fecha_fin = date("Y-m-d", strtotime($fecha_inicio . "+ 1 year"));
        $ultimosTDC = "************" . substr($data["card"], 12, 16);
        $nombre_titular = $data["cliente"]["name"] . ' ' . $data["cliente"]["middle_name"] . ' ' . $data["cliente"]["pater_surname"] . ' ' . $data["cliente"]["mater_surname"];
        $fecha_nacimiento = date("Y-m-d", strtotime($data["cliente"]["date_birth"]));
        $fecha_venta = date("Y-m-d");
        $celular = $data["cliente"]["cell_phone"];
        $correo = $data["cliente"]["email"];
        $sumaAsegurada =$data["seguro"]["sumaAsegurada"];


        $cumpleanos = new DateTime($fecha_nacimiento);
        $hoy = new DateTime();
        $anios = $hoy->diff($cumpleanos);
        $edad = $anios->y;


        foreach($data["asistencias"] as $asistencia){

            $queryAsist = "select assistance as producto, cuenta_ike, clProyecto from hsbc_assistance WHERE id = " . $asistencia["id_assistance"];
            $rows = $conexion->getData($queryAsist);
            $valAs = $rows[0];
            $data_ben = [
                "Cuenta_IKE" => $valAs['cuenta_ike'],
                "Movimiento_IKE" => "2",
                "Clave" => $clave,
                "Fecha_Inicio" => $fecha_inicio,
                "Fecha_Fin" => $fecha_fin,
                "card" => $data["card"],
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
                "Producto" => $data["cliente"]["client_type"] === "ap" ? "Accidentes Personales" : "Apoyo por hospitalización",
                "Programa" => $valAs['producto'],
                "Suma_Asegurada" => $sumaAsegurada,
                "Fecha_Venta" => $fecha_venta
            ];

            $i = 1;
            $arrayBen = isset($data["beneficiarios"]) ? $data["beneficiarios"] : [];

            foreach($arrayBen  as $beneficiario){
                $nombreB = $beneficiario["name"] . ' ' . $beneficiario["middle_name"];
                $paternoB = $beneficiario["pater_surname"];
                $maternoB = $beneficiario["mater_surname"];
                $fechaNacB = date("Y-m-d", strtotime($beneficiario["date_birth"]));
                $civilB = $beneficiario["marital_status"];
                $sexoB = $beneficiario["sex"];
                $rfcB = $beneficiario["rfc"];
                $parentescoB = $beneficiario["relationship"];
                $nacionalidadB = $beneficiario["nationality"];
                $residenciaB = $beneficiario["residence"];
                $actividadB = $beneficiario["economic_activity"];
                
                $k = ($i == 1 ? "" : $i);
                $data_ben["Nombre_B$k"] = $nombreB;
                $data_ben["Apellido_Paterno_B$k"] = $paternoB;
                $data_ben["Apellido_Materno_B$k"] = $maternoB;
                $data_ben["Fecha_Nacimiento_B$k"] = $fechaNacB;
                $data_ben["Estado_Civil_B$k"] = $civilB;
                $data_ben["Porcentaje_B$k"] = $beneficiario["percentage"];
                $data_ben["Sexo_B$k"] = $sexoB;
                $data_ben["Rfc_B$k"] = $rfcB;
                $data_ben["Parentesco_B$k"] = $parentescoB;
                $data_ben["Nacionalidad_B$k"] = $nacionalidadB;
                $data_ben["Residencia_B$k"] = $residenciaB;
                $data_ben["Actividad_B$k"] = $actividadB;

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
                if ($errorApi !== 'OK') {
                    error_log("Error envio API afiliados. ($errorApi)");
                    return ["code" => 400, "msg" => "Error API afiliados (E3)"];
                }
            } else
                return ["code" => 400, "msg" => "Error API afiliados (E2)"];
        }
        return ["code" => 200, "msg" => "OK"];

    } else
        return ["code" => 400, "msg" => "Error API afiliados (E1)"];
}

function sendMail($data){

    $nameClient = $data["cliente"]["name"] . " " . $data["cliente"]['middle_name'] . " " . $data["cliente"]['pater_surname'] . " " . $data["cliente"]['mater_surname'];
    $mailClient = $data["cliente"]['email'];

    $asistencias = '';
    foreach ($data["asistencias"] as $asis)
        $asistencias .= $asis["name"] . '<br/>';

    $asistencias = substr($asistencias, 0, -5);

    $total = $data["pagoTotalMensual"];

    $mail = new PHPMailer(true);
    $body = file_get_contents('email/confirmationMail.html', dirname(__FILE__));
    $body = str_replace('clienteVar', $nameClient, $body);
    $body = str_replace('fechaVar', date('d-m-Y H:i:s'), $body);
    $productos = '<tr>
    <th width="25%" align="center" style="border: 2px solid #929292;color: #014c82;">'.$nameClient.'</th>
    <th width="25%" align="center" style="border: 2px solid #929292;color: #014c82;">'.$asistencias.'</th>              
    <th width="25%" align="center" style="border: 2px solid #929292;color: #014c82;">$'. formatoMoneda($total).'</th>
    </tr>';
    $body = str_replace('<tableProductos></tableProductos>', $productos, $body);
    $mail->isSMTP();
    $mail->Host = 'smtp1.us.scanscope.net';
    $mail->AddEmbeddedImage('../img/headerMail.png', 'headerMail', 'headerMail.png');
    $mail->AddEmbeddedImage('../img/footerMail.png', 'footerMail', 'footerMail.png');
    $mail->Port = 587;
    $mail->ClearAllRecipients();

    $mail->CharSet = 'UTF-8';
    $mail->MsgHTML($body);
    $mail->IsHTML(true);

    $mail->SetFrom('notificaciones@ikeasistencia.com', 'Programa de Asistencias - HSBC');
    $mail->Subject = 'Kit de Bienvenida - HSBC';
    $mail->addAddress($mailClient, $nameClient);

    foreach($data["asistencias"] as $val){
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
    if($sexo == 'h'){
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

switch ($action):
    // ah
    case 'getSumaAsegurada':
        $fechaNac = $_POST['fechaNac'];
        $sexo = $_POST['sexo'];
        echo getSumaAsegurada($conexion, $fechaNac, $sexo);
        break;
    case 'sendCode':
        $cellPhone = $_POST['cell_phone'];
        $code = genCode();

        try {
            sendCodeCell($code, $cellPhone, $conexion);
            $response = array("code" => 200, "msg" => "ok", "codeCell" => $code);

        } catch (Exception $e) {
            $response = array("code" => 400, "msg" => "Error en app de envío de sms, intente más tarde por favor");
        }
        echo json_encode($response);
        break;

    case 'insertAllData':
        $allData = $_POST['allData'];
        echo insertAllData($conexion, $allData);
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
endswitch;