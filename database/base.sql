-- Création de la base de données
CREATE DATABASE IF NOT EXISTS BNGRC;
USE BNGRC;

-- Supprimer les tables si elles existent (DROP propre)
DROP TABLE IF EXISTS attribution_BNGRC;
DROP TABLE IF EXISTS besoin_BNGRC;
DROP TABLE IF EXISTS don_BNGRC;
DROP TABLE IF EXISTS ville_BNGRC;

-- Table des villes
CREATE TABLE ville_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom_ville VARCHAR(100) NOT NULL,
    region VARCHAR(100) NOT NULL
);

-- Table des besoins avec urgence (version PRO: critique, urgent, modere, faible)
CREATE TABLE besoin_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ville_id INT NOT NULL,
    type_besoin VARCHAR(50) NOT NULL,
    description VARCHAR(255) NOT NULL,
    quantite_demandee DECIMAL(15,2) NOT NULL,
    unite VARCHAR(50),
    niveau_urgence VARCHAR(20) NOT NULL, -- 'critique', 'urgent', 'modere', 'faible'
    date_besoin DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ville_id) REFERENCES ville_BNGRC(id)
);

-- Table des dons avec date
CREATE TABLE don_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    donateur VARCHAR(200) NOT NULL,
    type_don VARCHAR(50) NOT NULL,
    description VARCHAR(255) NOT NULL,
    quantite_totale DECIMAL(15,2) NOT NULL,
    unite VARCHAR(50),
    date_don DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table des attributions (qui suit les distributions aux villes)
CREATE TABLE attribution_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    besoin_id INT NOT NULL,
    don_id INT NOT NULL,
    quantite_attribuee DECIMAL(15,2) NOT NULL,
    date_attribution DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (besoin_id) REFERENCES besoin_BNGRC(id),
    FOREIGN KEY (don_id) REFERENCES don_BNGRC(id)
);

-- Table des prix unitaires (catalogue)
CREATE TABLE prix_unitaire_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type_article VARCHAR(50) NOT NULL, -- 'nature' ou 'materiaux'
    description VARCHAR(255) NOT NULL,
    unite VARCHAR(50) NOT NULL,
    prix_unitaire DECIMAL(15,2) NOT NULL -- en Ariary
);

-- Table des achats (liée directement au don)
CREATE TABLE achat_BNGRC (
    id INT PRIMARY KEY AUTO_INCREMENT,
    don_id INT NOT NULL, -- Le don en argent qui finance l'achat
    besoin_id INT NOT NULL, -- Le besoin auquel l'achat est destiné
    description_article VARCHAR(255) NOT NULL,
    quantite DECIMAL(15,2) NOT NULL,
    prix_unitaire_achat DECIMAL(15,2) NOT NULL,
    montant_total DECIMAL(15,2) NOT NULL, -- quantite * prix_unitaire_achat
    date_achat DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (don_id) REFERENCES don_BNGRC(id),
    FOREIGN KEY (besoin_id) REFERENCES besoin_BNGRC(id)
);

-- Insertion des villes (10 villes de Madagascar)
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

-- Insertion des besoins avec les nouveaux niveaux d'urgence (PRO)
INSERT INTO besoin_BNGRC (ville_id, type_besoin, description, quantite_demandee, unite, niveau_urgence, date_besoin) VALUES
-- Antananarivo
(1, 'nature', 'Riz', 5000, 'kg', 'critique', '2026-02-10 08:30:00'),
(1, 'nature', 'Huile végétale', 800, 'litre', 'urgent', '2026-02-12 09:15:00'),
(1, 'materiaux', 'Tôles', 200, 'plaques', 'critique', '2026-02-14 10:00:00'),
(1, 'argent', 'Fonds pour reconstruction', 50000000, 'Ariary', 'critique', '2026-02-15 11:30:00'),

