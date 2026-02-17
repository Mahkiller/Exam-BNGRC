<?php
// app/controllers/ResetController.php

class ResetController extends Controller {
    
    public function reset() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/dashboard');
            return;
        }
        
        try {
            $db = Database::getInstance();
            $db->beginTransaction();
            
            // Récupérer les IDs des dons à protéger
            $stmt = $db->query("
                SELECT id FROM don_BNGRC 
                WHERE donateur IN (
                    'Croix-Rouge', 'UNICEF', 'PNUD', 'ONG Miarakapa',
                    'Banque Mondiale', 'UE', 'JICA', 'USAID', 'Coopération Suisse'
                )
            ");
            $donsProtect = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (empty($donsProtect)) {
                throw new Exception("Aucun don à protéger trouvé");
            }
            
            $donsList = implode(',', $donsProtect);
            
            // 1. Supprimer les mouvements de stock (sauf ceux des dons protégés)
            $db->exec("
                DELETE FROM mouvement_stock_BNGRC 
                WHERE source_type != 'don' 
                OR (source_type = 'don' AND source_id NOT IN ($donsList))
            ");
            
            // 2. Supprimer les attributions
            $db->exec("DELETE FROM attribution_BNGRC");
            
            // 3. Supprimer les besoins
            $db->exec("DELETE FROM besoin_BNGRC");
            
            // 4. Supprimer les achats
            $db->exec("DELETE FROM achat_BNGRC");
            
            // 5. Supprimer les ventes
            $db->exec("DELETE FROM vente_BNGRC");
            
            // 6. Remettre le stock à zéro
            $db->exec("UPDATE produit_BNGRC SET stock_actuel = 0");
            
            // 7. Réinitialiser les stocks avec les dons protégés
            $stmt = $db->query("
                SELECT produit_id, quantite_totale 
                FROM don_BNGRC 
                WHERE id IN ($donsList) AND produit_id IS NOT NULL
            ");
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $db->prepare("
                    UPDATE produit_BNGRC 
                    SET stock_actuel = stock_actuel + ? 
                    WHERE id = ?
                ")->execute([$row['quantite_totale'], $row['produit_id']]);
            }
            
            // 8. Remettre l'argent des dons
            $stmt = $db->query("
                SELECT SUM(quantite_totale) as total 
                FROM don_BNGRC 
                WHERE type_don = 'argent' AND id IN ($donsList)
            ");
            $totalArgent = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            $db->prepare("
                UPDATE produit_BNGRC 
                SET stock_actuel = ? 
                WHERE nom_produit = 'Argent'
            ")->execute([$totalArgent]);
            
            $db->commit();
            
            $_SESSION['message'] = '✅ Réinitialisation réussie ! Seuls les dons de base ont été conservés.';
            
        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = '❌ Erreur lors de la réinitialisation : ' . $e->getMessage();
        }
        
        $this->redirect('/dashboard');
    }
}