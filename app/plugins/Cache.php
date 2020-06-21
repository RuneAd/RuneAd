<?php
class Cache {

    public $file;
    public $expires;
    public $data;
    public $assoc;

    public function __construct($file, $expires ,$assoc = true) {
        $this->file = $file;
        $this->expires = $expires;
        $this->assoc = $assoc;
    }

    public function get() {
        if (!$this->exists()) {
            return null;
        }

        if ($this->isExpired()) {
            return null;
        }

        $this->data = json_decode(file_get_contents($this->getFilePath()), $this->assoc);
        return $this->data;
    }

    public function isExpired() {
        if (!$this->exists()) {
            return true;
        }

        return (time() - filemtime($this->getFilePath())) > $this->expires;
    }

    public function save($data) {
        $cached = fopen($this->getFilePath(), 'w');
        fwrite($cached, json_encode($data));
        fclose($cached);
    }

    public function getFilePath() {
        return 'app/cache/'.$this->file.'.json';
    }

    public function getData() {
        return empty($this->data) ? [] : $this->data;
    }

    public function getTimeLeft() {
        $time = $this->expires - (time() - filemtime($this->getFilePath()));
        return Functions::formatSeconds($time);
    }

    public function exists() {
        return file_exists($this->getFilePath());
    }
}
