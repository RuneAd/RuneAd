<?php
namespace Fox;

class CSRF {
    
    private static $instance;
    
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new CSRF();
        }
        return self::$instance;
    }

    public function getToken() {
        $token = \Functions::generateString(30);
        $this->save($token);
        return $token;
    }

    public function save($token) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['token'] = $token;
    }

    public function validate() {
        if (!isset($_POST['_token']) || empty($_POST['_token'])) {
            return false;
        }

        $token = filter_var($_POST['_token'], FILTER_SANITIZE_STRING);
        $saved = $_SESSION['token'];

        if ($token !== $saved) {
            return false;
        }

        return [
            'success' => true,
            'token' => $this->getToken()
        ];
    }
    
} 
?>
