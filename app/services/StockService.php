<?php
class StockService {
    private $donModel;
    private $besoinModel;
    
    public function __construct($donModel, $besoinModel) {
        $this->donModel = $donModel;
        $this->besoinModel = $besoinModel;
    }
    
    // Vérifier si assez de stock
    public function verifierStockDisponible($type, $quantiteDemandee) {
        $stockActuel = $this->getStockRestant($type);
        return $stockActuel >= $quantiteDemandee;
    }
    
    // Calculer stock restant par type - CORRIGÉ
    public function getStockRestant($type) {
        $totalDons = $this->donModel->getStockTotal($type);
        $totalAttribue = $this->besoinModel->getTotalAttribue($type);
        
        // Pour les dons en nature et materiaux, pas de soustraction d'achats
        if ($type !== 'argent') {
            return $totalDons - $totalAttribue;
        }
        
        // Pour l'argent, soustraire aussi les achats
        $db = Database::getInstance();
        $stmt = $db->query("SELECT SUM(montant_total) as total FROM achat_BNGRC");
        $totalAchats = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        return $totalDons - $totalAttribue - $totalAchats;
    }
    
    // Stock global par type
    public function getStockGlobal() {
        $types = ['nature', 'materiaux', 'argent'];
        $stock = [];
        foreach ($types as $type) {
            $stock[$type] = $this->getStockRestant($type);
        }
        return $stock;
    }
    
    // Stock détaillé par article - CORRIGÉ pour inclure les achats
    public function getStockDetaille() {
        $dons = $this->donModel->getAll();
        $stock = [];
        
        foreach ($dons as $don) {
            // Calculer ce qui a déjà été attribué pour ce don
            $stmt = Database::getInstance()->prepare("
                SELECT SUM(quantite_attribuee) as total 
                FROM attribution_BNGRC 
                WHERE don_id = ?
            ");
            $stmt->execute([$don['id']]);
            $attribue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            
            $reste = $don['quantite_totale'] - $attribue;
            
            if ($reste > 0) {
                $stock[] = [
                    'description' => $don['description'],
                    'type' => $don['type_don'],
                    'quantite' => $reste,
                    'unite' => $don['unite'],
                    'source' => 'don',
                    'donateur' => $don['donateur']
                ];
            }
        }
        
        return $stock;
    }
}