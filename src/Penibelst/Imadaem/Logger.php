<?php
namespace Penibelst\Imadaem;

class Logger {
    protected $file;
    protected $level;
    protected $levelText;
    protected $startTime;

    public function __construct($level = E_USER_ERROR, $file = '')
    {
        $this->startTime = microtime(true);
        $this->file = $file;
        $this->level = $level;
        $this->levelText = array(
            E_USER_ERROR => 'E',
            E_USER_WARNING => 'W',
            E_USER_NOTICE => 'N');
    }

    protected function message($level, $msg) {
        if ($level <= $this->level) {
            $msg = join(';', array(
                sprintf('%.6f', microtime(true) - $this->startTime),
                $this->levelText[$level],
                $msg));
            if (empty($this->file)) {
                return error_log($msg);
            } else {
                return error_log($msg . PHP_EOL, 3, $this->file);
            }
        }
        return false;
    }

    public function error($msg) {
        return $this->message(E_USER_ERROR, $msg);
    }

    public function warning($msg) {
        return $this->message(E_USER_WARNING, $msg);
    }

    public function notice($msg) {
        return $this->message(E_USER_NOTICE, $msg);
    }
}
