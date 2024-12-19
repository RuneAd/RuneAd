<?php
namespace Fox;

/**
 * Class Request
 * @package RuneAd\Req
 */
class Request {

    private static $instance;

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Request();
        }
        return self::$instance;
    }

    /**
     * Returns true if $_GET is not empty.
     * @return bool
     */
    public function isQuery() {
        return !empty($_GET);
    }

    /**
     * Returns true if $_POST is not empty.
     * @return bool
     */
    public function isPost() {
        return $_SERVER['REQUEST_METHOD'] === "POST";
    }

    /**
     * Returns true if $_REQUEST is not empty. Will also return true if $_POST or $_GET is not empty
     * as this gets all variables within the super-globals
     * @return bool
     */
    public function isRequest() {
        return !empty($_REQUEST);
    }

    /**
     * Returns if a key is set in $_GET
     * @param string $key
     * @return bool
     */
    public function hasQuery($key) {
        return isset($_GET[$key]);
    }

    /**
     * Returns a value from $_GET
     * @param string|null $key
     * @return mixed
     */
    public function getQuery($key = null) {
        return $key === null ? $_GET : ($_GET[$key] ?? null);
    }

    /**
     * Returns if a key is set in $_POST
     * @param string $key
     * @return bool
     */
    public function hasPost($key) {
        return isset($_POST[$key]) && !empty($_POST[$key]);
    }

    /**
     * Returns a value from $_POST
     * @param string|null $key
     * @param string|null $filter
     * @return mixed
     */
    public function getPost($key = null, $filter = null) {
        $value = $key === null ? $_POST : ($_POST[$key] ?? null);

        if ($filter !== null && $value !== null) {
            switch ($filter) {
                case 'string':
                    $value = htmlspecialchars(filter_var($value, FILTER_SANITIZE_STRING));
                    break;
                case 'int':
                    $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                    break;
                case 'email':
                    $value = filter_var($value, FILTER_SANITIZE_EMAIL);
                    break;
                case 'url':
                    $value = filter_var($value, FILTER_SANITIZE_URL);
                    break;
            }
        }

        return $value;
    }

    /**
     * Returns if a key is set in $_REQUEST
     * @param string $key
     * @return bool
     */
    public function hasRequest($key) {
        return isset($_REQUEST[$key]) && !empty($_REQUEST[$key]);
    }

    /**
     * Returns a value from $_REQUEST
     * @param string|null $key
     * @return mixed
     */
    public function getRequest($key = null) {
        return $key === null ? $_REQUEST : ($_REQUEST[$key] ?? null);
    }

    /**
     * @return string IP Address
     */
    public function getAddress() {
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }

        $client  = $_SERVER['HTTP_CLIENT_IP'] ?? null;
        $forward = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null;
        $remote  = $_SERVER['REMOTE_ADDR'] ?? null;

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            return $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            return $forward;
        } else {
            return $remote;
        }
    }

    /**
     * Redirects to given URL within application
     * @param string $url
     * @param bool $internal
     */
    public function redirect($url, $internal = true) {
        header("Location: " . ($internal ? web_root . $url : $url));
        exit;
    }

    /**
     * Redirects to a URL after a delay in seconds.
     * @param string $url
     * @param int $time
     * @param bool $internal
     */
    public function delayedRedirect($url, $time, $internal = false) {
        header("refresh:{$time}; url=" . ($internal ? web_root . $url : $url));
        exit;
    }

    /**
     * Verifies the access token
     * @return bool
     */
    public function verifyToken() {
        return $this->getBearerToken() === api_key;
    }

    /**
     * Retrieves the bearer token from the authorization header
     * @return string|null
     */
    public function getBearerToken() {
        $headers = $this->getAuthorizationHeader();
        if (!empty($headers) && preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Gets the authorization header
     * @return string|null
     */
    public function getAuthorizationHeader() {
        if (isset($_SERVER['Authorization'])) {
            return trim($_SERVER['Authorization']);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            return trim($_SERVER['HTTP_AUTHORIZATION']);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            return $requestHeaders['Authorization'] ?? null;
        }
        return null;
    }
}
