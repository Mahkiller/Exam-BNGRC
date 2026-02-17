-- Création de la base de données
CREATE DATABASE IF NOT EXISTS ETU004082_4338_4433;
USE ETU004082_4338_4433;

-- Supprimer toutes les tables (dans l'ordre à cause des clés étrangères)
DROP TABLE IF EXISTS mouvement_stock_BNGRC;
DROP TABLE IF EXISTS achat_BNGRC;
DROP TABLE IF EXISTS vente_BNGRC;
DROP TABLE IF EXISTS attribution_BNGRC;
DROP TABLE IF EXISTS besoin_BNGRC;
DROP TABLE IF EXISTS don_BNGRC;
DROP TABLE IF EXISTS produit_BNGRC;
DROP TABLE IF EXISTS categorie_produit_BNGRC;
DROP TABLE IF EXISTS prix_unitaire_BNGRC;
DROP TABLE IF EXISTS ville_BNGRC;
DROP TABLE IF EXISTS configuration_BNGRC;

-- ============================================
-- 1. TABLE DES VILLES
-- ============================================
CREATE TABLE ville_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom_ville VARCHAR(100) NOT NULL,
    region VARCHAR(100) NOT NULL,
    image_path VARCHAR(255) NULL
);

-- Insertion des villes
INSERT INTO ville_BNGRC (nom_ville, region) VALUES
('Toamasina', 'Atsinanana'),
('Mananjary', 'Vatovavy'),
('Farafangana', 'Atsimo Atsinanana'),
('Nosy Be', 'Diana'),
('Morondava', 'Menabe');

-- ============================================
-- 2. TABLE DES CATEGORIES DE PRODUITS
-- ============================================
CREATE TABLE categorie_produit_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom_categorie VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

INSERT INTO categorie_produit_BNGRC (nom_categorie, description) VALUES
('nature', 'Produits alimentaires et denrées'),
('materiel', 'Matériaux de construction et équipements'),
('argent', 'Dons financiers');

-- ============================================
-- 3. TABLE DES PRODUITS (STOCK)
-- ============================================
CREATE TABLE produit_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    categorie_id INT NOT NULL,
    nom_produit VARCHAR(100) NOT NULL,
    description TEXT,
    unite_mesure VARCHAR(20) NOT NULL,
    prix_unitaire_reference DECIMAL(15,2),
    stock_actuel DECIMAL(15,2) DEFAULT 0,
    seuil_alerte DECIMAL(15,2) DEFAULT 0,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_mise_a_jour DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categorie_produit_BNGRC(id)
);

-- Insertion des produits
INSERT INTO produit_BNGRC (categorie_id, nom_produit, unite_mesure, prix_unitaire_reference, stock_actuel, seuil_alerte) VALUES
-- Nature (catégorie 1)
(1, 'Riz', 'kg', 3000, 0, 500),
(1, 'Eau', 'L', 1000, 0, 1000),
(1, 'Huile', 'L', 6000, 0, 200),
(1, 'Haricots', 'kg', 4000, 0, 100),

-- Matériel (catégorie 2)
(2, 'Tôle', 'plaque', 25000, 0, 50),
(2, 'Bâche', 'piece', 15000, 0, 80),
(2, 'Clous', 'kg', 8000, 0, 100),
(2, 'Bois', 'piece', 10000, 0, 50),
(2, 'Groupe électrogène', 'piece', 6750000, 0, 2),

-- Argent (catégorie 3)
(3, 'Argent', 'Ariary', 1, 0, 0);

-- ============================================
-- 4. TABLE DES PRIX UNITAIRES
-- ============================================
CREATE TABLE prix_unitaire_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    produit_id INT NOT NULL,
    prix_unitaire DECIMAL(15,2) NOT NULL,
    date_validite DATE DEFAULT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produit_id) REFERENCES produit_BNGRC(id)
);

