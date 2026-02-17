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

-- ============================================
-- REQUÊTES DE VÉRIFICATION
-- ============================================

-- 1. Voir tous les produits avec leur stock
SELECT 
    p.id,
    c.nom_categorie,
    p.nom_produit,
    p.unite_mesure,
    p.stock_actuel,
    p.prix_unitaire_reference,
    p.seuil_alerte,
    CASE 
        WHEN p.stock_actuel <= p.seuil_alerte THEN '⚠️ ALERTE STOCK FAIBLE'
        ELSE 'OK'
    END as statut_stock
FROM produit_BNGRC p
JOIN categorie_produit_BNGRC c ON p.categorie_id = c.id
ORDER BY c.nom_categorie, p.nom_produit;

-- 2. Voir les besoins avec les produits
SELECT 
    b.id,
    v.nom_ville,
    p.nom_produit,
    b.type_besoin,
    b.quantite_demandee,
    b.unite,
    b.niveau_urgence
FROM besoin_BNGRC b
JOIN ville_BNGRC v ON b.ville_id = v.id
LEFT JOIN produit_BNGRC p ON b.produit_id = p.id
ORDER BY FIELD(b.niveau_urgence, 'critique', 'urgent', 'modere', 'faible'), v.nom_ville;

-- 3. Voir les dons par type
SELECT 
    type_don,
    COUNT(*) as nombre_dons,
    SUM(quantite_totale) as total
FROM don_BNGRC
GROUP BY type_don;

-- 4. Voir toutes les attributions avec détails
SELECT 
    a.id,
    v.nom_ville,
    p.nom_produit,
    b.description as besoin_description,
    d.donateur,
    a.quantite_attribuee,
    b.unite,
    a.date_attribution
FROM attribution_BNGRC a
JOIN besoin_BNGRC b ON a.besoin_id = b.id
JOIN ville_BNGRC v ON b.ville_id = v.id
LEFT JOIN produit_BNGRC p ON b.produit_id = p.id
JOIN don_BNGRC d ON a.don_id = d.id
ORDER BY a.date_attribution DESC;

-- 5. Compter le nombre total d'enregistrements
SELECT 'Villes' as table_name, COUNT(*) as total FROM ville_BNGRC
UNION ALL
SELECT 'Catégories', COUNT(*) FROM categorie_produit_BNGRC
UNION ALL
SELECT 'Produits', COUNT(*) FROM produit_BNGRC
UNION ALL
SELECT 'Besoins', COUNT(*) FROM besoin_BNGRC
UNION ALL
SELECT 'Dons', COUNT(*) FROM don_BNGRC
UNION ALL
SELECT 'Attributions', COUNT(*) FROM attribution_BNGRC
UNION ALL
SELECT 'Prix unitaires', COUNT(*) FROM prix_unitaire_BNGRC
UNION ALL
SELECT 'Achats', COUNT(*) FROM achat_BNGRC
UNION ALL
SELECT 'Mouvements stock', COUNT(*) FROM mouvement_stock_BNGRC;

-- 6. Vérifier les besoins par niveau d'urgence
SELECT 
    niveau_urgence, 
    COUNT(*) as nombre_besoins,
    SUM(quantite_demandee) as quantite_totale
FROM besoin_BNGRC
GROUP BY niveau_urgence
ORDER BY FIELD(niveau_urgence, 'critique', 'urgent', 'modere', 'faible');

-- 7. Vérifier les dons par type
SELECT 
    type_don, 
    COUNT(*) as nombre_dons, 
    SUM(quantite_totale) as quantite_totale
FROM don_BNGRC
GROUP BY type_don;

-- 8. Vérifier les besoins par ville
SELECT 
    v.nom_ville, 
    COUNT(b.id) as nombre_besoins,
    SUM(b.quantite_demandee) as total_demande
FROM ville_BNGRC v
LEFT JOIN besoin_BNGRC b ON v.id = b.ville_id
GROUP BY v.nom_ville
ORDER BY nombre_besoins DESC;

-- 9. Vérifier les dons par type de donateur
SELECT 
    CASE 
        WHEN donateur LIKE '%(%)%' OR donateur IN ('Banque Mondiale', 'UNICEF', 'Croix-Rouge Internationale', 'PNUD', 'Médecins Sans Frontières', 'TotalEnergies', 'Air France', 'Société Générale') THEN 'International'
        ELSE 'National'
    END as type_donateur,
    COUNT(*) as nombre_dons,
    SUM(quantite_totale) as quantite_totale
