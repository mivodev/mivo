/**
 * Global Alert Helper for Mivo
 * Provides a standardized way to trigger premium SweetAlert2 dialogs.
 */
const Mivo = {
    /**
     * Show a simple alert dialog.
     * @param {string} type - 'success', 'error', 'warning', 'info', 'question'
     * @param {string} title - The title of the alert
     * @param {string} message - The body text/HTML
     * @returns {Promise}
     */
    alert: function(type, title, message = '') {
        const typeMap = {
            'success': { icon: 'check-circle-2', color: 'text-success' },
            'error':   { icon: 'x-circle', color: 'text-error' },
            'warning': { icon: 'alert-triangle', color: 'text-warning' },
            'info':    { icon: 'info', color: 'text-info' },
            'question':{ icon: 'help-circle', color: 'text-question' }
        };

        const config = typeMap[type] || typeMap['info'];

        return Swal.fire({
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
            didOpen: () => {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        });
    },

    /**
     * Show a confirmation dialog.
     * @param {string} title - The title of the confirmation
     * @param {string} message - The body text/HTML
     * @param {string} confirmText - Text for the confirm button
     * @param {string} cancelText - Text for the cancel button
     * @returns {Promise} Resolves if confirmed, rejects if cancelled
     */
    confirm: function(title, message = '', confirmText = 'Yes, Proceed', cancelText = 'Cancel') {
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
            didOpen: () => {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        }).then(result => result.isConfirmed);
    },

    /**
     * Show a premium stacking toast.
     * @param {string} type - 'success', 'error', 'warning', 'info'
     * @param {string} title - Title
     * @param {string} message - Body text
     * @param {number} duration - ms before auto-close
     */
    toast: function(type, title, message = '', duration = 5000) {
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

        // Auto-close with progress bar
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
};

// Also expose as global shortcuts if needed
window.Mivo = Mivo;
