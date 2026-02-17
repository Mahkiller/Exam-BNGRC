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
    
    public function getDonsNonUtilises() {
        return $this->donModel->getDonsNonUtilises();
    }
    
    public function getStatsDonateurs() {
        return $this->donModel->getStatsParTypeDonateur();
    }
    
    public function getTopDonateurs($limit = 5) {
        return $this->donModel->getTopDonateurs($limit);
    }
    
public function ajouterDon($donateur, $type_don, $description, $quantite, $unite, $produit_id = null) {
    try {
        if (empty($donateur) || empty($type_don) || empty($description) || $quantite <= 0 || empty($unite)) {
            return [
                'success' => false,
                'message' => 'Tous les champs sont requis et la quantité doit être positive'
            ];
        }
        
        $don_id = $this->donModel->create($donateur, $type_don, $description, $quantite, $unite, $produit_id);
        
        if ($don_id) {
            return [
                'success' => true,
                'message' => 'Don enregistré avec succès',
                'don_id' => $don_id
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement dans la base de données'
            ];
        }
    } catch (Exception $e) {
        error_log("Exception dans ajouterDon: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage()
        ];
    }
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
        
        // Vérifier que le don a assez de stock
        $don = $this->donModel->getById($don_id);
        if (!$don) {
            return [
                'success' => false,
                'message' => 'Don introuvable'
            ];
        }
        
        // Calculer le montant déjà utilisé pour ce don
        $montant_utilise = $this->getMontantUtiliseDon($don_id);
        $montant_disponible = $don['quantite_totale'] - $montant_utilise;
        
        if ($quantite > $montant_disponible) {
            return [
                'success' => false,
                'message' => 'Stock insuffisant pour ce don. Disponible: ' . $montant_disponible
            ];
        }
        
        $result = $this->donModel->attribuer($besoin_id, $don_id, $quantite);
        
        return [
            'success' => $result,
            'message' => $result ? 'Attribution réussie' : 'Erreur lors de l\'attribution'
        ];
    }
    
    private function getMontantUtiliseDon($don_id) {
        $db = Database::getInstance();
        
        // Somme des attributions
        $stmt = $db->prepare("SELECT SUM(quantite_attribuee) as total FROM attribution_BNGRC WHERE don_id = ?");
        $stmt->execute([$don_id]);
        $attributions = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // Somme des achats (si don en argent)
        $stmt = $db->prepare("SELECT SUM(montant_total) as total FROM achat_BNGRC WHERE don_id = ?");
        $stmt->execute([$don_id]);
        $achats = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        return $attributions + $achats;
    }
    
    public function getDonsAvecUtilisation() {
        $dons = $this->getAllDons();
        $result = [];
        
        foreach ($dons as $don) {
            $montant_utilise = $this->getMontantUtiliseDon($don['id']);
            $result[] = [
                'don' => $don,
                'total' => $don['quantite_totale'],
                'utilise' => $montant_utilise,
                'reste' => $don['quantite_totale'] - $montant_utilise,
                'type' => $don['type_don']
            ];
        }
        
        return $result;
    }
    
    // Récupérer les catégories de produits
    public function getCategories() {
        return $this->donModel->getCategories();
    }
    
    // Récupérer les produits
    public function getProduits() {
        return $this->donModel->getProduits();
    }
    
    // Récupérer les produits par catégorie
    public function getProduitsByCategorie($categorie_id) {
        return $this->donModel->getProduitsByCategorie($categorie_id);
    }
    
    // Récupérer le prix d'un produit
    public function getPrixProduit($produit_id) {
        return $this->donModel->getPrixProduit($produit_id);
    }
    
    // Récupérer les infos d'un produit
    public function getProduitInfo($produit_id) {
        if (!$produit_id) return null;
        return $this->donModel->getProduitInfo($produit_id);
    }
}