<?php include 'layout/header.php'; ?>

<div class="content-wrapper">
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
    <div class="row mb-5 animate-slide-top">
        <div class="col-md-6">
            <h1>⚙️ Configuration Vente</h1>
            <p class="text-muted">Gérez les paramètres de dépréciation et frais</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="/ventes" class="btn-secondary btn-lg">
                ← Retour Ventes
            </a>
        </div>
    </div>
    
    <!-- Formulaire de configuration -->
    <div class="row">
        <div class="col-lg-8">
            <div class="form-container animate-slide-right">
                <h2>📋 Paramètres de Configuration</h2>
                <form method="POST" action="/ventes/update-config">
                    <div class="form-row">
                        <?php foreach ($data['configuration'] as $config): ?>
                            <div class="form-group half stagger-item">
                                <div class="stock-item">
                                    <h6 class="card-title">
                                        <?php 
                                            $titles = [
                                                'taux_change_vente' => '💱 Taux de Dépréciation Vente',
                                                'frais_vente' => '💰 Frais Administratifs',
                                                'tva_vente' => '📊 TVA Vente'
                                            ];
                                            echo isset($titles[$config['param_key']]) ? 
                                                $titles[$config['param_key']] : 
                                                htmlspecialchars($config['param_key']);
                                        ?>
                                    </h6>
                                    <p class="text-muted small">
                                        <?php echo htmlspecialchars($config['description']); ?>
                                    </p>
                                    
                                    <div class="input-group">
                                        <input type="number" class="form-control" 
                                               name="config_<?php echo $config['param_key']; ?>"
                                               value="<?php echo htmlspecialchars($config['param_value']); ?>"
                                               min="0" max="100" step="0.01"
                                               required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                    
                                    <!-- Exemple de calcul -->
                                    <div class="mt-3 p-2 bg-white rounded border">
                                        <small class="text-muted">
                                            <strong>Exemple:</strong><br>
                                            <?php 
                                                if ($config['param_key'] === 'taux_change_vente') {
                                                    $value = (float)$config['param_value'];
                                                    $exemple_prix = 5000000;
                                                    $prix_vente = $exemple_prix * (1 - $value / 100);
                                                    echo "Si prix référence = " . number_format($exemple_prix, 0, ',', ' ') . " Ar<br>";
                                                    echo "Prix vente = " . number_format($prix_vente, 0, ',', ' ') . " Ar<br>";
                                                    echo "Réduction = " . number_format($exemple_prix - $prix_vente, 0, ',', ' ') . " Ar";
                                                } else {
                                                    echo "Sur un montant de vente de 1 000 000 Ar<br>";
                                                    echo "Frais = " . number_format($config['param_value'] * 10000, 0, ',', ' ') . " Ar";
                                                }
                                            ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="mt-4 pt-4 border-top">
                        <h6>ℹ️ Explanation</h6>
                        <ul class="text-muted small">
                            <li><strong>Taux de Dépréciation:</strong> Pourcentage de réduction appliqué au prix de référence lors de la vente d'un don matériel</li>
                            <li><strong>Frais Administratifs:</strong> Frais ajoutés sur le montant total de vente (réservé pour évolutions futures)</li>
                            <li><strong>TVA:</strong> Taxe éventuellement applicable (réservé pour évolutions futures)</li>
                        </ul>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn-primary w-100 stagger-item">
                            ✓ Enregistrer Configuration
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Info laterale -->
        <div class="col-lg-4">
            <div class="stock-info animate-slide-left">
                <h2>💡 Guide</h2>
                <div class="bg-light p-3 rounded">
                    <h6>Comprendre la Dépréciation</h6>
                    <p class="text-muted">
                        Quand on vend un don, on applique un taux de réduction pour simuler une vente à un prix inférieur au prix de référence.
                    </p>
                    
                    <div class="example p-2 bg-white rounded border mb-3">
                        <p><strong>Exemple avec 10% de dépréciation:</strong></p>
                        <ul class="mb-0 small">
                            <li>iPhone (prix réf: 5 000 000 Ar)</li>
                            <li>Réduction 10%: 500 000 Ar</li>
                            <li>Prix de vente: 4 500 000 Ar</li>
                        </ul>
                    </div>
                    
                    <hr>
                    
                    <h6>Cas d'Usage</h6>
                    <ul class="text-muted small">
                        <li>✓ Vendre les dons excédentaires</li>
                        <li>✓ Générer des revenus</li>
                        <li>✓ Liquide pour autres dépenses</li>
                        <li>✗ Jamais de produits en demande</li>
                    </ul>
                </div>
            </div>
            
            <div class="stock-info mt-3 animate-slide-left">
                <h2>⚠️ Restrictions</h2>
                <div class="bg-light p-3 rounded">
                    <p class="text-muted small">
                        <strong>Un produit ne peut être vendu que si:</strong>
                    </p>
                    <ul class="small text-muted">
                        <li>✓ Il n'a pas de besoin actif</li>
                        <li>✓ Tous les besoins antérieurs sont satisfaits</li>
                    </ul>
                    <p class="text-muted small mt-3">
                        Si vous tentez de vendre un produit en demande, un message d'erreur s'affichera avec les détails des besoins.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
