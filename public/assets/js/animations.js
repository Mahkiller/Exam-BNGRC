/**
 * Animations Module
 * Gère les animations des éléments à l'entrée d'une page
 * Crée des effets fluides et attrayants
 */

class PageAnimations {
    constructor() {
        this.initialized = false;
    }

    /**
     * Initialise les animations au chargement
     */
    init() {
        if (this.initialized) return;
        this.initialized = true;

        document.addEventListener('DOMContentLoaded', () => {
            this.animatePageEntry();
            this.observeViewport();
        });

        // Réanimer si c'est une navigation AJAX
        window.addEventListener('pageChange', () => {
            this.animatePageEntry();
        });
    }

    /**
     * Lance les animations à l'entrée d'une page
     */
    animatePageEntry() {
        // Animation du titre
        const titles = document.querySelectorAll('h1, h2');
        titles.forEach((el, index) => {
            el.classList.add('animate-slide-top');
            el.style.animationDelay = (index * 0.1) + 's';
        });

        // Animation des stat cards avec stagger
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach((card) => {
            card.classList.add('stagger-item', 'animate-scale');
        });

        // Animation des ville cards
        const villeCards = document.querySelectorAll('.ville-card');
        villeCards.forEach((card) => {
            card.classList.add('stagger-item', 'animate-slide-bottom');
        });

        // Animation des tableaux
        const tables = document.querySelectorAll('table');
        tables.forEach((table) => {
            table.classList.add('animate-slide-top');
        });

        // Animation des lignes du tableau
        const tableRows = document.querySelectorAll('tbody tr');
        tableRows.forEach((row) => {
            row.classList.add('stagger-item', 'animate-fade');
        });

        // Animation des cards génériques
        const cards = document.querySelectorAll('.card, .besoin-card, .don-card, .donateur-card');
        cards.forEach((card) => {
            card.classList.add('stagger-item', 'animate-slide-left');
        });

        // Animation des formulaires
        const forms = document.querySelectorAll('form');
        forms.forEach((form) => {
            form.classList.add('animate-slide-right');
        });

        // Animation des inputs
        const formGroups = document.querySelectorAll('.form-group');
        formGroups.forEach((group) => {
            group.classList.add('stagger-item', 'animate-fade');
        });

        // Animation des alertes
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach((alert) => {
            alert.classList.add('animate-slide-top');
        });

        // Animation du stock info
        const stockInfo = document.querySelector('.stock-info');
        if (stockInfo) {
            stockInfo.classList.add('animate-scale');
        }

        // Animation des quick menus
        const quickLinks = document.querySelectorAll('.quick-link');
        quickLinks.forEach((link) => {
            link.classList.add('stagger-item', 'animate-bounce');
        });

        // Animation des stock badges
        const stockBadges = document.querySelectorAll('.stock-badge');
        stockBadges.forEach((badge) => {
            badge.classList.add('stagger-item', 'animate-fade');
        });

        // Animation des listes
        const listItems = document.querySelectorAll('ul li, ol li');
        listItems.forEach((item) => {
            item.classList.add('stagger-item', 'animate-slide-left');
        });
    }

    /**
     * Observer les éléments qui entrent dans le viewport
     * Pour animer les éléments au scroll
     */
    observeViewport() {
        if (!('IntersectionObserver' in window)) {
            console.warn('IntersectionObserver not supported');
            return;
        }

        const options = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.animateElement(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, options);

        // Observer les cartes et autres éléments
        const elements = document.querySelectorAll('.stat-card, .ville-card, .card, .besoin-card, .donateur-card, tbody tr');
        elements.forEach(el => observer.observe(el));
    }

    /**
     * Anime un élément spécifique
     */
    animateElement(element) {
        if (element.classList.contains('stat-card')) {
            element.classList.add('animate-scale');
        } else if (element.classList.contains('ville-card')) {
            element.classList.add('animate-slide-bottom');
        } else if (element.classList.contains('donateur-card')) {
            element.classList.add('animate-slide-left');
        } else {
            element.classList.add('animate-fade');
        }
    }

    /**
     * Anime une action spécifique (ajout, suppression, etc.)
     */
    animateAction(element, type = 'success') {
        element.classList.add('animate-pulse');
        
        setTimeout(() => {
            element.classList.remove('animate-pulse');
        }, 2000);
    }

    /**
     * Animation de transition entre pages (si AJAX)
     */
    animatePageTransition(container) {
        container.classList.add('fadeOut');
        setTimeout(() => {
            container.classList.remove('fadeOut');
            container.classList.add('animate-fade');
            this.animatePageEntry();
        }, 200);
    }

    /**
     * Animation d'erreur avec secousse
     */
    animateError(element) {
        element.style.animation = 'none';
        setTimeout(() => {
            element.style.animation = 'shake 0.5s';
        }, 10);
    }
}

/**
 * Animation de secousse pour les erreurs
 */
const style = document.createElement('style');
style.textContent = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
    
    .fadeOut {
        animation: fadeOut 0.3s ease-out forwards;
    }
    
    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Initialiser les animations au chargement
const pageAnimations = new PageAnimations();
pageAnimations.init();

/**
 * Fonction globale pour animer des éléments dynamiquement
 */
window.animateElement = (selector, animation = 'animate-fade') => {
    const elements = document.querySelectorAll(selector);
    elements.forEach((el, index) => {
        el.classList.add(animation);
        el.style.animationDelay = (index * 0.1) + 's';
    });
};

/**
 * Animation au clic des boutons
 */
document.addEventListener('click', function(e) {
    const btn = e.target.closest('button, a[href*="#"]');
    if (btn && btn.classList.contains('btn-primary')) {
        btn.classList.add('animate-bounce');
        setTimeout(() => btn.classList.remove('animate-bounce'), 1000);
    }
});
