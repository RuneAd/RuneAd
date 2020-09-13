<?php
class AntiCSRF {

    private static $token_name = "fox_csrf_token";
    private $csrf_key;

    public function __constructor() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Generates and returns a psuedo-randomly generated token
     */
    public function getToken() {
        $token = Functions::generateString(30);
        $_SESSION[self::$token_name] = $token;
        return $token;
    }

    /** 
     * validates token on POST request
     */
    public function isValidPost() {
        if (!isset($_SESSION[self::$token_name])) {
            return false;
        }

        $token = $_SESSION[self::$token_name];

        if (isset($_POST['csrf_token']) && $_POST['csrf_token'] == $token) {
            unset($_SESSION[self::$token_name]);
            return true;
        }

        unset($_SESSION[self::$token_name]);
        return false;
    }

    /**
     * Validates token on GET query
     */
    public function isValidQuery() {
        if (!isset($_SESSION[self::$token_name])) {
            return false;
        }

        $token = $_SESSION[self::$token_name];

        if (isset($_POST['csrf_token']) && $_POST['csrf_token'] == $token) {
            unset($_SESSION[self::$token_name]);
            return true;
        }

        unset($_SESSION[self::$token_name]);
        return false;
    }
}