<?php
//require_once("../Conekta.php");
require_once("./ConektaApi.php");

/**
 * Clase de pago 
 */
class Payment{
    private $tarjetaHabiente;
    private $correo;
    private $telefono;
    private $total;
    private $plan;
    private $forma_pago;
    private $tarjeta = null;
    private $renovacion;
    private $tipo_pago;
    private $empresa;
    private $productos;
    private $idLink;
    private $correoasesor;
    private $ApiKey="";
    private $ApiVersion="";
    public  $order_id;
    public  $conexion;
    public $conektaTokenId;
    private $cl_account;
    private $account;
    public  $error;
    public  $return_url;
    public  $three_ds_url;
    public  $paymentStatus;

    public function __construct($conektaTokenId,$tarjetaHabiente,$correo,$telefono,$tarjeta,$total,
      $plan,$forma_pago,$tipo_pago,$account,$cl_account,$renovacion,$empresa,$productos,$_conexion,$return_url,$idLink="",$correoasesor=""){
        $this->conektaTokenId=$conektaTokenId;
        $this->tarjetaHabiente=$tarjetaHabiente;
        $this->correo=$correo;
        $this->telefono=$telefono;
        $this->tarjeta=$tarjeta; 
        $this->total=$total;
        $this->forma_pago=$forma_pago; 
        $this->tipo_pago=$tipo_pago; 
        $this->plan=$plan; 
        $this->account=$account; 
        $this->cl_account=$cl_account;
        $this->renovacion=$renovacion;
        $this->empresa=$empresa;
        $this->productos=$productos;
        $this->conexion=$_conexion;
        $this->idLink=$idLink;
        $this->correoasesor=$correoasesor;
        $this->order_id = "";
        $this->return_url = $return_url; 
    }

    public function Pay(){
      $ConektaAPI = new ConektaApi($this->conektaTokenId,$this->ApiKey);
     
      $this->conexion->beginTransaction();


      if(!$ConektaAPI->createCustomer($this->correo,$this->telefono,$this->tarjetaHabiente)){
        $this->error = "Error al crear el Customer en conekta";
        $this->conexion->rollback();
        return false;
      }
      if(!$this->guardarOrden()){
        $this->error = 'Algo ha salido mal al Guardar su orden';
        $this->conexion->rollback();
        return false;
      }

      
      $metadata = $this->collectMetadata();


        if(!$this->guardarTitular()){
            $this->error = 'Algo ha salido mal al Guardar su titular';
            $this->conexion->rollback();
            return false;
        }
        if(!$this->guardarBeneficiarios()){
            $this->error = 'Algo ha salido mal al Guardar su beneficiario';
            $this->conexion->rollback();
            return false;
        }
        if(!$this->guardarBeneficiariosMuerte()){
            $this->error = 'Algo ha salido mal al Guardar su beneficiario de muerte';
            $this->conexion->rollback();
            return false;
        }

        if(!$ConektaAPI->CreateOrder($this->plan,$this->total,$metadata,$this->return_url)){
          $this->error = "Error al crear la Orden en conekta";
          $this->conexion->rollback();
          return false;
        }
  
        $this->order_id = $ConektaAPI->getOrderId();
        $this->three_ds_url = $ConektaAPI->getThreeDsUrl();
        $this->paymentStatus = $ConektaAPI->getPaymentStatus();        

        
        if(!$this->updateOrder()){
          return false;
        }
        if(!$this->updateBeneficiarios()){
          return false;
        }
        if(!$this->updateBeneficiariosMuerte()){
          return false;
        }
        
        $this->actualizarFolio();

        $this->conexion->commit();
        return true;
    }

    public function collectMetadata(){
      return array(
        "Cuenta" => $this->cl_account,
        "Programa" => $this->plan, 
        "E-mail" => $this->correo,
        "Tel" => $this->telefono,
        "Pago" => $this->tipo_pago,
        "Empresa" => $this->empresa,
        "renovacion" => $this->renovacion
      );
    }
    
    public function saveDataLink(){
      $this->conektaTokenId = new stdClass;
      $this->conektaTokenId = $this->idLink;
      $this->conexion->beginTransaction();

      if(!$this->guardarOrden()){
        $this->error = 'Algo ha salido mal al Guardar su orden';
        $this->conexion->rollback();
        return false;
      }
      if(!$this->guardarTitular()){
          $this->error = 'Algo ha salido mal al Guardar su titular';
          $this->conexion->rollback();
          return false;
      }
      if(!$this->guardarBeneficiarios()){
          $this->error = 'Algo ha salido mal al Guardar su beneficiario';
          $this->conexion->rollback();
          return false;
      }
      if(!$this->guardarBeneficiariosMuerte()){
          $this->error = 'Algo ha salido mal al Guardar su beneficiario de muerte';
          $this->conexion->rollback();
          return false;
      }
      
      $this->actualizarFolio();

      $this->conexion->commit();
      return true;
  }


