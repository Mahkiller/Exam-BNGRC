<?php
class DonService {
    private $donModel;
    private $validationService;
    private $stockService;
    
    public function __construct($donModel, $validationService, $stockService) {
        $this->donModel = $donModel;
        $this->validationService = $validationService;
        $this->stockService = $stockService;
    }
    
    public function getAllDons() {
        return $this->donModel->getAll();
    }
    
    public function getTotalDons() {
        return $this->donModel->getTotalDons();
    }
    
    public function getDonsRecents($limit) {
        return $this->donModel->getDonsRecents($limit);
    }
    
    public function getStockGlobal() {
        return $this->stockService->getStockGlobal();
    }
    
    public function getStockDetaille() {
        return $this->stockService->getStockDetaille();
    }
    
    public function getBesoinsNonSatisfaits() {
        return $this->donModel->getBesoinsNonSatisfaits();
    }
    
    public function getDonsDisponibles() {
        return $this->donModel->getDonsNonUtilises();
    }
    
    public function getStatsDonateurs() {
        return $this->donModel->getStatsParTypeDonateur();
    }
    
    public function getTopDonateurs($limit = 5) {
        return $this->donModel->getTopDonateurs($limit);
    }
    
    public function ajouterDon($donateur, $type_don, $description, $quantite, $unite) {
        if (empty($donateur) || empty($type_don) || empty($description) || $quantite <= 0 || empty($unite)) {
            return [
                'success' => false,
                'message' => 'Tous les champs sont requis et la quantité doit être positive'
            ];
        }
        
        $result = $this->donModel->create($donateur, $type_don, $description, $quantite, $unite);
        
        return [
            'success' => $result,
            'message' => $result ? 'Don enregistré avec succès' : 'Erreur lors de l\'enregistrement'
        ];
    }
    
    public function attribuerDon($besoin_id, $don_id, $quantite, $type = null) {
        // Si don_id n'est pas fourni, chercher un don disponible du même type
        if (!$don_id && $type) {
            $donsDispo = $this->getDonsDisponibles();
            foreach ($donsDispo as $don) {
                if ($don['type_don'] == $type && $don['reste_disponible'] >= $quantite) {
                    $don_id = $don['id'];
                    break;
                }
            }
        }
        
        if (!$don_id) {
            return [
                'success' => false,
                'message' => 'Aucun don disponible correspondant trouvé'
            ];
        }
        
        $result = $this->donModel->attribuer($besoin_id, $don_id, $quantite);
        
        return [
            'success' => $result,
            'message' => $result ? 'Attribution réussie' : 'Erreur lors de l\'attribution'
        ];
    }
}