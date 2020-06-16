<?php
class ServerPing {
    
    private $address;
    private $port;
    private $ping;

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
        $message = "runenexus";

        try {
            $start  = microtime(true);
            $socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
            socket_connect($socket, $this->address, $this->port);
            $status = socket_sendto($socket, $message, strlen($message), 0, $this->getAddress(), $this->getPort());
            $end    = microtime(true);

            socket_close($socket);
            return number_format(($end - $start) * 1000, 0);
        } catch (Exception $e) {
            echo $e->getMessage();
            return -1;
        }
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
        return $this->ping;
    }


}