    <footer class="mt-auto py-6 text-center text-xs text-accents-5 opacity-60">
        <?= \App\Config\SiteConfig::getFooter() ?>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize Lucide Icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
        
        <?php if (\App\Helpers\FlashHelper::has()): ?>
            <?php $flash = \App\Helpers\FlashHelper::get(); ?>
            document.addEventListener('DOMContentLoaded', () => {
                // Map Flash Type to Lucide Icon & Color Class
                const typeMap = {
                    'success': { icon: 'check-circle-2', color: 'text-success' },
                    'error':   { icon: 'x-circle', color: 'text-error' },
                    'warning': { icon: 'alert-triangle', color: 'text-warning' },
                    'info':    { icon: 'info', color: 'text-info' },
                    'question':{ icon: 'help-circle', color: 'text-question' }
                };

                const type = '<?= $flash['type'] ?>';
                const config = typeMap[type] || typeMap['info'];
                
                let title = '<?= addslashes($flash['title']) ?>';
                let message = '<?= addslashes($flash['message'] ?? '') ?>';
                const params = <?= json_encode($flash['params'] ?? []) ?>;
                const isTranslated = <?= $flash['isTranslated'] ? 'true' : 'false' ?>;
                
                const showFlash = () => {
                    if (isTranslated && window.i18n) {
                        title = window.i18n.t(title, params);
                        message = window.i18n.t(message, params);
                    }

                    // Use Custom Toasts for most notifications (Success, Info, Error)
                    // Only use Modal (Swal) for specific heavy warnings or questions if needed
                    if (['success', 'info', 'error', 'warning'].includes(type)) {
                        // Assuming Mivo.toast is available globally or via another script check
                        if (window.Mivo && window.Mivo.toast) {
                             Mivo.toast(type, title, message);
                        } else {
                             console.log('Toast:', title, message);
                        }
                    } else {
                        // Use Swal for 'question' or fallback
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                iconHtml: `<i data-lucide="${config.icon}" class="w-12 h-12 ${config.color}"></i>`,
                                title: title,
                                text: message,
                                confirmButtonText: 'OK',
                                customClass: {
                                    popup: 'swal2-premium-card',
                                    confirmButton: 'btn btn-primary',
                                    cancelButton: 'btn btn-secondary',
                                },
                                buttonsStyling: false,
                                heightAuto: false,
                                didOpen: () => {
                                    lucide.createIcons();
                                }
                            });
                        } else {
                             alert(`${title}\n${message}`);
                        }
                    }
                };

                if (window.i18n && window.i18n.ready) {
                    window.i18n.ready.then(showFlash);
                } else {
                    showFlash();
                }
            });
        <?php endif; ?>
    </script>
</body>
</html>