    public function guardarOrden(){
        $query = "INSERT INTO orders (name,url,email,telephone,number_card,description,total,plan,forma_pago,account,cl_Account,beneficiary,rfc_curp,date_created,order_id,id_token, renovacion,empresa)
        VALUES (:name, :url, :email, :telephone, :number_card, :description, :total, :plan, :forma_pago, :account, :cl_Account, :beneficiary_name1, :rfc_curp, '".$this->conexion->formatDay()."', :order_id, :id_token, :renovacion, :empresa)";
        $queryArray = [
            'name' => $this->tarjetaHabiente,
            'url' => $this->empresa != "" ? "Automarsh" : "",
            'email'=> $this->correo,
            'telephone'=> $this->telefono,
            'number_card'=> $this->conexion->encryptCard($this->tarjeta),
            'description' => $this->plan,
            'total' => $this->total, 
            'plan' => $this->plan, 
            'forma_pago' => $this->forma_pago,
            'account' => $this->account, 
            'cl_Account' => $this->cl_account,
            'beneficiary_name1' => $this->tarjetaHabiente ,
            'rfc_curp' => $this->correoasesor,
            'order_id'=>$this->conektaTokenId,
            'id_token'=>$this->conektaTokenId,
            'renovacion' => $this->renovacion,
            'empresa' => $this->empresa
        ];

        if(!$this->conexion->insertData($query, $queryArray)){
            throw new Exception('Algo ha salido mal al guardar su orden');
            return false;
        }
        return true;
    }

    public function guardarTitular(){
        $orden = $this->conexion->sessionFilter($this->productos,"titular", "titular");
        foreach($orden as $titular){
            $this->titular = $titular["nombre"].' '.$titular["paterno"].' '.$titular["materno"];
            #GUARDADO DE TITULAR EN TABLA BENEFICIARIOS
            $query = "INSERT INTO beneficiaries (order_id, date_created, consec_soprov, beneficiary_name, beneficiary_pat, beneficiary_mat, date_birth, email, telephone, plan, folio_consecutivo, tipo, parentesco)
            VALUES (:order_id, '".$this->conexion->formatDay()."', 0, :beneficiary_name, :beneficiary_pat, :beneficiary_mat, :date_birth, :email, :telephone, :plan, :folio_consecutivo, :tipo, :parentesco)";
            $queryArray = [
                'order_id' => $this->conektaTokenId,
                'beneficiary_name' => $titular["nombre"],
                'beneficiary_pat' => $titular["paterno"],
                'beneficiary_mat' => $titular["materno"],
                'date_birth' => $this->conexion->formatDay($titular["cumpleanos"]),
                'email'=> $titular["email"],
                'telephone'=> $this->telefono,
                'plan' => $this->plan,
                'folio_consecutivo' => 0,
                'tipo' => 'titular',
                'parentesco' => "titular"
            ];
            if(!$this->conexion->insertData($query, $queryArray)){
                throw new Exception('Algo ha salido mal al guardar su titular');
                return false;
            }
        }
        return true;
    }

    public function guardarBeneficiarios(){
        $beneficiarios = $this->conexion->sessionFilter($this->productos,"beneficiario", "beneficiario");
        foreach($beneficiarios as $beneficiario){
            $query = "INSERT INTO beneficiaries (order_id, date_created, consec_soprov, beneficiary_name, beneficiary_pat, beneficiary_mat, date_birth, email, telephone, plan, tipo, parentesco,folio_consecutivo)
            VALUES (:order_id, '".$this->conexion->formatDay()."', 0, :beneficiary_name, :beneficiary_pat, :beneficiary_mat, :date_birth, :email, :telephone, :plan, :tipo, :parentesco,:folio_consecutivo)";
            $queryArray = [
                'order_id' => $this->conektaTokenId,
                'beneficiary_name' => $beneficiario["nombre"],
                'beneficiary_pat' => $beneficiario["paterno"],
                'beneficiary_mat' => $beneficiario["materno"],
                'date_birth' => $this->conexion->formatDay($beneficiario["cumpleanos"]),
                'email'=> $beneficiario["email"],
                'telephone'=> $this->telefono,
                'plan' => $this->plan,
                'tipo' => 'beneficiario',
                'parentesco' => $beneficiario["parentesco"],
                'folio_consecutivo' => 0
            ];
            if(!$this->conexion->insertData($query, $queryArray)){
                throw new Exception('Algo ha salido mal al guardar sus beneficiarios');
                return false;
            }

        }
        return true;
    }

