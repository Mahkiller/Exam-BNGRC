<?php
class AchatService {
    private $achatModel;
    private $donModel;
    private $besoinModel;
    
    public function __construct($achatModel, $donModel, $besoinModel) {
        $this->achatModel = $achatModel;
        $this->donModel = $donModel;
        $this->besoinModel = $besoinModel;
    }
    
    // Récupérer tous les achats
    public function getAllAchats() {
        return $this->achatModel->getAll();
    }
    
    // Récupérer les achats par ville
    public function getAchatsByVille($ville_id) {
        return $this->achatModel->getByVille($ville_id);
    }
    
    // Créer un achat - CORRIGÉ pour créer un don en nature
    public function creerAchat($don_id, $besoin_id, $description_article, $quantite, $prix_unitaire) {
        // Validation
        if (empty($don_id) || empty($besoin_id) || $quantite <= 0 || $prix_unitaire <= 0) {
            return [
                'success' => false,
                'message' => 'Données invalides'
            ];
        }
        
        // Vérifier que le don existe et est de type argent
        $don = $this->donModel->getById($don_id);
        if (!$don || $don['type_don'] !== 'argent') {
            return [
                'success' => false,
                'message' => 'Le don doit être de type argent'
            ];
        }
        
        $montant_achat = $quantite * $prix_unitaire;
        
        // Vérifier qu'il y a assez d'argent disponible dans le don
        $montant_utilise = $this->getMontantUtilise($don_id);
        $montant_disponible = $don['quantite_totale'] - $montant_utilise;
        
        if ($montant_achat > $montant_disponible) {
            return [
                'success' => false,
                'message' => 'Montant insuffisant. Disponible: ' . number_format($montant_disponible) . ' Ar'
            ];
        }
        
        // 1. Créer l'achat
        $result = $this->achatModel->create($don_id, $besoin_id, $description_article, $quantite, $prix_unitaire);
        
        if ($result) {
            // 2. CRÉER UN DON EN NATURE AUTOMATIQUEMENT
            // Déterminer le type d'article
            $type_article = 'nature'; // Par défaut
            $description_lower = strtolower($description_article);
            
            if (strpos($description_lower, 'tôle') !== false || 
                strpos($description_lower, 'ciment') !== false ||
                strpos($description_lower, 'clou') !== false ||
                strpos($description_lower, 'bâche') !== false ||
                strpos($description_lower, 'bois') !== false ||
                strpos($description_lower, 'parpaing') !== false ||
                strpos($description_lower, 'brique') !== false) {
                $type_article = 'materiaux';
            }
            
            // Déterminer l'unité
            $unite = 'kg';
            if (strpos($description_lower, 'litre') !== false) {
                $unite = 'litre';
            } elseif (strpos($description_lower, 'sac') !== false) {
                $unite = 'sac';
            } elseif (strpos($description_lower, 'plaque') !== false) {
                $unite = 'plaque';
            } elseif (strpos($description_lower, 'm3') !== false) {
                $unite = 'm3';
            } elseif (strpos($description_lower, 'unité') !== false || 
                      strpos($description_lower, 'pieces') !== false ||
                      strpos($description_lower, 'pièces') !== false) {
                $unite = 'pièce';
            }
            
            // Créer le don en nature
            $donNatureId = $this->donModel->create(
                'Achat via ' . $don['donateur'], // Donateur = achat
                $type_article,
                $description_article . ' (acheté)',
                $quantite,
                $unite
            );
            
            // Log pour debug
            error_log("Achat créé avec succès - Don en nature ID: " . $donNatureId);
        }
        
        return [
            'success' => $result,
            'message' => $result ? 'Achat créé avec succès' : 'Erreur lors de la création'
        ];
    }
    
    // Supprimer un achat
    public function supprimerAchat($id) {
        $result = $this->achatModel->delete($id);
        return [
            'success' => $result,
            'message' => $result ? 'Achat supprimé' : 'Erreur'
        ];
    }
    
    // Récupérer les prix unitaires
    public function getPrixUnitaires() {
        return $this->achatModel->getPrixUnitaires();
    }
    
    // Récupérer un achat
    public function getAchat($id) {
        return $this->achatModel->getById($id);
    }
    
    // Montant utilise d'un don
    public function getMontantUtilise($don_id) {
        $stmt = Database::getInstance()->prepare("
            SELECT SUM(montant_total) as total FROM achat_BNGRC WHERE don_id = ?
        ");
        $stmt->execute([$don_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    // Montant total des achats par ville
    public function getMontantTotalParVille() {
        return $this->achatModel->getMontantTotalParVille();
    }
    
    // Achats récents
    public function getAchatsRecents($limit = 10) {
        return $this->achatModel->getRecents($limit);
    }
    
    // Total montant achats
    public function getTotalMontantAchats() {
        return $this->achatModel->getTotalMontantAchats();
    }
}