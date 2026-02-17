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
            
            // Récupérer les IDs des dons protégés
            $stmt = $db->query("SELECT id FROM don_BNGRC WHERE protege = TRUE");
            $donsProtect = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Si on a des dons protégés, on les garde, sinon on garde tout
            if (!empty($donsProtect)) {
                $donsList = implode(',', $donsProtect);
                
                // 1. Supprimer les mouvements de stock liés aux dons non protégés
                $db->exec("
                    DELETE FROM mouvement_stock_BNGRC 
                    WHERE source_type = 'don' AND source_id NOT IN ($donsList)
                ");
                
                // 2. Supprimer les attributions liées aux dons non protégés
                $db->exec("
                    DELETE FROM attribution_BNGRC 
                    WHERE don_id NOT IN ($donsList)
                ");
                
                // 3. Supprimer les achats liés aux dons non protégés
                $db->exec("
                    DELETE FROM achat_BNGRC 
                    WHERE don_id NOT IN ($donsList)
                ");
                
                // 4. Supprimer les ventes liées aux dons non protégés
                $db->exec("
                    DELETE FROM vente_BNGRC 
                    WHERE don_id IS NOT NULL AND don_id NOT IN ($donsList)
                ");
                
                // 5. Supprimer les dons non protégés
                $db->exec("DELETE FROM don_BNGRC WHERE protege != TRUE");
                
            } else {
                // Si pas de dons protégés, on supprime tout
                $db->exec("DELETE FROM mouvement_stock_BNGRC");
                $db->exec("DELETE FROM attribution_BNGRC");
                $db->exec("DELETE FROM achat_BNGRC");
                $db->exec("DELETE FROM vente_BNGRC");
                $db->exec("DELETE FROM don_BNGRC");
            }
            
            // Réinitialiser les stocks avec les dons protégés
            $db->exec("UPDATE produit_BNGRC SET stock_actuel = 0");
            
            // Remettre les stocks des dons protégés
            if (!empty($donsProtect)) {
                $donsList = implode(',', $donsProtect);
                
                // Pour les produits
                $stmt = $db->query("
                    SELECT produit_id, SUM(quantite_totale) as total 
                    FROM don_BNGRC 
                    WHERE produit_id IS NOT NULL AND id IN ($donsList)
                    GROUP BY produit_id
                ");
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $db->prepare("
                        UPDATE produit_BNGRC 
                        SET stock_actuel = stock_actuel + ? 
                        WHERE id = ?
                    ")->execute([$row['total'], $row['produit_id']]);
                }
                
                // Pour l'argent
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
            }
            
            $db->commit();
            
            $_SESSION['message'] = '✅ Réinitialisation réussie ! Seuls les dons de base ont été conservés.';
            
        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = '❌ Erreur lors de la réinitialisation : ' . $e->getMessage();
        }
        
        $this->redirect('/dashboard');
    }
}