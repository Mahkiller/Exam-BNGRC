<?php
class DonModel extends Model {
    
    // Récupérer tous les dons
    public function getAll() {
        $stmt = $this->db->query("
            SELECT *, 
                   CASE 
                       WHEN donateur LIKE '%(%)%' OR donateur IN ('Banque Mondiale', 'UNICEF', 'Croix-Rouge Internationale', 'PNUD', 'Médecins Sans Frontières', 'TotalEnergies', 'Air France', 'Société Générale', 'Orange Madagascar', 'Airtel Madagascar') THEN 'International'
                       ELSE 'National/Malgache'
                   END as type_donateur
            FROM don_BNGRC 
            ORDER BY date_don DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Ajouter un don - MODIFIÉ pour retourner l'ID
    public function create($donateur, $type_don, $description, $quantite, $unite) {
        $stmt = $this->db->prepare("
            INSERT INTO don_BNGRC 
            (donateur, type_don, description, quantite_totale, unite, date_don) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $result = $stmt->execute([$donateur, $type_don, $description, $quantite, $unite]);
        
        if ($result) {
            return $this->db->lastInsertId(); // Retourne l'ID du don créé
        }
        return false;
    }
    
    // Récupérer le stock total par type
    public function getStockTotal($type_don) {
        $stmt = $this->db->prepare("
            SELECT SUM(quantite_totale) as total 
            FROM don_BNGRC 
            WHERE type_don = ?
        ");
        $stmt->execute([$type_don]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    // Récupérer les dons non utilisés (stock disponible) - CORRIGÉ pour inclure les achats
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
    
    // Attribuer un don à un besoin
    public function attribuer($besoin_id, $don_id, $quantite) {
        $stmt = $this->db->prepare("
            INSERT INTO attribution_BNGRC 
            (besoin_id, don_id, quantite_attribuee, date_attribution) 
            VALUES (?, ?, ?, NOW())
        ");
        return $stmt->execute([$besoin_id, $don_id, $quantite]);
    }
    
    // Récupérer les besoins non satisfaits (simplifié)
    public function getBesoinsNonSatisfaits() {
        $besoinModel = new BesoinModel();
        return $besoinModel->getNonSatisfaits();
    }
    
    // Récupérer les dons récents (VERSION CORRIGÉE)
    public function getDonsRecents($limit) {
        $stmt = $this->db->prepare("
            SELECT * FROM don_BNGRC 
            ORDER BY date_don DESC 
            LIMIT ?
        ");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Compter le total des dons
    public function getTotalDons() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM don_BNGRC");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Récupérer un don par ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM don_BNGRC WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Récupérer les stats par type de donateur
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
    
    // Récupérer le top donateurs
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
}