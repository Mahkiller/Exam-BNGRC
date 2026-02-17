<?php
// NE PAS METTRE DOCTYPE HTML ICI !
?>

<h1 class="animate-slide-top">📝 Gestion des besoins</h1>

<div class="stats-mini">
    <?php foreach ($stats_urgence as $stat): ?>
    <div class="stat-mini stagger-item <?= $stat['niveau_urgence'] ?>">
        <span class="stat-label"><?= ucfirst($stat['niveau_urgence']) ?></span>
        <span class="stat-value"><?= $stat['nombre'] ?></span>
    </div>
    <?php endforeach; ?>
</div>

<div class="form-container animate-slide-right">
    <h2>Ajouter un besoin</h2>
    <form action="<?= BASE_URL ?>/besoins/ajouter" method="POST" id="besoin-form">
        <div class="form-group stagger-item">
            <label>Ville *</label>
            <select name="ville_id" required>
                <option value="">-- Sélectionnez une ville --</option>
                <?php foreach ($villes as $ville): ?>
                <option value="<?= $ville['id'] ?>"><?= htmlspecialchars($ville['nom_ville']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group stagger-item">
            <label>Type de besoin *</label>
            <select name="type_besoin" id="type_besoin" required onchange="toggleBesoinSections()">
                <option value="">-- Sélectionnez un type --</option>
                <option value="nature">🌾 Nature (riz, huile, eau...)</option>
                <option value="materiaux">🔨 Matériaux (tôles, ciment...)</option>
                <option value="argent">💰 Argent</option>
            </select>
        </div>
        
        <!-- Section Argent -->
        <div id="besoin-argent-section" class="form-section" style="display:none;">
            <div class="form-group stagger-item">
                <label>Montant (Ariary) *</label>
                <input type="number" name="quantite_argent" step="100" min="0" placeholder="ex: 1000000">
            </div>
        </div>
        
        <!-- Section Produit -->
        <div id="besoin-produit-section" class="form-section" style="display:none;">
            <div class="form-group stagger-item">
                <label>Produit *</label>
                <select name="produit_id" id="produit_id" required>
                    <option value="">-- Sélectionnez un produit --</option>
                    <?php foreach ($produits as $produit): ?>
                        <option value="<?= $produit['id'] ?>" data-unite="<?= $produit['unite_mesure'] ?>">
                            <?= htmlspecialchars($produit['nom_produit']) ?> (<?= $produit['unite_mesure'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group stagger-item">
                <label>Quantité *</label>
                <input type="number" name="quantite" step="0.01" min="0.01" required>
            </div>
            
            <div class="form-group stagger-item">
                <label>Unité</label>
                <input type="text" name="unite" id="unite" readonly>
            </div>
        </div>
        
        <div class="form-group stagger-item">
            <label>Niveau d'urgence *</label>
            <select name="niveau_urgence" required>
                <option value="critique">🔴 CRITIQUE</option>
                <option value="urgent">🟠 URGENT</option>
                <option value="modere">🟡 Modéré</option>
                <option value="faible">🟢 Faible</option>
            </select>
        </div>
        
        <button type="submit" class="btn-primary stagger-item">Ajouter le besoin</button>
    </form>
</div>

<h2 class="animate-slide-top" style="animation-delay: 0.3s;">Liste des besoins</h2>
<table class="table animate-slide-bottom">
    <thead>
        <tr>
            <th>Ville</th>
            <th>Région</th>
            <th>Type</th>
            <th>Description</th>
            <th>Quantité</th>
            <th>Attribué</th>
            <th>Reste</th>
            <th>Urgence</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($besoins as $besoin): 
            $reste = $besoin['quantite_demandee'] - $besoin['quantite_attribuee'];
            $urgenceClass = $besoin['niveau_urgence'];
        ?>
        <tr class="stagger-item <?= $urgenceClass ?> <?= $reste > 0 ? 'en-attente' : 'satisfait' ?>">
            <td><?= $besoin['nom_ville'] ?></td>
            <td><?= $besoin['region'] ?></td>
            <td><?= $besoin['type_besoin'] ?></td>
            <td><?= $besoin['description'] ?></td>
            <td><?= $besoin['quantite_demandee'] ?> <?= $besoin['unite'] ?></td>
            <td><?= $besoin['quantite_attribuee'] ?> <?= $besoin['unite'] ?></td>
            <td><strong><?= $reste ?> <?= $besoin['unite'] ?></strong></td>
            <td>
                <span class="badge <?= $urgenceClass ?>">
                    <?= strtoupper($besoin['niveau_urgence']) ?>
                </span>
            </td>
            <td><?= date('d/m/Y', strtotime($besoin['date_besoin'])) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
function toggleBesoinSections() {
    const typeBesoin = document.getElementById('type_besoin').value;
    const argentSection = document.getElementById('besoin-argent-section');
    const produitSection = document.getElementById('besoin-produit-section');
    
    if (typeBesoin === 'argent') {
        argentSection.style.display = 'block';
        produitSection.style.display = 'none';
        document.querySelector('[name="quantite_argent"]').setAttribute('required', 'required');
        document.querySelector('[name="produit_id"]').removeAttribute('required');
    } else if (typeBesoin === 'nature' || typeBesoin === 'materiaux') {
        argentSection.style.display = 'none';
        produitSection.style.display = 'block';
        document.querySelector('[name="quantite_argent"]').removeAttribute('required');
        document.querySelector('[name="produit_id"]').setAttribute('required', 'required');
    } else {
        argentSection.style.display = 'none';
        produitSection.style.display = 'none';
    }
}

document.getElementById('produit_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    if (selectedOption.value) {
        document.getElementById('unite').value = selectedOption.getAttribute('data-unite');
    } else {
        document.getElementById('unite').value = '';
    }
});

document.addEventListener('DOMContentLoaded', function() {
    toggleBesoinSections();
});
</script>