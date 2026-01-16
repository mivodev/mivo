<?php 
    $title = 'Install MIVO - Setup';
    include ROOT . '/app/Views/layouts/header_public.php'; 
?>
    <!-- Install Container -->
    <main class="flex-grow flex items-center justify-center flex-col w-full">
    <div class="w-full max-w-full sm:max-w-md z-10 p-4 sm:p-6 animate-fade-in-up">
        
        <div class="text-center mb-6 sm:mb-10">
            <!-- Brand / Logo Area -->
            <div class="flex justify-center mb-6 sm:mb-8">
                <div class="relative group">
                    <!-- <div class="absolute -inset-1 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div> -->
                     <img src="/assets/img/logo-m.svg" alt="MIVO Logo" class="relative h-10 sm:h-12 w-auto block dark:hidden transform transition-transform duration-300 group-hover:scale-105">
                     <img src="/assets/img/logo-m-dark.svg" alt="MIVO Logo" class="relative h-10 sm:h-12 w-auto hidden dark:block transform transition-transform duration-300 group-hover:scale-105">
                </div>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight mb-2">Welcome to MIVO</h1>
            <p class="text-accents-5 text-sm">System Installation & Setup</p>
        </div>

        <div class="card p-6 sm:p-8 space-y-6">
            <form action="/install" method="POST" class="space-y-6">
                
                <!-- Steps UI -->
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-foreground text-background text-xs font-bold mt-0.5">1</div>
                        <div>
                            <h3 class="font-medium text-sm text-foreground">Database Setup</h3>
                            <p class="text-xs text-accents-5 mt-0.5">Tables will be created automatically (SQLite).</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                         <div class="flex-shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-foreground text-background text-xs font-bold mt-0.5">2</div>
                        <div>
                            <h3 class="font-medium text-sm text-foreground">Encryption Key</h3>
                             <p class="text-xs text-accents-5 mt-0.5">Secure key generation for passwords.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                         <div class="flex-shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-foreground text-background text-xs font-bold mt-0.5">3</div>
                        <div class="w-full">
                            <h3 class="font-medium text-sm text-foreground mb-3">Admin Account</h3>
                             
                             <div class="space-y-3">
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-accents-5">Username</label>
                                    <input type="text" name="username" value="admin" class="form-input" required>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-accents-5">Password</label>
                                    <input type="password" name="password" placeholder="Min. 8 characters" class="form-input" required>
                                </div>
                             </div>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full btn btn-primary h-10 shadow-lg hover:shadow-primary/20">
                        Install MIVO
                    </button>
                </div>
            </form>
        </div>
        
    </div>
    </main>

    <?php include ROOT . '/app/Views/layouts/footer_public.php'; ?>


