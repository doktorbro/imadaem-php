<?php
namespace Penibelst\Imadaem;

class Resizer {

    const FILE_MODE = 0755;
    const INFO = 'info.json';
    protected $format;
    protected $identifier;
    protected $isInfo;
    protected $logger;
    protected $quality;
    protected $region;
    protected $rotation;
    protected $size;
    protected $srcFile;
    protected $srcRoot;

    public function __construct($options, Logger $logger = null)
    {
        $options = array_merge(
            array(
                'dstRoot' => '',
                'srcRoot' => ''),
            $options);

        $this->logger = ($logger) ?: new Logger();
        $this->logger->notice('__: ' . Date('c'));

        $this->dstRoot = $options['dstRoot'];
        $this->logger->notice('dstRoot: "' . $this->dstRoot . '"');

        $this->srcRoot = $options['srcRoot'];
        $this->logger->notice('srcRoot: "' . $this->srcRoot . '"');
    }

    public function dump()
    {
        echo '<pre>';
        print_r($this);
        echo '</pre>';
    }

    private function parseRequest()
    {
        $this->logger->notice('Request: "' . $_SERVER['REQUEST_URI'] . '"');
        $request = explode(DIRECTORY_SEPARATOR,
            ltrim($_SERVER['REQUEST_URI'], DIRECTORY_SEPARATOR));
        if (in_array('', $request)) {
            return;
        }

        if ((! empty($this->dstRoot)) && ($request[0] != $this->dstRoot)) {
            return;
        }

        $offset = (empty($this->dstRoot)) ? 0 : 1;

        $this->identifier = $request[$offset];
        $this->logger->notice('Identifier: "' . $this->identifier . '"');

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
        $this->logger->notice('srcFile: "' . $srcFile . '"');
        if (! file_exists($srcFile)) return;
        $this->logger->notice('Source file exists.');

        if ($this->size == 'full') {
            $dir = $this->dstDir();
            if ((! is_dir($dir)) && (! mkdir($dir, $this::FILE_MODE, true))) {
                $this->logger->error('Cannot create directory');
                return;
            }

            if (! copy($srcFile, $this->dstFile())) {
                $this->logger->error('Cannot copy file');
                return;
            }
        }
        return true;
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
