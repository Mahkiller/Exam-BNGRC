<?php
class AchatModel extends Model {
    
    // Récupérer tous les achats avec tous les détails
    public function getAll() {
        $stmt = $this->db->query("
            SELECT a.*, 
                   v.nom_ville, 
                   b.description as besoin_description, 
                   d.donateur,
                   p.nom_produit,
                   p.unite_mesure,
                   c.nom_categorie
            FROM achat_BNGRC a
            JOIN besoin_BNGRC b ON a.besoin_id = b.id
            JOIN ville_BNGRC v ON b.ville_id = v.id
            JOIN don_BNGRC d ON a.don_id = d.id
            JOIN produit_BNGRC p ON a.produit_id = p.id
            LEFT JOIN categorie_produit_BNGRC c ON p.categorie_id = c.id
            ORDER BY a.date_achat DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupérer les achats par ville
    public function getByVille($ville_id) {
        $stmt = $this->db->prepare("
            SELECT a.*, 
                   v.nom_ville, 
                   b.description as besoin_description, 
                   d.donateur,
                   p.nom_produit,
                   p.unite_mesure
            FROM achat_BNGRC a
            JOIN besoin_BNGRC b ON a.besoin_id = b.id
            JOIN ville_BNGRC v ON b.ville_id = v.id
            JOIN don_BNGRC d ON a.don_id = d.id
            JOIN produit_BNGRC p ON a.produit_id = p.id
            WHERE v.id = ?
            ORDER BY a.date_achat DESC
        ");
        $stmt->execute([$ville_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Créer un achat avec produit_id
    public function create($don_id, $besoin_id, $produit_id, $quantite, $prix_unitaire) {
        $montant_total = $quantite * $prix_unitaire;
        
        $stmt = $this->db->prepare("
            INSERT INTO achat_BNGRC 
            (don_id, besoin_id, produit_id, quantite, prix_unitaire_achat, montant_total, date_achat)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$don_id, $besoin_id, $produit_id, $quantite, $prix_unitaire, $montant_total]);
    }
    
    // Récupérer un achat par ID
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT a.*, 
                   v.nom_ville, 
                   b.description as besoin_description, 
                   d.donateur,
                   p.nom_produit,
                   p.unite_mesure
            FROM achat_BNGRC a
            JOIN besoin_BNGRC b ON a.besoin_id = b.id
            JOIN ville_BNGRC v ON b.ville_id = v.id
            JOIN don_BNGRC d ON a.don_id = d.id
            JOIN produit_BNGRC p ON a.produit_id = p.id
            WHERE a.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Supprimer un achat
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM achat_BNGRC WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    // Récupérer tous les produits avec leurs prix
    public function getProduits() {
        $stmt = $this->db->query("
            SELECT p.*, c.nom_categorie
            FROM produit_BNGRC p
            JOIN categorie_produit_BNGRC c ON p.categorie_id = c.id
            WHERE c.nom_categorie IN ('nature', 'materiaux')
            ORDER BY c.nom_categorie, p.nom_produit
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupérer les prix unitaires des produits
    public function getPrixUnitaires() {
        $stmt = $this->db->query("
            SELECT p.id, p.nom_produit, p.prix_unitaire_reference, p.unite_mesure
            FROM produit_BNGRC p
            WHERE p.prix_unitaire_reference IS NOT NULL
            ORDER BY p.nom_produit
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupérer le prix unitaire d'un produit
    public function getPrixProduit($produit_id) {
        $stmt = $this->db->prepare("
            SELECT prix_unitaire_reference FROM produit_BNGRC
            WHERE id = ?
        ");
        $stmt->execute([$produit_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['prix_unitaire_reference'] ?? 0;
    }
    
    // Montant total des achats par ville
    public function getMontantTotalParVille() {
        $stmt = $this->db->query("
            SELECT v.nom_ville, v.id, SUM(a.montant_total) as montant_total
            FROM achat_BNGRC a
            JOIN besoin_BNGRC b ON a.besoin_id = b.id
            JOIN ville_BNGRC v ON b.ville_id = v.id
            GROUP BY v.id, v.nom_ville
            ORDER BY montant_total DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récents achats
    public function getRecents($limit = 10) {
        $stmt = $this->db->prepare("
            SELECT a.*, 
                   v.nom_ville, 
                   b.description as besoin_description, 
                   d.donateur,
                   p.nom_produit,
                   p.unite_mesure
            FROM achat_BNGRC a
            JOIN besoin_BNGRC b ON a.besoin_id = b.id
            JOIN ville_BNGRC v ON b.ville_id = v.id
            JOIN don_BNGRC d ON a.don_id = d.id
            JOIN produit_BNGRC p ON a.produit_id = p.id
            ORDER BY a.date_achat DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Total montant achats
    public function getTotalMontantAchats() {
        $stmt = $this->db->query("SELECT SUM(montant_total) as total FROM achat_BNGRC");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
}
