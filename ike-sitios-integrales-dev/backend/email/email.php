<?php
    require_once "src/PHPMailer.php";
    require_once "src/SMTP.php";
    require_once "src/Exception.php";
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    class Email {
        private $name;
        private $email;
        private $validity_start;
        private $validity_end;
        private $renovation;
        private $order_id;
        private $conexion;

        public function __construct($titular, $email, $inicioVigencia, $finVigencia, $renovacion, $order_id, $conexion){
            $this->name = $titular;
            $this->email = $email;
            $this->validity_start = $inicioVigencia;
            $this->validity_end   = $finVigencia;
            $this->renovation = $renovacion;
            $this->order_id = $order_id;
            $this->conexion = $conexion;
        }

        public function send(){
            if(!$this->email()){
                return false;
            }
            return true;
        }

        public function email(){
            try {
                $mail = new PHPMailer;
                $mail->IsSMTP();
                $mail->SMTPDebug = 0;
                $mail->IsHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Host = "email-smtp.us-east-1.amazonaws.com";
                $mail->Port = $this->conexion->puertoEmail;
                $mail->SMTPSecure="TLS";
                $mail->AddReplyTo($this->email, $this->name);
                $mail->From = "";
                $mail->FromName = "";
                $mail->Subject = "";
                $mail->AddEmbeddedImage('../../img/img.jpg','img','img.jpg');
                $mail->AddAttachment('', 'WK.pdf');
                $body = file_get_contents("../email/email.html");
                $body = str_replace("username", $this->name, $body);
                $mail->MsgHTML($body);
                $mail->AddAddress($this->email);
                $mail->SMTPAuth = true;
                $mail->Username = "AKIASWTWHVISO27R4S7R";
                $mail->Password = "BIWnz2eaTb7DgUA1VdNWw3NX1jvlPjDoPPETy4CZIR01";
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );
                if(!$mail->Send()){
                    error_log("No se pudo enviar el correo: " .$mail->ErrorInfo);
                    return false;
                }else{
                    $queryArray =['order_id' => $this->order_id];
                    $query = "
                            UPDATE  SET send_mail = 1 
                                WHERE  = :";
                    $this->conexion->insertData($query, $queryArray);
                    return true;
                }
            } catch (\Exception $e) {
                $this->error = $e->getMessage();
            }
        }
    }
?>