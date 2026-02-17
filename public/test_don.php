<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once dirname(__DIR__) . '/app/config/database.php';
require_once dirname(__DIR__) . '/app/models/Model.php';
require_once dirname(__DIR__) . '/app/models/DonModel.php';
try {
    $db = Database::getInstance();
    echo "✅ Connexion DB réussie<br>";
    $donModel = new DonModel();
    $donateur = "Test Direct";
    $type_don = "argent";
    $description = "Argent test";
    $quantite = 10000;
    $unite = "Ariary";
    $produit_id = null;
    echo "Tentative d'insertion...<br>";
    $result = $donModel->create($donateur, $type_don, $description, $quantite, $unite, $produit_id);
    if ($result) {
        echo "✅ Don inséré avec succès! ID: " . $result . "<br>";
    } else {
        echo "❌ Échec de l'insertion<br>";
    }
    $stmt = $db->query("SELECT * FROM don_BNGRC ORDER BY id DESC LIMIT 5");
    $dons = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<h3>Derniers dons:</h3>";
    echo "<pre>";
    print_r($dons);
    echo "</pre>";
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "<br>";
    echo "Fichier: " . $e->getFile() . "<br>";
    echo "Ligne: " . $e->getLine() . "<br>";
}
