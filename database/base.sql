-- Création de la base de données
CREATE DATABASE IF NOT EXISTS ETU004082_4338_4433;
USE ETU004082_4338_4433;

-- Supprimer toutes les tables (dans l'ordre à cause des clés étrangères)
DROP TABLE IF EXISTS mouvement_stock_BNGRC;
DROP TABLE IF EXISTS achat_BNGRC;
DROP TABLE IF EXISTS attribution_BNGRC;
DROP TABLE IF EXISTS besoin_BNGRC;
DROP TABLE IF EXISTS don_BNGRC;
DROP TABLE IF EXISTS produit_BNGRC;
DROP TABLE IF EXISTS categorie_produit_BNGRC;
DROP TABLE IF EXISTS prix_unitaire_BNGRC;
DROP TABLE IF EXISTS ville_BNGRC;

-- ============================================
-- 1. TABLE DES VILLES
-- ============================================
CREATE TABLE ville_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom_ville VARCHAR(100) NOT NULL,
    region VARCHAR(100) NOT NULL,
    image_path VARCHAR(255) NULL -- Chemin vers l'image de la ville
);

-- ============================================
-- 2. TABLE DES CATEGORIES DE PRODUITS
-- ============================================
CREATE TABLE categorie_produit_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom_categorie VARCHAR(50) NOT NULL UNIQUE, -- 'nature', 'materiaux', 'argent'
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
    unite_mesure VARCHAR(20) NOT NULL, -- 'kg', 'litre', 'piece', 'sac', etc.
    prix_unitaire_reference DECIMAL(15,2), -- Prix en Ariary
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
    produit_id INT NULL, -- NULL si c'est un besoin en argent
    type_besoin VARCHAR(50) NOT NULL, -- 'nature', 'materiaux', 'argent'
    description VARCHAR(255) NOT NULL,
    quantite_demandee DECIMAL(15,2) NOT NULL,
    unite VARCHAR(50),
    niveau_urgence VARCHAR(20) NOT NULL, -- 'critique', 'urgent', 'modere', 'faible'
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
    type_don VARCHAR(50) NOT NULL, -- 'argent', 'nature', 'materiaux'
    produit_id INT NULL, -- NULL si don en argent ou don non spécifique
    description VARCHAR(255) NOT NULL,
    quantite_totale DECIMAL(15,2) NOT NULL,
    unite VARCHAR(50),
    date_don DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produit_id) REFERENCES produit_BNGRC(id)
);

-- ============================================
-- 6. TABLE DES ATTRIBUTIONS (distribution directe)
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
-- 7. TABLE DES PRIX UNITAIRES (catalogue)
-- ============================================
CREATE TABLE prix_unitaire_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    produit_id INT NOT NULL,
    prix_unitaire DECIMAL(15,2) NOT NULL, -- en Ariary
    date_validite DATE DEFAULT NULL, -- NULL = prix courant
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produit_id) REFERENCES produit_BNGRC(id)
);

-- ============================================
-- 8. TABLE DES ACHATS (avec dons en argent)
-- ============================================
CREATE TABLE achat_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    don_id INT NOT NULL, -- Le don en argent qui finance
    besoin_id INT NOT NULL, -- Le besoin auquel l'achat est destiné
    produit_id INT NOT NULL, -- Le produit acheté
    quantite DECIMAL(15,2) NOT NULL,
    prix_unitaire_achat DECIMAL(15,2) NOT NULL,
    montant_total DECIMAL(15,2) NOT NULL, -- quantite * prix_unitaire_achat
    date_achat DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (don_id) REFERENCES don_BNGRC(id),
    FOREIGN KEY (besoin_id) REFERENCES besoin_BNGRC(id),
    FOREIGN KEY (produit_id) REFERENCES produit_BNGRC(id)
);

