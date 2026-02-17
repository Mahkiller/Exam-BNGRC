const ANIMATION_CONFIG = {
    enabled: true, 
    durations: {
        default: 600,      
        fast: 300,         
        slow: 900,         
        stagger: 100       
    },
    stagger: {
        enabled: true,     
        increment: 100,    
        maxDelay: 1000     
    },
    viewport: {
        enabled: true,     
        threshold: 0.1,    
        rootMargin: '0px 0px -50px 0px' // Marge pour trigger
    },
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
    selectors: {
        titles: ['h1', 'h2'],              
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
            fadeOnly: true 
        }
    },
    debug: false, 
    hooks: {
        onInit: () => console.log('🎨 Animations initialisées'),
        onAnimationStart: (element) => {}, 
        onAnimationEnd: (element) => {},   
        onPageChange: () => {}              
    }
};
function getResponsiveConfig() {
    const width = window.innerWidth;
    if (width <= 400) return ANIMATION_CONFIG.responsive.small;
    if (width <= 600) return ANIMATION_CONFIG.responsive.mobile;
    if (width <= 768) return ANIMATION_CONFIG.responsive.tablet;
    return ANIMATION_CONFIG.responsive.desktop;
}
function areAnimationsEnabled() {
    return ANIMATION_CONFIG.enabled && !localStorage.getItem('disableAnimations');
}
function disableAnimations() {
    localStorage.setItem('disableAnimations', 'true');
    location.reload();
}
function enableAnimations() {
    localStorage.removeItem('disableAnimations');
    location.reload();
}
function getAnimationClass(type) {
    return `animate-${type}`;
}
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
