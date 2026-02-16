<?php
class BesoinModel extends Model {
    
    // Récupérer tous les besoins avec infos ville
    public function getAll() {
        $stmt = $this->db->query("
            SELECT b.*, v.nom_ville, v.region,
                   COALESCE(SUM(a.quantite_attribuee), 0) as quantite_attribuee
            FROM besoin_BNGRC b
            JOIN ville_BNGRC v ON b.ville_id = v.id
            LEFT JOIN attribution_BNGRC a ON b.id = a.besoin_id
            GROUP BY b.id
            ORDER BY 
                FIELD(b.niveau_urgence, 'critique', 'urgent', 'modere', 'faible'),
                b.date_besoin DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupérer un besoin par ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM besoin_BNGRC WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Récupérer les besoins non satisfaits
    public function getNonSatisfaits() {
        $stmt = $this->db->query("
            SELECT b.*, v.nom_ville, v.region,
                   COALESCE(SUM(a.quantite_attribuee), 0) as attribue,
                   (b.quantite_demandee - COALESCE(SUM(a.quantite_attribuee), 0)) as reste
            FROM besoin_BNGRC b
            JOIN ville_BNGRC v ON b.ville_id = v.id
            LEFT JOIN attribution_BNGRC a ON b.id = a.besoin_id
            GROUP BY b.id
            HAVING reste > 0
            ORDER BY 
                FIELD(b.niveau_urgence, 'critique', 'urgent', 'modere', 'faible'),
                reste DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Ajouter un besoin
    public function create($ville_id, $type_besoin, $description, $quantite, $unite, $niveau_urgence) {
        $stmt = $this->db->prepare("
            INSERT INTO besoin_BNGRC 
            (ville_id, type_besoin, description, quantite_demandee, unite, niveau_urgence, date_besoin) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$ville_id, $type_besoin, $description, $quantite, $unite, $niveau_urgence]);
    }
    
    // Récupérer le total attribué par type
    public function getTotalAttribue($type_besoin) {
        $stmt = $this->db->prepare("
            SELECT SUM(a.quantite_attribuee) as total 
            FROM attribution_BNGRC a
            JOIN besoin_BNGRC b ON a.besoin_id = b.id
            WHERE b.type_besoin = ?
        ");
        $stmt->execute([$type_besoin]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    // Statistiques des urgences
    public function getStatsUrgence() {
        $stmt = $this->db->query("
            SELECT 
                niveau_urgence,
                COUNT(*) as nombre,
                SUM(quantite_demandee) as quantite_totale
            FROM besoin_BNGRC
            GROUP BY niveau_urgence
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupérer tous les besoins par ville pour le dashboard
    public function getAllBesoinsParVille() {
        $stmt = $this->db->query("
            SELECT 
                v.nom_ville,
                v.region,
                b.type_besoin,
                b.description,
                b.quantite_demandee,
                b.unite,
                b.niveau_urgence,
                COALESCE(SUM(a.quantite_attribuee), 0) as attribue,
                (b.quantite_demandee - COALESCE(SUM(a.quantite_attribuee), 0)) as reste
            FROM besoin_BNGRC b
            JOIN ville_BNGRC v ON b.ville_id = v.id
            LEFT JOIN attribution_BNGRC a ON b.id = a.besoin_id
            GROUP BY b.id
            ORDER BY v.nom_ville, FIELD(b.niveau_urgence, 'critique', 'urgent', 'modere', 'faible')
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupérer les besoins critiques non satisfaits
    public function getBesoinsCritiquesNonSatisfaits() {
        $stmt = $this->db->query("
            SELECT b.*, v.nom_ville,
                   COALESCE(SUM(a.quantite_attribuee), 0) as attribue,
                   (b.quantite_demandee - COALESCE(SUM(a.quantite_attribuee), 0)) as manque
            FROM besoin_BNGRC b
            JOIN ville_BNGRC v ON b.ville_id = v.id
            LEFT JOIN attribution_BNGRC a ON b.id = a.besoin_id
            WHERE b.niveau_urgence = 'critique'
            GROUP BY b.id
            HAVING manque > 0
            ORDER BY manque DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupérer la liste des villes pour le formulaire
    public function getVilles() {
        $stmt = $this->db->query("SELECT id, nom_ville FROM ville_BNGRC ORDER BY nom_ville");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Compter le total des besoins
    public function getTotalBesoins() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM besoin_BNGRC");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    // Compter les villes aidées (qui ont reçu au moins une attribution)
    public function getVillesAidees() {
        $stmt = $this->db->query("
            SELECT COUNT(DISTINCT b.ville_id) as total
            FROM besoin_BNGRC b
            JOIN attribution_BNGRC a ON b.id = a.besoin_id
        ");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    // Récupérer les besoins récents (VERSION CORRIGÉE)
    public function getBesoinsRecents($limit) {
        $stmt = $this->db->prepare("
            SELECT b.*, v.nom_ville 
            FROM besoin_BNGRC b
            JOIN ville_BNGRC v ON b.ville_id = v.id
            ORDER BY b.date_besoin DESC 
            LIMIT ?
        ");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}