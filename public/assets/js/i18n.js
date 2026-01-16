class I18n {
    constructor() {
        this.currentLang = localStorage.getItem('mivo_lang') || 'en';
        this.translations = {};
        this.isLoaded = false;
        // The ready promise resolves after the first language load
        this.ready = this.init();
    }

    async init() {
        await this.loadLanguage(this.currentLang);
        this.isLoaded = true;
    }

    async loadLanguage(lang) {
        try {
            // Add cache busting to ensure fresh translation files
            const cacheBuster = Date.now();
            const response = await fetch(`/lang/${lang}.json?v=${cacheBuster}`);
            if (!response.ok) throw new Error(`Failed to load language: ${lang}`);
            
            this.translations = await response.json();
            this.currentLang = lang;
            localStorage.setItem('mivo_lang', lang);
            this.applyTranslations();
            
            // Dispatch event for other components
            window.dispatchEvent(new CustomEvent('languageChanged', { detail: { lang } }));
            
            // Update html lang attribute
            document.documentElement.lang = lang;
        } catch (error) {
            console.error('I18n Error:', error);
        }
    }

    applyTranslations() {
        document.querySelectorAll('[data-i18n]').forEach(element => {
            const key = element.getAttribute('data-i18n');
            const translation = this.getNestedValue(this.translations, key);
            
            if (translation) {
                if (element.tagName === 'INPUT' && element.getAttribute('placeholder')) {
                    element.placeholder = translation;
                } else {
                    // Check if element has child nodes that are not text (e.g. icons)
                    // If simple text, just replace
                    // If complex, try to preserve icon? 
                    // For now, let's assume strictly text replacement or user wraps text in span
                    // Better approach: Look for a text node? 
                    // Simplest for now: innerText
                    element.textContent = translation; 
                }
            } else {
                // Log missing translation for developers (only if fully loaded)
                if (this.isLoaded) {
                    console.warn(`[i18n] Missing translation for key: "${key}" (lang: ${this.currentLang})`);
                }
            }
        });
    }

    getNestedValue(obj, path) {
        return path.split('.').reduce((acc, part) => acc && acc[part], obj);
    }
    
    t(key, params = {}) {
        let text = this.getNestedValue(this.translations, key);
        
        if (!text) {
            if (this.isLoaded) {
                console.warn(`[i18n] Missing translation for key: "${key}" (lang: ${this.currentLang})`);
            }
            text = key; // Fallback to key
        }
        
        // Simple interpolation: {key}
        if (params) {
            Object.keys(params).forEach(param => {
                text = text.replace(new RegExp(`{${param}}`, 'g'), params[param]);
            });
        }
        return text;
    }
}

// Initialize
window.i18n = new I18n();

// Global helper
function changeLanguage(lang) {
    window.i18n.loadLanguage(lang);
}
