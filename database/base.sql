-- Création de la base de données
CREATE DATABASE IF NOT EXISTS BNGRC;
USE BNGRC;

    -- Table des villes
    CREATE TABLE ville_BNGRC (
        id INT PRIMARY KEY AUTO_INCREMENT,
        nom_ville VARCHAR(100) NOT NULL,
        region VARCHAR(100) NOT NULL
    );

    -- Table des besoins avec urgence
    CREATE TABLE besoin_BNGRC (
        id INT PRIMARY KEY AUTO_INCREMENT,
        ville_id INT NOT NULL,
        type_besoin VARCHAR(50) NOT NULL,
        description VARCHAR(255) NOT NULL,
        quantite_demandee DECIMAL(15,2) NOT NULL,
        unite VARCHAR(50),
        niveau_urgence VARCHAR(20) NOT NULL, -- 'urgent', 'necessaire', 'pas_besoin'
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

-- Insertion des besoins avec différents niveaux d'urgence
INSERT INTO besoin_BNGRC (ville_id, type_besoin, description, quantite_demandee, unite, niveau_urgence, date_besoin) VALUES
-- Antananarivo
(1, 'nature', 'Riz', 5000, 'kg', 'urgent', '2026-02-10 08:30:00'),
(1, 'nature', 'Huile végétale', 800, 'litre', 'necessaire', '2026-02-12 09:15:00'),
(1, 'materiaux', 'Tôles', 200, 'plaques', 'urgent', '2026-02-14 10:00:00'),
(1, 'argent', 'Fonds pour reconstruction', 50000000, 'Ariary', 'urgent', '2026-02-15 11:30:00'),

-- Toamasina
(2, 'nature', 'Riz', 3000, 'kg', 'urgent', '2026-02-11 14:20:00'),
(2, 'materiaux', 'Clous', 500, 'kg', 'necessaire', '2026-02-13 15:45:00'),
(2, 'nature', 'Eau potable', 10000, 'litre', 'urgent', '2026-02-14 16:30:00'),

-- Antsiranana
(3, 'nature', 'Riz', 2000, 'kg', 'necessaire', '2026-02-12 07:45:00'),
(3, 'materiaux', 'Ciment', 100, 'sacs', 'urgent', '2026-02-15 09:20:00'),
(3, 'argent', 'Aide financière', 20000000, 'Ariary', 'pas_besoin', '2026-02-16 10:10:00'),

-- Mahajanga
(4, 'nature', 'Huile', 600, 'litre', 'urgent', '2026-02-13 11:40:00'),
(4, 'nature', 'Sucre', 1500, 'kg', 'necessaire', '2026-02-14 13:15:00'),
(4, 'materiaux', 'Bâches', 300, 'pieces', 'urgent', '2026-02-15 14:50:00'),

-- Fianarantsoa
(5, 'nature', 'Riz', 4000, 'kg', 'urgent', '2026-02-11 08:25:00'),
(5, 'nature', 'Lait en poudre', 800, 'kg', 'necessaire', '2026-02-12 09:55:00'),
(5, 'argent', 'Aide d\'urgence', 15000000, 'Ariary', 'urgent', '2026-02-14 10:30:00'),

-- Toliara
(6, 'nature', 'Eau potable', 15000, 'litre', 'urgent', '2026-02-10 15:40:00'),
(6, 'nature', 'Riz', 2500, 'kg', 'necessaire', '2026-02-12 16:20:00'),
(6, 'materiaux', 'Tôles', 150, 'plaques', 'urgent', '2026-02-15 17:00:00'),

-- Antsirabe
(7, 'nature', 'Pommes de terre', 2000, 'kg', 'necessaire', '2026-02-13 08:10:00'),
(7, 'materiaux', 'Outils', 100, 'lots', 'pas_besoin', '2026-02-14 09:45:00'),
(7, 'argent', 'Fonds scolaire', 8000000, 'Ariary', 'necessaire', '2026-02-16 10:20:00'),

-- Morondava
(8, 'nature', 'Riz', 1800, 'kg', 'urgent', '2026-02-12 11:30:00'),
(8, 'nature', 'Poisson séché', 500, 'kg', 'necessaire', '2026-02-13 12:15:00'),
(8, 'materiaux', 'Cordes', 200, 'rouleaux', 'urgent', '2026-02-15 14:40:00'),

-- Manakara
(9, 'nature', 'Riz', 2200, 'kg', 'urgent', '2026-02-11 07:50:00'),
(9, 'materiaux', 'Bois', 50, 'm3', 'necessaire', '2026-02-13 08:30:00'),
(9, 'nature', 'Fruit', 800, 'kg', 'pas_besoin', '2026-02-14 09:15:00'),

-- Sambava
(10, 'nature', 'Vanille', 100, 'kg', 'necessaire', '2026-02-12 13:20:00'),
(10, 'materiaux', 'Sacs de jute', 500, 'pieces', 'urgent', '2026-02-14 14:45:00'),
(10, 'argent', 'Fonds agricole', 5000000, 'Ariary', 'urgent', '2026-02-15 15:30:00');

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
(1, 3, 2000, '2026-02-16 09:00:00'),  -- Riz distribué par Croix-Rouge
(1, 6, 1500, '2026-02-16 10:15:00'),  -- Riz distribué par Miarakapa
(3, 2, 100, '2026-02-16 11:30:00'),   -- Tôles distribuées par UNICEF
(4, 1, 20000000, '2026-02-16 14:00:00'), -- Fonds Banque Mondiale

-- Attributions pour Toamasina (besoins 5-7)
(5, 6, 1000, '2026-02-16 09:30:00'),   -- Riz par Miarakapa
(6, 19, 200, '2026-02-16 10:45:00'),   -- Clous par SIPROMAD
(7, 3, 5000, '2026-02-16 13:15:00'),   -- Eau par Croix-Rouge

-- Attributions pour Antsiranana (besoins 8-10)
(8, 23, 50, '2026-02-16 08:30:00'),    -- Riz par Rakotondrainibe
(9, 20, 30, '2026-02-16 11:00:00'),    -- Ciment par SIPROMAD
(10, 4, 5000000, '2026-02-16 15:30:00'), -- Fonds PNUD

-- Attributions pour Mahajanga (besoins 11-13)
(11, 16, 300, '2026-02-16 09:45:00'),  -- Huile par Tiko
(12, 18, 400, '2026-02-16 12:00:00'),  -- Sucre par Star
(13, 2, 50, '2026-02-16 14:30:00'),    -- Bâches UNICEF

-- Attributions pour Fianarantsoa (besoins 14-16)
(14, 6, 1000, '2026-02-16 10:00:00'),  -- Riz Miarakapa
(15, 19, 200, '2026-02-16 13:45:00'),  -- Lait SIPROMAD
(16, 5, 3000000, '2026-02-16 15:00:00'); -- Fonds MSF

-- 1. Vérifier toutes les villes
SELECT * FROM ville_BNGRC;
-- ou plus détaillé
SELECT id, nom_ville, region FROM ville_BNGRC ORDER BY region, nom_ville;

-- 2. Vérifier tous les besoins
SELECT * FROM besoin_BNGRC;
-- ou plus détaillé avec jointure ville
SELECT b.id, v.nom_ville, b.type_besoin, b.description, 
       b.quantite_demandee, b.unite, b.niveau_urgence, b.date_besoin
FROM besoin_BNGRC b
JOIN ville_BNGRC v ON b.ville_id = v.id
ORDER BY v.nom_ville, b.niveau_urgence;

-- 3. Vérifier tous les dons
SELECT * FROM don_BNGRC;
-- ou plus détaillé
SELECT id, donateur, type_don, description, 
       quantite_totale, unite, date_don 
FROM don_BNGRC 
ORDER BY date_don DESC;

-- 4. Vérifier toutes les attributions
SELECT * FROM attribution_BNGRC;
-- ou plus détaillé avec les détails
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

-- 6. Vérifier les besoins par niveau d'urgence
SELECT niveau_urgence, COUNT(*) as nombre_besoins
FROM besoin_BNGRC
GROUP BY niveau_urgence;

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

-- 9. Vérifier les dons par type de donateur (national/international)
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

-- 11. Vérifier les besoins non encore attribués (ou partiellement)
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

-- 13. Vérifier les distributions par date
SELECT DATE(date_attribution) as jour, COUNT(*) as nombre_attributions, SUM(quantite_attribuee) as total_jour
FROM attribution_BNGRC
GROUP BY DATE(date_attribution)
ORDER BY jour;

-- 14. Vérifier les besoins urgents non satisfaits
SELECT v.nom_ville, b.description, b.quantite_demandee,
       COALESCE(SUM(a.quantite_attribuee), 0) as deja_distribue,
       (b.quantite_demandee - COALESCE(SUM(a.quantite_attribuee), 0)) as manque
FROM besoin_BNGRC b
JOIN ville_BNGRC v ON b.ville_id = v.id
LEFT JOIN attribution_BNGRC a ON b.id = a.besoin_id
WHERE b.niveau_urgence = 'urgent'
GROUP BY b.id, v.nom_ville, b.description, b.quantite_demandee
HAVING manque > 0
ORDER BY manque DESC;

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

-- 17. Vérification rapide : les 5 dernières attributions avec tous les détails
SELECT 
    v.nom_ville,
    b.description as besoin_description,
    b.type_besoin,
    d.donateur,
    d.description as don_description,
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