<?php
?>
<div class="achats-container">
    <div class="header-section">
        <h1 class="animate-slide-top">🛒 Gestion des Achats</h1>
        <a href="<?= BASE_URL ?>/achats/creer" class="btn btn-primary">➕ Nouvel achat</a>
    </div>
    <!-- Filtrage par ville -->
    <div class="filter-section">
        <form method="GET" action="<?= BASE_URL ?>/achats" class="filter-form">
            <label for="ville">Filtrer par ville:</label>
            <select id="ville" name="ville_id" onchange="this.form.submit()">
                <option value="">-- Toutes les villes --</option>
                <?php foreach ($villes as $ville): ?>
                    <option value="<?= $ville['id'] ?>" <?= ($ville_id == $ville['id'] ? 'selected' : '') ?>>
                        <?= htmlspecialchars($ville['nom_ville'] ?? $ville['ville']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    <!-- Statistiques -->
    <div class="stats-grid">
        <div class="stat-card stagger-item">
            <div class="stat-icon">🛒</div>
            <div class="stat-content">
                <div class="stat-value"><?= count($achats) ?></div>
                <div class="stat-label">Total d'achats</div>
            </div>
        </div>
        <div class="stat-card stagger-item">
            <div class="stat-icon">💳</div>
            <div class="stat-content">
                <div class="stat-value"><?= number_format($total_montant, 0) ?> Ar</div>
                <div class="stat-label">Montant total</div>
            </div>
        </div>
    </div>
    <!-- Montants par ville -->
    <div class="montants-section">
        <h3 class="animate-slide-top" style="animation-delay: 0.2s;">Montants d'achat par ville</h3>
        <div class="montants-grid">
            <?php foreach ($montant_par_ville as $item): ?>
                <div class="montant-card stagger-item">
                    <div class="montant-ville"><?= htmlspecialchars($item['nom_ville']) ?></div>
                    <div class="montant-value"><?= number_format($item['montant_total'], 0) ?> Ar</div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($montant_par_ville)): ?>
                <p style="grid-column: 1/-1; text-align: center; color: 
            <?php endif; ?>
        </div>
    </div>
    <!-- Tableau des achats -->
    <div class="table-container animate-slide-bottom">
        <h3>Liste des achats</h3>
        <?php if (!empty($achats)): ?>
            <table class="achats-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Ville</th>
                        <th>Besoin</th>
                        <th>Article</th>
                        <th>Quantité</th>
                        <th>Prix unitaire</th>
                        <th>Montant</th>
                        <th>Donateur (Don)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($achats as $achat): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($achat['date_achat'])) ?></td>
                            <td>
                                <strong><?= htmlspecialchars($achat['nom_ville']) ?></strong>
                            </td>
                            <td><?= htmlspecialchars($achat['besoin_description'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($achat['nom_produit']) ?></td>
                            <td><?= number_format($achat['quantite'], 2) ?> <?= htmlspecialchars($achat['unite_mesure']) ?></td>
                            <td><?= number_format($achat['prix_unitaire_achat'], 0) ?> Ar</td>
                            <td class="montant-cell">
                                <strong><?= number_format($achat['montant_total'], 0) ?> Ar</strong>
                            </td>
                            <td><?= htmlspecialchars($achat['donateur'] ?? '-') ?></td>
                            <td class="actions-cell">
                                <form method="POST" action="<?= BASE_URL ?>/achats/supprimer" style="display: inline;">
                                    <input type="hidden" name="id" value="<?= $achat['id'] ?>">
                                    <button type="submit" class="btn-delete" onclick="return confirm('Confirmer la suppression?')">
                                        🗑️ Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p>Aucun achat enregistré</p>
                <a href="<?= BASE_URL ?>/achats/creer" class="btn btn-primary">Créer le premier achat</a>
            </div>
        <?php endif; ?>
    </div>
</div>
<style>
.achats-container {
    padding: 20px;
}
.header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}
.filter-section {
    margin-bottom: 20px;
}
.filter-form {
    display: flex;
    gap: 10px;
    align-items: center;
}
.filter-form select {
    padding: 8px 12px;
    border: 1px solid 
    border-radius: 4px;
    font-size: 14px;
}
.montants-section {
    margin: 30px 0;
}
.montants-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 20px;
}
.montant-card {
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}
.montant-ville {
    font-size: 14px;
    color: 
    margin-bottom: 8px;
}
.montant-value {
    font-size: 20px;
    font-weight: bold;
    color: 
}
.table-container {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}
.achats-table {
    width: 100%;
    border-collapse: collapse;
}
.achats-table thead {
    background: 
    border-bottom: 2px solid 
}
.achats-table th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: 
}
.achats-table td {
    padding: 12px;
    border-bottom: 1px solid 
}
.achats-table tbody tr:hover {
    background: 
}
.montant-cell {
    color: 
    font-weight: bold;
}
.actions-cell {
    text-align: center;
}
.btn-delete {
    padding: 6px 12px;
    background: 
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    transition: background 0.3s;
}
.btn-delete:hover {
    background: 
}
.empty-state {
    padding: 40px;
    text-align: center;
    color: 
}
.empty-state p {
    margin-bottom: 20px;
}
</style>
