<?php
namespace Fox;

/**
 * Class Cookies
 * @package Fox
 */
class Session {

    private static $instance;

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Session();
        }
        return self::$instance;
    }

    public function has($key) {
        return isset($_SESSION[$key]) && !empty($_SESSION[$key]);
    }

    public function get($key) {
        if (!$this->has($key)) {
            return null;
        }
        return $_SESSION[$key];
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function delete($key) {
        if (!$this->has($key)) {
            return false;
        }
        unset($_SESSION[$key]);
        return true;
    }

    public function update($key, $value) {
        if (!$this->has($key)) {
            return false;
        }
        $_SESSION[$key] = $value;
        return true;
    }


}