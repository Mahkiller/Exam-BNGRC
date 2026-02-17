<?php
// app/views/recap_financier.php
?>

<div class="recap-container">
    <div class="header-section">
        <h1 class="animate-slide-top">📊 Récapitulatif Financier</h1>
        <button id="btn-actualiser" class="btn btn-primary">🔄 Actualiser</button>
    </div>

    <!-- Besoins -->
    <div class="recap-section">
        <h2 class="animate-slide-top" style="animation-delay: 0.1s;">📋 Besoins</h2>
        <div class="recap-cards">
            <div class="recap-card stagger-item">
                <div class="recap-label">Montant total des besoins</div>
                <div class="recap-value" id="besoins-total">
                    <?= number_format($besoins['total_montant'], 0) ?> Ar
                </div>
            </div>
            <div class="recap-card stagger-item">
                <div class="recap-label">Montant satisfait</div>
                <div class="recap-value success" id="besoins-satisfait">
                    <?= number_format($besoins['satisfait'], 0) ?> Ar
                </div>
            </div>
            <div class="recap-card stagger-item">
                <div class="recap-label">Montant restant</div>
                <div class="recap-value warning" id="besoins-reste">
                    <?= number_format($besoins['reste'], 0) ?> Ar
                </div>
            </div>
        </div>

        <!-- Barre de progression -->
        <div class="progress-container">
            <div class="progress-label">
                <span>Couverture: <strong id="besoins-pourcent"><?= number_format($besoins['pourcentage'], 1) ?></strong>%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" id="besoins-bar" style="width: <?= $besoins['pourcentage'] ?>%"></div>
            </div>
        </div>
    </div>

    <!-- Dons reçus -->
    <div class="recap-section">
        <h2 class="animate-slide-top" style="animation-delay: 0.2s;">💰 Dons Reçus (en Ariary)</h2>
        <div class="recap-cards">
            <div class="recap-card stagger-item">
                <div class="recap-label">Total reçu</div>
                <div class="recap-value" id="dons-total">
                    <?= number_format($dons['total_recu'], 0) ?> Ar
                </div>
            </div>
            <div class="recap-card stagger-item">
                <div class="recap-label">Total dépensé</div>
                <div class="recap-value" id="dons-dispache">
                    <?= number_format($dons['dispache'], 0) ?> Ar
                </div>
            </div>
            <div class="recap-card stagger-item">
                <div class="recap-label">Solde restant</div>
                <div class="recap-value success" id="dons-reste">
                    <?= number_format($dons['reste'], 0) ?> Ar
                </div>
            </div>
        </div>

        <!-- Barre de progression -->
        <div class="progress-container">
            <div class="progress-label">
                <span>Dépensé: <strong id="dons-pourcent"><?= number_format($dons['pourcentage'], 1) ?></strong>%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" id="dons-bar" style="width: <?= $dons['pourcentage'] ?>%"></div>
            </div>
        </div>
    </div>

    <!-- Achats effectués -->
    <div class="recap-section">
        <h2 class="animate-slide-top" style="animation-delay: 0.3s;">🛒 Achats Effectués</h2>
        <div class="recap-cards">
            <div class="recap-card stagger-item">
                <div class="recap-label">Montant total des achats</div>
                <div class="recap-value" id="achats-total">
                    <?= number_format($achats_total, 0) ?> Ar
                </div>
            </div>
        </div>

        <!-- Derniers achats -->
        <div class="recent-achats">
            <h3>Derniers achats effectués</h3>
            <?php if (!empty($achats_recents)): ?>
                <table class="recap-table animate-slide-bottom">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Article</th>
                            <th>Quantité</th>
                            <th>Prix unitaire</th>
                            <th>Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($achats_recents as $achat): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($achat['date_achat'])) ?></td>
                                <td><?= htmlspecialchars($achat['description_article']) ?></td>
                                <td><?= number_format($achat['quantite'], 2) ?></td>
                                <td><?= number_format($achat['prix_unitaire_achat'], 0) ?> Ar</td>
                                <td><strong><?= number_format($achat['montant_total'], 0) ?> Ar</strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; color: #999; padding: 20px;">Aucun achat effectué</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.recap-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.recap-section {
    background: white;
    padding: 25px;
    border-radius: 8px;
    margin-bottom: 30px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

