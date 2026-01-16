    <?php if (isset($session) && !empty($session)): ?>
            </div> <!-- /.max-w-7xl (Sidebar content) -->
        </main>
    </div> <!-- /.flex-col (Main Content Wrapper) -->
</div> <!-- /.flex h-screen (Sidebar Layout Root) -->
    <?php else: ?>
    </div> <!-- /.container (Navbar Global) -->
    
    <footer class="border-t border-accents-2 bg-background mt-auto transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 py-6 text-center text-sm text-accents-5">
            <p><?= \App\Config\SiteConfig::getFooter() ?></p>
        </div>
    </footer>
    <?php endif; ?>

    <script>
        // Global Theme Toggle Logic (Class-based for multiple instances)
        document.addEventListener('DOMContentLoaded', () => {
            const toggleButtons = document.querySelectorAll('.theme-toggle');
            
            // Function to update all icons based on current mode
            const updateIcons = (isDark) => {
                const darkIcons = document.querySelectorAll('.theme-toggle-dark-icon');
                const lightIcons = document.querySelectorAll('.theme-toggle-light-icon');
                
                if (isDark) {
                    darkIcons.forEach(el => el.classList.add('hidden'));
                    lightIcons.forEach(el => el.classList.remove('hidden'));
                } else {
                    darkIcons.forEach(el => el.classList.remove('hidden'));
                    lightIcons.forEach(el => el.classList.add('hidden'));
                }
            };

            // Initial Check
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                updateIcons(true);
            } else {
                updateIcons(false);
            }

            // Click Handlers
            toggleButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Update LocalStorage & HTML Class
                    if (localStorage.theme === 'dark') {
                        document.documentElement.classList.remove('dark');
                        localStorage.theme = 'light';
                        updateIcons(false);
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.theme = 'dark';
                        updateIcons(true);
                    }
                });
            });

            // Sidebar Toggle Logic
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const sidebarClose = document.getElementById('sidebar-close');

            if (sidebar && mobileMenuToggle) {
                const toggleSidebar = () => {
                   const isClosed = sidebar.classList.contains('-translate-x-full');
                   if (isClosed) {
                       // Open
                       sidebar.classList.remove('-translate-x-full');
                       sidebarOverlay.classList.remove('hidden');
                       // Small delay to allow display:block to apply before opacity transition
                       setTimeout(() => sidebarOverlay.classList.remove('opacity-0'), 10);
                   } else {
                       // Close
                       sidebar.classList.add('-translate-x-full');
                       sidebarOverlay.classList.add('opacity-0');
                       setTimeout(() => sidebarOverlay.classList.add('hidden'), 200);
                   }
                };

                mobileMenuToggle.addEventListener('click', toggleSidebar);
                if (sidebarClose) sidebarClose.addEventListener('click', toggleSidebar);
                if (sidebarOverlay) sidebarOverlay.addEventListener('click', toggleSidebar);
            }
            
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

                    // Use Toasts for all flash notifications
                    Mivo.toast(type, title, message);
                };

                if (window.i18n && window.i18n.ready) {
                    window.i18n.ready.then(showFlash);
                } else {
                    showFlash();
                }
            });
        <?php endif; ?>
    </script>
    <script>
        // Global Dropdown & Sidebar Logic
        function toggleMenu(menuId, button) {
            const menu = document.getElementById(menuId);
            if (!menu) return;
            
            // Handle Dropdowns (IDs start with 'lang-' or 'session-')
            if (menuId.startsWith('lang-') || menuId === 'session-dropdown') {
                if (menu.classList.contains('invisible')) {
                    // Open
                    menu.classList.remove('opacity-0', 'scale-95', 'invisible', 'pointer-events-none');
                    menu.classList.add('opacity-100', 'scale-100', 'visible', 'pointer-events-auto');
                } else {
                    // Close
                    menu.classList.add('opacity-0', 'scale-95', 'invisible', 'pointer-events-none');
                    menu.classList.remove('opacity-100', 'scale-100', 'visible', 'pointer-events-auto');
                }
                return;
            }

            // Handle Collapsible (Max-Height + Fade for Navbar)
            const isOpening = menu.style.maxHeight === '0px' || menu.style.maxHeight === '';
            const chevron = button.querySelector('[data-lucide="chevron-down"]');
            const burger = button.querySelector('[data-lucide="menu"]');
            
            if (isOpening) {
                menu.style.maxHeight = menu.scrollHeight + "px";
                if (chevron) chevron.classList.add('rotate-180');
                if (burger) burger.classList.add('rotate-90');
                
                if (menuId === 'mobile-navbar-menu') {
                    menu.classList.remove('opacity-0', 'invisible');
                    menu.classList.add('opacity-100', 'visible');
                }
            } else {
                menu.style.maxHeight = "0px";
                if (chevron) chevron.classList.remove('rotate-180');
                if (burger) burger.classList.remove('rotate-90');
                
                if (menuId === 'mobile-navbar-menu') {
                    menu.classList.add('opacity-0', 'invisible');
                    menu.classList.remove('opacity-100', 'visible');
                }
            }
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const dropdowns = document.querySelectorAll('[id^="lang-dropdown"], #session-dropdown');
            dropdowns.forEach(dropdown => {
                if (!dropdown.classList.contains('invisible')) {
                    // Find the trigger button (previous sibling usually)
                    // Robust way: check if click is inside dropdown OR inside the button that toggles it
                    // Since button calls toggleMenu, we just need to ignore clicks inside dropdown and button?
                    // Actually, simpler: just check if click is OUTSIDE dropdown.
                    // But if click is on button, let button handler toggle it (don't double toggle).
                    
                    const button = document.querySelector(`button[onclick*="'${dropdown.id}'"]`);
                    
                    if (!dropdown.contains(event.target) && (!button || !button.contains(event.target))) {
                         dropdown.classList.add('opacity-0', 'scale-95', 'invisible', 'pointer-events-none');
                         dropdown.classList.remove('opacity-100', 'scale-100', 'visible', 'pointer-events-auto');
                    }
                }
            });
        });

        // Helper for confirm actions
        async function confirmAction(url, message) {
            const title = message.includes('Reboot') ? 'Reboot Router?' : 'Shutdown Router?';
            const okText = message.includes('Reboot') ? 'Reboot' : 'Shutdown';
            
            const confirmed = await Mivo.confirm(title, message, okText, 'Cancel');
            if (!confirmed) return;

            try {
                const res = await fetch(url, { method: 'POST' });
                const data = await res.json();
                
                if (data.success) {
                    Mivo.toast('success', title.replace('?', ''), 'The command has been sent to the router.');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Action Failed',
                        text: data.error || 'Unknown error occurred.',
                        background: 'rgba(255, 255, 255, 0.8)',
                        backdrop: 'rgba(0,0,0,0.1)'
                    });
                }
            } catch (err) {
                Mivo.toast('error', 'Connection Error', 'Failed to reach the server.');
            }
        }
    </script>
</body>
</html>