-- Toamasina
(2, 'nature', 'Riz', 3000, 'kg', 'critique', '2026-02-11 14:20:00'),
(2, 'materiaux', 'Clous', 500, 'kg', 'urgent', '2026-02-13 15:45:00'),
(2, 'nature', 'Eau potable', 10000, 'litre', 'critique', '2026-02-14 16:30:00'),

-- Antsiranana
(3, 'nature', 'Riz', 2000, 'kg', 'urgent', '2026-02-12 07:45:00'),
(3, 'materiaux', 'Ciment', 100, 'sacs', 'critique', '2026-02-15 09:20:00'),
(3, 'argent', 'Aide financière', 20000000, 'Ariary', 'faible', '2026-02-16 10:10:00'),

-- Mahajanga
(4, 'nature', 'Huile', 600, 'litre', 'critique', '2026-02-13 11:40:00'),
(4, 'nature', 'Sucre', 1500, 'kg', 'urgent', '2026-02-14 13:15:00'),
(4, 'materiaux', 'Bâches', 300, 'pieces', 'critique', '2026-02-15 14:50:00'),

-- Fianarantsoa
(5, 'nature', 'Riz', 4000, 'kg', 'critique', '2026-02-11 08:25:00'),
(5, 'nature', 'Lait en poudre', 800, 'kg', 'urgent', '2026-02-12 09:55:00'),
(5, 'argent', 'Aide d\'urgence', 15000000, 'Ariary', 'critique', '2026-02-14 10:30:00'),

-- Toliara
(6, 'nature', 'Eau potable', 15000, 'litre', 'critique', '2026-02-10 15:40:00'),
(6, 'nature', 'Riz', 2500, 'kg', 'urgent', '2026-02-12 16:20:00'),
(6, 'materiaux', 'Tôles', 150, 'plaques', 'critique', '2026-02-15 17:00:00'),

-- Antsirabe
(7, 'nature', 'Pommes de terre', 2000, 'kg', 'urgent', '2026-02-13 08:10:00'),
(7, 'materiaux', 'Outils', 100, 'lots', 'modere', '2026-02-14 09:45:00'),
(7, 'argent', 'Fonds scolaire', 8000000, 'Ariary', 'urgent', '2026-02-16 10:20:00'),

-- Morondava
(8, 'nature', 'Riz', 1800, 'kg', 'critique', '2026-02-12 11:30:00'),
(8, 'nature', 'Poisson séché', 500, 'kg', 'urgent', '2026-02-13 12:15:00'),
(8, 'materiaux', 'Cordes', 200, 'rouleaux', 'urgent', '2026-02-15 14:40:00'),

-- Manakara
(9, 'nature', 'Riz', 2200, 'kg', 'critique', '2026-02-11 07:50:00'),
(9, 'materiaux', 'Bois', 50, 'm3', 'urgent', '2026-02-13 08:30:00'),
(9, 'nature', 'Fruit', 800, 'kg', 'faible', '2026-02-14 09:15:00'),

-- Sambava
(10, 'nature', 'Vanille', 100, 'kg', 'urgent', '2026-02-12 13:20:00'),
(10, 'materiaux', 'Sacs de jute', 500, 'pieces', 'modere', '2026-02-14 14:45:00'),
(10, 'argent', 'Fonds agricole', 5000000, 'Ariary', 'critique', '2026-02-15 15:30:00');

-- Insertion des dons (particuliers et organisations)
INSERT INTO don_BNGRC (donateur, type_don, description, quantite_totale, unite, date_don) VALUES
-- Organisations Internationales
('Banque Mondiale', 'argent', 'Fonds d\'urgence cyclone', 100000000, 'Ariary', '2026-02-05 09:00:00'),
('UNICEF', 'nature', 'Kits scolaires', 500, 'lots', '2026-02-06 10:30:00'),
('Croix-Rouge Internationale', 'nature', 'Nourriture et eau', 20000, 'kg', '2026-02-07 11:45:00'),
('PNUD', 'argent', 'Aide au développement', 75000000, 'Ariary', '2026-02-08 14:20:00'),
('Médecins Sans Frontières', 'nature', 'Médicaments', 1000, 'kg', '2026-02-09 15:10:00'),

