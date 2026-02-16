
/ (Exam-BNGRC)
/ (Exam-BNGRC)
├─ app/
│  ├─ controllers/
│  │  ├─ BesoinsController.php
│  │  ├─ DonsController.php
│  │  ├─ DashboardController.php
│  │  └─ AuthController.php
│  ├─ models/
│  │  ├── BesoinModel.php
│  │  └── DonModel.php
│  ├─ services/                           NOUVEAU (logique métier)
│  │  ├── BesoinService.php
│  │  ├── DonService.php
│  │  ├── StockService.php               (vérifie les quantités)
│  │  └── ValidationService.php           (règles de gestion)
│  ├─ views/
│  │  ├── layout/
│  │  │  ├── header.php
│  │  │  └── footer.php
│  │  ├── dashboard.php
│  │  ├── besoins.php
│  │  ├── dons.php
│  │  └── attribution.php
│  └─ config/
│     ├── config.php                   (connexion DB)
│     ├── routes.php
│     └── service.php                      AJOUTÉ (injection de services)
├─ public/
│  ├── index.php
│  ├── assets/
│  │  ├── css/
│  │  │  └── style.css
│  │  └── js/
│  │     └── script.js
├─ database/
│  └── base.sql
├─ .htaccess
├─ todo.md
└─ README.md
\