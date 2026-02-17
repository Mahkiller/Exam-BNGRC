# 🎨 Guide des Animations - BNGRC

Votre application BNGRC est maintenant entièrement animée avec des effets fluides et professionnels !

## ✨ Types d'Animations Disponibles

### 1. **Slide In (Glissement)**
- `animate-slide-left` : Glisse depuis la gauche
- `animate-slide-right` : Glisse depuis la droite
- `animate-slide-top` : Glisse depuis le haut
- `animate-slide-bottom` : Glisse depuis le bas

```html
<!-- Titre qui glisse depuis le haut -->
<h1 class="animate-slide-top">Mon Titre</h1>

<!-- Formulaire qui glisse depuis la droite -->
<form class="animate-slide-right">...</form>

<!-- Tableaux qui glissent depuis le bas -->
<table class="animate-slide-bottom">...</table>
```

### 2. **Fade In (Fondu)**
- `animate-fade` : Apparition progressive (fondu)

```html
<div class="animate-fade">Contenu qui apparaît progressivement</div>
```

### 3. **Scale (Zoom)**
- `animate-scale` : Zoom depuis 85% à 100%

```html
<!-- Cards qui zooment en apparaissant -->
<div class="stat-card animate-scale">Stats Card</div>
```

### 4. **Pulse & Bounce**
- `animate-pulse` : Clignotement (pour attirer l'attention)
- `animate-bounce` : Rebond (pour les éléments interactifs)

```html
<!-- Badge urgent qui pulse -->
<span class="animate-pulse">🔴 URGENT</span>

<!-- Bouton qui rebondit -->
<a class="quick-link animate-bounce">Cliquez-moi</a>
```

## 🎯 Effet Stagger (Cascade)

Utilise `stagger-item` pour créer un effet de cascade - chaque élément s'anime légèrement après le précédent.

```html
<div class="stats-grid">
    <div class="stat-card stagger-item">💰</div>
    <div class="stat-card stagger-item">📦</div>
    <div class="stat-card stagger-item">🏙️</div>
</div>
```

Les délais s'appliquent automatiquement :
- 1er élément : 0.1s
- 2e élément : 0.2s
- 3e élément : 0.3s
- Et ainsi de suite...

## 🎬 Comment Utiliser

### Sur les Pages Principalement Animées

Les pages suivantes sont **complètement animées** :

- ✅ **Dashboard** - Titre, stats cards, ville cards
- ✅ **Gestion des Besoins** - Formulaire, tableau
- ✅ **Gestion des Dons** - Stats, stock info, tableau
- ✅ **Attribution** - Tous les éléments

### Ajouter une Animation à un Nouvel Élément

1. **Sur un titre** :
```php
<h1 class="animate-slide-top">Mon Titre</h1>
```

2. **Sur un formulaire** :
```php
<form class="form-container animate-slide-right">
    <div class="form-group stagger-item">...</div>
    <div class="form-group stagger-item">...</div>
    <button class="animate-bounce">Envoyer</button>
</form>
```

3. **Sur un tableau** :
```php
<table class="table animate-slide-bottom">
    <tbody>
        <tr class="stagger-item">...</tr>
        <tr class="stagger-item">...</tr>
    </tbody>
</table>
```

4. **Sur des cartes** :
```php
<div class="stat-card stagger-item">...</div>
<div class="stat-card stagger-item">...</div>
```

## ⏱️ Délais d'Animation Personnalisés

### Ajouter un délai manuel

```html
<div class="animate-fade" style="animation-delay: 0.5s;">
    Cet élément apparaît après 0.5 secondes
</div>
```

### Délais prédéfinis pour les titres

```html
<h1 class="animate-slide-top">Titre Principal</h1>
<h2 class="animate-slide-top" style="animation-delay: 0.2s;">Sous-titre</h2>
<h3 class="animate-slide-top" style="animation-delay: 0.3s;">Titre 3</h3>
```

## 📱 Animations Responsive

Les animations s'adaptent automatiquement aux appareils mobiles :
- Sur mobile : délai stagger réduit à 0.1s (plus rapide)
- Sur desktop : délais normaux en cascade

## 🎨 Durée des Animations

Toutes les animations durent **0.6 secondes** par défaut (sauf pulse et bounce qui sont plus longs).

Pour modifier la durée, éditez [style.css](style.css) :

```css
@keyframes slideInFromTop {
    /* Modifier 0.6s pour changer la durée */
    animation: slideInFromTop 0.6s ease-out;
}
```

## 🔧 Personnalisation Avancée

### Créer une nouvelle animation

1. Ajoutez l'animation CSS dans `style.css` :
```css
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-up {
    animation: fadeInUp 0.6s ease-out;
}
```

2. Utilisez-la dans vos vues :
```html
<div class="animate-fade-up">Mon contenu</div>
```

### Modifier la famille de courbes d'ease

Remplacez `ease-out` par :
- `linear` : Animation constante
- `ease` : Accélère puis ralentit (par défaut)
- `ease-in` : Commence lentement
- `ease-out` : Termine lentement (recommandé)
- `ease-in-out` : Commence et finit lentement

## 💡 Bonnes Pratiques

### ✅ À FAIRE

- Utilisez `animate-slide-top` pour les titres
- Utilisez `stagger-item` pour les listes et grilles
- Combinez `animate-fade` avec `stagger-item` pour les tableaux
- Laissez les animations courtes (< 1 seconde)

### ❌ À ÉVITER

- Ne pas animer tous les éléments (trop de mouvement)
- Éviter les animations trop longues (> 1 seconde)
- Ne pas combiner plusieurs animations sur le même élément
- Éviter les animations infinies (sauf pour les éléments interactifs)

## 🎥 Exemples Complets

### Dashboard Complet
```php
<div class="dashboard-container">
    <h1 class="animate-slide-top">📊 Tableau de bord</h1>
    
    <div class="stats-grid">
        <div class="stat-card stagger-item">Stats 1</div>
        <div class="stat-card stagger-item">Stats 2</div>
        <div class="stat-card stagger-item">Stats 3</div>
    </div>
    
    <div class="stock-info animate-scale">
        <h3>Stock disponible</h3>
        <div class="stock-mini-grid">
            <span class="stock-badge stagger-item">Riz</span>
            <span class="stock-badge stagger-item">Argent</span>
            <span class="stock-badge stagger-item">Tôles</span>
        </div>
    </div>
</div>
```

### Formulaire Animé
```php
<div class="form-container animate-slide-right">
    <h2>Ajouter un besoin</h2>
    <form>
        <div class="form-group stagger-item">
            <label>Ville</label>
            <select></select>
        </div>
        <div class="form-group stagger-item">
            <label>Type</label>
            <select></select>
        </div>
        <div class="form-group stagger-item">
            <label>Description</label>
            <input>
        </div>
        <button class="btn-primary stagger-item">Envoyer</button>
    </form>
</div>
```

## 🐛 Dépannage

### Les animations ne s'affichent pas ?

1. Vérifiez que `animations.js` est chargé
2. Vérifiez que les classes d'animation sont correctes
3. Ouvrez la console (F12) et cherchez les erreurs

### Les animations sont trop rapides/lentes ?

Modifiez la durée dans `style.css` :
```css
/* Plus rapide : 0.3s */
.animate-fade {
    animation: fadeIn 0.3s ease-out;
}

/* Plus lent : 0.9s */
.animate-fade {
    animation: fadeIn 0.9s ease-out;
}
```

### Les animations se déclenchent trop tard ?

Réduisez les délais dans `animations.js` ou en CSS :
```css
.stagger-item:nth-child(2) {
    animation-delay: 0.1s; /* Au lieu de 0.2s */
}
```

## 📚 Fichiers Modifiés

- `public/assets/css/style.css` - Animations CSS
- `public/assets/js/animations.js` - Déclenchement automatique
- `app/views/layout/footer.php` - Chaargement du script
- `app/views/dashboard.php` - Animations appliquées
- `app/views/besoins.php` - Animations appliquées
- `app/views/dons.php` - Animations appliquées

## 🎯 Pages à Compléter

Pour un effet cohérent partout, n'hésitez pas à ajouter les mêmes animations à :
- `app/views/attribution.php`
- `app/views/achats.php`
- `app/views/recap_financier.php`

---

**C'est tout !** Vos pages sont maintenant super animées et professionnelles. 🚀