-- Organisations Nationales (ONG malagasy)
('Miarakapa', 'nature', 'Riz et provisions', 5000, 'kg', '2026-02-10 08:15:00'),
('Tsinjo', 'materiaux', 'Matériaux de construction', 300, 'lots', '2026-02-11 09:40:00'),
('Fitia', 'argent', 'Aide financière', 15000000, 'Ariary', '2026-02-12 10:55:00'),
('Vahatra', 'nature', 'Vêtements et couvertures', 2000, 'pieces', '2026-02-13 11:20:00'),
('HARDI', 'materiaux', 'Outils agricoles', 150, 'lots', '2026-02-14 13:30:00'),

-- Entreprises Internationales
('TotalEnergies', 'argent', 'Aide carburant', 25000000, 'Ariary', '2026-02-05 16:45:00'),
('Air France', 'nature', 'Transport de matériel', 100, 'vols', '2026-02-06 17:20:00'),
('Société Générale', 'argent', 'Fonds solidarité', 30000000, 'Ariary', '2026-02-07 18:00:00'),
('Orange Madagascar', 'nature', 'Téléphones et recharge', 500, 'kits', '2026-02-08 19:30:00'),
('Airtel Madagascar', 'argent', 'Aide communication', 10000000, 'Ariary', '2026-02-09 20:15:00'),

-- Entreprises Nationales (Malagasy)
('Tiko', 'nature', 'Huile et savon', 2000, 'litre', '2026-02-10 07:30:00'),
('JIRAMA', 'materiaux', 'Poteaux électriques', 100, 'pieces', '2026-02-11 08:50:00'),
('Star Madagascar', 'nature', 'Biscuits et boissons', 1500, 'kg', '2026-02-12 09:25:00'),
('Socolait', 'nature', 'Lait et produits laitiers', 800, 'kg', '2026-02-13 10:40:00'),
('SIPROMAD', 'materiaux', 'Matériaux de construction', 200, 'lots', '2026-02-14 11:55:00'),

-- Particuliers malagasy
('Rakotoarisoa Jean', 'argent', 'Don personnel', 500000, 'Ariary', '2026-02-15 14:10:00'),
('Rasoamanana Marie', 'nature', 'Vêtements', 50, 'pieces', '2026-02-15 15:30:00'),
('Rakotondrainibe Paul', 'nature', 'Riz', 100, 'kg', '2026-02-16 08:20:00'),
('Randrianasolo Faly', 'argent', 'Aide cyclonique', 200000, 'Ariary', '2026-02-16 09:45:00'),
('Raharimanana Lalao', 'materiaux', 'Ustensiles cuisine', 30, 'lots', '2026-02-16 10:15:00'),

-- Particuliers étrangers
('Pierre Dubois (France)', 'argent', 'Don personnel', 1000000, 'Ariary', '2026-02-14 12:30:00'),
('Maria Schmidt (Allemagne)', 'nature', 'Médicaments', 50, 'kg', '2026-02-14 13:45:00'),
('John Smith (USA)', 'argent', 'Aide humanitaire', 2000000, 'Ariary', '2026-02-15 11:00:00'),
('Chen Wei (Chine)', 'nature', 'Vêtements chauds', 200, 'pieces', '2026-02-15 12:20:00'),
('Maria Garcia (Espagne)', 'argent', 'Soutien enfants', 500000, 'Ariary', '2026-02-16 07:40:00');

