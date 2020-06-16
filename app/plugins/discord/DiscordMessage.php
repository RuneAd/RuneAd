<?php
class DiscordMessage extends Discord {

    
    private $channel_id;
    private $title;
    private $message;
    private $is_rich = true;
    private $tts = false;

    public function __construct($dataArr) {
        foreach ($dataArr as $key => $value) {
            $this->$key = $value;
        }
    }

    public function send() {
        $this->setEndpoint("/channels/{$this->channel_id}/messages"); 

        $data = ['tts' => $this->tts ];

        if ($this->is_rich) {
            $data['embed'] = [
                'title' => $this->title,
                'description' => $this->message
            ];
        } else {
            $data['content'] = $this->message;
        }
        
        try {
            return parent::sendMessage($data);
        } catch (Exception $e) {
            return null;
        }
    }



}