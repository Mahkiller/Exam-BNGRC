# Lancer le serveur Flight PHP

## Étapes simples

### 1. Installer les dépendances (première fois seulement)
```bash
composer install
```

### 2. Préparer la base de données
```bash
# Créer la base de données et importer les tables
mysql -u root BNGRC < database/base.sql
```

### 3. Lancer le serveur
```bash
php -S localhost:8000 -t public router.php
```

### 4. Accéder à l'application
Ouvrez votre navigateur et allez à : **http://localhost:8000**

---

## Troubleshooting

### Erreur "Composer not found"
- Installer Composer depuis https://getcomposer.org

### Erreur "MySQL Connection Refused"
- Vérifier que MySQL/XAMPP est en cours d'exécution
- Adapter les identifiants dans `app/config/database.php` si nécessaire

### Routes ne fonctionnent pas
- Vérifier que vous êtes sur `localhost:8000` (pas `localhost/...`)
- Le fichier `router.php` à la racine est essential pour le serveur PHP intégré

### Fichier CSS/JS not found
- Vérifier que les fichiers sont dans `public/assets/`
- Utiliser `/assets/css/style.css` dans les URLs (pas de chemin relatif)
