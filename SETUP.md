# Guide de Démarrage - Flight PHP

## Prérequis
- PHP 7.4 ou supérieur
- MySQL/MariaDB
- Composer

## Installation

```bash
# Installer les dépendances Composer
composer install
```

## Configuration Base de Données

1. Créer une base de données MySQL nommée `ETU004082_4338_4433`
2. Importer le schéma :
   ```bash
   mysql -u root -p ETU004082_4338_4433 < database/base.sql
   ```
3. Adapter les identifiants dans [app/config/database.php](app/config/database.php) si nécessaire

## Lancer le serveur de développement

### Option 1 : Serveur PHP intégré (recommandé pour le développement)
```bash
php -S localhost:8000 -t public router.php
```

Puis accédez à : **http://localhost:8000**

### Option 2 : Avec Apache (via XAMPP)
- Placer le projet dans `htdocs/`
- Accéder à : `http://localhost/Php/Exam-BNGRC/Exam-BNGRC/public/`

## Structure du Projet

```
├── app/
│   ├── config/        # Configuration (BD, services)
│   ├── controllers/   # Contrôleurs MVC
│   ├── core/         # Classes de base (Controller, Model)
│   ├── models/       # Modèles de données
│   ├── services/     # Services métier
│   └── views/        # Templates HTML
├── database/         # Scripts SQL
├── public/           # Point d'entrée (index.php) + assets (CSS, JS)
├── vendor/           # Dépendances Composer (Flight)
├── composer.json     # Configuration Composer
└── router.php        # Routeur pour serveur PHP intégré
```

## Routes Disponibles

- `GET / ` - Dashboard
- `GET /dashboard` - Dashboard
- `GET /besoins` - Liste des besoins
- `GET /besoins/create` - Formulaire création besoin
- `POST /besoins/store` - Soumettre nouveau besoin
- `GET /besoins/edit/@id` - Formulaire édition
- `POST /besoins/update/@id` - Soumettre modification
- `POST /besoins/delete/@id` - Supprimer
- `GET /dons` - Liste des dons
- `GET /dons/create` - Formulaire création don
- `POST /dons/store` - Soumettre nouveau don
- `GET /dons/edit/@id` - Formulaire édition
- `POST /dons/update/@id` - Soumettre modification
- `POST /dons/delete/@id` - Supprimer
- `GET /attribution` - Attributions

## Notes

- Le fichier `router.php` est essentiel pour le serveur PHP intégré (`php -S`)
- Les assets statiques (CSS, JS) sont servis depuis `public/assets/`
- La session est automatiquement initialisée
- Les erreurs MySQL affichent des messages détaillés (en développement)
