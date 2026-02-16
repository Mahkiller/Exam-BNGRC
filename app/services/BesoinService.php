<?php
class BesoinService {
    private $besoinModel;
    private $validationService;
    
    public function __construct($besoinModel, $validationService) {
        $this->besoinModel = $besoinModel;
        $this->validationService = $validationService;
    }
    
    public function getAllBesoins() {
        return $this->besoinModel->getAll();
    }
    
    public function getTotalBesoins() {
        return $this->besoinModel->getTotalBesoins();
    }
    
    public function getVillesAidees() {
        return $this->besoinModel->getVillesAidees();
    }
    
    public function getBesoinsRecents($limit) {
        return $this->besoinModel->getBesoinsRecents($limit);
    }
    
    public function getAllBesoinsParVille() {
        return $this->besoinModel->getAllBesoinsParVille();
    }
    
    public function getVilles() {
        return $this->besoinModel->getVilles();
    }
    
    public function getStatsUrgence() {
        return $this->besoinModel->getStatsUrgence();
    }
    
    public function getBesoinsCritiques() {
        return $this->besoinModel->getBesoinsCritiquesNonSatisfaits();
    }
    
    public function ajouterBesoin($ville_id, $type_besoin, $description, $quantite, $unite, $niveau_urgence) {
        if (empty($ville_id) || empty($type_besoin) || empty($description) || $quantite <= 0 || empty($unite)) {
            return [
                'success' => false,
                'message' => 'Tous les champs sont requis et la quantité doit être positive'
            ];
        }
        
        $result = $this->besoinModel->create($ville_id, $type_besoin, $description, $quantite, $unite, $niveau_urgence);
        
        return [
            'success' => $result,
            'message' => $result ? 'Besoin ajouté avec succès' : 'Erreur lors de l\'ajout du besoin'
        ];
    }
}