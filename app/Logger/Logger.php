<?php

namespace app\Logger;

class Logger
{
    private $ip;
    private $logFile;
    private $fileAdress;

    public function setup()
    {
        $date = date('Y-m-d');
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->logFile = $date . ".log";

        if (!is_dir("../Logs")) {
            mkdir("../Logs");
        }
    }

    public function log(string $message, string $level)
    {
        $this->setup();

        $this->fileAdress = fopen('../Logs/' . $this->logFile, 'a');
        fwrite($this->fileAdress, "Ip:[" . $this->ip . "] Date:[" . date("Y-m-d H:i:s") . "] Level:[" . $level . "] Log:[" . REQUEST_ID . " " . $message . "]" . PHP_EOL);
        fclose($this->fileAdress);
    }
}