<?php
class DonModel extends Model {
    public function getAll() {
        $stmt = $this->db->query("
            SELECT d.*, 
                   CASE 
                       WHEN d.donateur LIKE '%(%)%' OR d.donateur IN ('Banque Mondiale', 'UNICEF', 'Croix-Rouge Internationale', 'PNUD', 'Médecins Sans Frontières', 'TotalEnergies', 'Air France', 'Société Générale', 'Orange Madagascar', 'Airtel Madagascar') THEN 'International'
                       ELSE 'National/Malgache'
                   END as type_donateur,
                   p.nom_produit,
                   p.unite_mesure as produit_unite,
                   c.nom_categorie
            FROM don_BNGRC d
            LEFT JOIN produit_BNGRC p ON d.produit_id = p.id
            LEFT JOIN categorie_produit_BNGRC c ON p.categorie_id = c.id
            ORDER BY d.date_don DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function create($donateur, $type_don, $description, $quantite, $unite, $produit_id = null) {
        try {
            if ($type_don === 'argent') {
                $produit_id = null;
            }
            $sql = "INSERT INTO don_BNGRC 
                    (donateur, type_don, produit_id, description, quantite_totale, unite, date_don, protege) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW(), 0)";
            $stmt = $this->db->prepare($sql);
            $params = [$donateur, $type_don, $produit_id, $description, $quantite, $unite];
            $result = $stmt->execute($params);
            if ($result) {
                $don_id = $this->db->lastInsertId();
                if ($type_don !== 'argent' && $produit_id) {
                    $check = $this->db->prepare("SELECT id FROM produit_BNGRC WHERE id = ?");
                    $check->execute([$produit_id]);
                    if ($check->rowCount() > 0) {
                        $stmt = $this->db->prepare("UPDATE produit_BNGRC SET stock_actuel = stock_actuel + ? WHERE id = ?");
                        $stmt->execute([$quantite, $produit_id]);
                        $stmt = $this->db->prepare("
                            INSERT INTO mouvement_stock_BNGRC 
                            (produit_id, type_mouvement, quantite, source_type, source_id, date_mouvement)
                            VALUES (?, 'entree', ?, 'don', ?, NOW())
                        ");
                        $stmt->execute([$produit_id, $quantite, $don_id]);
                    }
                }
                return $don_id;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erreur PDO dans create don: " . $e->getMessage());
            throw new Exception("Erreur base de données: " . $e->getMessage());
        }
    }
    public function getStockTotal($type_don) {
        if ($type_don === 'argent') {
            $stmt = $this->db->prepare("
                SELECT SUM(quantite_totale) as total 
                FROM don_BNGRC 
                WHERE type_don = ?
            ");
            $stmt->execute([$type_don]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } else {
            $categorie_id = ($type_don === 'nature') ? 1 : 2;
            $stmt = $this->db->prepare("
                SELECT SUM(stock_actuel) as total 
                FROM produit_BNGRC 
                WHERE categorie_id = ?
            ");
            $stmt->execute([$categorie_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        }
    }
    public function getDonsNonUtilises() {
        $stmt = $this->db->query("
            SELECT d.*,
                   COALESCE(SUM(a.quantite_attribuee), 0) as total_attribue,
                   (d.quantite_totale - COALESCE(SUM(a.quantite_attribuee), 0)) as reste_disponible
            FROM don_BNGRC d
            LEFT JOIN attribution_BNGRC a ON d.id = a.don_id
            GROUP BY d.id
            HAVING reste_disponible > 0
            ORDER BY reste_disponible DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function attribuer($besoin_id, $don_id, $quantite) {
        $stmt = $this->db->prepare("
            INSERT INTO attribution_BNGRC 
            (besoin_id, don_id, quantite_attribuee, date_attribution) 
            VALUES (?, ?, ?, NOW())
        ");
        return $stmt->execute([$besoin_id, $don_id, $quantite]);
    }
    public function getBesoinsNonSatisfaits() {
        $besoinModel = new BesoinModel();
        return $besoinModel->getNonSatisfaits();
    }
    public function getDonsRecents($limit) {
        $stmt = $this->db->prepare("
            SELECT d.*, p.nom_produit
            FROM don_BNGRC d
            LEFT JOIN produit_BNGRC p ON d.produit_id = p.id
            ORDER BY d.date_don DESC 
            LIMIT ?
        ");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTotalDons() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM don_BNGRC");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM don_BNGRC WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getStatsParTypeDonateur() {
        $stmt = $this->db->query("
            SELECT 
                CASE 
                    WHEN donateur LIKE '%(%)%' OR donateur IN ('Banque Mondiale', 'UNICEF', 'Croix-Rouge Internationale', 'PNUD', 'Médecins Sans Frontières', 'TotalEnergies', 'Air France', 'Société Générale', 'Orange Madagascar', 'Airtel Madagascar', 'Pierre Dubois (France)', 'Maria Schmidt (Allemagne)', 'John Smith (USA)', 'Chen Wei (Chine)', 'Maria Garcia (Espagne)') THEN 'International'
                    ELSE 'National'
                END as type_donateur,
                COUNT(*) as nombre_dons,
                SUM(quantite_totale) as quantite_totale
            FROM don_BNGRC
            GROUP BY type_donateur
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTopDonateurs($limit = 5) {
        $stmt = $this->db->prepare("
            SELECT donateur, SUM(quantite_totale) as total_donne
            FROM don_BNGRC
            GROUP BY donateur
            ORDER BY total_donne DESC
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
            WHERE c.nom_categorie IN ('nature', 'materiel')
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
            WHERE p.categorie_id = ? AND c.nom_categorie IN ('nature', 'materiel')
            ORDER BY p.nom_produit
        ");
        $stmt->execute([$categorie_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getPrixProduit($produit_id) {
        $stmt = $this->db->prepare("
            SELECT prix_unitaire_reference, unite_mesure FROM produit_BNGRC
            WHERE id = ?
        ");
        $stmt->execute([$produit_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?? ['prix_unitaire_reference' => 0, 'unite_mesure' => ''];
    }
    public function getProduitInfo($produit_id) {
        if (!$produit_id) return null;
        $stmt = $this->db->prepare("
            SELECT p.id, p.nom_produit, p.description, p.unite_mesure, 
                   p.prix_unitaire_reference, p.stock_actuel, p.seuil_alerte, p.categorie_id,
                   c.nom_categorie
            FROM produit_BNGRC p
            JOIN categorie_produit_BNGRC c ON p.categorie_id = c.id
            WHERE p.id = ?
        ");
        $stmt->execute([$produit_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getDonsProteges() {
        $stmt = $this->db->query("SELECT id FROM don_BNGRC WHERE protege = 1");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