-- Insertion des attributions (distribution des dons aux besoins)
INSERT INTO attribution_BNGRC (besoin_id, don_id, quantite_attribuee, date_attribution) VALUES
-- Attributions pour Antananarivo (besoins 1-4)
(1, 3, 2000, '2026-02-16 09:00:00'),
(1, 6, 1500, '2026-02-16 10:15:00'),
(3, 2, 100, '2026-02-16 11:30:00'),
(4, 1, 20000000, '2026-02-16 14:00:00'),
(2, 16, 400, '2026-02-16 15:00:00'),

-- Attributions pour Toamasina (besoins 5-7)
(5, 6, 1000, '2026-02-16 09:30:00'),
(5, 3, 800, '2026-02-16 10:45:00'),
(6, 19, 200, '2026-02-16 13:15:00'),
(7, 3, 5000, '2026-02-16 14:30:00'),

-- Attributions pour Antsiranana (besoins 8-10)
(8, 23, 50, '2026-02-16 08:30:00'),
(9, 20, 30, '2026-02-16 11:00:00'),
(10, 4, 5000000, '2026-02-16 15:30:00'),

-- Attributions pour Mahajanga (besoins 11-13)
(11, 16, 300, '2026-02-16 09:45:00'),
(12, 18, 400, '2026-02-16 12:00:00'),
(13, 2, 50, '2026-02-16 14:30:00'),

-- Attributions pour Fianarantsoa (besoins 14-16)
(14, 6, 1000, '2026-02-16 10:00:00'),
(14, 3, 800, '2026-02-16 11:30:00'),
(15, 19, 200, '2026-02-16 13:45:00'),
(16, 5, 3000000, '2026-02-16 15:00:00');

INSERT INTO prix_unitaire_BNGRC (type_article, description, unite, prix_unitaire) VALUES
-- Nature (aliments)
('nature', 'Riz', 'kg', 2500),
('nature', 'Huile végétale', 'litre', 6000),
('nature', 'Eau potable', 'litre', 500),
('nature', 'Sucre', 'kg', 3500),
('nature', 'Lait en poudre', 'kg', 12000),
('nature', 'Poisson séché', 'kg', 8000),
('nature', 'Pommes de terre', 'kg', 2000),
('nature', 'Fruits', 'kg', 3000),
('nature', 'Vanille', 'kg', 50000),
('nature', 'Biscuits', 'kg', 4000),
('nature', 'Farine', 'kg', 2500),
('nature', 'Sel', 'kg', 1000),

-- Matériaux
('materiaux', 'Tôles', 'plaque', 25000),
('materiaux', 'Clous', 'kg', 5000),
('materiaux', 'Ciment', 'sac', 28000),
('materiaux', 'Bâches', 'piece', 15000),
('materiaux', 'Bois', 'm3', 300000),
('materiaux', 'Outils', 'lot', 25000),
('materiaux', 'Poteaux électriques', 'piece', 150000),
('materiaux', 'Sacs de jute', 'piece', 500),
('materiaux', 'Cordes', 'rouleau', 8000),
('materiaux', 'Vis', 'kg', 4000),
('materiaux', 'Peinture', 'bidon', 35000),
('materiaux', 'Ustensiles cuisine', 'lot', 15000);

-- 1. Vérifier toutes les villes
SELECT * FROM ville_BNGRC;
SELECT id, nom_ville, region FROM ville_BNGRC ORDER BY region, nom_ville;

-- 2. Vérifier tous les besoins
SELECT * FROM besoin_BNGRC;
SELECT b.id, v.nom_ville, b.type_besoin, b.description, 
       b.quantite_demandee, b.unite, b.niveau_urgence, b.date_besoin
FROM besoin_BNGRC b
JOIN ville_BNGRC v ON b.ville_id = v.id
ORDER BY v.nom_ville, b.niveau_urgence;

-- 3. Vérifier tous les dons
SELECT * FROM don_BNGRC;
SELECT id, donateur, type_don, description, 
       quantite_totale, unite, date_don 
FROM don_BNGRC 
ORDER BY date_don DESC;