-- ============================================
-- 9. TABLE DES MOUVEMENTS DE STOCK
-- ============================================
CREATE TABLE mouvement_stock_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    produit_id INT NOT NULL,
    type_mouvement ENUM('entree', 'sortie') NOT NULL,
    quantite DECIMAL(15,2) NOT NULL,
    source_type ENUM('don', 'achat', 'attribution') NOT NULL,
    source_id INT NOT NULL,
    date_mouvement DATETIME DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    FOREIGN KEY (produit_id) REFERENCES produit_BNGRC(id)
);

-- ============================================
-- INSERTION DES DONNÉES DE BASE
-- ============================================

-- 1. Insertion des villes
INSERT INTO ville_BNGRC (nom_ville, region) VALUES
('Antananarivo', 'Analamanga'),
('Toamasina', 'Atsinanana'),
('Antsiranana', 'Diana'),
('Mahajanga', 'Boeny'),
('Fianarantsoa', 'Haute Matsiatra'),
('Toliara', 'Atsimo Andrefana'),
('Antsirabe', 'Vakinankaratra'),
('Morondava', 'Menabe'),
('Manakara', 'Vatovavy'),
('Sambava', 'Sava');

-- 2. Insertion des catégories
INSERT INTO categorie_produit_BNGRC (nom_categorie, description) VALUES
('nature', 'Produits alimentaires et denrées'),
('materiaux', 'Matériaux de construction et équipements'),
('argent', 'Dons financiers');

-- 3. Insertion des produits
INSERT INTO produit_BNGRC (categorie_id, nom_produit, unite_mesure, prix_unitaire_reference, stock_actuel, seuil_alerte) VALUES
-- Nature (catégorie 1)
(1, 'Riz', 'kg', 2500, 23500, 1000),
(1, 'Huile végétale', 'litre', 6000, 5000, 200),
(1, 'Eau potable', 'litre', 500, 50000, 5000),
(1, 'Sucre', 'kg', 3500, 2000, 300),
(1, 'Lait en poudre', 'kg', 12000, 500, 100),
(1, 'Poisson séché', 'kg', 8000, 800, 100),
(1, 'Pommes de terre', 'kg', 2000, 8000, 200),
(1, 'Fruits', 'kg', 3000, 3500, 100),
(1, 'Vanille', 'kg', 50000, 45, 10),
(1, 'Biscuits', 'kg', 4000, 1500, 100),
(1, 'Farine', 'kg', 2500, 5000, 200),
(1, 'Sel', 'kg', 1000, 2000, 100),

-- Matériaux (catégorie 2)
(2, 'Tôles', 'plaque', 25000, 150, 50),
(2, 'Clous', 'kg', 5000, 2500, 100),
(2, 'Ciment', 'sac', 28000, 800, 50),
(2, 'Bâches', 'piece', 15000, 400, 50),
(2, 'Bois', 'm3', 300000, 75, 10),
(2, 'Outils', 'lot', 25000, 60, 20),
(2, 'Poteaux électriques', 'piece', 150000, 35, 5),
(2, 'Sacs de jute', 'piece', 500, 5000, 100),
(2, 'Cordes', 'rouleau', 8000, 300, 20),
(2, 'Vis', 'kg', 4000, 1200, 50),
(2, 'Peinture', 'bidon', 35000, 150, 10),
(2, 'Ustensiles cuisine', 'lot', 15000, 200, 15),

-- Argent (catégorie 3)
(3, 'Argent', 'Ariary', 1, 190000000, 0);

-- 4. Insertion des prix unitaires (catalogue)
INSERT INTO prix_unitaire_BNGRC (produit_id, prix_unitaire)
SELECT id, prix_unitaire_reference FROM produit_BNGRC WHERE prix_unitaire_reference IS NOT NULL;

-- 5. Insertion des besoins
INSERT INTO besoin_BNGRC (ville_id, produit_id, type_besoin, description, quantite_demandee, unite, niveau_urgence, date_besoin) VALUES
-- Antananarivo
(1, 1, 'nature', 'Riz', 5000, 'kg', 'critique', '2026-02-10 08:30:00'),
(1, 2, 'nature', 'Huile végétale', 800, 'litre', 'urgent', '2026-02-12 09:15:00'),
(1, 13, 'materiaux', 'Tôles', 200, 'plaques', 'critique', '2026-02-14 10:00:00'),
(1, NULL, 'argent', 'Fonds pour reconstruction', 50000000, 'Ariary', 'critique', '2026-02-15 11:30:00'),