FROM don_BNGRC
GROUP BY type_donateur;

-- 10. Vérifier les attributions par ville
SELECT 
    v.nom_ville, 
    COUNT(a.id) as nombre_attributions, 
    SUM(a.quantite_attribuee) as total_distribue
FROM attribution_BNGRC a
JOIN besoin_BNGRC b ON a.besoin_id = b.id
JOIN ville_BNGRC v ON b.ville_id = v.id
GROUP BY v.nom_ville
ORDER BY total_distribue DESC;

-- 11. Vérifier les besoins non encore attribués
SELECT 
    b.id, 
    v.nom_ville, 
    p.nom_produit,
    b.description, 
    b.quantite_demandee, 
    b.unite,
    COALESCE(SUM(a.quantite_attribuee), 0) as total_attribue,
    (b.quantite_demandee - COALESCE(SUM(a.quantite_attribuee), 0)) as reste_a_distribuer
FROM besoin_BNGRC b
JOIN ville_BNGRC v ON b.ville_id = v.id
LEFT JOIN produit_BNGRC p ON b.produit_id = p.id
LEFT JOIN attribution_BNGRC a ON b.id = a.besoin_id
GROUP BY b.id, v.nom_ville, p.nom_produit, b.description, b.quantite_demandee, b.unite
HAVING reste_a_distribuer > 0
ORDER BY reste_a_distribuer DESC;

-- 12. Vérifier les dons non encore utilisés
SELECT 
    d.id, 
    d.donateur, 
    d.type_don,
    p.nom_produit,
    d.description, 
    d.quantite_totale,
    d.unite,
    COALESCE(SUM(a.quantite_attribuee), 0) as total_attribue,
    (d.quantite_totale - COALESCE(SUM(a.quantite_attribuee), 0)) as reste_disponible
FROM don_BNGRC d
LEFT JOIN produit_BNGRC p ON d.produit_id = p.id
LEFT JOIN attribution_BNGRC a ON d.id = a.don_id
GROUP BY d.id, d.donateur, d.type_don, p.nom_produit, d.description, d.quantite_totale, d.unite
HAVING reste_disponible > 0
ORDER BY reste_disponible DESC;

-- 13. Vérifier les besoins CRITIQUES non satisfaits
SELECT 
    v.nom_ville, 
    p.nom_produit,
    b.description, 
    b.quantite_demandee,
    COALESCE(SUM(a.quantite_attribuee), 0) as deja_distribue,
    (b.quantite_demandee - COALESCE(SUM(a.quantite_attribuee), 0)) as manque
FROM besoin_BNGRC b
JOIN ville_BNGRC v ON b.ville_id = v.id
LEFT JOIN produit_BNGRC p ON b.produit_id = p.id
LEFT JOIN attribution_BNGRC a ON b.id = a.besoin_id
WHERE b.niveau_urgence = 'critique'
GROUP BY b.id, v.nom_ville, p.nom_produit, b.description, b.quantite_demandee
HAVING manque > 0
ORDER BY manque DESC;

-- 14. Statistiques des urgences
SELECT 
    SUM(CASE WHEN niveau_urgence = 'critique' THEN 1 ELSE 0 END) as critique_count,
    SUM(CASE WHEN niveau_urgence = 'urgent' THEN 1 ELSE 0 END) as urgent_count,
    SUM(CASE WHEN niveau_urgence = 'modere' THEN 1 ELSE 0 END) as modere_count,
    SUM(CASE WHEN niveau_urgence = 'faible' THEN 1 ELSE 0 END) as faible_count,
    COUNT(*) as total_besoins
FROM besoin_BNGRC;

-- 15. Top 10 des plus grands donateurs
SELECT 
    donateur, 
    type_don,
    SUM(quantite_totale) as total_donne
FROM don_BNGRC
GROUP BY donateur, type_don
ORDER BY total_donne DESC
LIMIT 10;

-- 16. Vérifier les besoins par type et urgence
SELECT 
    type_besoin, 
    niveau_urgence, 
    COUNT(*) as nombre, 
    SUM(quantite_demandee) as quantite_totale
