<?php
// NE PAS METTRE DOCTYPE HTML ICI !
?>

<?php if (isset($_SESSION['message'])): ?>
    <div class="alert success animate-slide-top">
        <?= $_SESSION['message'] ?>
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert error animate-slide-top">
        <?= $_SESSION['error'] ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<h1 class="animate-slide-top">📦 Gestion des dons</h1>

<div class="stats-row">
    <div class="stat-card stagger-item">
        <div class="stat-label">Types de donateurs</div>
        <div class="stat-value">2</div>
    </div>
    <div class="stat-card stagger-item">
        <div class="stat-label">Dons internationaux</div>
        <div class="stat-value"><?= $stats_donateurs['International'] ?? 0 ?></div>
    </div>
    <div class="stat-card stagger-item">
        <div class="stat-label">Dons nationaux</div>
        <div class="stat-value"><?= $stats_donateurs['National'] ?? 0 ?></div>
    </div>
</div>

<div class="stock-info animate-scale">
    <h2>Stock disponible par catégorie</h2>
    <div class="stock-grid">
        <div class="stock-item stagger-item">
            <span>Nature (riz, eau, etc.):</span>
            <strong><?= $stock['nature'] ?? 0 ?> unités</strong>
        </div>
        <div class="stock-item stagger-item">
            <span>Matériaux (tôles, ciment):</span>
            <strong><?= $stock['materiaux'] ?? 0 ?> unités</strong>
        </div>
        <div class="stock-item stagger-item">
            <span>Argent:</span>
            <strong><?= number_format($stock['argent'] ?? 0) ?> Ar</strong>
        </div>
    </div>
</div>

<?php if (!empty($top_donateurs)): ?>
<div class="top-donateurs animate-slide-left">
    <h3>🏆 Top donateurs</h3>
    <ol>
        <?php foreach ($top_donateurs as $donateur): ?>
        <li class="stagger-item">
            <?= $donateur['donateur'] ?> - 
            <?= number_format($donateur['total_donne']) ?> 
            unités
        </li>
        <?php endforeach; ?>
    </ol>
</div>
<?php endif; ?>

<div class="form-container animate-slide-right">
    <h2>Enregistrer un don</h2>
    <form action="<?= BASE_URL ?>/dons/ajouter" method="POST" id="don-form">
        <!-- Donateur -->
        <div class="form-group stagger-item">
            <label>Donateur *</label>
            <input type="text" name="donateur" placeholder="Nom du donateur" required>
        </div>
        
        <!-- Type de don (choix entre argent et nature/matériaux) -->
        <div class="form-group stagger-item">
            <label>Type de don *</label>
            <select name="type_don" id="type_don" required onchange="updateCategories()">
                <option value="">-- Sélectionnez un type --</option>
                <option value="argent">💰 Argent</option>
                <option value="produit">📦 Produit (Nature/Matériaux)</option>
            </select>
        </div>
        
        <!-- Section Argent -->
        <div id="argent-section" class="form-section" style="display:none;">
            <div class="form-group stagger-item">
                <label>Montant (Ariary) *</label>
                <input type="number" name="quantite_argent" step="100" min="0" placeholder="ex: 1000000">
            </div>
        </div>
        
        <!-- Section Produit -->
        <div id="produit-section" class="form-section" style="display:none;">
            <!-- Catégorie -->
            <div class="form-group stagger-item">
                <label>Catégorie de produit *</label>
                <select name="categorie_id" id="categorie_id" required onchange="updateProduits()">
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
                <div class="select-row">
                    <select name="produit_id" id="produit_id">
                        <option value="">-- Sélectionnez un produit --</option>
                    </select>
                    <button type="button" class="btn-secondary" onclick="toggleNewProductForm()">+ Nouveau</button>
                </div>
            </div>
            
            <!-- Formulaire nouveau produit (caché) -->
            <div id="new-product-form" style="display:none; border: 1px solid #ddd; padding: 15px; border-radius: 4px; background: #f9f9f9; margin-bottom: 15px;">
                <h4>Ajouter un nouveau produit</h4>
                <div class="form-group">
                    <label>Nom du produit *</label>
                    <input type="text" name="nouveau_produit_nom" placeholder="ex: Riz">
                </div>
                <div class="form-group">
                    <label>Prix unitaire (Ar) *</label>
                    <input type="number" name="nouveau_produit_prix" step="100" min="0" placeholder="ex: 2500">
                </div>
            </div>
            
            <!-- Quantité et Unité -->
            <div class="form-row">
                <div class="form-group half stagger-item">
                    <label>Quantité *</label>
                    <input type="number" name="quantite_produit" step="0.01" min="0.01" placeholder="ex: 100">
                </div>
                <div class="form-group half stagger-item">
                    <label>Unité *</label>
                    <input type="text" name="unite_produit" placeholder="kg, litre, plaques..." readonly>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn-primary stagger-item">Enregistrer le don</button>
    </form>
