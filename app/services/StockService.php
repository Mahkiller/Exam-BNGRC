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