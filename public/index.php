<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once dirname(__DIR__) . '/vendor/autoload.php';
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
require_once dirname(__DIR__) . '/app/config/config.php';
require_once dirname(__DIR__) . '/app/config/database.php';
require_once dirname(__DIR__) . '/app/config/ServiceContainer.php'; 
Flight::set('flight.base_url', BASE_URL);
$requested_file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($requested_file !== __FILE__ && is_file($requested_file)) {
    return false;
}
Flight::route('/', function () {
    $controller = new DashboardController();
    return $controller->index();
});
Flight::route('/dashboard', function () {
    $controller = new DashboardController();
    return $controller->index();
});
Flight::route('POST /reset', function () {
    $controller = new ResetController();
    return $controller->reset();
});
Flight::route('/besoins', function () {
    $controller = new BesoinsController();
    return $controller->index();
});
Flight::route('/besoins/ajouter', function () {
    $controller = new BesoinsController();
    return $controller->ajouter();
});
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
Flight::route('POST /reset', function () {
    $controller = new ResetController();
    return $controller->reset();
});
Flight::route('/dons/attribution', function () {
    $controller = new DonsController();
    return $controller->attribution();
});
Flight::route('/api/creer-produit', function () {
    $controller = new ApiController();
    return $controller->creerProduit();
});
Flight::route('/api/produits-by-categorie', function () {
    $controller = new ApiController();
    return $controller->getProduitsByCategorie();
});
Flight::route('/attribution/attribuer', function () {
    $controller = new DonsController();
    return $controller->attribuer();
});
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
Flight::route('/recap', function () {
    $controller = new RecapController();
    return $controller->index();
});
Flight::route('/recap/actualiser', function () {
    $controller = new RecapController();
    return $controller->actualiser();
});
Flight::route('/stock', function () {
    $controller = new StockController();
    return $controller->index();
});
Flight::route('/ventes', function () {
    $controller = new VenteController();
    return $controller->index();
});
Flight::route('/ventes/vendre', function () {
    $controller = new VenteController();
    return $controller->vendre();
});
Flight::route('/ventes/config', function () {
    $controller = new VenteController();
    return $controller->config();
});
Flight::route('/ventes/update-config', function () {
    $controller = new VenteController();
    return $controller->updateConfig();
});
Flight::route('/ventes/check-product', function () {
    $controller = new VenteController();
    return $controller->checkProduct();
});
Flight::map('notFound', function() {
    http_response_code(404);
    echo '<h1>404 - Page non trouvée</h1>';
    echo '<p>La page que vous recherchez n\'existe pas.</p>';
    echo '<a href="' . BASE_URL . '/dashboard">Retour au dashboard</a>';
});
Flight::start();
