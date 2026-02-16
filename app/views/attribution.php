<?php
// NE PAS METTRE DOCTYPE HTML ICI !
?>

<h1>Attribution des dons aux besoins</h1>

<div class="stock-warning">
    <h3>Stock disponible</h3>
    <div class="stock-mini-grid">
        <span class="stock-badge nature">
            🌾 Nature: <strong><?= $stock['nature'] ?? 0 ?></strong>
        </span>
        <span class="stock-badge materiaux">
            🔨 Matériaux: <strong><?= $stock['materiaux'] ?? 0 ?></strong>
        </span>
        <span class="stock-badge argent">
            💰 Argent: <strong><?= number_format($stock['argent'] ?? 0) ?> Ar</strong>
        </span>
    </div>
    <p class="rule-note">⚠️ Règle: La quantité donnée ne doit pas dépasser le stock disponible</p>
</div>

<h2>Besoins non satisfaits</h2>
<table class="table" id="attribution-table">
    <thead>
        <tr>
            <th>Ville</th>
            <th>Besoin</th>
            <th>Type</th>
            <th>Quantité</th>
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
        <tr class="<?= $urgenceClass ?>">
            <td><?= $besoin['nom_ville'] ?></td>
            <td><?= $besoin['description'] ?></td>
            <td><?= $besoin['type_besoin'] ?></td>
            <td><?= $besoin['quantite_demandee'] ?> <?= $besoin['unite'] ?></td>
            <td><?= $besoin['attribue'] ?> <?= $besoin['unite'] ?></td>
            <td class="reste" data-type="<?= $besoin['type_besoin'] ?>">
                <strong><?= $besoin['reste'] ?> <?= $besoin['unite'] ?></strong>
            </td>
            <td>
                <span class="badge <?= $urgenceClass ?>">
                    <?= strtoupper($besoin['niveau_urgence']) ?>
                </span>
            </td>
            <td>
                <input type="number" class="quantite-input" 
                       min="0.01" max="<?= $besoin['reste'] ?>" step="0.01"
                       placeholder="Quantité">
                <select class="don-select" style="display: none;">
                    <!-- Sera rempli par JS si plusieurs dons disponibles -->
                </select>
                <button class="btn-attribuer" 
                        data-besoin-id="<?= $besoin['id'] ?>"
                        data-type="<?= $besoin['type_besoin'] ?>"
                        data-max="<?= $besoin['reste'] ?>"
                        data-unite="<?= $besoin['unite'] ?>">
                    Attribuer
                </button>
                <span class="error-message" style="color: red; display: none;"></span>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>