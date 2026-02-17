<?php
// app/services/ResetService.php

class ResetService {
    private $db;
    private $donModel;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->donModel = new DonModel();
    }
    
    public function resetAllExceptDons() {
        try {
            $this->db->beginTransaction();
            
            // Récupérer les IDs des dons à protéger
            $donsProtect = $this->donModel->getDonsProtect();
            
            if (empty($donsProtect)) {
                throw new Exception("Aucun don à protéger trouvé");
            }
            
            $donsList = implode(',', $donsProtect);
            
            // 1. Supprimer les mouvements de stock (sauf ceux des dons protégés)
            $this->db->exec("
                DELETE FROM mouvement_stock_BNGRC 
                WHERE source_type != 'don' 
                OR (source_type = 'don' AND source_id NOT IN ($donsList))
            ");
            
            // 2. Supprimer les attributions
            $this->db->exec("DELETE FROM attribution_BNGRC");
            
            // 3. Supprimer les besoins
            $this->db->exec("DELETE FROM besoin_BNGRC");
            
            // 4. Supprimer les achats
            $this->db->exec("DELETE FROM achat_BNGRC");
            
            // 5. Supprimer les ventes
            $this->db->exec("DELETE FROM vente_BNGRC");
            
            // 6. Remettre le stock à zéro pour tous les produits
            $this->db->exec("UPDATE produit_BNGRC SET stock_actuel = 0 WHERE id != 10");
            
            // 7. Remettre l'argent à zéro
            $this->db->exec("UPDATE produit_BNGRC SET stock_actuel = 0 WHERE nom_produit = 'Argent'");
            
            // 8. Réinitialiser les stocks avec les dons protégés
            $stmt = $this->db->query("
                SELECT d.produit_id, d.quantite_totale 
                FROM don_BNGRC d 
                WHERE d.id IN ($donsList)
            ");
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($row['produit_id']) {
                    $this->db->prepare("
                        UPDATE produit_BNGRC 
                        SET stock_actuel = stock_actuel + ? 
                        WHERE id = ?
                    ")->execute([$row['quantite_totale'], $row['produit_id']]);
                }
            }
            
            // 9. Remettre l'argent des dons
            $stmt = $this->db->query("
                SELECT SUM(quantite_totale) as total 
                FROM don_BNGRC 
                WHERE type_don = 'argent' AND id IN ($donsList)
            ");
            $totalArgent = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            $this->db->prepare("
                UPDATE produit_BNGRC 
                SET stock_actuel = ? 
                WHERE nom_produit = 'Argent'
            ")->execute([$totalArgent]);
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Réinitialisation réussie ! Seuls les dons de base ont été conservés.'
            ];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => 'Erreur lors de la réinitialisation : ' . $e->getMessage()
            ];
        }
    }
}