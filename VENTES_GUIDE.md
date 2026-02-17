# 💰 Guide du Système de Vente - BNGRC

## 📋 Vue d'ensemble

Le système de vente permet de **vendre les dons matériels non nécessaires** pour générer des revenus. Avec un système de dépréciation configurable, vous pouvez vendre les produits à un prix inférieur à leur prix de référence.

---

## ✨ Fonctionnalités

### 1. **Vente de Produits**
- Vendre uniquement les produits **sans besoin actif**
- Application automatique du taux de dépréciation
- Suivi complet des ventes avec date, quantité, prix

### 2. **Protection des Besoins**
- **Vérification automatique**: Un produit ne peut pas être vendu s'il y a un besoin non satisfait
- Message d'erreur détaillé affichant les besoins actifs
- Liaison avec le don original

### 3. **Configuration du Taux de Change**
- Taux de dépréciation configurable (par défaut: 10%)
- Frais administratifs (réservé pour évolutions futures)
- TVA (réservé pour évolutions futures)

### 4. **Statistiques**
- Total ventes effectuées
- Quantité totale vendue
- Montant total généré
- Taux de dépréciation moyen
- Ventes par catégorie

---

## 🎯 Cas d'Usage

### Exemple: Vendre un iPhone

**Prix de référence:** 5 000 000 Ar
**Taux de dépréciation:** 10%
**Quantité:** 1

**Calcul:**
```
Prix de vente = 5 000 000 × (1 - 10%) = 4 500 000 Ar
Montant total = 1 × 4 500 000 = 4 500 000 Ar
```

---

## 📖 Guide d'Utilisation

### Étape 1: Accéder à la Page de Vente

1. Cliquez sur **"💰 Ventes"** dans le menu latéral gauche
2. Vous verrez:
   - Statistics des ventes
   - Formulaire de nouvelle vente
   - Historique des ventes

### Étape 2: Vendre un Produit

1. **Sélectionner le produit** dans le dropdown
   - Les produits avec stock sont affichés
   - Stock disponible est indiqué
   
2. **Vérification automatique**:
   - ⚠️ Si le produit a un besoin actif: **Message d'erreur** avec détails
   - ✓ Si OK: Vous pouvez continuer

3. **Remplir le formulaire**:
   - **Quantité**: Nombre d'unités à vendre
   - **Prix unitaire de référence**: Remis automatiquement
   - **Prix de vente**: Calculé automatiquement (référence - réduction)
   - **Montant total**: Calculé automatiquement
   - **Acheteur** (optionnel): Nom de la personne qui achète
   - **Don associé** (optionnel): Lier à un don original
   - **Notes** (optionnel): Notes additionnelles

4. **Cliquer "✓ Valider la Vente"**

5. **Message de confirmation** avec montant gagné

### Étape 3: Configurer le Taux de Change

1. Cliquez sur **"⚙️ Configuration Change"** (en haut à droite de la page Ventes)
2. Vous verrez les 3 paramètres:
   - **💱 Taux de Dépréciation Vente**: Réduction appliquée (%)
   - **💰 Frais Administratifs**: Frais optionnels (%)
   - **📊 TVA Vente**: TVA optionnelle (%)

3. **Modifier les valeurs**:
   - Entrez un pourcentage de 0 à 100
   - Utilisez les décimales (ex: 10.5)

4. Cliquez "✓ Enregistrer Configuration"

---

## ✅ Vérifications de Sécurité

Le système vérifie automatiquement:

1. **Besoin actif**: Produit ne peut pas être vendu
2. **Quantité disponible**: Stock suffisant
3. **Paramètres valides**: Entre 0 et 100%
4. **Stock est réduit**: Automatiquement après vente

---

## 📊 Historique des Ventes

La table affiche:
- **Date**: Quand la vente a eu lieu
- **Produit**: Nom du produit vendu
- **Catégorie**: Nature/Matériaux/etc
- **Quantité**: Nombre d'unités vendues
- **Prix Réf.**: Prix original du don
- **Prix Vente**: Prix après dépréciation
- **Montant**: Total gagné
- **Taux**: Taux appliqué (%)
- **Acheteur**: Qui a acheté

---

## ⚠️ Cas d'Erreur Importants

### ❌ "Ce produit est encore en demande"

**Cause**: Le produit a un ou plusieurs besoins non satisfaits

**Solution**:
1. Satisfaire d'abord les besoins
2. Attribuer les dons aux villes qui en ont besoin
3. Puis revenir vendre le surplus

**Exemple d'message d'erreur**:
```
Ce produit est encore en demande, on ne peut pas le vendre.

Besoins actifs:
- Riz pour Antananarivo (5000 kg, critique)
- Riz pour Toamasina (3000 kg, urgent)
```

### ❌ "Les champs obligatoires sont manquants"

**Cause**: Vous n'avez pas rempli tous les champs obligatoires (*)

**Solution**:
- **Produit**: Obligatoire
- **Quantité**: Obligatoire et > 0
- **Prix unitaire**: Automatiquement rempli

### ❌ "Le pourcentage doit être entre 0 et 100"

**Cause**: Vous avez entré une valeur invalide dans la configuration

**Solution**:
- Entrez un nombre entre 0 et 100
- Utilisez . ou , pour les décimales (ex: 10.5%)

---

## 🔄 Flux Global

```
1. Arriver un don matériel
   ↓
2. Enregistrer le don → Stock ↑
   ↓
3. Satisfaire les besoins avec le don
   ↓
4. Stock excédentaire → Vendre
   ↓
5. Générer du revenu
   ↓
6. Utiliser pour autres dépenses
```

---

## 💡 Bonnes Pratiques

✓ **À faire:**
- Satisfaire les besoins en priorité
- Vendre seulement le surplus
- Documenter les ventes avec nombre et notes
- Vérifier le taux de dépréciation régulièrement
- Utiliser les revenus pour acheter des nécessités

✗ **À éviter:**
- Vendre un produit en demande
- Utiliser un taux de dépréciation trop bas
- Oublier de configurer le taux
- Vendre sans raison/notes

---

## 📈 Statistiques & Rapports

La page principale de ventes affiche:
- **Total Ventes**: Nombre de ventes effectuées
- **Quantité Vendue**: Total d'unités vendues
- **Montant Total**: Total argent généré
- **Taux Moyen**: Moyenne des taux applied
- **Ventes par Catégorie**: Tableau par type (Nature/Matériaux)

---

## 🔧 Configuration Minimale

Au démarrage, la configuration par défaut est:
- **Taux de Dépréciation**: 10%
- **Frais Administratifs**: 5%
- **TVA**: 0%

Vous pouvez les modifier à tout moment via **⚙️ Configuration Change**

---

## 📞 Aide & Support

En cas de problème:

1. **Page de Ventes ne s'affiche pas?**
   - Vérifier que vous êtes connecté
   - Vérifier les tables de base de données
   - Vérifier les routes Flight

2. **Erreur "Produit en demande"?**
   - Vérifier les besoins actifs
   - Satisfaire les besoins d'abord
   - Le message affiche les détails

3. **Configuration ne se sauvegarde pas?**
   - Vérifier les droits SQL
   - Vérifier la table `configuration_BNGRC`

---

## 🎯 Prochain Développement

Possibles améliorations:
- [ ] Export ventes (PDF/Excel)
- [ ] Graphiques de ventes
- [ ] Historique des configurations
- [ ] Multi-devise
- [ ] Remise par volume
- [ ] Partenaires de vente
- [ ] Notifications de stock bas
