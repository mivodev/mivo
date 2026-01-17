/**
 * Mivo JS Core "The Kernel"
 * Central management for Modules (Services) and Components (UI).
 */
class MivoCore {
    constructor() {
        this.modules = {};
        this.components = {};
        this.events = new EventTarget();
        this.isReady = false;

        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.init());
        } else {
            this.init();
        }
    }

    /**
     * Register a Global Module (Service)
     * @param {string} name 
     * @param {Object} instance 
     */
    registerModule(name, instance) {
        this.modules[name] = instance;
        console.debug(`[Mivo] Module '${name}' registered.`);
    }

    /**
     * Register a UI Component definition
     * @param {string} name 
     * @param {Class} classRef 
     */
    registerComponent(name, classRef) {
        this.components[name] = classRef;
        console.debug(`[Mivo] Component '${name}' registered.`);
    }

    /**
     * Listen to global events
     * @param {string} eventName 
     * @param {function} callback 
     */
    on(eventName, callback) {
        this.events.addEventListener(eventName, (e) => callback(e.detail));
    }

    /**
     * Emit global events
     * @param {string} eventName 
     * @param {any} data 
     */
    emit(eventName, data) {
        this.events.dispatchEvent(new CustomEvent(eventName, { detail: data }));
        console.debug(`[Mivo] Event emitted: ${eventName}`, data);
    }

    init() {
        if (this.isReady) return;
        this.isReady = true;
        console.log('[Mivo] Framework initialized.');
        
        // Dispatch ready event for external scripts
        this.emit('ready', { timestamp: Date.now() });
    }
}

// Global Singleton
window.Mivo = new MivoCore();
