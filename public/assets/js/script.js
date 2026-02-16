document.addEventListener('DOMContentLoaded', function() {
    
    // Gestion attribution AJAX
    const boutonsAttribuer = document.querySelectorAll('.btn-attribuer');
    
    boutonsAttribuer.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
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
                
                // Désactiver le bouton pendant la requête
                btn.disabled = true;
                btn.textContent = 'Attribution...';
                
                // Appel AJAX
                fetch('<?= BASE_URL ?>/attribution/attribuer', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `besoin_id=${encodeURIComponent(besoinId)}&don_id=&quantite=${encodeURIComponent(quantite)}&type=${encodeURIComponent(type)}`
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Afficher message de succès et recharger
                        showSuccess(errorSpan, 'Attribution réussie!');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        showError(errorSpan, data.message || 'Erreur lors de l\'attribution');
                        btn.disabled = false;
                        btn.textContent = 'Attribuer';
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showError(errorSpan, 'Erreur de communication: ' + error.message);
                    btn.disabled = false;
                    btn.textContent = 'Attribuer';
                });
            });
        });
    });
    
    function showError(element, message) {
        element.textContent = message;
        element.style.display = 'block';
        element.style.color = 'red';
        element.style.fontWeight = 'bold';
        setTimeout(() => {
            element.style.display = 'none';
        }, 4000);
    }
    
    function showSuccess(element, message) {
        element.textContent = message;
        element.style.display = 'block';
        element.style.color = 'green';
        element.style.fontWeight = 'bold';
    }
    
    function verifierStockDisponible(type, quantite, callback) {
        // Vérification simple - on suppose que c'est disponible
        // Dans une vraie app, appel API pour vérifier le stock
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
    }
});