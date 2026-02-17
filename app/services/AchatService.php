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
    public function getAllAchats() {
        return $this->achatModel->getAll();
    }
    public function getAchatsByVille($ville_id) {
        return $this->achatModel->getByVille($ville_id);
    }
    public function creerAchat($don_id, $besoin_id, $produit_id, $quantite, $prix_unitaire) {
        if (empty($don_id) || empty($besoin_id) || empty($produit_id) || $quantite <= 0 || $prix_unitaire <= 0) {
            return [
                'success' => false,
                'message' => 'Données invalides'
            ];
        }
        $don = $this->donModel->getById($don_id);
        if (!$don || $don['type_don'] !== 'argent') {
            return [
                'success' => false,
                'message' => 'Le don doit être de type argent'
            ];
        }
        $montant_achat = $quantite * $prix_unitaire;
        $montant_utilise = $this->getMontantUtilise($don_id);
        $montant_disponible = $don['quantite_totale'] - $montant_utilise;
        if ($montant_achat > $montant_disponible) {
            return [
                'success' => false,
                'message' => 'Montant insuffisant. Disponible: ' . number_format($montant_disponible) . ' Ar'
            ];
        }
        $result = $this->achatModel->create($don_id, $besoin_id, $produit_id, $quantite, $prix_unitaire);
        return [
            'success' => $result,
            'message' => $result ? 'Achat créé avec succès' : 'Erreur lors de la création'
        ];
    }
    public function supprimerAchat($id) {
        $result = $this->achatModel->delete($id);
        return [
            'success' => $result,
            'message' => $result ? 'Achat supprimé' : 'Erreur'
        ];
    }
    public function getProduits() {
        return $this->achatModel->getProduits();
    }
    public function getPrixUnitaires() {
        return $this->achatModel->getPrixUnitaires();
    }
    public function getPrixProduit($produit_id) {
        return $this->achatModel->getPrixProduit($produit_id);
    }
    public function getAchat($id) {
        return $this->achatModel->getById($id);
    }
    public function getMontantUtilise($don_id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT SUM(montant_total) as total FROM achat_BNGRC WHERE don_id = ?
        ");
        $stmt->execute([$don_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    public function getMontantTotalParVille() {
        return $this->achatModel->getMontantTotalParVille();
    }
    public function getAchatsRecents($limit = 10) {
        return $this->achatModel->getRecents($limit);
    }
    public function getTotalMontantAchats() {
        return $this->achatModel->getTotalMontantAchats();
    }
}
