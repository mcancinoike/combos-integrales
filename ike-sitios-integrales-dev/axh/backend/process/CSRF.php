<?php
    class CSRF{

        public function verify_csrf_token($token) {
            return isset($_COOKIE['csrf_token']) && hash_equals($_COOKIE['csrf_token'], $token);
        }

        public function generarTokenCSRF(){

            $token = bin2hex(random_bytes(32));
            setcookie('csrf_token', $token, time() + 3600, '/', '', true, true);
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
            $url = htmlspecialchars($this->getFullSelfUrl());
            if (strpos($url, '?') !== false) {
                $newUrl = strtok($url, '?');
                header("Location: $newUrl");
                exit();
            }
        }


        public function getFullSelfUrl($forwarded_host = false) {
            $ssl   = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
            $proto = strtolower($_SERVER['SERVER_PROTOCOL']);
            $proto = substr($proto, 0, strpos($proto, '/')) . ($ssl ? 's' : '' );
            if ($forwarded_host && isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
                $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
            } else {
                if (isset($_SERVER['HTTP_HOST'])) {
                    $host = $_SERVER['HTTP_HOST'];
                } else {
                    $port = $_SERVER['SERVER_PORT'];
                    $port = ((!$ssl && $port=='80') || ($ssl && $port=='443' )) ? '' : ':' . $port;
                    $host = $_SERVER['SERVER_NAME'] . $port;
                }
            }
            $request = $_SERVER['REQUEST_URI'];
            return $proto . '://' . $host . $request;
        }

    }
