<?php
/**
 * prevents cron jobs from overlapping
 */
class CronLock {

    private $delay = (60 * 60 * 1);
    private $file_path;

    public function __construct($name, $delay = (60 * 60 * 1)) {
        $this->file_path =  DOC_ROOT."/cron_locks/".strtolower($name).".lock";
        $this->delay = $delay;
    }

    public function isLocked() {
        if (file_exists($this->file_path)) {
            if (time() - filemtime($this->file_path) < $this->delay) {
                return true;
            }
        }
        return false;
    }

    public function setDelay($delay) {
        $this->delay = $delay;
    }

    public function setFile($file_path) {
        $this->file_path =  DOC_ROOT."/cron_locks/".strtolower($file_path).".lock";
    }

    public function writeLock() {
        file_put_contents($this->file_path, json_encode(['last_write' => time()]));
    }

}
