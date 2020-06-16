<?php
namespace Fox;

/**
 * Class Cookies
 * @package Fox
 */
class Cookies {

    private static $instance;
    private $cookies;
    private $path;

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Cookies(web_root);
        }
        return self::$instance;
    }

    /**
     * Cookies constructor.
     * @param $path
     */
    public function __construct($path) {
        $this->path    = $path;
        $this->cookies = $_COOKIE;
    }

    /**
     * Returns a value from $_COOKIE
     * @param $key
     * @return mixed
     */
    public function get($key) {
        if (!$this->has($key)) {
            return null;
        }
        return $this->cookies[$key];
    }

    /**
     * Returns if $_COOKIE contains a key.
     * @param $key
     * @return bool
     */
    public function has($key) {
        return isset($this->cookies[$key]) && !empty($this->cookies[$key]);
    }

    /**
     * Updates an existing cookie with a value. Returns false if cookie doesn't exist.
     * @param $key
     * @param $value
     * @param null $expires
     * @return bool
     */
    public function update($key, $value, $expires = null) {
        if ($expires == null)
            $expires = 86400;

        if (!$this->has($key)) {
            return false;
        }

        setcookie($key, $value, time() + $expires, $this->path);
        return true;
    }

    /**
     * Sets a cookie with an expire time. 86400 = 1 day.
     * @param $key
     * @param $value
     * @param $expires
     */
    public function set($key, $value, $expires = 86400) {
        setcookie($key, $value, time() + $expires, $this->path);
    }

    /**
     * Sets a cookies expire date to before current time to expire it.
     * Jank but deal with it.
     * @param $key
     */
    public function delete($key) {
        setcookie($key, null, time() - 1000,  $this->path);
    }

}