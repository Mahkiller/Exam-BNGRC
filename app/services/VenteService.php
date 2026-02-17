<?php
class VenteService {
    private $venteModel;
    private $validationService;
    
    public function __construct($venteModel, $validationService) {
        $this->venteModel = $venteModel;
        $this->validationService = $validationService;
    }
    
    // Récupérer toutes les ventes
    public function getAllVentes() {
        return $this->venteModel->getAll();
    }
    
    // Récupérer le total des montants de ventes
    public function getTotalVentes() {
        return $this->venteModel->getTotalVentes();
    }
    
    // Récupérer les stocks disponibles
    public function getStocksDisponibles() {
        return $this->venteModel->getStocksDisponibles();
    }
    
    // Vérifier si un produit peut être vendu
    public function canSellProduct($produit_id) {
        return !$this->venteModel->hasUnmetNeed($produit_id);
    }
    
    // Récupérer les besoins actifs
    public function getActiveNeedsForProduct($produit_id) {
        return $this->venteModel->getActiveNeedsForProduct($produit_id);
    }
    
    // Récupérer le taux de change
    public function getTauxChange() {
        return $this->venteModel->getTauxChange();
    }
    
    // Récupérer la configuration
    public function getConfiguration() {
        return $this->venteModel->getConfiguration();
    }
    
    // Mettre à jour la configuration
    public function updateConfiguration($param_key, $param_value) {
        if ($param_key === 'taux_change_vente' || $param_key === 'frais_vente' || $param_key === 'tva_vente') {
            if (!is_numeric($param_value) || $param_value < 0 || $param_value > 100) {
                throw new Exception('Le pourcentage doit être entre 0 et 100');
            }
        }
        return $this->venteModel->updateConfiguration($param_key, $param_value);
    }
    
    // Effectuer une vente
    public function vendreProduct($don_id, $produit_id, $quantite_vendue, $prix_unitaire_reference, $acheteur = null, $notes = null) {
        // Vérifications
        if ($quantite_vendue <= 0) {
            throw new Exception('La quantité doit être supérieure à 0');
        }
        
        // Vérifier les besoins non satisfaits
        if ($this->venteModel->hasUnmetNeed($produit_id)) {
            $needs = $this->venteModel->getActiveNeedsForProduct($produit_id);
            $msg = "Ce produit est encore en demande, on ne peut pas le vendre.\n";
            foreach ($needs as $need) {
                $msg .= "- " . $need['description'] . " à " . $need['nom_ville'] . "\n";
            }
            throw new Exception($msg);
        }
        
        // Calculs
        $taux_depreciation = $this->getTauxChange();
        $prix_vente_unitaire = $prix_unitaire_reference * (1 - $taux_depreciation);
        $montant_total = $quantite_vendue * $prix_vente_unitaire;
        
        // Créer la vente
        $vente_id = $this->venteModel->create(
            $don_id,
            $produit_id,
            $quantite_vendue,
            $prix_unitaire_reference,
            $prix_vente_unitaire,
            $montant_total,
            $taux_depreciation,
            $acheteur,
            $notes
        );
        
        if ($vente_id) {
            // Mettre à jour le stock
            $this->venteModel->reduceStock($produit_id, $quantite_vendue);
            
            // Enregistrer le mouvement
            $this->venteModel->recordStockMovement($produit_id, $quantite_vendue, $vente_id);
            
            return [
                'success' => true,
                'vente_id' => $vente_id,
                'montant_total' => $montant_total,
                'taux_depreciation' => $taux_depreciation
            ];
        }
        
        throw new Exception('Erreur lors de la création de la vente');
    }
    
    // Statistiques
    public function getSalesStats() {
        return $this->venteModel->getSalesStats();
    }
    
    public function getSalesByCategory() {
        return $this->venteModel->getSalesByCategory();
    }
}