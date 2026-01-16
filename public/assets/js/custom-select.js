class CustomSelect {
    static instances = [];

    constructor(selectElement) {
        if (selectElement.dataset.customSelectInitialized === 'true') {
            return;
        }
        selectElement.dataset.customSelectInitialized = 'true';

        this.originalSelect = selectElement;
        this.originalSelect.style.display = 'none';
        this.options = Array.from(this.originalSelect.options);
        
        // Settings
        this.wrapper = document.createElement('div');
        
        // Standard classes
        let wrapperClasses = 'custom-select-wrapper relative active-select';
        
        // Intelligent Width: 
        // If original select expects full width, wrapper must be full width.
        // Otherwise, use w-fit (Crucial for Right-Alignment in toolbars to work).
        const widthClass = Array.from(this.originalSelect.classList).find(c => c.startsWith('w-') && c !== 'w-full');
        const isFullWidth = this.originalSelect.classList.contains('w-full') || 
                           this.originalSelect.classList.contains('form-control') || 
                           this.originalSelect.classList.contains('form-input');

        if (widthClass) {
             wrapperClasses += ' ' + widthClass;
        } else if (isFullWidth) {
             wrapperClasses += ' w-full';
        } else {
             wrapperClasses += ' w-fit';
        }
        
        this.wrapper.className = wrapperClasses;
        
        this.init();
        
        // Store instance
        if (!CustomSelect.instances) CustomSelect.instances = [];
        CustomSelect.instances.push(this);
    }

    init() {
        // Create Trigger
        this.trigger = document.createElement('div');
        
        const isFilter = this.originalSelect.classList.contains('form-filter');
        const baseClass = isFilter ? 'form-filter' : 'form-input';
        
        this.trigger.className = `${baseClass} flex items-center justify-between cursor-pointer pr-3`;
        this.trigger.style.paddingLeft = '0.75rem';
        
        this.trigger.innerHTML = `
            <span class="custom-select-value truncate text-foreground flex-1 text-left">${this.originalSelect.options[this.originalSelect.selectedIndex].text}</span>
            <div class="custom-select-icon flex-shrink-0 ml-2 transition-transform duration-200 transform">
                <i data-lucide="chevron-down" class="w-4 h-4 text-foreground opacity-70"></i>
            </div>
        `;
        
        // Inherit classes from original select (excluding custom-select marker)
        if (this.originalSelect.classList.length > 0) {
             const inheritedClasses = Array.from(this.originalSelect.classList)
                .filter(c => c !== 'custom-select' && c !== 'hidden')
                .join(' ');
             if (inheritedClasses) {
                 this.trigger.className += ' ' + inheritedClasses;
             }
        }

        // Final sanity check for full width
        if (this.wrapper.classList.contains('w-full')) {
            this.trigger.classList.add('w-full');
        }
        
        // Create Options Menu Wrapper (No Scroll Here)
        this.menu = document.createElement('div');
        // Create Options Menu Wrapper (No Scroll Here)
        // Create Options Menu Wrapper (No Scroll Here)
        this.menu = document.createElement('div');
        this.menu.className = 'custom-select-dropdown';
        
        // Create Scrollable List Container
        this.listContainer = document.createElement('div');
        this.listContainer.className = 'overflow-y-auto flex-1 py-1 custom-scrollbar';

        // Search Functionality
        if (this.originalSelect.dataset.search === 'true') {
            const searchContainer = document.createElement('div');
            searchContainer.className = 'p-2 bg-background z-10 border-b border-accents-2 flex-shrink-0 rounded-t-md';
            
            this.searchInput = document.createElement('input');
            this.searchInput.type = 'text';
            this.searchInput.className = 'w-full px-2 py-1 text-sm bg-accents-1 border border-accents-2 rounded focus:outline-none focus:ring-1 focus:ring-foreground';
            this.searchInput.placeholder = 'Search...';
            
            searchContainer.appendChild(this.searchInput);
            this.menu.appendChild(searchContainer);
            
            // Search Event
            this.searchInput.addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase();
                this.options.forEach((option, index) => {
                    const item = this.listContainer.querySelector(`[data-index="${index}"]`);
                    if (item) {
                        const text = option.text.toLowerCase();
                        item.style.display = text.includes(term) ? 'flex' : 'none';
                    }
                });
            });
            
            this.searchInput.addEventListener('click', (e) => e.stopPropagation());
        }

        // Build Options
        this.options.forEach((option, index) => {
            const item = document.createElement('div');
            item.className = 'px-3 py-2 text-sm cursor-pointer hover:bg-accents-1 transition-colors flex items-center justify-between whitespace-nowrap';
            if(option.selected) item.classList.add('bg-accents-1', 'font-medium');
            
            item.textContent = option.text;
            item.dataset.value = option.value;
            item.dataset.index = index;
            
            item.addEventListener('click', () => {
                this.select(index);
            });
            
            this.listContainer.appendChild(item);
        });

        // Append List to Menu
        this.menu.appendChild(this.listContainer);

        // Append to wrapper
        this.wrapper.appendChild(this.trigger);
        this.wrapper.appendChild(this.menu);
        this.originalSelect.parentNode.insertBefore(this.wrapper, this.originalSelect);

        // Event Listeners
        this.trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggle();
        });

        document.addEventListener('click', (e) => {
            if (!this.wrapper.contains(e.target)) {
                this.close();
            }
        });

        if (typeof lucide !== 'undefined') {
            lucide.createIcons({ root: this.trigger });
        }
    }

    toggle() {
        if (!this.menu.classList.contains('open')) {
            this.open();
        } else {
            this.close();
        }
    }

    open() {
        CustomSelect.instances.forEach(instance => {
            if (instance !== this) instance.close();
        });

        // Smart Positioning
        const rect = this.wrapper.getBoundingClientRect();
        const spaceRight = window.innerWidth - rect.left;
        
        // Reset positioning classes
        this.menu.classList.remove('right-0', 'origin-top-right', 'left-0', 'origin-top-left');

        // Logic: Zone Check - If near right edge (< 300px), Force Right Align.
        // Doing this purely based on coordinates prevents "Layout Jumping" caused by measuring content width.
        if (spaceRight < 300) {
             this.menu.classList.add('right-0', 'origin-top-right');
        } else {
             this.menu.classList.add('left-0', 'origin-top-left');
        }

        // Apply visual open states
        this.menu.classList.add('open');
        
        this.trigger.classList.add('ring-1', 'ring-foreground');
        const icon = this.trigger.querySelector('.custom-select-icon');
        if(icon) icon.classList.add('rotate-180');
        
        if (this.searchInput) {
            setTimeout(() => this.searchInput.focus(), 50);
        }
    }

    close() {
        this.menu.classList.remove('open');
        
        this.trigger.classList.remove('ring-1', 'ring-foreground');
        const icon = this.trigger.querySelector('.custom-select-icon');
        if(icon) icon.classList.remove('rotate-180');
    }

    select(index) {
        // Update Original Select
        this.originalSelect.selectedIndex = index;
        
        // Update UI
        this.trigger.querySelector('.custom-select-value').textContent = this.options[index].text;
        
        // Update Active State in List
        Array.from(this.listContainer.children).forEach((child) => {
             // Safe check
             if (!child.dataset.index) return;
             
            if (parseInt(child.dataset.index) === index) {
                child.classList.add('bg-accents-1', 'font-medium');
            } else {
                child.classList.remove('bg-accents-1', 'font-medium');
            }
        });

        this.close();
        this.originalSelect.dispatchEvent(new Event('change'));
    }

    refresh() {
        // Clear list items
        this.listContainer.innerHTML = '';
        
        // Re-read options
        this.options = Array.from(this.originalSelect.options);
        
        this.options.forEach((option, index) => {
            const item = document.createElement('div');
            item.className = 'px-3 py-2 text-sm cursor-pointer hover:bg-accents-1 transition-colors flex items-center justify-between whitespace-nowrap';
            if(option.selected) item.classList.add('bg-accents-1', 'font-medium');
            
            item.textContent = option.text;
            item.dataset.value = option.value;
            item.dataset.index = index;
            
            item.addEventListener('click', () => {
                this.select(index);
            });
            
            this.listContainer.appendChild(item);
        });
        
        // Update Trigger
        if (this.originalSelect.selectedIndex >= 0) {
             this.trigger.querySelector('.custom-select-value').textContent = this.originalSelect.options[this.originalSelect.selectedIndex].text;
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('select.custom-select').forEach(el => new CustomSelect(el));
});
