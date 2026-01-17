/**
 * Mivo Module: I18n
 * Internationalization support.
 */
class I18n {
    constructor() {
        this.currentLang = localStorage.getItem('mivo_lang') || 'en';
        this.translations = {};
        this.isLoaded = false;
        
        // Expose global helper for legacy onclicks
        window.changeLanguage = (lang) => this.loadLanguage(lang);
        
        this.ready = this.init();
    }

    async init() {
        await this.loadLanguage(this.currentLang);
        this.isLoaded = true;
    }

    async loadLanguage(lang) {
        try {
            const cacheBuster = Date.now();
            const response = await fetch(`/lang/${lang}.json?v=${cacheBuster}`);
            if (!response.ok) throw new Error(`Failed to load language: ${lang}`);
            
            this.translations = await response.json();
            this.currentLang = lang;
            localStorage.setItem('mivo_lang', lang);
            
            this.applyTranslations();
            
            // Dispatch via Mivo Event Bus
            if (window.Mivo) {
                window.Mivo.emit('languageChanged', { lang });
            }
            
            // Legacy Event for compatibility
            window.dispatchEvent(new CustomEvent('languageChanged', { detail: { lang } }));
            
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
                    element.textContent = translation; 
                }
            } else {
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
            if (this.isLoaded) console.warn(`[i18n] Missing translation for key: "${key}"`);
            text = key;
        }
        
        if (params) {
            Object.keys(params).forEach(param => {
                text = text.replace(new RegExp(`{${param}}`, 'g'), params[param]);
            });
        }
        return text;
    }
}

// Register Module
if (window.Mivo) {
    window.Mivo.registerModule('I18n', new I18n());
    // Alias for global usage if needed
    window.i18n = window.Mivo.modules.I18n; 
} else {
    // Fallback if Mivo not loaded
    window.i18n = new I18n();
}
