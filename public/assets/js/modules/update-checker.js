/**
 * Mivo Update Checker
 * Checks GitHub for latest release and notifies user.
 * Caches result for 24 hours to avoid rate limits.
 */
document.addEventListener('DOMContentLoaded', () => {
    const REPO = 'mivodev/mivo';
    const CHECK_INTERVAL = 24 * 60 * 60 * 1000; // 24 Hours
    const CACHE_KEY = 'mivo_update_cache';
    
    // UI Elements
    const badge = document.getElementById('update-badge');
    const content = document.getElementById('notification-content');
    const dropdown = document.getElementById('notification-dropdown');

    if (!badge || !content) return;

    // Current Version from PHP (injected in footer)
    const currentVersion = window.MIVO_VERSION || 'v0.0.0';

    const checkUpdate = async () => {
        try {
            // Check Cache
            const cached = localStorage.getItem(CACHE_KEY);
            if (cached) {
                const data = JSON.parse(cached);
                const age = Date.now() - data.timestamp;
                if (age < CHECK_INTERVAL && data.version) {
                    processUpdate(data);
                    return;
                }
            }

            // Fetch from GitHub
            // console.log('Checking for updates...');
            const res = await fetch(`https://api.github.com/repos/${REPO}/releases/latest`);
            if (!res.ok) throw new Error('GitHub API Error');
            
            const json = await res.json();
            const latestVersion = json.tag_name; // e.g., "v1.3.0"
            const body = json.body || 'No release notes.';
            const htmlUrl = json.html_url;

            const cacheData = {
                version: latestVersion,
                body: body,
                url: htmlUrl,
                timestamp: Date.now()
            };

            localStorage.setItem(CACHE_KEY, JSON.stringify(cacheData));
            processUpdate(cacheData);

        } catch (error) {
            console.warn('Update check failed:', error);
        }
    };

    const processUpdate = (data) => {
        // Simple string comparison for now. Ideally semver.
        if (data.version !== currentVersion && compareVersions(data.version, currentVersion) > 0) {
            // Show Badge
            badge.classList.remove('hidden');
            
            // Update Dropdown Content
            content.innerHTML = `
                <div class="text-left space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-foreground">New Version Available!</span>
                        <span class="text-xs bg-accents-2 px-1.5 py-0.5 rounded text-foreground">${data.version}</span>
                    </div>
                    <p class="text-xs text-accents-5 line-clamp-3">${data.body.substring(0, 100)}...</p>
                    <a href="${data.url}" target="_blank" class="block w-full text-center px-3 py-2 bg-foreground text-background font-bold rounded-lg text-xs hover:bg-foreground/90 transition-colors mt-2">
                        View Release
                    </a>
                </div>
            `;
        } else {
            // Up to date
            content.innerHTML = `
                <div class="py-2">
                    <i data-lucide="check-circle" class="w-8 h-8 text-emerald-500 mx-auto mb-2"></i>
                    <p>You are using the latest version.</p>
                    <p class="text-xs text-accents-4 mt-1">Current: ${currentVersion}</p>
                </div>
            `;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    };

    // Helper: Compare "v1.2.0" vs "v1.1.0"
    const compareVersions = (v1, v2) => {
        const clean = v => v.replace(/^v/, '').split('.').map(Number);
        const a = clean(v1);
        const b = clean(v2);

        for (let i = 0; i < Math.max(a.length, b.length); i++) {
            const valA = a[i] || 0;
            const valB = b[i] || 0;
            if (valA > valB) return 1;
            if (valA < valB) return -1;
        }
        return 0;
    };

    // Init
    checkUpdate();
});
