<?php
class ServerPing {
    
    private $address;
    private $port;

    /**
     * @param $server_ip
     * @param $port
     * class constructor
     */
    public function __construct($address = null, $port = null) {
        $this->address = $address;
        $this->port = $port;
    }

    public function connect() {
        
        $socket  = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_nonblock($socket);

        $attempts   = 0;
        $waitCount  = 1000; // time to wait in milliseconds
        $connected  = false;

        while (!(@socket_connect($socket, $this->address, $this->port)) && $attempts++ < $waitCount) {
            if (socket_last_error($socket) == SOCKET_EISCONN) {
                $connected = true;
                break;
            }
            usleep(1000); // sleep 1ms - corresponds to the $waitCount 
        }

        socket_set_block($socket);
        socket_close($socket);
        return $attempts < $waitCount ? $attempts : -1;

    }

    /**
     * set the ip address to ping.
     * @param $address
     */
    public function setAddress($address) {
        $this->address = $address;
        return $this;
    }

    /**
     * set the port to ping
     * @param $port
     */
    public function setPort($port) {
        $this->port = $port;
        return $this;
    }

    /**
     * @return String the ip address
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @return String the port id
     */
    public function getPort() {
        return $this->port;
    }

}