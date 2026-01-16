<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'MIVO' ?></title>
    <!-- Tailwind CSS (Local) -->
    <link rel="stylesheet" href="/assets/css/styles.css">
    <script src="/assets/js/lucide.min.js"></script>
    <script src="/assets/js/sweetalert2.all.min.js" defer></script>
    <script src="/assets/js/alert-helper.js" defer></script>
    <script src="/assets/js/i18n.js" defer></script>
    <style>
        /* Custom Keyframes */
        @keyframes fade-in-up {
            0% { opacity: 0; transform: translateY(10px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fade-in-up 0.4s ease-out forwards;
        }
    </style>
    <script>
        // Check local storage for theme
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-background text-foreground antialiased min-h-screen relative overflow-hidden font-sans selection:bg-accents-2 selection:text-foreground flex flex-col">
    
    <!-- Background Elements (Common) -->
    <div class="absolute inset-0 z-0 pointer-events-none">
        <!-- Subtle Grid Pattern -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMCwwLDAsMC4zKSIvPjwvc3ZnPg==')] dark:bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNSkiLz48L3N2Zz4=')] [mask-image:linear-gradient(to_bottom,white,transparent)]"></div>
        <div class="absolute -top-[20%] -left-[10%] w-[70vw] h-[70vw] rounded-full bg-blue-500/20 dark:bg-blue-500/5 blur-[120px] animate-pulse" style="animation-duration: 4s;"></div>
        <div class="absolute top-[30%] -right-[15%] w-[60vw] h-[60vw] rounded-full bg-purple-500/20 dark:bg-purple-500/5 blur-[100px] animate-pulse" style="animation-duration: 6s; animation-delay: 1s;"></div>
    </div>

    <!-- Floating Theme Toggle (Bottom Right) -->
    <button id="theme-toggle" class="fixed bottom-6 right-6 z-50 p-3 rounded-full bg-background border border-accents-2 shadow-lg text-accents-5 hover:text-foreground hover:border-foreground transition-all duration-300 group" style="position: fixed; bottom: 1.5rem; right: 1.5rem;">
        <i data-lucide="moon" class="w-5 h-5 block dark:hidden group-hover:scale-110 transition-transform"></i>
        <i data-lucide="sun" class="w-5 h-5 hidden dark:block group-hover:scale-110 transition-transform"></i>
    </button>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();

            // Theme Toggle Logic
            const themeToggleBtn = document.getElementById('theme-toggle');
            const htmlElement = document.documentElement;

            if(themeToggleBtn){
                themeToggleBtn.addEventListener('click', () => {
                    if (htmlElement.classList.contains('dark')) {
                        htmlElement.classList.remove('dark');
                        localStorage.theme = 'light';
                    } else {
                        htmlElement.classList.add('dark');
                        localStorage.theme = 'dark';
                    }
                });
            }
        });
    </script>
