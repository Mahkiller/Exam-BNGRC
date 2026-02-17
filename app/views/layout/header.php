<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?? 'BNGRC - Gestion des Sinistrés' ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/icon/favicon.ico">
    <script>
        const BASE_URL = '<?= BASE_URL ?>';
    </script>
</head>
<body>
    <div class="app-container">
        <!-- SIDEBAR GAUCHE -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">BNGRC</div>
                <div class="logo-sub">Gestion des sinistrés</div>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <a href="<?= BASE_URL ?>/dashboard" class="nav-item">📊 Tableau de bord</a>
                    <a href="<?= BASE_URL ?>/besoins" class="nav-item">📝 Besoins</a>
                    <a href="<?= BASE_URL ?>/dons" class="nav-item">📦 Dons reçus</a>
                    <a href="<?= BASE_URL ?>/attribution" class="nav-item">🔄 Attribution</a>
                </div>
                <div class="nav-section">
                    <div class="nav-section-title">GESTION</div>
                    <a href="<?= BASE_URL ?>/achats" class="nav-item">🛒 Achats</a>
                    <a href="<?= BASE_URL ?>/stock" class="nav-item">📦 Stock</a>
                    <a href="<?= BASE_URL ?>/ventes" class="nav-item">💰 Ventes</a>
                    <a href="<?= BASE_URL ?>/recap" class="nav-item">📈 Récapitulatif</a>
                </div>
            </nav>
            <div class="sidebar-footer">
                <div class="user-info">
                    <span class="user-icon">👤</span>
                    <span class="user-name">Agent BNGRC</span>
                </div>
            </div>
        </aside>
        <!-- CONTENU PRINCIPAL -->
        <main class="main-content">
            <div class="content-wrapper">
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert success"><?= $_SESSION['message'] ?></div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert error"><?= $_SESSION['error'] ?></div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
