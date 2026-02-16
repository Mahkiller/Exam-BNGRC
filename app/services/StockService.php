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
    
    // Calculer stock restant par type
    public function getStockRestant($type) {
        $totalDons = $this->donModel->getStockTotal($type);
        $totalAttribue = $this->besoinModel->getTotalAttribue($type);
        
        // Si c'est de l'argent, il faut aussi soustraire les achats
        if ($type === 'argent') {
            $db = Database::getInstance();
            $stmt = $db->query("SELECT SUM(montant_total) as total FROM achat_BNGRC");
            $totalAchats = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            return $totalDons - $totalAttribue - $totalAchats;
        }
        
        return $totalDons - $totalAttribue;
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
    
    // Stock détaillé par article
    public function getStockDetaille() {
        $donsNonUtilises = $this->donModel->getDonsNonUtilises();
        $stock = [];
        foreach ($donsNonUtilises as $don) {
            $stock[] = [
                'description' => $don['description'],
                'type' => $don['type_don'],
                'quantite' => $don['reste_disponible'],
                'unite' => $don['unite']
            ];
        }
        return $stock;
    }
}