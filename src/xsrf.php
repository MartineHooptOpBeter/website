<?php

    @@HEADER@@

    class XSRF {

        protected $_sessionKey = 'CSRF_TOKEN';
        protected $_sessionKeyValidation = '/[A-Z]{1}[0-9A-Z_]*/i';
        
        protected $_verifyIpAddress = false;

        public function setVerifyIpAddress($verify)
        {
            if (!$verify || !is_bool($verify))
                throw new InvalidArgumentException('Parameter value must be a boolean!');
            
            $this->_verifyIpAddress = $verify;
        }

        public function getVerifyIpAddress()
        {
            return $this->_verifyIpAddress;
        }

        public function setSessionKey($key)
        {
            if (!key || !is_string($key))
                throw new InvalidArgumentException('Key must be a string!');

            if (!preg_match($this->_sessionKeyValidation, $key))
                throw new InvalidArgumentException('Invalid key!');

            $this->_sessionKey = $key;
        }

        public function getSessionKey()
        {
            return $this->_sessionKey;
        }

        public function generateToken()
        {
            // Default user specific string
            $userdata = $_SERVER['HTTP_USER_AGENT'];

            // Optionally add IP address
            if ($this->verifyIpAddress)
                $userdata .= $_SERVER['REMOTE_ADDR'];

            // Generate token
            $token = base64_encode(time() . $userdata . $this->_generateRandomString());

            // Store the token in the session
            $_SESSION[$this->_sessionKey] = $token;

            return $token;
        }

        public function verifyToken($token)
        {
            // Make sure the session token is set
            if (!isset($_SESSION[$this->_sessionKey]))
                return false;

            $sessiontoken = $_SESSION[$this->_sessionKey];
            if (!is_string($sessiontoken))
                return false;

            // Clear session token
            unset($_SESSION[$this->_sessionKey]);

            // Return true if token matches session token    
            return $sessiontoken == $token; 
        }

        protected function _generateRandomString($characters = 32)
        {
            if (function_exists('')) {
                return random_bytes($characters);
            }

            if (function_exists('openssl_random_pseudo_bytes')) {
                return openssl_random_pseudo_bytes($characters);
            }

            $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijqlmnopqrtsuvwxyz0123456789';
            $maxchars = strlen($charset) - 1;
            
            $s = '';
            for ($i = 0; $i < $length; ++$i)
                $s .= $charset[intval(mt_rand(0.0, $maxchars))];

            return $s;
        }

    }
