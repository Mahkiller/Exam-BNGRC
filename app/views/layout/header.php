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
    <header class="header">
        <div class="header-left">
            <div class="logo">BNGRC <span>Gestion des sinistrés</span></div>
        </div>
        <nav class="main-nav">
            <a href="<?= BASE_URL ?>/dashboard" class="nav-link">Tableau de bord</a>
            <a href="<?= BASE_URL ?>/besoins" class="nav-link">Besoins</a>
            <a href="<?= BASE_URL ?>/dons" class="nav-link">Dons</a>
            <a href="<?= BASE_URL ?>/attribution" class="nav-link">Attribution</a>
            <a href="<?= BASE_URL ?>/achats" class="nav-link">Achats</a>
            <a href="<?= BASE_URL ?>/recap" class="nav-link">📊 Recap</a>
        </nav>
    </header>
    <main class="container">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert success"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>