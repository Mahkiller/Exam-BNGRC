<?php
$requested_file = __DIR__ . '/public' . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (is_file($requested_file) || is_dir($requested_file)) {
    return false; 
}
require_once __DIR__ . '/public/index.php';
