<?php
use GuzzleHttp\Client;

class Discord {

    protected $access_token;
    protected $is_bot;
    protected $endpoint;
    
    public function __construct($token = null) {
        $this->access_token = $token;
    }

    public function setToken($token) {
        $this->access_token = $token;
    }

    public function setEndpoint($endpoint) {
        $this->endpoint = $endpoint;
    }

    public function setIsBot($isBot) {
        $this->is_bot = $isBot;
    }

    public function setChannel($channelId) {
        $this->channel_id = $channelId;
    }

    public function set($dataArr) {
        foreach ($dataArr as $key => $value) {
            $this->$key = $value;
        }
    }

    public function get($type = "GET", $data = null) {
        $client = new GuzzleHttp\Client();

        if ($this->is_bot) {
            return json_decode($client->request($type, discord['api_url'].$this->endpoint, [
                'headers' => [
                    'Authorization' => 'Bot '.discord['bot_key'],
                    'Content-Type'  => 'application/x-www-form-urlencoded'
                ]
            ])->getBody(), true);
        }

        return json_decode($client->request($type, discord['api_url'].$this->endpoint, [
            'headers' => [
                'Authorization' => 'Bearer '.$this->access_token,
                'Content-Type'  => 'application/x-www-form-urlencoded'
            ]
        ])->getBody(), true);
    }

    public function sendMessage($data) {
        $response = (new Client())->request('POST', discord['api_url'].$this->endpoint, [
            'headers' => [
                'Authorization' => 'Bot '.discord['bot_key'],
                'Content-Type'  => 'application/json'
            ],
            'body' => json_encode($data)
        ]);

        return json_decode($response->getBody(), true);
    }

    public function revokeAccess() {
        $client = new GuzzleHttp\Client();

        $response = $client->request('POST', discord['api_url'].'/oauth2/token/revoke', [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'form_params' => [
                'client_id'     => discord['client_id'],
                'client_secret' => discord['client_secret'],
                'token'         => $this->access_token,
            ]
        ]);

        $json = json_decode($response->getBody(), true);
        return $json;
    }

}