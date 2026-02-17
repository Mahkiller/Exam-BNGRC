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

-- ============================================
-- 2. TABLE DES CATEGORIES DE PRODUITS
-- ============================================
CREATE TABLE categorie_produit_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom_categorie VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

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

-- ============================================
-- 4. TABLE DES BESOINS
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

-- ============================================
-- 5. TABLE DES DONS
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
    FOREIGN KEY (produit_id) REFERENCES produit_BNGRC(id)
);

-- ============================================
-- 6. TABLE DES ATTRIBUTIONS
-- ============================================
CREATE TABLE attribution_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    besoin_id INT NOT NULL,
    don_id INT NOT NULL,
    quantite_attribuee DECIMAL(15,2) NOT NULL,
    date_attribution DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (besoin_id) REFERENCES besoin_BNGRC(id),
    FOREIGN KEY (don_id) REFERENCES don_BNGRC(id)
);

-- ============================================
-- 7. TABLE DES PRIX UNITAIRES
-- ============================================
CREATE TABLE prix_unitaire_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    produit_id INT NOT NULL,
    prix_unitaire DECIMAL(15,2) NOT NULL,
    date_validite DATE DEFAULT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produit_id) REFERENCES produit_BNGRC(id)
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
    FOREIGN KEY (don_id) REFERENCES don_BNGRC(id),
    FOREIGN KEY (besoin_id) REFERENCES besoin_BNGRC(id),
    FOREIGN KEY (produit_id) REFERENCES produit_BNGRC(id)
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
    FOREIGN KEY (don_id) REFERENCES don_BNGRC(id),
    FOREIGN KEY (produit_id) REFERENCES produit_BNGRC(id)
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
    FOREIGN KEY (produit_id) REFERENCES produit_BNGRC(id)
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

-- ============================================
-- INSERTION DES DONNÉES
-- ============================================

-- 1. Insertion des villes
INSERT INTO ville_BNGRC (nom_ville, region) VALUES
('Toamasina', 'Atsinanana'),
('Mananjary', 'Vatovavy'),
('Farafangana', 'Atsimo Atsinanana'),
('Nosy Be', 'Diana'),
('Morondava', 'Menabe');

-- 2. Insertion des catégories
INSERT INTO categorie_produit_BNGRC (nom_categorie, description) VALUES
('nature', 'Produits alimentaires et denrées'),
('materiel', 'Matériaux de construction et équipements'),
('argent', 'Dons financiers');

-- 3. Insertion des produits
INSERT INTO produit_BNGRC (categorie_id, nom_produit, unite_mesure, prix_unitaire_reference, stock_actuel, seuil_alerte) VALUES
-- Nature (catégorie 1)
(1, 'Riz', 'kg', 3000, 5000, 500),
(1, 'Eau', 'L', 1000, 10000, 1000),
(1, 'Huile', 'L', 6000, 2000, 200),
(1, 'Haricots', 'kg', 4000, 1000, 100),

-- Matériel (catégorie 2)
(2, 'Tôle', 'plaque', 25000, 500, 50),
(2, 'Bâche', 'piece', 15000, 800, 80),
(2, 'Clous', 'kg', 8000, 1000, 100),
(2, 'Bois', 'piece', 10000, 500, 50),
(2, 'Groupe électrogène', 'piece', 6750000, 10, 2),

-- Argent (catégorie 3)
(3, 'Argent', 'Ariary', 1, 50000000, 0);

-- 4. Insertion des prix unitaires
INSERT INTO prix_unitaire_BNGRC (produit_id, prix_unitaire)
SELECT id, prix_unitaire_reference FROM produit_BNGRC WHERE prix_unitaire_reference IS NOT NULL;

-- 5. Insertion de la configuration
INSERT INTO configuration_BNGRC (param_key, param_value, description) VALUES
('taux_change_vente', '10', 'Taux de dépréciation pour la vente des dons (en %)'),
('frais_vente', '0', 'Frais administratifs sur les ventes (en %)'),
('tva_vente', '0', 'TVA applicable sur les ventes (en %)');

-- 6. Insertion des besoins (d'après ton tableau)
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

-- 7. Insertion des dons (pour avoir du stock)
INSERT INTO don_BNGRC (donateur, type_don, produit_id, description, quantite_totale, unite, date_don) VALUES
-- Dons en nature
('Croix-Rouge', 'nature', 1, 'Riz', 5000, 'kg', '2026-02-10 10:00:00'),
('UNICEF', 'nature', 2, 'Eau', 10000, 'L', '2026-02-11 11:00:00'),
('PNUD', 'nature', 3, 'Huile', 2000, 'L', '2026-02-12 12:00:00'),
('ONG Miarakapa', 'nature', 4, 'Haricots', 1000, 'kg', '2026-02-13 13:00:00'),

-- Dons en matériel
('Banque Mondiale', 'materiel', 5, 'Tôles', 500, 'plaque', '2026-02-14 14:00:00'),
('UE', 'materiel', 6, 'Bâches', 800, 'piece', '2026-02-15 15:00:00'),
('JICA', 'materiel', 7, 'Clous', 1000, 'kg', '2026-02-16 16:00:00'),
('USAID', 'materiel', 8, 'Bois', 500, 'piece', '2026-02-17 17:00:00'),
('Coopération Suisse', 'materiel', 9, 'Groupes électrogènes', 10, 'piece', '2026-02-18 18:00:00'),

-- Dons en argent
('Banque Mondiale', 'argent', NULL, 'Fonds d\'urgence', 50000000, 'Ariary', '2026-02-19 19:00:00');

-- 8. Mettre à jour le stock initial
UPDATE produit_BNGRC SET stock_actuel = 5000 WHERE nom_produit = 'Riz';
UPDATE produit_BNGRC SET stock_actuel = 10000 WHERE nom_produit = 'Eau';
UPDATE produit_BNGRC SET stock_actuel = 2000 WHERE nom_produit = 'Huile';
UPDATE produit_BNGRC SET stock_actuel = 1000 WHERE nom_produit = 'Haricots';
UPDATE produit_BNGRC SET stock_actuel = 500 WHERE nom_produit = 'Tôle';
UPDATE produit_BNGRC SET stock_actuel = 800 WHERE nom_produit = 'Bâche';
UPDATE produit_BNGRC SET stock_actuel = 1000 WHERE nom_produit = 'Clous';
UPDATE produit_BNGRC SET stock_actuel = 500 WHERE nom_produit = 'Bois';
UPDATE produit_BNGRC SET stock_actuel = 10 WHERE nom_produit = 'Groupe électrogène';
UPDATE produit_BNGRC SET stock_actuel = 50000000 WHERE nom_produit = 'Argent';