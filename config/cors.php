<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Max-Age: 3600");
    header("Content-Length: 0");
    header("HTTP/1.1 204 No Content");
    exit;
}
?>