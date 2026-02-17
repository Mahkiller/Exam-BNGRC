<?php
// app/views/stock.php
?>

<div class="stock-container">
    <div class="header-section">
        <h1 class="animate-slide-top">📦 Gestion du Stock</h1>
    </div>

    <!-- Filtre par catégorie -->
    <div class="filter-section">
        <form method="GET" action="<?= BASE_URL ?>/stock" class="filter-form">
            <label for="categorie">Filtrer par catégorie:</label>
            <select id="categorie" name="categorie_id" onchange="this.form.submit()">
                <option value="">-- Toutes les catégories --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($categorie_id == $cat['id'] ? 'selected' : '') ?>>
                        <?= htmlspecialchars($cat['nom_categorie']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <!-- Résumé par catégorie -->
    <div class="stock-summary">
        <h2>📊 Résumé par Catégorie</h2>
        <div class="summary-grid">
            <?php foreach ($categories as $cat): $total = $totaux[$cat['id']] ?? 0; ?>
                <div class="summary-card stagger-item">
                    <div class="category-name"><?= htmlspecialchars($cat['nom_categorie']) ?></div>
                    <div class="category-total">
                        <?php if ($cat['nom_categorie'] === 'argent'): ?>
                            💰 <?= number_format($total, 0) ?> Ar
                        <?php elseif ($cat['nom_categorie'] === 'nature'): ?>
                            🌾 <?= number_format($total, 0) ?> unités
                        <?php else: ?>
                            🔨 <?= number_format($total, 0) ?> unités
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Tableau détaillé du stock -->
    <div class="stock-details">
        <h2>📋 Détails du Stock</h2>
        <table class="stock-table animate-slide-bottom">
            <thead>
                <tr>
                    <th>Catégorie</th>
                    <th>Produit</th>
                    <th>Stock actuel</th>
                    <th>Unité</th>
                    <th>Seuil alerte</th>
                    <th>Prix unitaire</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produits as $produit): ?>
                    <tr class="stagger-item">
                        <td>
                            <span class="badge"><?= htmlspecialchars($produit['nom_categorie']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($produit['nom_produit']) ?></td>
                        <td class="stock-value">
                            <strong><?= number_format($produit['stock_actuel'], 2) ?></strong>
                        </td>
                        <td><?= htmlspecialchars($produit['unite_mesure']) ?></td>
                        <td><?= number_format($produit['seuil_alerte'], 2) ?></td>
                        <td><?= number_format($produit['prix_unitaire_reference'], 0) ?> Ar</td>
                        <td>
                            <?php if ($produit['stock_actuel'] <= $produit['seuil_alerte']): ?>
                                <span class="status warning">⚠️ ALERTE</span>
                            <?php else: ?>
                                <span class="status ok">✅ OK</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if (empty($produits)): ?>
            <p class="no-data">Aucun produit dans cette catégorie</p>
        <?php endif; ?>
    </div>
</div>

<style>
.stock-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.header-section {
    margin-bottom: 30px;
}

.header-section h1 {
    color: #333;
    font-size: 2rem;
    margin: 0;
}

.filter-section {
    margin-bottom: 25px;
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.filter-form {
    display: flex;
    gap: 10px;
    align-items: center;
}

.filter-form label {
    font-weight: 600;
    color: #333;
}

.filter-form select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    cursor: pointer;
}

.stock-summary {
    margin-bottom: 30px;
}

.stock-summary h2 {
    color: #333;
    margin-bottom: 15px;
    font-size: 1.3rem;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.summary-card {
    background: white;
    border-left: 4px solid #c62828;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}

.category-name {
    font-weight: 600;
    color: #666;
    font-size: 14px;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.category-total {
    font-size: 18px;
    font-weight: 700;
    color: #c62828;
}

.stock-details {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

.stock-details h2 {
    color: #333;
    margin-bottom: 20px;
    font-size: 1.3rem;
}

.stock-table {
    width: 100%;
    border-collapse: collapse;
}

.stock-table thead {
    background: #f5f5f5;
    border-bottom: 2px solid #ddd;
}

.stock-table th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #333;
    font-size: 13px;
}

.stock-table td {
    padding: 12px;
    border-bottom: 1px solid #eee;
    font-size: 13px;
}

.stock-table tbody tr:hover {
    background: #f9f9f9;
}

.stock-value {
    color: #c62828;
    font-weight: bold;
}

.badge {
    display: inline-block;
    padding: 4px 8px;
    background: #f0f0f0;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    color: #666;
}

.status {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.status.ok {
    background: #e8f5e9;
    color: #2e7d32;
}

.status.warning {
    background: #fff3e0;
    color: #e65100;
}

.no-data {
    text-align: center;
    color: #999;
    padding: 40px;
}

@media (max-width: 768px) {
    .summary-grid {
        grid-template-columns: 1fr;
    }
    
    .stock-table {
        font-size: 12px;
    }
    
    .stock-table th,
    .stock-table td {
        padding: 8px;
    }
}
</style>
