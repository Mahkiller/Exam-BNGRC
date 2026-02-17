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
            <select name="type_besoin" id="type_besoin" required onchange="updateCategoriesFor('besoin')">
                <option value="">-- Sélectionnez un type --</option>
                <option value="nature">📦 Produit (Nature/Matériaux)</option>
                <option value="argent">💰 Argent</option>
            </select>
        </div>
        
        <!-- Section Produit -->
        <div id="besoin-produit-section" class="form-section" style="display:none;">
            <!-- Catégorie -->
            <div class="form-group stagger-item">
                <label>Catégorie de produit *</label>
                <select name="categorie_id" id="besoin-categorie_id" onchange="updateProduitsFor('besoin')">
                    <option value="">-- Sélectionnez une catégorie --</option>
                    <?php foreach ($categories as $cat): ?>
                        <?php if ($cat['nom_categorie'] !== 'argent'): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom_categorie']) ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Produit -->
            <div class="form-group stagger-item">
                <label>Produit *</label>
                <select name="produit_id" id="besoin-produit_id">
                    <option value="">-- Sélectionnez un produit --</option>
                </select>
            </div>
        </div>
        
        <!-- Section Argent -->
        <div id="besoin-argent-section" class="form-section" style="display:none;">
            <div class="form-group stagger-item">
                <label>Montant (Ariary) *</label>
                <input type="number" name="quantite_argent" step="100" min="0" placeholder="ex: 1000000">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group half stagger-item">
                <label>Quantité *</label>
                <input type="number" name="quantite" step="0.01" min="0.01" required>
            </div>
            
            <div class="form-group half stagger-item">
                <label>Unité *</label>
                <input type="text" name="unite" placeholder="kg, litre, Ariary..." required>
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
// Données des produits par catégorie depuis PHP
const besoinProduitsByCategory = <?php
    $byCategory = [];
    foreach ($produits as $produit) {
        $catId = $produit['categorie_id'];
        if (!isset($byCategory[$catId])) {
            $byCategory[$catId] = [];
        }
        $byCategory[$catId][] = $produit;
    }
    echo json_encode($byCategory);
?>;

// Gérer le changement de type de besoin
function updateCategoriesFor(prefix) {
    const typeBesoin = document.getElementById(prefix + '-type_besoin').value;
    const produitSection = document.getElementById(prefix + '-produit-section');
    const argentSection = document.getElementById(prefix + '-argent-section');
    
    if (typeBesoin === 'nature') {
        produitSection.style.display = 'block';
        argentSection.style.display = 'none';
    } else if (typeBesoin === 'argent') {
        produitSection.style.display = 'none';
        argentSection.style.display = 'block';
    } else {
        produitSection.style.display = 'none';
        argentSection.style.display = 'none';
    }
}

// Mettre à jour les produits en fonction de la catégorie (pour besoins)
function updateProduitsFor(prefix) {
    const categorieId = parseInt(document.getElementById(prefix + '-categorie_id').value);
    const produitSelect = document.getElementById(prefix + '-produit_id');
    
    produitSelect.innerHTML = '<option value="">-- Sélectionnez un produit --</option>';
    
    if (categorieId && besoinProduitsByCategory[categorieId]) {
        besoinProduitsByCategory[categorieId].forEach(produit => {
            const option = document.createElement('option');
            option.value = produit.id;
            option.setAttribute('data-unite', produit.unite_mesure);
            option.setAttribute('data-prix', produit.prix_unitaire_reference);
            option.textContent = produit.nom_produit + ' (' + produit.unite_mesure + ')';
            produitSelect.appendChild(option);
        });
    }
    
    // Mettre à jour l'unité quand on change de produit
    produitSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            document.getElementsByName('unite')[0].value = selectedOption.getAttribute('data-unite');
        }
    });
}

// Initialiser au chargement
document.addEventListener('DOMContentLoaded', function() {
    const typeBesoinSelect = document.getElementById('besoin-type_besoin');
    if (typeBesoinSelect) {
        // Renommer les IDs pour le besoin
        typeBesoinSelect.id = 'type_besoin';
        typeBesoinSelect.addEventListener('change', function() {
            updateCategoriesFor('besoin');
        });
    }
});
</script>