-- 4. Vérifier toutes les attributions
SELECT * FROM attribution_BNGRC;
SELECT a.id, v.nom_ville, b.description AS besoin, 
       d.donateur, d.description AS don,
       a.quantite_attribuee, b.unite, a.date_attribution
FROM attribution_BNGRC a
JOIN besoin_BNGRC b ON a.besoin_id = b.id
JOIN ville_BNGRC v ON b.ville_id = v.id
JOIN don_BNGRC d ON a.don_id = d.id
ORDER BY a.date_attribution DESC;

-- 5. Compter le nombre total d'enregistrements
SELECT 'Villes' as table_name, COUNT(*) as total FROM ville_BNGRC
UNION ALL
SELECT 'Besoins', COUNT(*) FROM besoin_BNGRC
UNION ALL
SELECT 'Dons', COUNT(*) FROM don_BNGRC
UNION ALL
SELECT 'Attributions', COUNT(*) FROM attribution_BNGRC;

-- 6. Vérifier les besoins par niveau d'urgence (VERSION PRO)
SELECT niveau_urgence, COUNT(*) as nombre_besoins
FROM besoin_BNGRC
GROUP BY niveau_urgence
ORDER BY FIELD(niveau_urgence, 'critique', 'urgent', 'modere', 'faible');

-- 7. Vérifier les dons par type
SELECT type_don, COUNT(*) as nombre_dons, SUM(quantite_totale) as quantite_totale
FROM don_BNGRC
GROUP BY type_don;

-- 8. Vérifier les besoins par ville
SELECT v.nom_ville, COUNT(b.id) as nombre_besoins
FROM ville_BNGRC v
LEFT JOIN besoin_BNGRC b ON v.id = b.ville_id
GROUP BY v.nom_ville
ORDER BY nombre_besoins DESC;

-- 9. Vérifier les dons par type de donateur
SELECT 
    CASE 
        WHEN donateur LIKE '%(%)%' OR donateur IN ('Banque Mondiale', 'UNICEF', 'Croix-Rouge Internationale', 'PNUD', 'Médecins Sans Frontières', 'TotalEnergies', 'Air France', 'Société Générale', 'Orange Madagascar', 'Airtel Madagascar', 'Pierre Dubois (France)', 'Maria Schmidt (Allemagne)', 'John Smith (USA)', 'Chen Wei (Chine)', 'Maria Garcia (Espagne)') THEN 'International'
        ELSE 'National'
    END as type_donateur,
    COUNT(*) as nombre_dons,
    SUM(quantite_totale) as quantite_totale
FROM don_BNGRC
GROUP BY type_donateur;

-- 10. Vérifier les attributions par ville
SELECT v.nom_ville, COUNT(a.id) as nombre_attributions, SUM(a.quantite_attribuee) as total_distribue
FROM attribution_BNGRC a
JOIN besoin_BNGRC b ON a.besoin_id = b.id
JOIN ville_BNGRC v ON b.ville_id = v.id
GROUP BY v.nom_ville
ORDER BY total_distribue DESC;

-- 11. Vérifier les besoins non encore attribués
SELECT b.id, v.nom_ville, b.description, b.quantite_demandee, 
       COALESCE(SUM(a.quantite_attribuee), 0) as total_attribue,
       (b.quantite_demandee - COALESCE(SUM(a.quantite_attribuee), 0)) as reste_a_distribuer
FROM besoin_BNGRC b
JOIN ville_BNGRC v ON b.ville_id = v.id
LEFT JOIN attribution_BNGRC a ON b.id = a.besoin_id
GROUP BY b.id, v.nom_ville, b.description, b.quantite_demandee
HAVING reste_a_distribuer > 0
ORDER BY reste_a_distribuer DESC;

-- 12. Vérifier les dons non encore utilisés
SELECT d.id, d.donateur, d.description, d.quantite_totale,
       COALESCE(SUM(a.quantite_attribuee), 0) as total_attribue,
       (d.quantite_totale - COALESCE(SUM(a.quantite_attribuee), 0)) as reste_disponible
