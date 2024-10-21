<?php
    class CSRF{

        public function verify_csrf_token($token) {
            return isset($_COOKIE['csrf_token']) && hash_equals($_COOKIE['csrf_token'], $token);
        }

        public function generarTokenCSRF(){

            $token = bin2hex(random_bytes(32));
            setcookie('csrf_token', $token, [
                'expires' => time() + 3600, // Tiempo de expiración de la cookie
                'path' => '/', // Path válido para toda la aplicación
                'secure' => true, // Cookie solo enviada sobre HTTPS
                'httponly' => true, // Cookie accesible solo por HTTP
                'samesite' => 'Strict' // Cookie solo enviada para solicitudes del mismo sitio
            ]);
            
        }

        public function valiarTokenCSRF(){


            if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
                $token = $_COOKIE['csrf_token'] ?? '';
                if (!$this->verify_csrf_token($token)) {
                    http_response_code(403);
                    exit('<h1>Unauthorized</h1>');
                }
            }else{
                http_response_code(403);
                exit('<h1>NO ACTIONS AVAILABLE FOR THIS REQUEST METHOD</h1>');
            }
        }
        
        public function validacionQueryParams() {
            $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            if (strpos($url, '?') !== false) {
                $newUrl = strtok($url, '?');
                header("Location: $newUrl");
                exit();
            }
        }

        

    }
