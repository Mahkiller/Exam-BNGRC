<?php include 'layout/header.php'; ?>

<!-- Alerts -->
<?php if (!empty($data['success_message'])): ?>
    <div class="alert success animate-slide-top">
        <strong>✓ Succès!</strong> <?php echo htmlspecialchars($data['success_message']); ?>
    </div>
<?php endif; ?>

<?php if (!empty($data['error_message'])): ?>
    <div class="alert error animate-slide-top">
        <strong>✗ Erreur!</strong> <?php echo htmlspecialchars($data['error_message']); ?>
    </div>
<?php endif; ?>

<!-- Header -->
<div class="ventes-header animate-slide-top">
    <div class="header-left">
        <h1>💰 Gestion des Ventes</h1>
        <p class="text-muted">Vendez les dons non nécessaires</p>
    </div>
    <div class="header-right">
        <a href="/ventes/config" class="btn-secondary">
            ⚙️ Configuration
        </a>
    </div>
</div>

<!-- Stats -->
<div class="stats-grid mb-4">
    <?php if ($data['stats']): ?>
        <div class="stat-card stagger-item">
            <div class="stat-label">Total Ventes</div>
            <div class="stat-value"><?php echo number_format($data['stats']['total_ventes']); ?></div>
        </div>
        <div class="stat-card stagger-item">
            <div class="stat-label">Quantité Vendue</div>
            <div class="stat-value"><?php echo number_format($data['stats']['quantite_totale_vendue']); ?></div>
        </div>
        <div class="stat-card stagger-item">
            <div class="stat-label">Montant Total</div>
            <div class="stat-value"><?php echo number_format($data['stats']['montant_total_ventes'], 0, ',', ' '); ?> Ar</div>
        </div>
        <div class="stat-card stagger-item">
            <div class="stat-label">Taux Moyen</div>
            <div class="stat-value"><?php echo number_format($data['stats']['taux_moyen'] * 100, 1); ?>%</div>
        </div>
    <?php endif; ?>
</div>

<!-- Formulaire de vente et infos -->
<div class="ventes-grid">
    <!-- Colonne gauche : Formulaire -->
    <div class="ventes-col">
        <div class="form-container animate-slide-right">
            <h2>📝 Nouvelle Vente</h2>
            <form method="POST" action="/ventes/vendre" class="vente-form">
                <div class="form-group stagger-item">
                    <label for="produit_id">Produit *</label>
                    <select class="form-control" id="produit_id" name="produit_id" required onchange="updateProduitInfo()">
                        <option value="">-- Sélectionner un produit --</option>
                        <?php if (!empty($data['stocks'])): ?>
                            <?php foreach ($data['stocks'] as $stock): ?>
                                <option value="<?php echo $stock['id']; ?>" 
                                        data-price="<?php echo $stock['prix_unitaire_reference']; ?>"
                                        data-stock="<?php echo $stock['stock_actuel']; ?>"
                                        data-unite="<?php echo $stock['unite_mesure']; ?>">
                                    <?php echo htmlspecialchars($stock['nom_produit']); ?> 
                                    (Stock: <?php echo number_format($stock['stock_actuel']); ?> <?php echo $stock['unite_mesure']; ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>Aucun produit disponible</option>
                        <?php endif; ?>
                    </select>
                    <div id="product-warning" class="alert alert-warning mt-2" style="display: none;"></div>
                </div>
                
                <div class="form-row">
                    <div class="form-group half stagger-item">
                        <label for="quantite_vendue">Quantité *</label>
                        <input type="number" class="form-control" id="quantite_vendue" name="quantite_vendue" 
                               min="0.01" step="0.01" required placeholder="0.00">
                        <small class="text-muted" id="stock-info"></small>
                    </div>
                    <div class="form-group half stagger-item">
                        <label for="prix_unitaire_reference">Prix Unit. Réf. *</label>
                        <input type="number" class="form-control" id="prix_unitaire_reference" 
                               name="prix_unitaire_reference" min="0" step="0.01" required readonly>
                        <small class="text-muted">Ar</small>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group half stagger-item">
                        <label for="prix_vente">Prix Vente (après dépréciation)</label>
                        <input type="number" class="form-control" id="prix_vente" 
                               readonly placeholder="Calculé automatiquement">
                        <small class="text-muted">Taux: <span id="taux-display"><?php echo $data['taux_change'] * 100; ?>%</span></small>
                    </div>
                    <div class="form-group half stagger-item">
                        <label for="montant_total_calc">Montant Total</label>
                        <input type="number" class="form-control" id="montant_total_calc" 
                               readonly placeholder="Calculé automatiquement">
                        <small class="text-muted">Ar</small>
                    </div>
                </div>
                
                <div class="form-group stagger-item">
                    <label for="don_id">Don Associé</label>
                    <select class="form-control" id="don_id" name="don_id">
                        <option value="">-- Optionnel --</option>
                        <?php if (!empty($data['dons'])): ?>
                            <?php foreach ($data['dons'] as $don): ?>
                                <option value="<?php echo $don['id']; ?>">
                                    <?php echo htmlspecialchars($don['donateur']); ?> - 
                                    <?php echo htmlspecialchars($don['description']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="form-group stagger-item">
                    <label for="acheteur">Acheteur</label>
                    <input type="text" class="form-control" id="acheteur" name="acheteur" 
                           placeholder="Nom de l'acheteur (optionnel)">
                </div>
                
                <div class="form-group stagger-item">
                    <label for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3" 
                              placeholder="Notes additionnelles (optionnel)"></textarea>
                </div>
                
                <button type="submit" class="btn-primary w-100 stagger-item">
                    ✓ Valider la Vente
                </button>
            </form>
        </div>
    </div>
    
    <!-- Colonne droite : Infos -->
    <div class="ventes-col">
        <div class="stock-info animate-slide-left">
            <h2>ℹ️ Informations Produit</h2>
            <div id="produit-info-content">
                <p class="text-muted">Sélectionnez un produit pour voir les détails</p>
            </div>
        </div>
        
        <div class="stock-info mt-3 animate-slide-left">
            <h2>📊 Ventes par Catégorie</h2>
            <?php if (!empty($data['sales_by_category'])): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Catégorie</th>
                            <th>Montant Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['sales_by_category'] as $cat): ?>
                            <tr class="stagger-item">
                                <td><?php echo htmlspecialchars($cat['nom_categorie']); ?></td>
                                <td><?php echo number_format($cat['montant_total'], 0, ',', ' '); ?> Ar</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted">Aucune vente par catégorie</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Historique des ventes -->
<div class="historique-section">
    <div class="stock-info animate-slide-bottom">
        <h2>📋 Historique des Ventes</h2>
        <?php if (!empty($data['ventes'])): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Produit</th>
                            <th>Catégorie</th>
                            <th>Quantité</th>
                            <th>Prix Réf.</th>
                            <th>Prix Vente</th>
                            <th>Montant</th>
                            <th>Taux</th>
                            <th>Acheteur</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['ventes'] as $vente): ?>
                            <tr class="stagger-item">
                                <td><?php echo date('d/m/Y H:i', strtotime($vente['date_vente'])); ?></td>
                                <td><?php echo htmlspecialchars($vente['nom_produit']); ?></td>
                                <td><span class="badge badge-info"><?php echo htmlspecialchars($vente['nom_categorie']); ?></span></td>
                                <td><?php echo number_format($vente['quantite_vendue'], 2); ?> <?php echo $vente['unite_mesure']; ?></td>
                                <td><?php echo number_format($vente['prix_vente_unitaire'] / (1 - $vente['taux_depreciation']), 0, ',', ' '); ?> Ar</td>
                                <td><?php echo number_format($vente['prix_vente_unitaire'], 0, ',', ' '); ?> Ar</td>
                                <td><strong><?php echo number_format($vente['montant_total'], 0, ',', ' '); ?> Ar</strong></td>
                                <td><?php echo number_format($vente['taux_depreciation'] * 100, 1); ?>%</td>
                                <td><?php echo !empty($vente['acheteur']) ? htmlspecialchars($vente['acheteur']) : '--'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-4">Aucune vente enregistrée</p>
        <?php endif; ?>
    </div>
