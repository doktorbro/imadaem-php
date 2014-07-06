<?php

function loader($class)
{
    $file = implode(DIRECTORY_SEPARATOR, array_merge(
        array(__DIR__, '..', 'src'),
        explode('\\', $class))) . '.php';
    if (file_exists($file)) {
        require $file;
    }
}

spl_autoload_register('loader');