FROM don_BNGRC d
LEFT JOIN attribution_BNGRC a ON d.id = a.don_id
GROUP BY d.id, d.donateur, d.description, d.quantite_totale
HAVING reste_disponible > 0
ORDER BY reste_disponible DESC;

-- 13. Vérifier les besoins CRITIQUES non satisfaits (la priorité des priorités !)
SELECT v.nom_ville, b.description, b.quantite_demandee,
       COALESCE(SUM(a.quantite_attribuee), 0) as deja_distribue,
       (b.quantite_demandee - COALESCE(SUM(a.quantite_attribuee), 0)) as manque
FROM besoin_BNGRC b
JOIN ville_BNGRC v ON b.ville_id = v.id
LEFT JOIN attribution_BNGRC a ON b.id = a.besoin_id
WHERE b.niveau_urgence = 'critique'
GROUP BY b.id, v.nom_ville, b.description, b.quantite_demandee
HAVING manque > 0
ORDER BY manque DESC;

-- 14. Statistiques des urgences (version PRO)
SELECT 
    SUM(CASE WHEN niveau_urgence = 'critique' THEN 1 ELSE 0 END) as critique_count,
    SUM(CASE WHEN niveau_urgence = 'urgent' THEN 1 ELSE 0 END) as urgent_count,
    SUM(CASE WHEN niveau_urgence = 'modere' THEN 1 ELSE 0 END) as modere_count,
    SUM(CASE WHEN niveau_urgence = 'faible' THEN 1 ELSE 0 END) as faible_count,
    COUNT(*) as total_besoins
FROM besoin_BNGRC;

-- 15. Vérifier le top 5 des plus grands donateurs
SELECT donateur, SUM(quantite_totale) as total_donne
FROM don_BNGRC
GROUP BY donateur
ORDER BY total_donne DESC
LIMIT 5;

-- 16. Vérifier les besoins par type et urgence
SELECT type_besoin, niveau_urgence, COUNT(*) as nombre, SUM(quantite_demandee) as quantite_totale
FROM besoin_BNGRC
GROUP BY type_besoin, niveau_urgence
ORDER BY type_besoin, niveau_urgence;

-- 17. Vérification rapide : les 5 dernières attributions
SELECT 
    v.nom_ville,
    b.description as besoin_description,
    b.niveau_urgence,
    d.donateur,
    a.quantite_attribuee,
    b.unite,
    a.date_attribution
FROM attribution_BNGRC a
JOIN besoin_BNGRC b ON a.besoin_id = b.id
JOIN ville_BNGRC v ON b.ville_id = v.id
JOIN don_BNGRC d ON a.don_id = d.id
ORDER BY a.date_attribution DESC
LIMIT 5;

-- 18. Statistiques générales
SELECT 
    (SELECT COUNT(*) FROM ville_BNGRC) as total_villes,
    (SELECT COUNT(*) FROM besoin_BNGRC) as total_besoins,
    (SELECT COUNT(*) FROM don_BNGRC) as total_dons,
    (SELECT COUNT(*) FROM attribution_BNGRC) as total_attributions,
    (SELECT SUM(quantite_demandee) FROM besoin_BNGRC WHERE type_besoin = 'argent') as total_argent_demande,
    (SELECT SUM(quantite_totale) FROM don_BNGRC WHERE type_don = 'argent') as total_argent_donne;

    -- 1. Vérifier tous les prix unitaires (catalogue)
SELECT * FROM prix_unitaire_BNGRC;
-- Ou plus détaillé
SELECT id, type_article, description, unite, prix_unitaire, 
       FORMAT(prix_unitaire, 0) as prix_formate
FROM prix_unitaire_BNGRC
ORDER BY type_article, description;

-- 2. Vérifier tous les achats
SELECT * FROM achat_BNGRC;

