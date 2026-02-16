/ (Exam-BNGRC)
├─ app/
│  ├─ controllers/
│  │  ├─ BesoinsController.php
│  │  ├─ DonsController.php
│  │  ├─ AchatsController.php
│  │  ├─ DashboardController.php
│  │  ├─ RecapController.php
│  │  └─ CategoriesController.php
│  ├─ models/
│  │  ├── BesoinModel.php
│  │  ├── DonModel.php
│  │  └── AchatModel.php
│  ├─ services/
│  │  ├── BesoinService.php
│  │  ├── DonService.php
│  │  ├── AchatService.php
│  │  ├── StockService.php
│  │  └── ValidationService.php
│  ├─ views/
│  │  ├── layout/
│  │  │  ├── header.php
│  │  │  └── footer.php
│  │  ├── dashboard.php
│  │  ├── dashboard_financier.php
│  │  ├── besoins.php
│  │  ├── dons.php
│  │  ├── attribution.php
│  │  ├── achats.php
│  │  └── recap_financier.php
│  ├─ config/
│  │  ├── config.php
│  │  ├── database.php
│  │  └── service.php
│  └─ core/
│     ├── Router.php
│     ├── Controller.php
│     └── Model.php
├─ public/
│  ├── index.php
│  ├── .htaccess
│  └── assets/
│     ├── css/
│     │  └── style.css
│     └── js/
│        └── script.js
├─ database/
│  ├── base.sql
├─ .htaccess
├─ to do list.md
├─ router.php
└─ README.md
# Exam-BNGRC

Application de gestion des aides, dons et besoins pour le BNGRC (Bureau National de Gestion des Risques et des Catastrophes).

## 📋 Fonctionnalités

*   **Dashboard** : Vue d'ensemble des statistiques (villes, besoins, dons).
*   **Gestion des Besoins** : Enregistrement des besoins par ville (Nature, Matériaux, etc.).
*   **Gestion des Dons** : Suivi des dons reçus (Argent, Nature) et des donateurs.
*   **Attribution** : Système de matching pour attribuer des dons aux besoins exprimés.
*   **Achats** : Gestion des achats de matériel via les dons financiers.
*   **Finance** : Tableaux de bord financiers et récapitulatifs.

## 🛠️ Architecture Technique

Le projet utilise une architecture **MVC (Modèle-Vue-Contrôleur)** propulsée par le micro-framework **Flight PHP**.

*   **Backend** : PHP 7.4+
*   **Framework** : Flight PHP (Routing, Engine)
*   **Base de données** : MySQL / MariaDB
*   **Frontend** : HTML5, CSS3, JavaScript
*   **Services** : Couche de services métier pour isoler la logique (`app/services/`).

## 🚀 Installation Rapide

### 1. Prérequis
*   Composer
*   PHP
*   MySQL

### 2. Installation des dépendances
```bash
composer install
```

### 3. Base de données
Créez une base de données nommée `BNGRC` et importez le script SQL :
```bash
mysql -u root -p BNGRC < database/base.sql
```
*Note : Configurez vos accès dans `app/config/database.php` si nécessaire.*

### 4. Lancement
Utilisez le serveur PHP intégré pour le développement :
```bash
php -S localhost:8000 -t public router.php
```
Accédez ensuite à **http://localhost:8000**.

## 📂 Structure du Projet

```
app/
├── config/        # Configuration (BD, services)
├── controllers/   # Contrôleurs (Besoins, Dons, Achats...)
├── core/          # Cœur du framework maison + Flight
├── models/        # Accès aux données (SQL)
├── services/      # Logique métier
└── views/         # Templates d'affichage
public/            # Point d'entrée (index.php) et assets
database/          # Scripts SQL
```

Pour plus de détails sur la migration vers Flight PHP, consultez MIGRATION_SUMMARY.md.
