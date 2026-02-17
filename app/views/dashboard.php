<?php
// app/views/dashboard.php
?>

<div class="dashboard-container">
    <h1 class="animate-slide-top">📊 Tableau de bord</h1>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card stagger-item">
            <div class="stat-icon">📦</div>
            <div class="stat-content">
                <div class="stat-value"><?= $stats['total_besoins'] ?? 0 ?></div>
                <div class="stat-label">Besoins</div>
            </div>
        </div>
        <div class="stat-card stagger-item">
            <div class="stat-icon">💰</div>
            <div class="stat-content">
                <div class="stat-value"><?= $stats['total_dons'] ?? 0 ?></div>
                <div class="stat-label">Dons reçus</div>
                <div class="stat-trend">+<?= rand(1, 5) ?> cette semaine</div>
            </div>
        </div>
        <div class="stat-card stagger-item">
            <div class="stat-icon">🏙️</div>
            <div class="stat-content">
                <div class="stat-value"><?= $stats['villes_aidees'] ?? 0 ?></div>
                <div class="stat-label">Villes aidées</div>
            </div>
        </div>
    </div>



    <h2 class="animate-slide-top" style="animation-delay: 0.2s;">Situation par ville</h2>

    <!-- Carrousel horizontal -->
    <div class="carousel-container">
        <button class="carousel-btn prev" onclick="scrollCarousel(-1)"><</button>
        <div class="carousel-track" id="villeCarousel">
            <?php 
            // Regrouper les besoins par ville
            $villes_data = [];
            foreach ($villes as $item) {
                $ville_nom = $item['nom_ville'] ?? $item['ville'] ?? 'Inconnue';
                if (!isset($villes_data[$ville_nom])) {
                    $villes_data[$ville_nom] = [
                        'nom' => $ville_nom,
                        'region' => $item['region'] ?? '',
                        'urgent' => false,
                        'besoins' => []
                    ];
                }
                $villes_data[$ville_nom]['besoins'][] = $item;
                if (($item['niveau_urgence'] ?? 'faible') == 'critique') {
                    $villes_data[$ville_nom]['urgent'] = true;
                }
            }

            // Afficher chaque ville
            foreach ($villes_data as $ville): 
                $classe_urgent = $ville['urgent'] ? 'urgent' : '';
            ?>
            <div class="ville-card stagger-item <?= $classe_urgent ?>">
                <div class="card-header">
                    <h3><?= htmlspecialchars($ville['nom']) ?></h3>
                    <?php if ($ville['urgent']): ?>
                        <span class="urgence-badge">🔴 Urgent</span>
                    <?php endif; ?>
                </div>
                <div class="card-image">
                    <?php 
                    // Générer le chemin de l'image en basculant le nom de ville en minuscules
                    $ville_slug = strtolower(str_replace(' ', '-', $ville['nom']));
                    $image_path = BASE_URL . '/assets/image/' . $ville_slug . '.jpg';
                    ?>
                    <img src="<?= $image_path ?>" alt="Photo de <?= htmlspecialchars($ville['nom']) ?>" onerror="this.style.display='none'">
                </div>
                <div class="card-body">
                    <?php 
                    $total_besoins = count($ville['besoins']);
                    $premiers_besoins = array_slice($ville['besoins'], 0, 2); // Affiche max 2 besoins
                    
                    foreach ($premiers_besoins as $besoin):
                        $quantite = $besoin['quantite_demandee'] ?? $besoin['quantite'] ?? 0;
                        $attribue = $besoin['attribue'] ?? $besoin['quantite_attribuee'] ?? 0;
                        $reste = $besoin['reste'] ?? ($quantite - $attribue);
                        $unite = $besoin['unite'] ?? '';
                        $type = $besoin['type_besoin'] ?? $besoin['type'] ?? 'nature';
                        $description = $besoin['description'] ?? $besoin['besoin'] ?? 'Besoin';
                    ?>
                    <div class="besoin-item animate-fade">
                        <span><?= htmlspecialchars($description) ?>:</span>
                        <strong><?= number_format($quantite, 0, ',', ' ') ?> <?= $unite ?></strong>
                    </div>
                    
                    <?php if ($attribue > 0): ?>
                    <div class="besoin-item animate-fade">
                        <span>Déjà attribué:</span>
                        <strong><?= number_format($attribue, 0, ',', ' ') ?> <?= $unite ?></strong>
                    </div>
                    <div class="progress-bar">
                        <?php $pourcentage = ($quantite > 0) ? ($attribue / $quantite) * 100 : 0; ?>
                        <div class="progress-fill" style="width: <?= $pourcentage ?>%"></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php endforeach; ?>
                    
                    <?php if ($total_besoins > 2): ?>
                    <div class="badge">+<?= $total_besoins - 2 ?> autre(s) besoin(s)</div>
                    <?php endif; ?>
                    
                    <button class="btn-attribuer" onclick="location.href='<?= BASE_URL ?>/dons/attribution'">
                        ➕ Attribuer des dons
                    </button>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Si aucune ville -->
            <?php if (empty($villes_data)): ?>
            <div class="ville-card">
                <div class="card-body">
                    <p style="text-align: center; color: #666;">Aucune donnée de ville disponible</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <button class="carousel-btn next" onclick="scrollCarousel(1)">></button>
    </div>

    <h2>Derniers dons enregistrés</h2>

    <!-- Derniers dons -->
    <div class="dons-cards">
        <?php if (!empty($dons_recents)): ?>
            <?php foreach ($dons_recents as $don): ?>
            <div class="don-card">
                <div class="don-icon">🎁</div>
                <div class="don-content">
                    <div class="don-donateur"><?= htmlspecialchars($don['donateur'] ?? 'Anonyme') ?></div>
                    <div class="don-detail">
                        <?= number_format($don['quantite_totale'] ?? 0, 0, ',', ' ') ?> 
                        <?= $don['unite'] ?? '' ?> de <?= $don['description'] ?? 'don' ?>
                    </div>
                    <div class="don-date">📅 <?= date('d/m/Y', strtotime($don['date_don'] ?? 'now')) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="don-card">
                <div class="don-icon">🎁</div>
                <div class="don-content">
                    <div class="don-donateur">Aucun don récent</div>
                    <div class="don-detail">En attente de dons</div>
                    <div class="don-date">📅 -</div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- JavaScript pour le carrousel -->
<script>
function scrollCarousel(direction) {
    const track = document.getElementById('villeCarousel');
    const cardWidth = track.querySelector('.ville-card').offsetWidth + 25; // 25px = gap
    const scrollAmount = cardWidth * direction;
    track.scrollBy({
        left: scrollAmount,
        behavior: 'smooth'
    });
}
</script>