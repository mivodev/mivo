document.addEventListener('DOMContentLoaded', () => {
    const checkBtn = document.getElementById('check-interface-btn');
    const ifaceSelect = document.getElementById('iface');

    if (checkBtn && ifaceSelect) {
        checkBtn.addEventListener('click', async () => {
            const originalText = checkBtn.innerHTML;
            checkBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i> Checking...';
            checkBtn.disabled = true;
            if (typeof lucide !== 'undefined') lucide.createIcons();

            // Collect Data
            const ip = document.querySelector('input[name="ipmik"]').value;
            const user = document.querySelector('input[name="usermik"]').value;
            const pass = document.querySelector('input[name="passmik"]').value;
            const idInput = document.querySelector('input[name="id"]');
            const id = idInput ? idInput.value : null;

            try {
                const response = await fetch('/api/router/interfaces', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ip, user, password: pass, id })
                });

                const data = await response.json();

                if (data.success && data.interfaces) {
                    // Update Select
                    ifaceSelect.innerHTML = ''; // Clear
                    
                    data.interfaces.forEach(iface => {
                        const opt = document.createElement('option');
                        opt.value = iface;
                        opt.textContent = iface;
                        if (iface === 'ether1') opt.selected = true; // Default preferred?
                         ifaceSelect.appendChild(opt);
                    });

                    // Refresh Custom Select
                    if (typeof CustomSelect !== 'undefined' && CustomSelect.instances) {
                        const instance = CustomSelect.instances.find(i => i.originalSelect.id === 'iface');
                        if (instance) instance.refresh();
                    }
                    
                    // Show success
                    checkBtn.innerHTML = '<i data-lucide="check" class="w-4 h-4 mr-2"></i> Interfaces Loaded';
                    setTimeout(() => {
                         checkBtn.innerHTML = originalText;
                         checkBtn.disabled = false;
                         if (typeof lucide !== 'undefined') lucide.createIcons();
                    }, 2000);

                } else {
                    alert('Error: ' + (data.error || 'Failed to fetch interfaces'));
                    checkBtn.innerHTML = originalText;
                    checkBtn.disabled = false;
                }

            } catch (err) {
                console.error(err);
                alert('Connection Error');
                checkBtn.innerHTML = originalText;
                checkBtn.disabled = false;
            }
        });
    }

    // Session Name Auto-Conversion
    const sessInput = document.querySelector('input[name="sessname"]');
    const sessPreview = document.getElementById('sessname-preview');
    
    if (sessInput) {
        // Initial set if editing
        if(sessPreview) sessPreview.textContent = sessInput.value;

        sessInput.addEventListener('input', (e) => {
            let val = e.target.value;
            // 1. Lowercase
            val = val.toLowerCase();
            // 2. Space -> Dash
            val = val.replace(/\s+/g, '-');
            // 3. Remove non-alphanumeric (except dash)
            val = val.replace(/[^a-z0-9-]/g, '');
            // 4. No double dashes
            val = val.replace(/-+/g, '-');

            // Write back to input (Auto Convert)
            e.target.value = val;
            
            // Update Preview
            if (sessPreview) {
                sessPreview.textContent = val || '...';
                sessPreview.className = val ? 'font-mono text-primary font-bold' : 'font-mono text-accents-4';
            }
        });
    }
});
