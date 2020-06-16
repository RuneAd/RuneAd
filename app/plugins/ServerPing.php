<?php
class ServerPing {
    
    private $address;
    private $port;
    private $ping;

    private $timeout = 2;

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

        $start   = microtime(true);
        $socket  = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
       
        socket_set_nonblock($socket);

        $con_per_sec = 100;

        for ($attempts = 0; $attempts < ($this->timeout * $con_per_sec); $attempts++) { 
            @socket_connect($socket, $this->address, $this->port);

            if(socket_last_error($socket) == SOCKET_EISCONN) { 
                break;
            }
            
            usleep(1000000 / $con_per_sec);
        }
        
        socket_set_block($socket);
        
        $this->ping = floor((microtime(true) - $start) * 1000);
        socket_close($socket);
        return;
    }

    /**
     * Sets a time in seconds to wait for a connection. accepts decimal values (ie. 1.5)
     * @param $seconds 
     */
    public function setTimeout($secs) {
        $this->timeout = $ecs;
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