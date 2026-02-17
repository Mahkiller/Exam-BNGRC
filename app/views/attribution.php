<?php
?>
<h1 class="animate-slide-top">🔄 Attribution des dons aux besoins</h1>
<div class="stock-warning animate-scale">
    <h3>Stock disponible par catégorie</h3>
    <div class="stock-summary-grid">
        <?php foreach ($totaux as $catId => $total): ?>
        <div class="stock-summary-card stagger-item">
            <div class="category-icon">
                <?php 
                    $icon = match($total['nom_categorie']) {
                        'nature' => '🌾',
                        'materiaux' => '🔨',
                        'argent' => '💰',
                        default => '📦'
                    };
                    echo $icon;
                ?>
            </div>
            <div class="category-info">
                <h4><?= htmlspecialchars($total['nom_categorie']) ?></h4>
                <p class="stock-value">
                    <?php 
                        if ($total['nom_categorie'] === 'argent') {
                            echo number_format($total['total_stock']) . ' Ar';
                        } else {
                            echo number_format($total['total_stock'], 2) . ' ' . $total['unite'];
                        }
                    ?>
                </p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <p class="rule-note animate-fade">⚠️ Règle: L'attribution doit respecter le stock disponible</p>
</div>
<!-- Détail du stock par produit -->
<h2 class="animate-slide-top" style="animation-delay: 0.2s;">Détail du stock disponible</h2>
<table class="table animate-slide-bottom">
    <thead>
        <tr>
            <th>Catégorie</th>
            <th>Produit</th>
            <th>Stock</th>
            <th>Unité</th>
            <th>Alerte</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($produits as $produit): 
            $status = $produit['stock_actuel'] > $produit['seuil_alerte'] ? 'OK' : 'ALERTE';
            $statusClass = $status === 'OK' ? 'ok' : 'alerte';
        ?>
        <tr class="stagger-item">
            <td><?= htmlspecialchars($produit['nom_categorie']) ?></td>
            <td><?= htmlspecialchars($produit['nom_produit']) ?></td>
            <td><?= number_format($produit['stock_actuel'], 2) ?></td>
            <td><?= htmlspecialchars($produit['unite_mesure']) ?></td>
            <td><?= number_format($produit['seuil_alerte'], 2) ?></td>
            <td>
                <span class="badge <?= $statusClass ?>">
                    <?php if ($status === 'OK'): ?>
                        ✅ OK
                    <?php else: ?>
                        ⚠️ ALERTE
                    <?php endif; ?>
                </span>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<h2 class="animate-slide-top" style="animation-delay: 0.3s;">Besoins non satisfaits</h2>
<table class="table animate-slide-bottom" id="attribution-table">
    <thead>
        <tr>
            <th>Ville</th>
            <th>Besoin</th>
            <th>Type</th>
            <th>Quantité démanée</th>
            <th>Attribué</th>
            <th>Reste</th>
            <th>Urgence</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($besoins_non_satisfaits as $besoin): 
            $urgenceClass = $besoin['niveau_urgence'];
        ?>
        <tr class="stagger-item <?= $urgenceClass ?>">
            <td><?= htmlspecialchars($besoin['nom_ville'] ?? '') ?></td>
            <td><?= htmlspecialchars($besoin['description'] ?? '') ?></td>
            <td><?= htmlspecialchars($besoin['type_besoin'] ?? '') ?></td>
            <td><?= number_format($besoin['quantite_demandee'] ?? 0, 2) ?> <?= htmlspecialchars($besoin['unite'] ?? '') ?></td>
            <td><?= number_format($besoin['attribue'] ?? 0, 2) ?> <?= htmlspecialchars($besoin['unite'] ?? '') ?></td>
            <td class="reste" data-type="<?= htmlspecialchars($besoin['type_besoin'] ?? '') ?>">
                <strong><?= number_format($besoin['reste'] ?? 0, 2) ?> <?= htmlspecialchars($besoin['unite'] ?? '') ?></strong>
            </td>
            <td>
                <span class="badge <?= htmlspecialchars($urgenceClass) ?>">
                    <?= strtoupper($urgenceClass) ?>
                </span>
            </td>
            <td>
                <input type="number" class="quantite-input" 
                       min="0.01" max="<?= $besoin['reste'] ?>" step="0.01"
                       placeholder="Quantité">
                <select class="don-select" style="display: none;">
                    <!-- Sera rempli par JS si plusieurs dons disponibles -->
                </select>
                <button class="btn-attribuer animate-bounce" 
                        data-besoin-id="<?= $besoin['id'] ?>"
                        data-type="<?= htmlspecialchars($besoin['type_besoin'] ?? '') ?>"
                        data-max="<?= $besoin['reste'] ?>"
                        data-unite="<?= htmlspecialchars($besoin['unite'] ?? '') ?>">
                    Attribuer
                </button>
                <span class="error-message" style="color: red; display: none;"></span>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
