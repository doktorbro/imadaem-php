<?php
namespace Imadaem;


class Log {
    protected $file;
    protected $level;
    protected $levelText;
    protected $startTime;

    public function __construct($level = E_USER_ERROR, $file = '')
    {
        $this->file = $file;
        $this->level = $level;
        $this->levelText = array(
            E_USER_ERROR => 'E',
            E_USER_WARNING => 'W',
            E_USER_NOTICE => 'N');
        $this->startTime = microtime(true);
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

class Imadaem {

    const FILE_MODE = 0755;
    const INFO = 'info.json';
    const LOG_FILE = 'log.csv';
    const LOG_LEVEL = E_USER_NOTICE;
    protected $format;
    protected $identifier;
    protected $isInfo;
    protected $log;
    protected $quality;
    protected $region;
    protected $rotation;
    protected $size;
    protected $srcFile;
    protected $srcRoot;

    public function __construct($options)
    {
        $this->log = new Log($this::LOG_LEVEL, $this::LOG_FILE);
        $this->log->notice('__: ' . Date('c'));
        $options = array_merge(
            array(
                'dstRoot' => '',
                'srcRoot' => ''),
            $options);

        $this->dstRoot = $options['dstRoot'];
        $this->log->notice('dstRoot: "' . $this->dstRoot . '"');

        $this->srcRoot = $options['srcRoot'];
        $this->log->notice('srcRoot: "' . $this->srcRoot . '"');
    }

    public function dump()
    {
        echo '<pre>';
        print_r($this);
        echo '</pre>';
    }

    private function parseRequest()
    {
        $this->log->notice('Request: "' . $_SERVER['REQUEST_URI'] . '"');
        $request = explode('/', ltrim($_SERVER['REQUEST_URI'], '/'));
        if (in_array('', $request)) {
            return;
        }

        if ((! empty($this->dstRoot)) && ($request[0] != $this->dstRoot)) {
            return;
        }

        $offset = (empty($this->dstRoot)) ? 0 : 1;

        $this->identifier = $request[$offset];
        $this->log->notice('Identifier: "' . $this->identifier . '"');

        //  Image Information Request
        if (count($request) < $offset + 2) {
            return;
        }
        if ($request[$offset + 1] == 'info.json') {
            $this->isInfo = true;
            return true;
        }

        //  Image Request
        if (count($request) != $offset + 5) {
            return;
        }

        list($this->region, $this->size, $this->rotation, $file) =
            array_splice($request, $offset + 1);

        list($this->quality, $this->format) = explode('.', $file);

        return true;
    }

    private function resizeImage()
    {
        $srcFile = join(DIRECTORY_SEPARATOR, array(
            $_SERVER['DOCUMENT_ROOT'],
            $this->srcRoot,
            urldecode($this->identifier)));
        $this->log->notice('srcFile: "' . $srcFile . '"');
        if (! file_exists($srcFile)) return;
        $this->log->notice('Source file exists.');

        if ($this->size == 'full') {
            $dir = $this->dstDir();
            if ((! is_dir($dir)) && (! mkdir($dir, $this::FILE_MODE, true))) {
                $this->log->error('Cannot create directory');
                return;
            }

            if (! copy($srcFile, $this->dstFile())) {
                $this->log->error('Cannot copy file');
                return;
            }
        }
    }

    public function run()
    {
        return ($this->parseRequest() && $this->resizeImage());
    }

    protected function dstFile()
    {
        if ($this->isInfo) {
            return join(DIRECTORY_SEPARATOR, array(
                $_SERVER['DOCUMENT_ROOT'],
                $this->dstRoot,
                urldecode($this->identifier),
                $this->INFO));
        } else {
            return join(DIRECTORY_SEPARATOR, array(
                $_SERVER['DOCUMENT_ROOT'],
                $this->dstRoot,
                urldecode($this->identifier),
                $this->region,
                $this->size,
                $this->rotation,
                $this->quality . '.' . $this->format));
        }
    }

    protected function dstDir()
    {
        return dirname($this->dstFile());
    }
}
