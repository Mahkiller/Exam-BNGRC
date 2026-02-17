/**
 * Configuration des Animations BNGRC
 * Modifiez ce fichier pour personnaliser les comportements des animations
 */

const ANIMATION_CONFIG = {
    // ==========================================
    // ACTIVATION GLOBALE
    // ==========================================
    enabled: true, // Mettre à false pour désactiver toutes les animations
    
    // ==========================================
    // DURÉES (en millisecondes)
    // ==========================================
    durations: {
        default: 600,      // Durée standard des animations
        fast: 300,         // Animations rapides (mobile)
        slow: 900,         // Animations lentes (effet dramatique)
        stagger: 100       // Délai entre les éléments en cascade
    },
    
    // ==========================================
    // DELAYS EN CASCADE (stagger effect)
    // ==========================================
    stagger: {
        enabled: true,     // Activer l'effet cascade
        increment: 100,    // Délai entre chaque élément (ms)
        maxDelay: 1000     // Délai maximum pour le dernier élément
    },
    
    // ==========================================
    // OBSERVATIONS VIEWPORT (scroll)
    // ==========================================
    viewport: {
        enabled: true,     // Animer les éléments au scroll
        threshold: 0.1,    // Déclencher quand 10% est visible
        rootMargin: '0px 0px -50px 0px' // Marge pour trigger
    },
    
    // ==========================================
    // ANIMATIONS SPÉCIFIQUES
    // ==========================================
    animations: {
        slideIn: {
            enabled: true,
            duration: 600,
            types: ['left', 'right', 'top', 'bottom']
        },
        
        fadeIn: {
            enabled: true,
            duration: 600
        },
        
        scale: {
            enabled: true,
            duration: 600,
            from: 0.85,
            to: 1
        },
        
        pulse: {
            enabled: true,
            duration: 2000
        },
        
        bounce: {
            enabled: true,
            duration: 1000
        }
    },
    
    // ==========================================
    // SÉLECTEURS À ANIMER AUTOMATIQUEMENT
    // ==========================================
    selectors: {
        titles: ['h1', 'h2'],              // Titres
        cards: [
            '.stat-card',
            '.ville-card',
            '.donateur-card',
            '.card',
            '.besoin-card',
            '.don-card'
        ],
        forms: ['form', '.form-container', '.form-group'],
        tables: ['table'],
        tableRows: ['tbody tr'],
        alerts: ['.alert'],
        buttons: ['.btn-primary', '.btn-attribuer'],
        lists: ['ul li', 'ol li']
    },
    
    // ==========================================
    // BREAKPOINTS RESPONSIVE
    // ==========================================
    responsive: {
        desktop: {
            breakpoint: 1024,
            duration: 600,
            staggerDelay: 100
        },
        tablet: {
            breakpoint: 768,
            duration: 500,
            staggerDelay: 75
        },
        mobile: {
            breakpoint: 600,
            duration: 400,
            staggerDelay: 50
        },
        small: {
            breakpoint: 400,
            duration: 300,
            staggerDelay: 50,
            fadeOnly: true // Seulement fade-in sur petit écran
        }
    },
    
    // ==========================================
    // LOGS & DEBUG
    // ==========================================
    debug: false, // Mettre à true pour voir les logs dans la console
    
    // ==========================================
    // HOOKS (fonctions de callback)
    // ==========================================
    hooks: {
        onInit: () => console.log('🎨 Animations initialisées'),
        onAnimationStart: (element) => {}, // Appelé au début d'une animation
        onAnimationEnd: (element) => {},   // Appelé à la fin d'une animation
        onPageChange: () => {}              // Appelé lors d'un changement de page
    }
};

// ==========================================
// HELPER: Obtenir la config pour le device
// ==========================================
function getResponsiveConfig() {
    const width = window.innerWidth;
    
    if (width <= 400) return ANIMATION_CONFIG.responsive.small;
    if (width <= 600) return ANIMATION_CONFIG.responsive.mobile;
    if (width <= 768) return ANIMATION_CONFIG.responsive.tablet;
    return ANIMATION_CONFIG.responsive.desktop;
}

// ==========================================
// HELPER: Vérifier si les animations sont activées
// ==========================================
function areAnimationsEnabled() {
    return ANIMATION_CONFIG.enabled && !localStorage.getItem('disableAnimations');
}

// ==========================================
// HELPER: Désactiver les animations (stockage local)
// ==========================================
function disableAnimations() {
    localStorage.setItem('disableAnimations', 'true');
    location.reload();
}

// ==========================================
// HELPER: Réactiver les animations
// ==========================================
function enableAnimations() {
    localStorage.removeItem('disableAnimations');
    location.reload();
}

// ==========================================
// HELPER: Obtenir la classe CSS pour une animation
// ==========================================
function getAnimationClass(type) {
    return `animate-${type}`;
}

// ==========================================
// Export
// ==========================================
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        ANIMATION_CONFIG,
        getResponsiveConfig,
        areAnimationsEnabled,
        disableAnimations,
        enableAnimations,
        getAnimationClass
    };
}