INSERT INTO prix_unitaire_BNGRC (produit_id, prix_unitaire)
SELECT id, prix_unitaire_reference FROM produit_BNGRC WHERE prix_unitaire_reference IS NOT NULL;

-- ============================================
-- 5. TABLE DES BESOINS (ceux du tableau précédent)
-- ============================================
CREATE TABLE besoin_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ville_id INT NOT NULL,
    produit_id INT NULL,
    type_besoin VARCHAR(50) NOT NULL,
    description VARCHAR(255) NOT NULL,
    quantite_demandee DECIMAL(15,2) NOT NULL,
    unite VARCHAR(50),
    niveau_urgence VARCHAR(20) NOT NULL,
    date_besoin DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ville_id) REFERENCES ville_BNGRC(id),
    FOREIGN KEY (produit_id) REFERENCES produit_BNGRC(id)
);

-- Insertion des besoins
INSERT INTO besoin_BNGRC (ville_id, produit_id, type_besoin, description, quantite_demandee, unite, niveau_urgence, date_besoin) VALUES
-- Toamasina
(1, 1, 'nature', 'Riz', 800, 'kg', 'urgent', '2026-02-16 00:00:00'),
(1, 2, 'nature', 'Eau', 1500, 'L', 'modere', '2026-02-15 00:00:00'),
(1, 5, 'materiel', 'Tôle', 120, 'plaque', 'urgent', '2026-02-16 00:00:00'),
(1, 6, 'materiel', 'Bâche', 200, 'piece', 'modere', '2026-02-15 00:00:00'),
(1, 9, 'materiel', 'Groupe électrogène', 3, 'piece', 'urgent', '2026-02-15 00:00:00'),
(1, NULL, 'argent', 'Argent', 12000000, 'Ariary', 'modere', '2026-02-16 00:00:00'),

-- Mananjary
(2, 1, 'nature', 'Riz', 500, 'kg', 'urgent', '2026-02-15 00:00:00'),
(2, 3, 'nature', 'Huile', 120, 'L', 'urgent', '2026-02-16 00:00:00'),
(2, 5, 'materiel', 'Tôle', 80, 'plaque', 'modere', '2026-02-15 00:00:00'),
(2, 7, 'materiel', 'Clous', 60, 'kg', 'modere', '2026-02-16 00:00:00'),
(2, NULL, 'argent', 'Argent', 6000000, 'Ariary', 'faible', '2026-02-15 00:00:00'),

-- Farafangana
(3, 1, 'nature', 'Riz', 600, 'kg', 'urgent', '2026-02-16 00:00:00'),
(3, 2, 'nature', 'Eau', 1000, 'L', 'modere', '2026-02-15 00:00:00'),
(3, 6, 'materiel', 'Bâche', 150, 'piece', 'modere', '2026-02-16 00:00:00'),
(3, 8, 'materiel', 'Bois', 100, 'piece', 'urgent', '2026-02-15 00:00:00'),
(3, NULL, 'argent', 'Argent', 8000000, 'Ariary', 'modere', '2026-02-16 00:00:00'),

-- Nosy Be
(4, 1, 'nature', 'Riz', 300, 'kg', 'faible', '2026-02-15 00:00:00'),
(4, 4, 'nature', 'Haricots', 200, 'kg', 'modere', '2026-02-16 00:00:00'),
(4, 5, 'materiel', 'Tôle', 40, 'plaque', 'faible', '2026-02-15 00:00:00'),
(4, 7, 'materiel', 'Clous', 30, 'kg', 'faible', '2026-02-16 00:00:00'),
(4, NULL, 'argent', 'Argent', 4000000, 'Ariary', 'faible', '2026-02-15 00:00:00'),

