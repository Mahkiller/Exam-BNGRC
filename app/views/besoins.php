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
    <form action="<?= BASE_URL ?>/besoins/ajouter" method="POST">
        <div class="form-group stagger-item">
            <label>Ville:</label>
            <select name="ville_id" required>
                <option value="">Sélectionner une ville</option>
                <?php foreach ($villes as $ville): ?>
                <option value="<?= $ville['id'] ?>"><?= $ville['nom_ville'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group stagger-item">
            <label>Type de besoin:</label>
            <select name="type_besoin" required>
                <option value="nature">Nature (riz, huile, eau...)</option>
                <option value="materiaux">Matériaux (tôle, ciment, clou...)</option>
                <option value="argent">Argent</option>
            </select>
        </div>
        
        <div class="form-group stagger-item">
            <label>Description:</label>
            <input type="text" name="description" placeholder="ex: Riz, Tôles, Fonds reconstruction" required>
        </div>
        
        <div class="form-group stagger-item">
            <label>Quantité:</label>
            <input type="number" name="quantite" step="0.01" min="0.01" required>
        </div>
        
        <div class="form-group stagger-item">
            <label>Unité:</label>
            <input type="text" name="unite" placeholder="kg, litre, Ariary, plaques..." required>
        </div>
        
        <div class="form-group stagger-item">
            <label>Niveau d'urgence:</label>
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