-- 3. Vérifier les achats avec détails (don, besoin, ville)
SELECT 
    a.id,
    d.donateur,
    d.description as don_description,
    v.nom_ville,
    b.description as besoin_description,
    a.description_article,
    a.quantite,
    a.prix_unitaire_achat,
    a.montant_total,
    FORMAT(a.montant_total, 0) as montant_formate,
    a.date_achat
FROM achat_BNGRC a
JOIN don_BNGRC d ON a.don_id = d.id
JOIN besoin_BNGRC b ON a.besoin_id = b.id
JOIN ville_BNGRC v ON b.ville_id = v.id
ORDER BY a.date_achat DESC;

-- 4. Compter les enregistrements
SELECT 'Prix unitaires' as table_name, COUNT(*) as total FROM prix_unitaire_BNGRC
UNION ALL
SELECT 'Achats', COUNT(*) FROM achat_BNGRC;

-- 5. Voir les achats par ville
SELECT 
    v.nom_ville,
    COUNT(a.id) as nombre_achats,
    SUM(a.montant_total) as total_depense,
    FORMAT(SUM(a.montant_total), 0) as total_formate
FROM achat_BNGRC a
JOIN besoin_BNGRC b ON a.besoin_id = b.id
JOIN ville_BNGRC v ON b.ville_id = v.id
GROUP BY v.nom_ville
ORDER BY total_depense DESC;

-- 6. Voir les achats par donateur
SELECT 
    d.donateur,
    d.type_don,
    COUNT(a.id) as nombre_achats,
    SUM(a.montant_total) as total_utilise,
    FORMAT(SUM(a.montant_total), 0) as total_formate
FROM achat_BNGRC a
JOIN don_BNGRC d ON a.don_id = d.id
GROUP BY d.donateur, d.type_don
ORDER BY total_utilise DESC;

-- 7. Voir les prix par type d'article
SELECT 
    type_article,
    COUNT(*) as nombre_articles,
    MIN(prix_unitaire) as prix_min,
    MAX(prix_unitaire) as prix_max,
    AVG(prix_unitaire) as prix_moyen,
    FORMAT(AVG(prix_unitaire), 0) as prix_moyen_formate
FROM prix_unitaire_BNGRC
GROUP BY type_article;

-- 8. Les 10 derniers achats
SELECT 
    a.date_achat,
    v.nom_ville,
    a.description_article,
    a.quantite,
    p.unite,
    a.prix_unitaire_achat,
    a.montant_total,
    d.donateur
FROM achat_BNGRC a
JOIN besoin_BNGRC b ON a.besoin_id = b.id
JOIN ville_BNGRC v ON b.ville_id = v.id
JOIN don_BNGRC d ON a.don_id = d.id
LEFT JOIN prix_unitaire_BNGRC p ON p.description = a.description_article
ORDER BY a.date_achat DESC
LIMIT 10;

-- 9. Montant total des achats
SELECT 
    SUM(montant_total) as total_achats,
    FORMAT(SUM(montant_total), 0) as total_formate,
    COUNT(*) as nombre_achats
FROM achat_BNGRC;

-- 10. Vérifier les achats par besoin (pour voir si un besoin a été satisfait par achat)
SELECT 
    b.id as besoin_id,
    v.nom_ville,
    b.description,
    b.quantite_demandee,
    b.unite,
    COUNT(a.id) as nombre_achats,
    SUM(a.quantite) as total_achete,
    (b.quantite_demandee - SUM(a.quantite)) as reste_a_acheter
FROM besoin_BNGRC b
JOIN ville_BNGRC v ON b.ville_id = v.id
LEFT JOIN achat_BNGRC a ON b.id = a.besoin_id
WHERE b.type_besoin IN ('nature', 'materiaux') -- Seulement ceux qu'on peut acheter
GROUP BY b.id, v.nom_ville, b.description, b.quantite_demandee, b.unite
ORDER BY reste_a_acheter DESC;