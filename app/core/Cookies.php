<?php
namespace Fox;

/**
 * Class Cookies
 * Handles cookie management functionality.
 * @package Fox
 */
class Cookies
{
    private static ?Cookies $instance = null;
    private array $cookies;
    private string $path;

    /**
     * Returns the singleton instance of the Cookies class.
     * @return Cookies
     */
    public static function getInstance(): Cookies
    {
        if (!self::$instance) {
            self::$instance = new Cookies(web_root);
        }
        return self::$instance;
    }

    /**
     * Cookies constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->cookies = $_COOKIE;
    }

    /**
     * Retrieves a value from the cookies.
     * @param string $key
     * @return string|null
     */
    public function get(string $key): ?string
    {
        return $this->has($key) ? $this->cookies[$key] : null;
    }

    /**
     * Checks if a cookie exists and is not empty.
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->cookies[$key]) && !empty($this->cookies[$key]);
    }

    /**
     * Updates an existing cookie with a new value.
     * @param string $key
     * @param string $value
     * @param int|null $expires Time in seconds. Default is 1 day.
     * @return bool
     */
    public function update(string $key, string $value, ?int $expires = null): bool
    {
        if (!$this->has($key)) {
            return false;
        }

        $expires = $expires ?? 86400;
        setcookie($key, $value, time() + $expires, $this->path);
        return true;
    }

    /**
     * Sets a cookie with an expiration time.
     * @param string $key
     * @param string $value
     * @param int $expires Time in seconds. Default is 1 day.
     */
    public function set(string $key, string $value, int $expires = 86400): void
    {
        setcookie($key, $value, time() + $expires, $this->path);
    }

    /**
     * Deletes a cookie by setting its expiration time in the past.
     * @param string $key
     */
    public function delete(string $key): void
    {
        setcookie($key, '', time() - 3600, $this->path);
    }
}