-- Toamasina
(2, 1, 'nature', 'Riz', 3000, 'kg', 'critique', '2026-02-11 14:20:00'),
(2, 14, 'materiaux', 'Clous', 500, 'kg', 'urgent', '2026-02-13 15:45:00'),
(2, 3, 'nature', 'Eau potable', 10000, 'litre', 'critique', '2026-02-14 16:30:00'),

-- Antsiranana
(3, 1, 'nature', 'Riz', 2000, 'kg', 'urgent', '2026-02-12 07:45:00'),
(3, 15, 'materiaux', 'Ciment', 100, 'sacs', 'critique', '2026-02-15 09:20:00'),
(3, NULL, 'argent', 'Aide financière', 20000000, 'Ariary', 'faible', '2026-02-16 10:10:00');

-- 6. Insertion des dons
INSERT INTO don_BNGRC (donateur, type_don, produit_id, description, quantite_totale, unite, date_don) VALUES
-- Dons en argent
('Banque Mondiale', 'argent', NULL, 'Fonds d\'urgence cyclone', 100000000, 'Ariary', '2026-02-05 09:00:00'),
('PNUD', 'argent', NULL, 'Aide au développement', 75000000, 'Ariary', '2026-02-08 14:20:00'),
('Fitia', 'argent', NULL, 'Aide financière', 15000000, 'Ariary', '2026-02-12 10:55:00'),

-- Dons en nature (avec produit_id)
('Croix-Rouge Internationale', 'nature', 1, 'Riz', 20000, 'kg', '2026-02-07 11:45:00'),
('Miarakapa', 'nature', 1, 'Riz', 5000, 'kg', '2026-02-10 08:15:00'),
('Tiko', 'nature', 2, 'Huile', 2000, 'litre', '2026-02-10 07:30:00');

-- 7. Insertion des attributions
INSERT INTO attribution_BNGRC (besoin_id, don_id, quantite_attribuee, date_attribution) VALUES
(1, 4, 2000, '2026-02-16 09:00:00'), -- Riz Croix-Rouge vers Antananarivo
(1, 5, 1500, '2026-02-16 10:15:00'); -- Riz Miarakapa vers Antananarivo

-- Table pour enregistrer les ventes
CREATE TABLE vente_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    don_id INT NOT NULL,                -- Le don matériel vendu
    produit_id INT NOT NULL,             -- Le produit vendu
    quantite_vendue DECIMAL(15,2) NOT NULL,
    prix_vente_unitaire DECIMAL(15,2) NOT NULL, -- Prix après dépréciation
    montant_total DECIMAL(15,2) NOT NULL,
    taux_depreciation DECIMAL(5,2) NOT NULL, -- Le taux appliqué (ex: 0.10)
    date_vente DATETIME DEFAULT CURRENT_TIMESTAMP,
    acheteur VARCHAR(255),                -- Optionnel
    notes TEXT,
    FOREIGN KEY (don_id) REFERENCES don_BNGRC(id),
    FOREIGN KEY (produit_id) REFERENCES produit_BNGRC(id)
);

-- Modifier la table mouvement_stock_BNGRC pour inclure les ventes
ALTER TABLE mouvement_stock_BNGRC 
MODIFY COLUMN source_type ENUM('don', 'achat', 'attribution', 'vente') NOT NULL;

CREATE TABLE IF NOT EXISTS configuration_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    param_key VARCHAR(50) NOT NULL UNIQUE,
    param_value VARCHAR(255) NOT NULL,
    description TEXT,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insérer les paramètres par défaut
INSERT INTO configuration_BNGRC (param_key, param_value, description) VALUES
('taux_change_vente', '10', 'Taux de dépréciation pour la vente des dons (en %)'),
('frais_vente', '0', 'Frais administratifs sur les ventes (en %)'),
('tva_vente', '0', 'TVA applicable sur les ventes (en %)');