FROM besoin_BNGRC
GROUP BY type_besoin, niveau_urgence
ORDER BY type_besoin, FIELD(niveau_urgence, 'critique', 'urgent', 'modere', 'faible');

-- 17. Les 10 dernières attributions
SELECT 
    v.nom_ville,
    p.nom_produit,
    b.description as besoin_description,
    b.niveau_urgence,
    d.donateur,
    a.quantite_attribuee,
    b.unite,
    a.date_attribution
FROM attribution_BNGRC a
JOIN besoin_BNGRC b ON a.besoin_id = b.id
JOIN ville_BNGRC v ON b.ville_id = v.id
LEFT JOIN produit_BNGRC p ON b.produit_id = p.id
JOIN don_BNGRC d ON a.don_id = d.id
ORDER BY a.date_attribution DESC
LIMIT 10;

-- 18. Statistiques générales complètes
SELECT 
    (SELECT COUNT(*) FROM ville_BNGRC) as total_villes,
    (SELECT COUNT(*) FROM besoin_BNGRC) as total_besoins,
    (SELECT COUNT(*) FROM don_BNGRC) as total_dons,
    (SELECT COUNT(*) FROM attribution_BNGRC) as total_attributions,
    (SELECT COUNT(*) FROM produit_BNGRC) as total_produits,
    (SELECT SUM(quantite_demandee) FROM besoin_BNGRC WHERE type_besoin = 'argent') as total_argent_demande,
    (SELECT SUM(quantite_totale) FROM don_BNGRC WHERE type_don = 'argent') as total_argent_donne,
    (SELECT SUM(quantite_totale) FROM don_BNGRC WHERE type_don = 'nature') as total_nature_donne,
    (SELECT SUM(quantite_totale) FROM don_BNGRC WHERE type_don = 'materiaux') as total_materiaux_donne;

-- 19. Vérifier tous les prix unitaires avec nom produit
SELECT 
    pu.id,
    p.nom_produit,
    p.unite_mesure,
    pu.prix_unitaire,
    FORMAT(pu.prix_unitaire, 0) as prix_formate,
    pu.date_validite,
    pu.date_creation
FROM prix_unitaire_BNGRC pu
JOIN produit_BNGRC p ON pu.produit_id = p.id
ORDER BY p.nom_produit;

-- 20. Vérifier les mouvements de stock
SELECT 
    ms.id,
    p.nom_produit,
    ms.type_mouvement,
    ms.quantite,
    ms.source_type,
    ms.source_id,
    ms.date_mouvement,
    ms.notes
FROM mouvement_stock_BNGRC ms
JOIN produit_BNGRC p ON ms.produit_id = p.id
ORDER BY ms.date_mouvement DESC;

-- 21. État des stocks (avec alerte)
SELECT 
    p.nom_produit,
    c.nom_categorie,
    p.stock_actuel,
    p.unite_mesure,
    p.seuil_alerte,
    p.prix_unitaire_reference,
    (p.stock_actuel * p.prix_unitaire_reference) as valeur_stock,
    CASE 
        WHEN p.stock_actuel <= 0 THEN '🟥 RUPTURE'
        WHEN p.stock_actuel <= p.seuil_alerte THEN '🟧 ALERTE'
        ELSE '🟩 OK'
    END as etat_stock
FROM produit_BNGRC p
JOIN categorie_produit_BNGRC c ON p.categorie_id = c.id
ORDER BY etat_stock, p.stock_actuel ASC;

-- 22. Vue d'ensemble par ville (tableau de bord)
SELECT 
    v.nom_ville,
    COUNT(DISTINCT b.id) as total_besoins,
    COUNT(DISTINCT CASE WHEN b.niveau_urgence = 'critique' THEN b.id END) as besoins_critiques,
    COUNT(DISTINCT a.id) as attributions_recues,
    COUNT(DISTINCT ach.id) as achats_effectues,
    COALESCE(SUM(a.quantite_attribuee), 0) as total_recu,
    COALESCE(SUM(ach.quantite), 0) as total_achete
FROM ville_BNGRC v
LEFT JOIN besoin_BNGRC b ON v.id = b.ville_id
LEFT JOIN attribution_BNGRC a ON b.id = a.besoin_id
LEFT JOIN achat_BNGRC ach ON b.id = ach.besoin_id
GROUP BY v.id, v.nom_ville
ORDER BY besoins_critiques DESC;