-- Morondava
(5, 1, 'nature', 'Riz', 700, 'kg', 'urgent', '2026-02-16 00:00:00'),
(5, 2, 'nature', 'Eau', 1200, 'L', 'modere', '2026-02-15 00:00:00'),
(5, 6, 'materiel', 'Bâche', 180, 'piece', 'modere', '2026-02-16 00:00:00'),
(5, 8, 'materiel', 'Bois', 150, 'piece', 'urgent', '2026-02-15 00:00:00'),
(5, NULL, 'argent', 'Argent', 10000000, 'Ariary', 'urgent', '2026-02-16 00:00:00');

-- ============================================
-- 6. TABLE DES DONS (avec champ protege)
-- ============================================
CREATE TABLE don_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    donateur VARCHAR(200) NOT NULL,
    type_don VARCHAR(50) NOT NULL,
    produit_id INT NULL,
    description VARCHAR(255) NOT NULL,
    quantite_totale DECIMAL(15,2) NOT NULL,
    unite VARCHAR(50),
    date_don DATETIME DEFAULT CURRENT_TIMESTAMP,
    protege BOOLEAN DEFAULT FALSE, -- TRUE pour les dons de base
    FOREIGN KEY (produit_id) REFERENCES produit_BNGRC(id) ON DELETE SET NULL
);

-- Insertion des dons de base (avec protege = TRUE)
INSERT INTO don_BNGRC (donateur, type_don, produit_id, description, quantite_totale, unite, date_don, protege) VALUES
-- Dons en argent
('Donateur 1', 'argent', NULL, 'Argent', 5000000, 'Ariary', '2026-02-16 00:00:00', TRUE),
('Donateur 2', 'argent', NULL, 'Argent', 3000000, 'Ariary', '2026-02-16 00:00:00', TRUE),
('Donateur 3', 'argent', NULL, 'Argent', 4000000, 'Ariary', '2026-02-17 00:00:00', TRUE),
('Donateur 4', 'argent', NULL, 'Argent', 1500000, 'Ariary', '2026-02-17 00:00:00', TRUE),
('Donateur 5', 'argent', NULL, 'Argent', 6000000, 'Ariary', '2026-02-17 00:00:00', TRUE),
('Donateur 6', 'argent', NULL, 'Argent', 20000000, 'Ariary', '2026-02-19 00:00:00', TRUE),

-- Dons en nature
('Donateur 7', 'nature', 1, 'Riz', 400, 'kg', '2026-02-16 00:00:00', TRUE),
('Donateur 8', 'nature', 2, 'Eau', 600, 'L', '2026-02-16 00:00:00', TRUE),
('Donateur 9', 'nature', 4, 'Haricots', 100, 'kg', '2026-02-17 00:00:00', TRUE),
('Donateur 10', 'nature', 1, 'Riz', 2000, 'kg', '2026-02-18 00:00:00', TRUE),
('Donateur 11', 'nature', 2, 'Eau', 5000, 'L', '2026-02-18 00:00:00', TRUE),
('Donateur 12', 'nature', 4, 'Haricots', 88, 'kg', '2026-02-17 00:00:00', TRUE),

-- Dons en matériel
('Donateur 13', 'materiel', 5, 'Tôle', 50, 'plaque', '2026-02-17 00:00:00', TRUE),
('Donateur 14', 'materiel', 6, 'Bâche', 70, 'piece', '2026-02-17 00:00:00', TRUE),
('Donateur 15', 'materiel', 5, 'Tôle', 300, 'plaque', '2026-02-18 00:00:00', TRUE),
('Donateur 16', 'materiel', 6, 'Bâche', 500, 'piece', '2026-02-19 00:00:00', TRUE);

-- ============================================
-- 7. TABLE DES ATTRIBUTIONS
-- ============================================
CREATE TABLE attribution_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    besoin_id INT NOT NULL,
    don_id INT NOT NULL,
    quantite_attribuee DECIMAL(15,2) NOT NULL,
    date_attribution DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (besoin_id) REFERENCES besoin_BNGRC(id) ON DELETE CASCADE,
    FOREIGN KEY (don_id) REFERENCES don_BNGRC(id) ON DELETE CASCADE
);

