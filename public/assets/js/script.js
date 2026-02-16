document.addEventListener('DOMContentLoaded', function() {
    
    // Gestion attribution AJAX
    const boutonsAttribuer = document.querySelectorAll('.btn-attribuer');
    
    boutonsAttribuer.forEach(btn => {
        btn.addEventListener('click', function(e) {
            const besoinId = this.dataset.besoinId;
            const type = this.dataset.type;
            const maxQuantite = parseFloat(this.dataset.max);
            const unite = this.dataset.unite;
            const input = this.closest('td').querySelector('.quantite-input');
            const quantite = parseFloat(input.value);
            const errorSpan = this.closest('td').querySelector('.error-message');
            
            // Validation
            if (isNaN(quantite) || quantite <= 0) {
                showError(errorSpan, 'Veuillez entrer une quantité valide');
                return;
            }
            
            if (quantite > maxQuantite) {
                showError(errorSpan, `La quantité ne peut pas dépasser ${maxQuantite} ${unite}`);
                return;
            }
            
            // Vérification rapide stock disponible (via API)
            verifierStockDisponible(type, quantite, function(disponible, message) {
                if (!disponible) {
                    showError(errorSpan, message);
                    return;
                }
                
                // Appel AJAX
                fetch('<?= BASE_URL ?>/dons/attribuer', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `besoin_id=${besoinId}&quantite=${quantite}&type=${type}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Recharger la page pour voir les mises à jour
                        location.reload();
                    } else {
                        showError(errorSpan, data.message);
                    }
                })
                .catch(error => {
                    showError(errorSpan, 'Erreur de communication');
                });
            });
        });
    });
    
    function showError(element, message) {
        element.textContent = message;
        element.style.display = 'block';
        setTimeout(() => {
            element.style.display = 'none';
        }, 3000);
    }
    
    function verifierStockDisponible(type, quantite, callback) {
        // Simulation - dans une vraie app, appel API
        // Pour l'instant, on suppose que c'est disponible
        callback(true, '');
    }
    
    // Validation formulaire quantité positive
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const quantiteInput = this.querySelector('input[name="quantite"]');
            if (quantiteInput) {
                const quantite = parseFloat(quantiteInput.value);
                if (quantite <= 0) {
                    e.preventDefault();
                    alert('La quantité doit être positive');
                }
            }
        });
    });
    
    // Filtre par niveau d'urgence (optionnel)
    const filtreUrgence = document.getElementById('filtre-urgence');
    if (filtreUrgence) {
        filtreUrgence.addEventListener('change', function() {
            const valeur = this.value;
            const lignes = document.querySelectorAll('tbody tr');
            
            lignes.forEach(ligne => {
                if (valeur === '' || ligne.classList.contains(valeur)) {
                    ligne.style.display = '';
                } else {
                    ligne.style.display = 'none';
                }
            });
        });
    }
});