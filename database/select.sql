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

-- Voir toutes les ventes avec détails
SELECT 
    v.id,
    d.donateur,
    p.nom_produit,
    v.quantite_vendue,
    v.prix_vente_unitaire,
    v.montant_total,
    v.taux_depreciation,
    (v.taux_depreciation * 100) as depreciation_pourcentage,
    v.date_vente,
    v.acheteur
FROM vente_BNGRC v
JOIN don_BNGRC d ON v.don_id = d.id
JOIN produit_BNGRC p ON v.produit_id = p.id
ORDER BY v.date_vente DESC;

-- Total des ventes par produit
SELECT 
    p.nom_produit,
    COUNT(v.id) as nombre_ventes,
    SUM(v.quantite_vendue) as quantite_totale_vendue,
    SUM(v.montant_total) as chiffre_affaires,
    FORMAT(SUM(v.montant_total), 0) as ca_formate
FROM vente_BNGRC v
JOIN produit_BNGRC p ON v.produit_id = p.id
GROUP BY p.nom_produit
ORDER BY chiffre_affaires DESC;