</div>

<script>
const tauxChange = <?php echo $data['taux_change']; ?>;

function updateProduitInfo() {
    const select = document.getElementById('produit_id');
    if (!select.value) return;
    
    const option = select.options[select.selectedIndex];
    const prixRef = parseFloat(option.dataset.price) || 0;
    const stock = parseFloat(option.dataset.stock) || 0;
    const unite = option.dataset.unite || '';
    
    document.getElementById('prix_unitaire_reference').value = prixRef.toFixed(2);
    document.getElementById('stock-info').textContent = `Stock disponible: ${stock} ${unite}`;
    
    // Vérifier si le produit peut être vendu
    const produitId = select.value;
    if (produitId) {
        checkProduct(produitId);
        calculatePrices();
    }
}

function checkProduct(produitId) {
    fetch(BASE_URL + '/ventes/check-product?produit_id=' + produitId)
        .then(r => r.json())
        .then(data => {
            const warningDiv = document.getElementById('product-warning');
            if (data.error) {
                warningDiv.innerHTML = '<strong>⚠️ Erreur!</strong> ' + data.error;
                warningDiv.style.display = 'block';
            } else if (!data.can_sell) {
                let html = '<strong>⚠️ Attention!</strong> ' + (data.message || 'Ce produit est en demande') + '<br>';
                if (data.needs && data.needs.length > 0) {
                    html += '<ul class="mb-0 mt-2">';
                    data.needs.forEach(need => {
                        html += `<li>${need.description} (${need.nom_ville}) - ${need.quantite_demandee} demandés</li>`;
                    });
                    html += '</ul>';
                }
                warningDiv.innerHTML = html;
                warningDiv.style.display = 'block';
            } else {
                warningDiv.style.display = 'none';
            }
        })
        .catch(err => {
            console.error('Erreur:', err);
        });
}

function calculatePrices() {
    const quantite = parseFloat(document.getElementById('quantite_vendue').value) || 0;
    const prixRef = parseFloat(document.getElementById('prix_unitaire_reference').value) || 0;
    
    const prixVente = prixRef * (1 - tauxChange);
    const montantTotal = quantite * prixVente;
    
    document.getElementById('prix_vente').value = prixVente.toFixed(2);
    document.getElementById('montant_total_calc').value = montantTotal.toFixed(2);
}

document.getElementById('quantite_vendue').addEventListener('input', calculatePrices);
document.getElementById('quantite_vendue').addEventListener('change', calculatePrices);
</script>

<?php include 'layout/footer.php'; ?>