-- ============================================
-- 8. TABLE DES ACHATS
-- ============================================
CREATE TABLE achat_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    don_id INT NOT NULL,
    besoin_id INT NOT NULL,
    produit_id INT NOT NULL,
    quantite DECIMAL(15,2) NOT NULL,
    prix_unitaire_achat DECIMAL(15,2) NOT NULL,
    montant_total DECIMAL(15,2) NOT NULL,
    date_achat DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (don_id) REFERENCES don_BNGRC(id) ON DELETE CASCADE,
    FOREIGN KEY (besoin_id) REFERENCES besoin_BNGRC(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produit_BNGRC(id) ON DELETE CASCADE
);

-- ============================================
-- 9. TABLE DES VENTES
-- ============================================
CREATE TABLE vente_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    don_id INT NULL,
    produit_id INT NOT NULL,
    quantite_vendue DECIMAL(15,2) NOT NULL,
    prix_vente_unitaire DECIMAL(15,2) NOT NULL,
    montant_total DECIMAL(15,2) NOT NULL,
    taux_depreciation DECIMAL(5,2) NOT NULL,
    date_vente DATETIME DEFAULT CURRENT_TIMESTAMP,
    acheteur VARCHAR(255),
    notes TEXT,
    FOREIGN KEY (don_id) REFERENCES don_BNGRC(id) ON DELETE SET NULL,
    FOREIGN KEY (produit_id) REFERENCES produit_BNGRC(id) ON DELETE CASCADE
);

-- ============================================
-- 10. TABLE DES MOUVEMENTS DE STOCK
-- ============================================
CREATE TABLE mouvement_stock_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    produit_id INT NOT NULL,
    type_mouvement ENUM('entree', 'sortie') NOT NULL,
    quantite DECIMAL(15,2) NOT NULL,
    source_type ENUM('don', 'achat', 'attribution', 'vente') NOT NULL,
    source_id INT NOT NULL,
    date_mouvement DATETIME DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    FOREIGN KEY (produit_id) REFERENCES produit_BNGRC(id) ON DELETE CASCADE
);

-- ============================================
-- 11. TABLE DE CONFIGURATION
-- ============================================
CREATE TABLE configuration_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    param_key VARCHAR(50) NOT NULL UNIQUE,
    param_value VARCHAR(255) NOT NULL,
    description TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO configuration_BNGRC (param_key, param_value, description) VALUES
('taux_change_vente', '10', 'Taux de dépréciation pour la vente des dons (en %)'),
('frais_vente', '0', 'Frais administratifs sur les ventes (en %)'),
('tva_vente', '0', 'TVA applicable sur les ventes (en %)');

-- ============================================
-- 12. MISE À JOUR DES STOCKS
-- ============================================
UPDATE produit_BNGRC SET stock_actuel = 5000 WHERE id = 1; -- Riz
UPDATE produit_BNGRC SET stock_actuel = 10000 WHERE id = 2; -- Eau
UPDATE produit_BNGRC SET stock_actuel = 2000 WHERE id = 3; -- Huile
UPDATE produit_BNGRC SET stock_actuel = 1000 WHERE id = 4; -- Haricots
UPDATE produit_BNGRC SET stock_actuel = 500 WHERE id = 5; -- Tôle
UPDATE produit_BNGRC SET stock_actuel = 800 WHERE id = 6; -- Bâche
UPDATE produit_BNGRC SET stock_actuel = 1000 WHERE id = 7; -- Clous
UPDATE produit_BNGRC SET stock_actuel = 500 WHERE id = 8; -- Bois
UPDATE produit_BNGRC SET stock_actuel = 10 WHERE id = 9; -- Groupe électrogène

-- Mettre à jour le stock d'argent à partir des dons
UPDATE produit_BNGRC SET stock_actuel = (
    SELECT SUM(quantite_totale) FROM don_BNGRC WHERE type_don = 'argent'
) WHERE nom_produit = 'Argent';