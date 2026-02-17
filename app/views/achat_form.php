<?php
?>
<div class="achat-form-container">
    <div class="header-section">
        <h1>Créer un nouvel achat</h1>
        <a href="<?= BASE_URL ?>/achats" class="btn btn-secondary">← Retour à la liste</a>
    </div>
    <div class="form-wrapper">
        <form action="<?= BASE_URL ?>/achats/creer" method="POST" class="achat-form">
            <!-- Sélection du don (argent) -->
            <div class="form-group">
                <label for="don_id">Don (Argent) *</label>
                <select name="don_id" id="don_id" required class="form-control">
                    <option value="">-- Sélectionnez un don --</option>
                    <?php foreach ($dons_argent as $don): ?>
                        <option value="<?= $don['id'] ?>">
                            <?= htmlspecialchars($don['donateur']) ?> - 
                            <?= number_format($don['quantite_totale']) ?> Ar 
                            (<?= date('d/m/Y', strtotime($don['date_don'])) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Sélection du besoin -->
            <div class="form-group">
                <label for="besoin_id">Besoin associé *</label>
                <select name="besoin_id" id="besoin_id" required class="form-control">
                    <option value="">-- Sélectionnez un besoin --</option>
                    <?php foreach ($besoins as $besoin): ?>
                        <option value="<?= $besoin['id'] ?>">
                            <?= htmlspecialchars($besoin['nom_ville'] ?? '') ?> - 
                            <?= htmlspecialchars($besoin['description']) ?> 
                            (<?= $besoin['quantite_demandee'] ?> <?= $besoin['unite'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Sélection du produit -->
            <div class="form-group">
                <label for="produit_id">Produit à acheter *</label>
                <select name="produit_id" id="produit_id" required class="form-control">
                    <option value="">-- Sélectionnez un produit --</option>
                    <?php foreach ($produits as $produit): ?>
                        <option value="<?= $produit['id'] ?>" data-prix="<?= $produit['prix_unitaire_reference'] ?>" data-unite="<?= $produit['unite_mesure'] ?>">
                            <?= htmlspecialchars($produit['nom_produit']) ?> 
                            (<?= number_format($produit['prix_unitaire_reference']) ?> Ar/<?= $produit['unite_mesure'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Quantité et Prix Unitaire -->
            <div class="form-row">
                <div class="form-group half">
                    <label for="quantite">Quantité *</label>
                    <input type="number" name="quantite" id="quantite" 
                           step="0.01" min="0.01" required class="form-control"
                           placeholder="ex: 10">
                </div>
                <div class="form-group half">
                    <label for="prix_unitaire">Prix unitaire (Ar) *</label>
                    <input type="number" name="prix_unitaire" id="prix_unitaire" 
                           step="100" min="0" required class="form-control"
                           placeholder="ex: 2500">
                </div>
            </div>
            <!-- Montant total (calculé automatiquement) -->
            <div class="form-group total-group">
                <label>Montant total (Ar)</label>
                <div class="total-display" id="total-display">0 Ar</div>
            </div>
            <!-- Boutons -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Enregistrer l'achat</button>
                <a href="<?= BASE_URL ?>/achats" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
    <!-- Liste des produits pour référence -->
    <div class="prix-reference">
        <h3>📋 Catalogue des produits</h3>
        <table class="prix-table">
            <thead>
                <tr>
                    <th>Catégorie</th>
                    <th>Produit</th>
                    <th>Prix unitaire</th>
                    <th>Unité</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produits as $produit): ?>
                <tr>
                    <td><?= htmlspecialchars($produit['nom_categorie']) ?></td>
                    <td><?= htmlspecialchars($produit['nom_produit']) ?></td>
                    <td><?= number_format($produit['prix_unitaire_reference']) ?> Ar</td>
                    <td><?= htmlspecialchars($produit['unite_mesure']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<style>
.achat-form-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}
.header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}
.form-wrapper {
    background: white;
    border-radius: 8px;
    padding: 25px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}
.achat-form .form-group {
    margin-bottom: 20px;
}
.achat-form .form-row {
    display: flex;
    gap: 20px;
}
.achat-form .half {
    flex: 1;
}
.achat-form label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: 
    font-size: 14px;
}
.achat-form .form-control {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid 
    border-radius: 4px;
    font-size: 14px;
}
.achat-form .form-control:focus {
    border-color: 
    outline: none;
    box-shadow: 0 0 0 2px rgba(198, 40, 40, 0.1);
}
.total-group {
    background: 
    padding: 15px;
    border-radius: 4px;
    margin: 20px 0;
}
.total-display {
    font-size: 28px;
    font-weight: bold;
    color: 
    text-align: right;
}
.form-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    margin-top: 20px;
}
.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
}
.btn-primary {
    background: 
    color: white;
}
.btn-primary:hover {
    background: 
}
.btn-secondary {
    background: 
    color: 
}
.btn-secondary:hover {
    background: 
}
.prix-reference {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.prix-reference h3 {
    margin-bottom: 15px;
    color: 
}
.prix-table {
    width: 100%;
    border-collapse: collapse;
}
.prix-table th {
    background: 
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: 
    font-size: 13px;
}
.prix-table td {
    padding: 10px 12px;
    border-bottom: 1px solid 
}
.prix-table tbody tr:hover {
    background: 
}
@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
        gap: 10px;
    }
    .form-actions {
        flex-direction: column;
    }
    .btn {
        text-align: center;
    }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const produitSelect = document.getElementById('produit_id');
    const quantiteInput = document.getElementById('quantite');
    const prixInput = document.getElementById('prix_unitaire');
    const totalDisplay = document.getElementById('total-display');
    function updateTotal() {
        const quantite = parseFloat(quantiteInput.value) || 0;
        const prix = parseFloat(prixInput.value) || 0;
        const total = quantite * prix;
        totalDisplay.textContent = new Intl.NumberFormat('fr-FR').format(total) + ' Ar';
    }
    produitSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const prix = selectedOption.getAttribute('data-prix');
        const unite = selectedOption.getAttribute('data-unite');
        if (prix) {
            prixInput.value = prix;
            updateTotal();
        }
    });
    quantiteInput.addEventListener('input', updateTotal);
    prixInput.addEventListener('input', updateTotal);
});
</script>
