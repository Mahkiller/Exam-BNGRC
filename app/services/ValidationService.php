<?php
class ValidationService {
    private $stockService;
    public function __construct($stockService) {
        $this->stockService = $stockService;
    }
    public function validerAttribution($type, $quantite) {
        if ($quantite <= 0) {
            return [
                'valide' => false,
                'message' => "ERREUR: La quantité doit être positive"
            ];
        }
        if (!$this->stockService->verifierStockDisponible($type, $quantite)) {
            $stockRestant = $this->stockService->getStockRestant($type);
            return [
                'valide' => false,
                'message' => "ERREUR: Quantité donnée ($quantite) supérieure au stock disponible ($stockRestant)"
            ];
        }
        return ['valide' => true, 'message' => ''];
    }
}
