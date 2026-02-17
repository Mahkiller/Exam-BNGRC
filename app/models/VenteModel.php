<?php
class VenteModel extends Model {
    
    // Récupérer toutes les ventes
    public function getAll() {
        $stmt = $this->db->query("
            SELECT v.*, 
                   p.nom_produit,
                   p.unite_mesure,
                   c.nom_categorie,
                   d.donateur
            FROM vente_BNGRC v
            LEFT JOIN produit_BNGRC p ON v.produit_id = p.id
            LEFT JOIN categorie_produit_BNGRC c ON p.categorie_id = c.id
            LEFT JOIN don_BNGRC d ON v.don_id = d.id
            ORDER BY v.date_vente DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupérer une vente par ID
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT v.*, 
                   p.nom_produit,
                   p.unite_mesure,
                   c.nom_categorie
            FROM vente_BNGRC v
            LEFT JOIN produit_BNGRC p ON v.produit_id = p.id
            LEFT JOIN categorie_produit_BNGRC c ON p.categorie_id = c.id
            WHERE v.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Créer une vente
    public function create($don_id, $produit_id, $quantite_vendue, $prix_unitaire_reference, 
                          $prix_vente_unitaire, $montant_total, $taux_depreciation, $acheteur = null, $notes = null) {
        try {
            if ($don_id === null || $don_id === '') {
                $stmt = $this->db->prepare("
                    INSERT INTO vente_BNGRC 
                    (produit_id, quantite_vendue, prix_vente_unitaire, montant_total, 
                     taux_depreciation, acheteur, notes, date_vente) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                
                $result = $stmt->execute([
                    $produit_id, 
                    $quantite_vendue, 
                    $prix_vente_unitaire, 
                    $montant_total, 
                    $taux_depreciation, 
                    $acheteur, 
                    $notes
                ]);
            } else {
                $stmt = $this->db->prepare("
                    INSERT INTO vente_BNGRC 
                    (don_id, produit_id, quantite_vendue, prix_vente_unitaire, montant_total, 
                     taux_depreciation, acheteur, notes, date_vente) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                
                $result = $stmt->execute([
                    $don_id, 
                    $produit_id, 
                    $quantite_vendue, 
                    $prix_vente_unitaire, 
                    $montant_total, 
                    $taux_depreciation, 
                    $acheteur, 
                    $notes
                ]);
            }
            
            if ($result) {
                return $this->db->lastInsertId();
            }
            return false;
            
        } catch (PDOException $e) {
            error_log("Erreur PDO dans create vente: " . $e->getMessage());
            throw new Exception("Erreur base de données: " . $e->getMessage());
        }
    }
    
    // Récupérer les stocks disponibles pour la vente
    public function getStocksDisponibles() {
        try {
            $stmt = $this->db->query("
                SELECT p.id, 
                       p.nom_produit,
                       p.stock_actuel,
                       p.unite_mesure,
                       p.prix_unitaire_reference,
                       c.nom_categorie,
                       c.id as categorie_id
                FROM produit_BNGRC p
                LEFT JOIN categorie_produit_BNGRC c ON p.categorie_id = c.id
                WHERE p.stock_actuel > 0 
                AND c.nom_categorie != 'argent'
                ORDER BY c.nom_categorie, p.nom_produit
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getStocksDisponibles: " . $e->getMessage());
            return [];
        }
    }
    
    // Vérifier s'il y a un besoin non satisfait pour un produit
    public function hasUnmetNeed($produit_id) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM besoin_BNGRC b
            LEFT JOIN attribution_BNGRC a ON b.id = a.besoin_id
            WHERE b.produit_id = ? 
            AND (a.id IS NULL OR a.quantite_attribuee < b.quantite_demandee)
        ");
        $stmt->execute([$produit_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    // Réduire le stock après vente
    public function reduceStock($produit_id, $quantite) {
        $stmt = $this->db->prepare("
            UPDATE produit_BNGRC 
            SET stock_actuel = stock_actuel - ? 
            WHERE id = ?
        ");
        return $stmt->execute([$quantite, $produit_id]);
    }
    
    // Enregistrer un mouvement de stock pour la vente
    public function recordStockMovement($produit_id, $quantite, $vente_id) {
        $stmt = $this->db->prepare("
            INSERT INTO mouvement_stock_BNGRC 
            (produit_id, type_mouvement, quantite, source_type, source_id, date_mouvement)
            VALUES (?, 'sortie', ?, 'vente', ?, NOW())
        ");
        return $stmt->execute([$produit_id, $quantite, $vente_id]);
    }
    
    // Récupérer le total des ventes
    public function getTotalVentes() {
        $stmt = $this->db->query("SELECT COALESCE(SUM(montant_total), 0) as total FROM vente_BNGRC");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    // Récupérer le taux de change de la configuration
    public function getTauxChange() {
        try {
            $stmt = $this->db->query("SHOW TABLES LIKE 'configuration_BNGRC'");
            if ($stmt->rowCount() == 0) {
                return 0.10;
            }
            
            $stmt = $this->db->prepare("
                SELECT param_value 
                FROM configuration_BNGRC 
                WHERE param_key = 'taux_change_vente'
            ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return isset($result['param_value']) ? (float)$result['param_value'] / 100 : 0.10;
            
        } catch (Exception $e) {
            error_log("Erreur getTauxChange: " . $e->getMessage());
            return 0.10;
        }
    }
    
    public function getConfiguration() {
        try {
            $stmt = $this->db->query("SHOW TABLES LIKE 'configuration_BNGRC'");
            if ($stmt->rowCount() == 0) {
                return [
                    ['param_key' => 'taux_change_vente', 'param_value' => '10', 'description' => 'Taux de dépréciation vente'],
                    ['param_key' => 'frais_vente', 'param_value' => '0', 'description' => 'Frais administratifs'],
                    ['param_key' => 'tva_vente', 'param_value' => '0', 'description' => 'TVA']
                ];
            }
            
            $stmt = $this->db->query("
                SELECT param_key, param_value, description
                FROM configuration_BNGRC
                ORDER BY param_key
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Erreur getConfiguration: " . $e->getMessage());
            return [];
        }
    }
    
    // Mettre à jour un paramètre de configuration
    public function updateConfiguration($param_key, $param_value) {
        $stmt = $this->db->prepare("
            UPDATE configuration_BNGRC 
            SET param_value = ?, date_modification = NOW()
            WHERE param_key = ?
        ");
        return $stmt->execute([$param_value, $param_key]);
    }
    
    // Récupérer les statistiques de ventes
    public function getSalesStats() {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total_ventes,
                COALESCE(SUM(quantite_vendue), 0) as quantite_totale_vendue,
                COALESCE(SUM(montant_total), 0) as montant_total_ventes,
                COALESCE(AVG(taux_depreciation), 0) as taux_moyen
            FROM vente_BNGRC
        ");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Récupérer les ventes par catégorie
    public function getSalesByCategory() {
        $stmt = $this->db->query("
            SELECT 
                COALESCE(c.nom_categorie, 'Non catégorisé') as nom_categorie,
                COUNT(v.id) as nombre_ventes,
                COALESCE(SUM(v.quantite_vendue), 0) as quantite_totale,
                COALESCE(SUM(v.montant_total), 0) as montant_total
            FROM vente_BNGRC v
            LEFT JOIN produit_BNGRC p ON v.produit_id = p.id
            LEFT JOIN categorie_produit_BNGRC c ON p.categorie_id = c.id
            GROUP BY c.id, c.nom_categorie
            ORDER BY montant_total DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupérer les besoins actifs pour un produit
    public function getActiveNeedsForProduct($produit_id) {
        $stmt = $this->db->prepare("
            SELECT b.id, 
                   b.description,
                   b.quantite_demandee,
                   b.niveau_urgence,
                   v.nom_ville,
                   COALESCE(SUM(a.quantite_attribuee), 0) as quantite_attribuee
            FROM besoin_BNGRC b
            LEFT JOIN attribution_BNGRC a ON b.id = a.besoin_id
            LEFT JOIN ville_BNGRC v ON b.ville_id = v.id
            WHERE b.produit_id = ?
            GROUP BY b.id
            HAVING quantite_attribuee < b.quantite_demandee
        ");
        $stmt->execute([$produit_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}