<?php
class ResetController extends Controller {
    public function reset() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/dashboard');
            return;
        }
        try {
            $db = Database::getInstance();
            $db->beginTransaction();
            $donModel = new DonModel();
            $donsProtect = $donModel->getDonsProteges();
            if (!empty($donsProtect)) {
                $donsList = implode(',', $donsProtect);
                $db->exec("
                    DELETE FROM mouvement_stock_BNGRC 
                    WHERE source_type = 'don' AND source_id NOT IN ($donsList)
                ");
                $db->exec("
                    DELETE FROM mouvement_stock_BNGRC 
                    WHERE source_type IN ('achat', 'vente', 'attribution')
                ");
                $db->exec("DELETE FROM vente_BNGRC");
                $db->exec("DELETE FROM achat_BNGRC");
                $db->exec("DELETE FROM attribution_BNGRC");
                $db->exec("DELETE FROM don_BNGRC WHERE protege != 1");
            } else {
                $db->exec("DELETE FROM mouvement_stock_BNGRC");
                $db->exec("DELETE FROM vente_BNGRC");
                $db->exec("DELETE FROM achat_BNGRC");
                $db->exec("DELETE FROM attribution_BNGRC");
                $db->exec("DELETE FROM don_BNGRC");
            }
            $db->exec("UPDATE produit_BNGRC SET stock_actuel = 0");
            if (!empty($donsProtect)) {
                $donsList = implode(',', $donsProtect);
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
            $_SESSION['message'] = '✅ Réinitialisation réussie ! Toutes les ventes, achats et dons non protégés ont été supprimés.';
        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = '❌ Erreur lors de la réinitialisation : ' . $e->getMessage();
        }
        $this->redirect('/dashboard');
    }
}
