/ (Exam-BNGRC)
├─ app/
│  ├─ controllers/
│  │  ├─ BesoinsController.php
│  │  ├─ DonsController.php
│  │  ├─ AchatsController.php
│  │  ├─ DashboardController.php
│  │  └─ RecapController.php
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
│  └── achats.sql   (si tu sépares la table achats)
├─ .htaccess
├─ todo.md
└─ README.md
