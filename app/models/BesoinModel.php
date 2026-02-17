<?php
class BesoinModel extends Model {
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
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM besoin_BNGRC WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
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
    public function create($ville_id, $type_besoin, $description, $quantite, $unite, $niveau_urgence, $produit_id = null) {
        $stmt = $this->db->prepare("
            INSERT INTO besoin_BNGRC 
            (ville_id, type_besoin, description, quantite_demandee, unite, niveau_urgence, produit_id, date_besoin) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$ville_id, $type_besoin, $description, $quantite, $unite, $niveau_urgence, $produit_id]);
    }
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
    public function getVilles() {
        $stmt = $this->db->query("SELECT id, nom_ville FROM ville_BNGRC ORDER BY nom_ville");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTotalBesoins() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM besoin_BNGRC");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    public function getVillesAidees() {
        $stmt = $this->db->query("
            SELECT COUNT(DISTINCT b.ville_id) as total
            FROM besoin_BNGRC b
            JOIN attribution_BNGRC a ON b.id = a.besoin_id
        ");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
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
    public function getCategories() {
        $stmt = $this->db->query("
            SELECT id, nom_categorie, description
            FROM categorie_produit_BNGRC
            ORDER BY nom_categorie
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getProduits() {
        $stmt = $this->db->query("
            SELECT p.id, p.categorie_id, p.nom_produit, p.description, 
                   p.unite_mesure, p.prix_unitaire_reference, p.stock_actuel, p.seuil_alerte,
                   c.nom_categorie
            FROM produit_BNGRC p
            JOIN categorie_produit_BNGRC c ON p.categorie_id = c.id
            WHERE c.nom_categorie IN ('nature', 'materiaux')
            ORDER BY c.nom_categorie, p.nom_produit
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getProduitsByCategorie($categorie_id) {
        $stmt = $this->db->prepare("
            SELECT p.id, p.categorie_id, p.nom_produit, p.description, 
                   p.unite_mesure, p.prix_unitaire_reference, p.stock_actuel, p.seuil_alerte,
                   c.nom_categorie
            FROM produit_BNGRC p
            JOIN categorie_produit_BNGRC c ON p.categorie_id = c.id
            WHERE p.categorie_id = ? AND c.nom_categorie IN ('nature', 'materiaux')
            ORDER BY p.nom_produit
        ");
        $stmt->execute([$categorie_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getProduitInfo($produit_id) {
        $stmt = $this->db->prepare("
            SELECT p.id, p.nom_produit, p.description, p.unite_mesure, 
                   p.prix_unitaire_reference, p.stock_actuel, p.seuil_alerte, p.categorie_id,
                   c.nom_categorie
            FROM produit_BNGRC p
            JOIN categorie_produit_BNGRC c ON p.categorie_id = c.id
            WHERE p.id = ? AND c.nom_categorie IN ('nature', 'materiaux')
        ");
        $stmt->execute([$produit_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
