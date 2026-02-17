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
            
            // 1. Supprimer tous les mouvements de stock
            $db->exec("DELETE FROM mouvement_stock_BNGRC");
            
            // 2. Supprimer toutes les attributions
            $db->exec("DELETE FROM attribution_BNGRC");
            
            // 3. Supprimer tous les achats
            $db->exec("DELETE FROM achat_BNGRC");
            
            // 4. Supprimer toutes les ventes
            $db->exec("DELETE FROM vente_BNGRC");
            
            // 5. Supprimer TOUS les dons (RIEN N'EST PROTÉGÉ)
            $db->exec("DELETE FROM don_BNGRC");
            
            // 6. Remettre tous les stocks à ZÉRO
            $db->exec("UPDATE produit_BNGRC SET stock_actuel = 0");
            
            $db->commit();
            
            $_SESSION['message'] = '✅ Réinitialisation réussie ! Tous les dons ont été supprimés.';
            
        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = '❌ Erreur lors de la réinitialisation : ' . $e->getMessage();
        }
        
        $this->redirect('/dashboard');
    }
}