.recap-section h2 {
    margin-bottom: 20px;
    color: #333;
    font-size: 1.3rem;
}

.recap-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.recap-card {
    background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #c62828;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.recap-label {
    font-size: 13px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 10px;
    font-weight: 600;
}

.recap-value {
    font-size: 28px;
    font-weight: 700;
    color: #c62828;
}

.recap-value.success {
    color: #4caf50;
}

.recap-value.warning {
    color: #ff9800;
}

.progress-container {
    margin-top: 15px;
}

.progress-label {
    font-size: 14px;
    color: #666;
    margin-bottom: 8px;
    display: flex;
    justify-content: space-between;
}

.progress-bar {
    background: #e0e0e0;
    height: 28px;
    border-radius: 14px;
    overflow: hidden;
    border: 1px solid #ddd;
}

.progress-fill {
    background: linear-gradient(90deg, #4caf50 0%, #81c784 100%);
    height: 100%;
    transition: width 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding-right: 10px;
    color: white;
    font-weight: bold;
    font-size: 12px;
}

.recent-achats {
    margin-top: 20px;
}

.recent-achats h3 {
    margin-bottom: 15px;
    color: #333;
}

.recap-table {
    width: 100%;
    border-collapse: collapse;
}

.recap-table thead {
    background: #f5f5f5;
    border-bottom: 2px solid #ddd;
}

.recap-table th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #333;
    font-size: 13px;
}

.recap-table td {
    padding: 12px;
    border-bottom: 1px solid #eee;
    font-size: 13px;
}

.recap-table tbody tr:hover {
    background: #f9f9f9;
}

#btn-actualiser {
    padding: 8px 16px;
    background: #c62828;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s;
}

#btn-actualiser:hover {
    background: #b71c1c;
}

#btn-actualiser:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>

<script>
document.getElementById('btn-actualiser').addEventListener('click', function() {
    const btn = this;
    btn.disabled = true;
    btn.textContent = '⏳ Actualisation...';

    fetch('<?= BASE_URL ?>/recap/actualiser')
        .then(response => response.json())
        .then(data => {
            // Actualiser besoins
            document.getElementById('besoins-total').textContent = 
                new Intl.NumberFormat('fr-FR').format(data.besoins.total_montant) + ' Ar';
            document.getElementById('besoins-satisfait').textContent = 
                new Intl.NumberFormat('fr-FR').format(data.besoins.satisfait) + ' Ar';
            document.getElementById('besoins-reste').textContent = 
                new Intl.NumberFormat('fr-FR').format(data.besoins.reste) + ' Ar';
            document.getElementById('besoins-pourcent').textContent = 
                data.besoins.pourcentage.toFixed(1);
            document.getElementById('besoins-bar').style.width = data.besoins.pourcentage + '%';

            // Actualiser dons
            document.getElementById('dons-total').textContent = 
                new Intl.NumberFormat('fr-FR').format(data.dons.total_recu) + ' Ar';
            document.getElementById('dons-dispache').textContent = 
                new Intl.NumberFormat('fr-FR').format(data.dons.dispache) + ' Ar';
            document.getElementById('dons-reste').textContent = 
                new Intl.NumberFormat('fr-FR').format(data.dons.reste) + ' Ar';
            document.getElementById('dons-pourcent').textContent = 
                data.dons.pourcentage.toFixed(1);
            document.getElementById('dons-bar').style.width = data.dons.pourcentage + '%';

            // Actualiser achats
            document.getElementById('achats-total').textContent = 
                new Intl.NumberFormat('fr-FR').format(data.achats_total) + ' Ar';

            btn.disabled = false;
            btn.textContent = '✅ Mis à jour!';
            setTimeout(() => {
                btn.textContent = '🔄 Actualiser';
            }, 2000);
        })
        .catch(error => {
            console.error('Erreur:', error);
            btn.disabled = false;
            btn.textContent = '❌ Erreur';
            setTimeout(() => {
                btn.textContent = '🔄 Actualiser';
            }, 2000);
        });
});
</script>
