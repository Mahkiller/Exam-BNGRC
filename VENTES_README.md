# 🎉 Système de Vente - Nouveautés

## ✨ Ce qui a été créé

### 📁 Fichiers Créés

1. **app/models/VenteModel.php** - Modèle de données pour les ventes
   - Gestion des ventes en base de données
   - Vérification des besoins actifs
   - Configuration des paramètres

2. **app/services/VenteService.php** - Service métier
   - Logique de vente (vérifications, calculs)
   - Gestion de la dépréciation
   - Vérification des contraintes

3. **app/controllers/VenteController.php** - Contrôleur
   - Routes pour afficher/vendre
   - Configuration du taux de change
   - Vérification AJAX des produits

4. **app/views/vente.php** - Page principale de vente
   - Formulaire de vente
   - Historique des ventes
   - Statistiques

5. **app/views/config_vente.php** - Configuration du taux de change
   - Interface pour modifier le taux de dépréciation
   - Frais administratifs
   - TVA

6. **VENTES_GUIDE.md** - Guide complet d'utilisation

### 📝 Fichiers Modifiés

1. **app/config/service.php** - Ajout du VenteService au container
2. **public/index.php** - Ajout des routes Flight pour Ventes
3. **app/views/layout/header.php** - Ajout du lien menu "Ventes"

---

## 🎯 Fonctionnalités Principales

### 1. Interdiction de Vendre si Besoin Actif ✅
```
❌ Vous ne pouvez PAS vendre un produit s'il y a un besoin non satisfait
```

Exemple de message d'erreur:
```
Ce produit est encore en demande, on ne peut pas le vendre.

Besoins actifs:
- Riz pour Antananarivo (5000 kg, critique)
- Riz pour Toamasina (3000 kg, urgent)
```

### 2. Dépréciation Configurable ✅
```
Prix de vente = Prix référence × (1 - Taux de dépréciation)
```

Exemple avec 10% de dépréciation:
- iPhone: 5 000 000 Ar (référence)
- Réduction: 500 000 Ar (10%)
- Prix de vente: 4 500 000 Ar

### 3. Configuration en Temps Réel ✅
- Modifié le taux de dépréciation à tout moment
- Change appliqué sur les **nouvelles ventes**
- Les ventes anciennes gardent leur taux original

---

## 📊 Accès

### URL Routes
- `/ventes` - Page principale de ventes
- `/ventes/config` - Configuration du taux
- `/ventes/vendre` - Endpoint POST pour vendre

### Menu
Cliquez sur **"💰 Ventes"** dans le menu latéral gauche

---

## 🔄 Processus de Vente

```
1. Aller à /ventes
   ↓
2. Sélectionner un produit
   ↓
3. Système vérifie: "Y a-t-il un besoin?"
   ├─ OUI → ❌ Erreur (besoins affichés)
   └─ NON → ✓ Continuer
   ↓
4. Remplir quantité et acheteur (optionnel)
   ↓
5. Système calcule:
   - Prix de vente = prix ref × (1 - taux)
   - Montant = quantité × prix de vente
   ↓
6. Valider la vente
   ↓
7. ✓ Stock réduit automatiquement
   ↓
8. ✓ Argent générée enregistrée
```

---

## 💻 Code Example

### Vendre un produit (depuis le code)
```php
$venteService = ServiceContainer::getVenteService();

try {
    $result = $venteService->vendreProduct(
        don_id: 1,
        produit_id: 5,
        quantite_vendue: 2,
        prix_unitaire_reference: 5000000,
        acheteur: "Ahmed Traore",
        notes: "Commande urgent"
    );
    
    echo "Vente réussie! Montant: " . $result['montant_total'] . " Ar";
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}
```

### Vérifier si produit peut être vendu
```php
$venteService = ServiceContainer::getVenteService();

if ($venteService->canSellProduct($produit_id)) {
    echo "✓ Produit disponible pour vente";
} else {
    $needs = $venteService->getActiveNeedsForProduct($produit_id);
    echo "❌ Besoins actifs: ";
    foreach ($needs as $need) {
        echo $need['description'] . ", ";
    }
}
```

### Récupérer le taux de change
```php
$venteService = ServiceContainer::getVenteService();
$taux = $venteService->getTauxChange(); // Retourne 0.10 pour 10%
```

---

## 🔐 Sécurité

Vérifications automatiques:
- ✓ Produit ne peut pas être vendu si besoin actif
- ✓ Quantité doit être > 0
- ✓ Stock suffisant
- ✓ Taux entre 0 et 100%
- ✓ Stock réduit automatiquement

---

## 📈 Qu'est-ce qu'on peut Vendre?

✅ **Oui si:**
- Stock disponible > 0
- Aucun besoin actif
- Produit naturel ou matériel (pas argent)

❌ **Non si:**
- Y a un besoin non satisfait
- Stock = 0
- Produit en argent (catégorie argent)

---

## 🌍 Configuration par Défaut

Au démarrage de l'appli:
- **Taux de dépréciation**: 10%
- **Frais administratifs**: 5%
- **TVA**: 0%

Table: `configuration_BNGRC`

---

## 📊 Données de Ventes

Enregistrées dans: `vente_BNGRC`

Colonnes:
- `id` - ID unique
- `don_id` - Don source
- `produit_id` - Produit vendu
- `quantite_vendue` - Quantité vendue
- `prix_vente_unitaire` - Prix après dépréciation
- `montant_total` - Total gagné
- `taux_depreciation` - Taux appliqué
- `acheteur` - Nom acheteur (optionnel)
- `date_vente` - Date/heure
- `notes` - Notes (optionnel)

---

## 🧪 Test Rapide

1. **Lancer le serveur**
   ```bash
   php -S localhost:8000 -t public router.php
   ```

2. **Aller à Ventes**
   - URL: `http://localhost:8000/ventes`

3. **Sélectionner un produit**
   - Essayez "Riz" ou "Tôles"

4. **Vérifier le message** (selon s'il y a besoin ou non)

5. **Remplir le formulaire et vendre**

---

## 📞 Troubleshooting

### Page /ventes ne s'affiche pas?
- Vérifier que VenteController.php existe
- Vérifier les routes dans index.php
- Vérifier la table `vente_BNGRC` en BD

### Configuration ne se sauvegarde pas?
- Vérifier table `configuration_BNGRC`
- Vérifier permissions SQL
- Vérifier les logs MySQL

### Stock ne se réduit pas?
- Vérifier que la vente est validée
- Vérifier la table `produit_BNGRC`
- Vérifier les logs application

---

## ✅ Checklist Déploiement

- [x] VenteModel.php créé
- [x] VenteService.php créé
- [x] VenteController.php créé
- [x] vente.php créé
- [x] config_vente.php créé
- [x] Routes Flight ajoutées
- [x] Menu lien ajouté
- [x] ServiceContainer mis à jour
- [x] Documentation créée
- [x] Syntaxe PHP vérifiée ✓

---

## 🚀 Prochaines Étapes

Optionnel pour plus tard:
- [ ] Export ventes en PDF/Excel
- [ ] Graphiques de ventes
- [ ] Remises par volume
- [ ] Partenaires de vente
- [ ] Notifications automatiques
