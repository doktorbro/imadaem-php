<?php

function loader($class)
{
    $file = 'src' . DIRECTORY_SEPARATOR .
        str_replace('\\', DIRECTORY_SEPARATOR, $class) .
        '.php';
    if (file_exists($file)) {
        require $file;
    }
}

spl_autoload_register('loader');
