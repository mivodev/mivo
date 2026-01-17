/**
 * Mivo Module: Updater
 * Handles version checking and update notifications.
 */
class UpdaterModule {
    constructor() {
        this.repo = 'dyzulk/mivo';
        this.cacheKey = 'mivo_update_data';
        this.ttl = 24 * 60 * 60 * 1000; // 24 hours
        
        // Wait for Mivo core to be ready
        if (window.Mivo) {
            window.Mivo.on('ready', () => this.init());
        }
    }

    async init() {
        const updateData = this.getCache();
        const now = Date.now();

        if (updateData && (now - updateData.timestamp < this.ttl)) {
            this.checkUpdate(updateData.version, updateData.url);
        } else {
            await this.fetchLatest();
        }
    }

    getCache() {
        const data = localStorage.getItem(this.cacheKey);
        return data ? JSON.parse(data) : null;
    }

    setCache(version, url) {
        const data = {
            version: version,
            url: url,
            timestamp: Date.now()
        };
        localStorage.setItem(this.cacheKey, JSON.stringify(data));
    }

    async fetchLatest() {
        try {
            const response = await fetch(`https://api.github.com/repos/${this.repo}/releases/latest`);
            if (!response.ok) throw new Error('Failed to fetch version');
            
            const data = await response.json();
            const version = data.tag_name; // e.g., v1.1.0
            const url = data.html_url;

            this.setCache(version, url);
            this.checkUpdate(version, url);
        } catch (error) {
            console.error('[Mivo] Update check failed:', error);
        }
    }

    checkUpdate(latestVersion, url) {
        if (!window.currentVersion) return;

        // Simple version comparison (removing 'v' prefix if exists)
        const current = window.currentVersion.replace('v', '');
        const latest = latestVersion.replace('v', '');

        if (this.isNewer(current, latest)) {
            this.showNotification(latestVersion, url);
        }
    }

    isNewer(current, latest) {
        const cParts = current.split('.').map(Number);
        const lParts = latest.split('.').map(Number);

        for (let i = 0; i < Math.max(cParts.length, lParts.length); i++) {
            const c = cParts[i] || 0;
            const l = lParts[i] || 0;
            if (l > c) return true;
            if (l < c) return false;
        }
        return false;
    }

    showNotification(version, url) {
        const badge = document.getElementById('update-badge');
        const content = document.getElementById('notification-content');

        if (badge) badge.classList.remove('hidden');
        if (content) {
            content.innerHTML = `
                <div class="flex flex-col items-center gap-3">
                    <div class="p-2 bg-blue-500/10 rounded-full">
                        <i data-lucide="rocket" class="w-6 h-6 text-blue-500"></i>
                    </div>
                    <div class="space-y-1">
                        <p class="font-bold text-foreground">New Version Available!</p>
                        <p class="text-xs text-accents-4">Version <span class="font-mono">${version}</span> is now available.</p>
                    </div>
                    <a href="${url}" target="_blank" class="w-full py-2 px-4 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-colors flex items-center justify-center gap-2">
                        <i data-lucide="download" class="w-3 h-3"></i>
                        <span>Download Update</span>
                    </a>
                </div>
            `;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }
}

// Register Module
if (window.Mivo) {
    window.Mivo.registerModule('Updater', new UpdaterModule());
}
