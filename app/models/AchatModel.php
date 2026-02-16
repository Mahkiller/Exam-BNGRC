<?php
class AchatModel extends Model {
    
    // Récupérer tous les achats
    public function getAll() {
        $stmt = $this->db->query("
            SELECT a.*, v.nom_ville, b.description as besoin_description, d.donateur
            FROM achat_BNGRC a
            JOIN besoin_BNGRC b ON a.besoin_id = b.id
            JOIN ville_BNGRC v ON b.ville_id = v.id
            JOIN don_BNGRC d ON a.don_id = d.id
            ORDER BY a.date_achat DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupérer les achats par ville
    public function getByVille($ville_id) {
        $stmt = $this->db->prepare("
            SELECT a.*, v.nom_ville, b.description as besoin_description, d.donateur
            FROM achat_BNGRC a
            JOIN besoin_BNGRC b ON a.besoin_id = b.id
            JOIN ville_BNGRC v ON b.ville_id = v.id
            JOIN don_BNGRC d ON a.don_id = d.id
            WHERE v.id = ?
            ORDER BY a.date_achat DESC
        ");
        $stmt->execute([$ville_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Créer un achat
    public function create($don_id, $besoin_id, $description_article, $quantite, $prix_unitaire) {
        $montant_total = $quantite * $prix_unitaire;
        
        $stmt = $this->db->prepare("
            INSERT INTO achat_BNGRC 
            (don_id, besoin_id, description_article, quantite, prix_unitaire_achat, montant_total, date_achat)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$don_id, $besoin_id, $description_article, $quantite, $prix_unitaire, $montant_total]);
    }
    
    // Récupérer un achat par ID
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT a.*, v.nom_ville, b.description as besoin_description, d.donateur
            FROM achat_BNGRC a
            JOIN besoin_BNGRC b ON a.besoin_id = b.id
            JOIN ville_BNGRC v ON b.ville_id = v.id
            JOIN don_BNGRC d ON a.don_id = d.id
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
    
    // Récupérer les prix unitaires
    public function getPrixUnitaires() {
        $stmt = $this->db->query("
            SELECT * FROM prix_unitaire_BNGRC
            ORDER BY type_article, description
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupérer un prix unitaire par type et description
    public function getPrixUnitaire($type_article, $description) {
        $stmt = $this->db->prepare("
            SELECT prix_unitaire FROM prix_unitaire_BNGRC
            WHERE type_article = ? AND description = ?
        ");
        $stmt->execute([$type_article, $description]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['prix_unitaire'] ?? 0;
    }
    
    // Montant total des achats par ville
    public function getMontantTotalParVille() {
        $stmt = $this->db->query("
            SELECT v.nom_ville, SUM(a.montant_total) as montant_total
            FROM achat_BNGRC a
            JOIN besoin_BNGRC b ON a.besoin_id = b.id
            JOIN ville_BNGRC v ON b.ville_id = v.id
            GROUP BY v.nom_ville, v.id
            ORDER BY montant_total DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récents achats
    public function getRecents($limit = 10) {
        $stmt = $this->db->prepare("
            SELECT a.*, v.nom_ville, b.description as besoin_description, d.donateur
            FROM achat_BNGRC a
            JOIN besoin_BNGRC b ON a.besoin_id = b.id
            JOIN ville_BNGRC v ON b.ville_id = v.id
            JOIN don_BNGRC d ON a.don_id = d.id
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