    public function guardarBeneficiariosMuerte(){
        $beneficiarios_muerte = $this->conexion->sessionFilter($this->productos,"beneficiarioMuerte", "beneficiarioMuerte");
        foreach($beneficiarios_muerte as $beneficiario_muerte){
            $query = "INSERT INTO beneficiaries_died (order_id, date_created, beneficiaryd_name, beneficiaryd_pat, beneficiaryd_mat, date_birth, plan, email, parentesco, porcentaje)
            VALUES (:order_id, '".$this->conexion->formatDay()."', :beneficiaryd_name, :beneficiaryd_pat, :beneficiaryd_mat, :date_birth, :plan, :email, :parentesco, :porcentaje)";
            $queryArray = [
                'order_id' => $this->conektaTokenId,
                'beneficiaryd_name' => $beneficiario_muerte["nombre"],
                'beneficiaryd_pat' => $beneficiario_muerte["paterno"],
                'beneficiaryd_mat' => $beneficiario_muerte["materno"],
                'date_birth' => $this->conexion->formatDay($beneficiario_muerte["cumpleanos"]),
                'plan' => $this->plan,
                'email' => $beneficiario_muerte["email"],
                'parentesco' => $beneficiario_muerte["parentescoMuerte"],
                'porcentaje' => 100
            ];
            if(!$this->conexion->insertData($query, $queryArray)){
                throw new Exception('Algo ha salido mal al guardar el Beneficiario de Muerte');
                return false;
            }
        }  
        return true;
    }

    public function actualizarFolio(){
      $folio = $this->conexion->getFolio($this->order_id);
      $folio_consecutivo = "AS".str_pad($folio, 10, '0', STR_PAD_LEFT);
      $data = [
        'folio_consecutivo' => $folio_consecutivo,
      ];
      $query = "UPDATE beneficiaries SET folio_consecutivo = :folio_consecutivo WHERE order_id = '".$this->order_id."'";
      if(!$this->conexion->insertData($query, $data)){
        error_log('Algo ha salido mal al actualizar el folio consecutivo');
        return false;
      }else{
        $folio_consecutivo_muerte = "ASB".str_pad($folio, 9, '0', STR_PAD_LEFT);
        $dataMuerte = [
          'folio_consecutivo' => $folio_consecutivo_muerte,
        ];
        $queryMuerte = "UPDATE beneficiaries_died SET folio_consecutivo = :folio_consecutivo WHERE order_id = '".$this->order_id."'";
        if(!$this->conexion->insertData($queryMuerte, $dataMuerte)){
          error_log('Algo ha salido mal al actualizar el folio consecutivo');
          return false;
        }
      }
    }

    public function updateOrder(){
      try {
        $query = "
                  UPDATE orders SET order_id=:order_id,
                  status = :status  
                  WHERE order_id=:id_token";
        $queryArray = [
            "order_id" => $this->order_id,
            "id_token" => $this->conektaTokenId,
            "status"    => $this->paymentStatus
        ];

        
        if($this->conexion->insertData($query, $queryArray)){
          return true;
        }else{
          $this->error = 'No se pudo actualizar el ID de orden';
          return false;
        }
    } catch (\Exception $e) {
        $this->error = $e->getMessage();
        return false;
      }
    }

    public function updateBeneficiarios(){
      try {
        $query = "
                  UPDATE beneficiaries SET order_id=:order_id
                  WHERE order_id=:id_token";
        $queryArray = [
            "order_id" => $this->order_id,
            "id_token" => $this->conektaTokenId
                  ];

        
        if($this->conexion->insertData($query, $queryArray)){
          return true;
        }else{
          $this->error = 'No se pudo actualizar el ID del beneficiario';
          return false;
        }
    } catch (\Exception $e) {
        $this->error = $e->getMessage();
        return false;
      }
    }

    public function  updateBeneficiariosMuerte(){
      try {
        $query = "
                  UPDATE beneficiaries_died SET order_id=:order_id
                  WHERE order_id=:id_token";
        $queryArray = [
            "order_id" => $this->order_id,
            "id_token" => $this->conektaTokenId
                  ];

        
        if($this->conexion->insertData($query, $queryArray)){
          return true;
        }else{
          $this->error = 'No se pudo actualizar el ID del beneficiario de su muerte';
          return false;
        }
    } catch (\Exception $e) {
        $this->error = $e->getMessage();
        return false;
      }
    }

    public function setPrivates($api,$version){
      $this->ApiKey= $api;
      $this->ApiVersion= $version;
    }
}

?>