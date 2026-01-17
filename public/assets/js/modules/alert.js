/**
 * Mivo Module: Alert
 * Wraps SweetAlert2 and provides Toast notifications.
 */
class AlertModule {
    constructor() {
        // No specific initialization needed for now
    }

    /**
     * Show a simple alert dialog.
     * @param {string} type - 'success', 'error', 'warning', 'info', 'question'
     * @param {string} title 
     * @param {string} message 
     */
    fire(type, title, message = '', options = {}) {
        const typeMap = {
            'success': { icon: 'check-circle-2', color: 'text-success' },
            'error':   { icon: 'x-circle', color: 'text-error' },
            'warning': { icon: 'alert-triangle', color: 'text-warning' },
            'info':    { icon: 'info', color: 'text-info' },
            'question':{ icon: 'help-circle', color: 'text-question' }
        };

        const config = typeMap[type] || typeMap['info'];

        // Default Config
        const defaultConfig = {
            iconHtml: `<i data-lucide="${config.icon}" class="w-12 h-12 ${config.color}"></i>`,
            title: title,
            html: message,
            confirmButtonText: 'OK',
            customClass: {
                popup: 'swal2-premium-card',
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-secondary',
            },
            buttonsStyling: false,
            heightAuto: false,
            scrollbarPadding: false,
            didOpen: () => {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        };

        // Merge user options with default config
        // Special deep merge for customClass if provided to avoid wiping defaults completely?
        // simple spread for now, user should know what they are doing if overriding classes.
        // Actually, let's smart merge customClass
        if (options.customClass) {
            options.customClass = { 
                ...defaultConfig.customClass, 
                ...options.customClass 
            };
        }

        const finalConfig = { ...defaultConfig, ...options };

        return Swal.fire(finalConfig);
    }

    /**
     * Show a confirmation dialog.
     */
    confirm(title, message = '', confirmText = 'Yes, Proceed', cancelText = 'Cancel') {
        return Swal.fire({
            iconHtml: `<i data-lucide="help-circle" class="w-12 h-12 text-question"></i>`,
            title: title,
            html: message,
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            customClass: {
                popup: 'swal2-premium-card',
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-secondary',
            },
            buttonsStyling: false,
            reverseButtons: true,
            heightAuto: false,
            scrollbarPadding: false,
            didOpen: () => {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        }).then(result => result.isConfirmed);
    }

    /**
     * Show a stacking toast notification.
     */
    toast(type, title, message = '', duration = 5000) {
        let container = document.getElementById('mivo-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'mivo-toast-container';
            document.body.appendChild(container);
        }

        const typeMap = {
            'success': { icon: 'check-circle-2', color: 'text-success' },
            'error':   { icon: 'x-circle', color: 'text-error' },
            'warning': { icon: 'alert-triangle', color: 'text-warning' },
            'info':    { icon: 'info', color: 'text-info' }
        };

        const config = typeMap[type] || typeMap['info'];
        
        const toast = document.createElement('div');
        toast.className = `mivo-toast ${config.color}`;
        
        toast.innerHTML = `
            <div class="mivo-toast-icon">
                <i data-lucide="${config.icon}" class="w-5 h-5"></i>
            </div>
            <div class="mivo-toast-content">
                <div class="mivo-toast-title">${title}</div>
                ${message ? `<div class="mivo-toast-message">${message}</div>` : ''}
            </div>
            <button class="mivo-toast-close">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
            <div class="mivo-toast-progress"></div>
        `;

        container.appendChild(toast);
        if (typeof lucide !== 'undefined') lucide.createIcons();

        // Close logic
        const closeToast = () => {
            toast.classList.add('mivo-toast-fade-out');
            setTimeout(() => {
                toast.remove();
                if (container.children.length === 0) container.remove();
            }, 300);
        };

        toast.querySelector('.mivo-toast-close').addEventListener('click', closeToast);

        // Progress Bar
        const progress = toast.querySelector('.mivo-toast-progress');
        const start = Date.now();
        
        const updateProgress = () => {
            const elapsed = Date.now() - start;
            const percentage = Math.min((elapsed / duration) * 100, 100);
            progress.style.width = percentage + '%';
            
            if (percentage < 100) {
                requestAnimationFrame(updateProgress);
            } else {
                closeToast();
            }
        };
        
        requestAnimationFrame(updateProgress);
    }

    /**
     * Modal Form Logic
     */
    form(title, html, confirmText = 'Save', preConfirmFn = null, didOpenFn = null, customClass = '') {
        return Swal.fire({
            title: title,
            html: html,
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: window.i18n ? window.i18n.t('common.cancel') : 'Cancel',
            customClass: {
                popup: `swal2-premium-card ${customClass}`,
                title: 'text-xl font-bold text-foreground mb-4',
                htmlContainer: 'text-left overflow-visible', // overflow-visible for selects
                confirmButton: 'btn btn-primary px-6',
                cancelButton: 'btn btn-secondary px-6',
                actions: 'gap-3'
            },
            buttonsStyling: false,
            reverseButtons: true,
            heightAuto: false,
            scrollbarPadding: false,
            didOpen: () => {
                if (typeof lucide !== 'undefined') lucide.createIcons();
                
                const popup = Swal.getHtmlContainer();
                
                if (didOpenFn && typeof didOpenFn === 'function') {
                    didOpenFn(popup);
                }

                // Initialize Custom Selects using Mivo Component if available
                if (popup && window.Mivo && window.Mivo.components.Select) {
                     const selects = popup.querySelectorAll('select');
                     selects.forEach(el => {
                         if (!el.classList.contains('custom-select')) {
                             el.classList.add('custom-select');
                         }
                         new window.Mivo.components.Select(el); 
                     });
                }

                const firstInput = popup.querySelector('input:not([type="hidden"]), textarea');
                if (firstInput) firstInput.focus();
            },
            preConfirm: () => {
                return preConfirmFn ? preConfirmFn() : true;
            }
        });
    }
}

// Register Module
if (window.Mivo) {
    const alertModule = new AlertModule();
    window.Mivo.registerModule('Alert', alertModule);
    
    // Add Aliases to Mivo object for easy access (Mivo.alert(...))
    // This maintains backward compatibility with the old object literal style
    window.Mivo.alert = (type, title, msg, opts) => alertModule.fire(type, title, msg, opts);
    window.Mivo.confirm = (t, m, c, cx) => alertModule.confirm(t, m, c, cx);
    window.Mivo.toast = (t, ti, m, d) => alertModule.toast(t, ti, m, d);
    // Aliases for Mivo.modal call style
    window.Mivo.modal = {
        form: (t, h, c, p, o, cc) => alertModule.form(t, h, c, p, o, cc)
    };
    // Wait, modal was nested. Let's expose the form method carefully or keep it on the module.
    // Let's just expose the module mostly.
}