</div>

<h2 class="animate-slide-top" style="animation-delay: 0.4s;">Stock détaillé disponible</h2>
<table class="table animate-slide-bottom">
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
            <tr class="stagger-item">
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

<h2 class="animate-slide-top" style="animation-delay: 0.5s;">Historique des dons</h2>
<table class="table animate-slide-bottom" style="animation-delay: 0.1s;">
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
        <tr class="stagger-item">
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

<script>
// Données des produits par catégorie depuis PHP
const produitsByCategory = <?php
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

// Gérer le changement de type de don
function updateCategories() {
    const typeDon = document.getElementById('type_don').value;
    const argentSection = document.getElementById('argent-section');
    const produitSection = document.getElementById('produit-section');
    
    if (typeDon === 'argent') {
        argentSection.style.display = 'block';
        produitSection.style.display = 'none';
        document.querySelector('[name="quantite_argent"]').setAttribute('required', 'required');
        document.querySelector('[name="categorie_id"]').removeAttribute('required');
        document.querySelector('[name="produit_id"]').removeAttribute('required');
    } else if (typeDon === 'produit') {
        argentSection.style.display = 'none';
        produitSection.style.display = 'block';
        document.querySelector('[name="quantite_argent"]').removeAttribute('required');
        document.querySelector('[name="categorie_id"]').setAttribute('required', 'required');
        document.querySelector('[name="produit_id"]').setAttribute('required', 'required');
    } else {
        argentSection.style.display = 'none';
        produitSection.style.display = 'none';
    }
    
    document.getElementById('categorie_id').value = '';
    document.getElementById('produit_id').innerHTML = '<option value="">-- Sélectionnez un produit --</option>';
}

// Mettre à jour les produits en fonction de la catégorie
function updateProduits() {
    const categorieId = parseInt(document.getElementById('categorie_id').value);
    const produitSelect = document.getElementById('produit_id');
    
    produitSelect.innerHTML = '<option value="">-- Sélectionnez un produit --</option>';
    
    if (categorieId && produitsByCategory[categorieId]) {
        produitsByCategory[categorieId].forEach(produit => {
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
            document.getElementsByName('unite_produit')[0].value = selectedOption.getAttribute('data-unite');
        } else {
            document.getElementsByName('unite_produit')[0].value = '';
        }
    });
}

// Basculer le formulaire de création de nouveau produit
function toggleNewProductForm() {
    const form = document.getElementById('new-product-form');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

// Initialiser le formulaire au chargement
document.addEventListener('DOMContentLoaded', function() {
    updateCategories();
});

// Gérer la soumission du formulaire
document.getElementById('don-form').addEventListener('submit', function(e) {
    const typeDon = document.getElementById('type_don').value;
    
    if (!typeDon) {
        e.preventDefault();
        alert('Veuillez sélectionner un type de don');
        return;
    }
    
    if (typeDon === 'argent') {
        const montant = document.querySelector('[name="quantite_argent"]').value;
        if (!montant || montant <= 0) {
            e.preventDefault();
            alert('Veuillez entrer un montant valide');
        }
    } else if (typeDon === 'produit') {
        const produitId = document.getElementById('produit_id').value;
        const quantite = document.querySelector('[name="quantite_produit"]').value;
        
        if (!produitId) {
            e.preventDefault();
            alert('Veuillez sélectionner un produit');
            return;
        }
        
        if (!quantite || quantite <= 0) {
            e.preventDefault();
            alert('Veuillez entrer une quantité valide');
            return;
        }
    }
});
</script>