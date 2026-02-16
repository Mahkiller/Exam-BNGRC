<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();


spl_autoload_register(function ($class) {
    $paths = [
        '../app/core/',
        '../app/controllers/',
        '../app/models/',
        '../app/services/',
        '../app/config/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});


require_once '../app/config/config.php';
require_once '../app/config/database.php';
require_once '../app/config/service.php';

// Router
$router = new Router();
$router->dispatch($_GET['url'] ?? '');