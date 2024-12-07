<?php
namespace Fox;

/**
 * Class Session
 * Handles session management functionality.
 * @package Fox
 */
class Session
{
    private static ?Session $instance = null;

    /**
     * Returns the singleton instance of the Session class.
     * @return Session
     */
    public static function getInstance(): Session
    {
        if (!self::$instance) {
            self::$instance = new Session();
        }
        return self::$instance;
    }

    /**
     * Checks if a session key exists and is not empty.
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]) && !empty($_SESSION[$key]);
    }

    /**
     * Retrieves a value from the session.
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key): mixed
    {
        return $this->has($key) ? $_SESSION[$key] : null;
    }

    /**
     * Sets a value in the session.
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Deletes a key from the session.
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        if ($this->has($key)) {
            unset($_SESSION[$key]);
            return true;
        }
        return false;
    }

    /**
     * Updates an existing session key with a new value.
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function update(string $key, mixed $value): bool
    {
        if ($this->has($key)) {
            $_SESSION[$key] = $value;
            return true;
        }
        return false;
    }
}
