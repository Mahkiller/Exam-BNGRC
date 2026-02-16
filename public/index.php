<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Require composer autoload
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Custom autoloader for app classes
spl_autoload_register(function ($class) {
    $paths = [
        dirname(__DIR__) . '/app/core/',
        dirname(__DIR__) . '/app/controllers/',
        dirname(__DIR__) . '/app/models/',
        dirname(__DIR__) . '/app/services/',
        dirname(__DIR__) . '/app/config/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Load configuration files
require_once dirname(__DIR__) . '/app/config/config.php';
require_once dirname(__DIR__) . '/app/config/database.php';
require_once dirname(__DIR__) . '/app/config/service.php';

// Initialize Flight
Flight::set('flight.base_url', BASE_URL);

// Handle static files
$requested_file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($requested_file !== __FILE__ && is_file($requested_file)) {
    return false;
}

// Routes Dashboard
Flight::route('/', function () {
    $controller = new DashboardController();
    return $controller->index();
});

Flight::route('/dashboard', function () {
    $controller = new DashboardController();
    return $controller->index();
});

// Routes Besoins
Flight::route('/besoins', function () {
    $controller = new BesoinsController();
    return $controller->index();
});

Flight::route('/besoins/ajouter', function () {
    $controller = new BesoinsController();
    return $controller->ajouter();
});

// Routes Dons
Flight::route('/dons', function () {
    $controller = new DonsController();
    return $controller->index();
});

Flight::route('/dons/ajouter', function () {
    $controller = new DonsController();
    return $controller->ajouter();
});

Flight::route('/attribution', function () {
    $controller = new DonsController();
    return $controller->attribution();
});

Flight::route('/attribution/attribuer', function () {
    $controller = new DonsController();
    return $controller->attribuer();
});

// Routes Achats
Flight::route('/achats', function () {
    $controller = new AchatController();
    return $controller->index();
});

Flight::route('/achats/creer', function () {
    $controller = new AchatController();
    return $controller->creer();
});

Flight::route('/achats/supprimer', function () {
    $controller = new AchatController();
    return $controller->supprimer();
});

// Routes Récapitulatif Financier
Flight::route('/recap', function () {
    $controller = new RecapController();
    return $controller->index();
});

Flight::route('/recap/actualiser', function () {
    $controller = new RecapController();
    return $controller->actualiser();
});

// 404 Handler
Flight::map('notFound', function() {
    http_response_code(404);
    echo 'Page non trouvée (404)';
});

// Start Flight
Flight::start();