<?php
// NE PAS METTRE DOCTYPE HTML ICI !
?>

<h1>Gestion des dons</h1>

<div class="stats-row">
    <div class="stat-card">
        <div class="stat-value"><?= count($stats_donateurs) ?></div>
        <div class="stat-label">Types de donateurs</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $stats_donateurs['International'] ?? 0 ?></div>
        <div class="stat-label">Dons internationaux</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $stats_donateurs['National'] ?? 0 ?></div>
        <div class="stat-label">Dons nationaux</div>
    </div>
</div>

<div class="stock-info">
    <h2>Stock disponible par catégorie</h2>
    <div class="stock-grid">
        <div class="stock-item">
            <span>Nature (riz, eau, etc.):</span>
            <strong><?= $stock['nature'] ?? 0 ?> unités</strong>
        </div>
        <div class="stock-item">
            <span>Matériaux (tôles, ciment):</span>
            <strong><?= $stock['materiaux'] ?? 0 ?> unités</strong>
        </div>
        <div class="stock-item">
            <span>Argent:</span>
            <strong><?= number_format($stock['argent'] ?? 0) ?> Ar</strong>
        </div>
    </div>
</div>

<?php if (!empty($top_donateurs)): ?>
<div class="top-donateurs">
    <h3>🏆 Top donateurs</h3>
    <ol>
        <?php foreach ($top_donateurs as $donateur): ?>
        <li>
            <?= $donateur['donateur'] ?> - 
            <?= number_format($donateur['total_donne']) ?> 
            unités
        </li>
        <?php endforeach; ?>
    </ol>
</div>
<?php endif; ?>

<div class="form-container">
    <h2>Enregistrer un don</h2>
    <form action="<?= BASE_URL ?>/dons/ajouter" method="POST">
        <div class="form-group">
            <label>Donateur:</label>
            <input type="text" name="donateur" placeholder="Nom du donateur" required>
        </div>
        
        <div class="form-group">
            <label>Type de don:</label>
            <select name="type_don" required>
                <option value="nature">Nature (riz, eau, nourriture...)</option>
                <option value="materiaux">Matériaux (tôle, ciment, outil...)</option>
                <option value="argent">Argent</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Description:</label>
            <input type="text" name="description" placeholder="ex: Riz, Tôles, Fonds solidarité" required>
        </div>
        
        <div class="form-group">
            <label>Quantité:</label>
            <input type="number" name="quantite" step="0.01" min="0.01" required>
        </div>
        
        <div class="form-group">
            <label>Unité:</label>
            <input type="text" name="unite" placeholder="kg, litre, Ariary, plaques..." required>
        </div>
        
        <button type="submit" class="btn-primary">Enregistrer le don</button>
    </form>
</div>

<h2>Stock détaillé disponible</h2>
<table class="table">
    <thead>
        <tr>
            <th>Description</th>
            <th>Type</th>
            <th>Quantité disponible</th>
            <th>Unité</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($stock_detail)): ?>
            <?php foreach ($stock_detail as $item): ?>
            <tr>
                <td><?= $item['description'] ?></td>
                <td><?= $item['type'] ?></td>
                <td><?= $item['quantite'] ?></td>
                <td><?= $item['unite'] ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align: center;">Aucun stock disponible</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<h2>Historique des dons</h2>
<table class="table">
    <thead>
        <tr>
            <th>Donateur</th>
            <th>Type</th>
            <th>Description</th>
            <th>Quantité</th>
            <th>Unité</th>
            <th>Origine</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($dons as $don): ?>
        <tr>
            <td><?= $don['donateur'] ?></td>
            <td><?= $don['type_don'] ?></td>
            <td><?= $don['description'] ?></td>
            <td><?= $don['quantite_totale'] ?></td>
            <td><?= $don['unite'] ?></td>
            <td>
                <span class="badge <?= ($don['type_donateur'] ?? 'National') == 'International' ? 'international' : 'national' ?>">
                    <?= $don['type_donateur'] ?? 'National' ?>
                </span>
            </td>
            <td><?= date('d/m/Y', strtotime($don['date_don'])) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>