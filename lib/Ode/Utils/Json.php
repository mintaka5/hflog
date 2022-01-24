<?php
namespace Ode\Utils;

class Json {
    public static function encode($data, $mimetype = 'application/json') {
        header('Content-Type: ' . $mimetype);
        echo json_encode($data);
        exit();
    }
}