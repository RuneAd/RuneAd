<?php
class ServerPing {
    
    private $address;
    private $port;
    private $ping = -1;

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
        $start   = microtime(true);
        $socket  = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
       
        socket_set_nonblock($socket);

        $attempts  = 0;
        $waitCount = 300; // higher the longer it waits for connection.

        while (!($connected = @socket_connect($socket, $this->address, $this->port)) && $attempts++ < $waitCount) {
            if (socket_last_error($socket) == SOCKET_EISCONN) { 
                $end = microtime(true);
                $this->ping = (($end - $start)*1000);
                break;
            }

            usleep(10000);
        }

        socket_set_block($socket);
        socket_close($socket);
        return;
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

    /**
     * @return int the servers ping in ms
     */
    public function getPing() {
        return floor($this->ping);
    }

}