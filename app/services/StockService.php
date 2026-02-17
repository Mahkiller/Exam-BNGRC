<?php
class StockService {
    private $donModel;
    private $besoinModel;
    private $venteModel;
    public function __construct($donModel, $besoinModel) {
        $this->donModel = $donModel;
        $this->besoinModel = $besoinModel;
        $this->venteModel = new VenteModel();
    }
    public function verifierStockDisponible($type, $quantiteDemandee) {
        $stockActuel = $this->getStockRestant($type);
        return $stockActuel >= $quantiteDemandee;
    }
    public function getStockRestant($type) {
        $totalDons = $this->donModel->getStockTotal($type);
        $totalAttribue = $this->besoinModel->getTotalAttribue($type);
        if ($type !== 'argent') {
            return $totalDons - $totalAttribue;
        }
        $db = Database::getInstance();
        $stmt = $db->query("SELECT COALESCE(SUM(montant_total), 0) as total FROM achat_BNGRC");
        $totalAchats = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalVentes = $this->venteModel->getTotalVentes();
        return $totalDons - $totalAttribue - $totalAchats + $totalVentes;
    }
    public function getStockGlobal() {
        $types = ['nature', 'materiaux', 'argent'];
        $stock = [];
        foreach ($types as $type) {
            $stock[$type] = $this->getStockRestant($type);
        }
        return $stock;
    }
    public function getStockDetaille() {
        $dons = $this->donModel->getAll();
        $stock = [];
        foreach ($dons as $don) {
            $stmt = Database::getInstance()->prepare("
                SELECT COALESCE(SUM(quantite_attribuee), 0) as total 
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
