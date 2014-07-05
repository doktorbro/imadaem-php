<?php
if (file_exists($_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'])) {
    return false;
} else {
    require_once '../src/Penibelst/Imadaem/Resizer.php';
    require_once '../src/Penibelst/Imadaem/Logger.php';

    $logger = new Penibelst\Imadaem\Logger(E_USER_NOTICE);
    $resizer = new Penibelst\Imadaem\Resizer(
        array(
            'dstRoot' => 'api',
            'expires' => 7 * 24 * 60 * 60,
            'srcRoot' => 'images'),
        $logger);
    $resizer->run();
}
