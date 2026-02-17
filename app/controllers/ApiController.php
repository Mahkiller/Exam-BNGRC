<?php
class ApiController extends Controller {
    public function creerProduit() {
        header('Content-Type: application/json');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
                return;
            }
            $categorie_id = $_POST['categorie_id'] ?? null;
            $nom = $_POST['nom'] ?? null;
            $unite = $_POST['unite'] ?? null;
            $prix = $_POST['prix'] ?? 0;
            if (!$categorie_id || !$nom || !$unite) {
                echo json_encode(['success' => false, 'message' => 'Données manquantes']);
                return;
            }
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT id FROM produit_BNGRC WHERE nom_produit = ? AND unite_mesure = ?");
            $stmt->execute([$nom, $unite]);
            $existant = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($existant) {
                echo json_encode([
                    'success' => true, 
                    'produit_id' => $existant['id'],
                    'message' => 'Produit déjà existant'
                ]);
                return;
            }
            $stmt = $db->prepare("
                INSERT INTO produit_BNGRC (categorie_id, nom_produit, unite_mesure, prix_unitaire_reference, stock_actuel, seuil_alerte)
                VALUES (?, ?, ?, ?, 0, 10)
            ");
            $result = $stmt->execute([$categorie_id, $nom, $unite, $prix]);
            if ($result) {
                $produit_id = $db->lastInsertId();
                echo json_encode([
                    'success' => true,
                    'produit_id' => $produit_id,
                    'message' => 'Produit créé avec succès'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la création']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
        }
    }
    public function getProduitsByCategorie() {
        header('Content-Type: application/json');
        try {
            $categorie_id = $_GET['categorie_id'] ?? null;
            if (!$categorie_id) {
                echo json_encode(['success' => false, 'message' => 'Catégorie manquante']);
                return;
            }
            $db = Database::getInstance();
            $stmt = $db->prepare("
                SELECT id, nom_produit, unite_mesure, prix_unitaire_reference 
                FROM produit_BNGRC 
                WHERE categorie_id = ?
                ORDER BY nom_produit
            ");
            $stmt->execute([$categorie_id]);
            $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'produits' => $produits]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
        }
    }
}
