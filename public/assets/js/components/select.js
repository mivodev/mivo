/**
 * Mivo Component: Select
 * Standardized Custom Select for Forms, Filters, and Navigation.
 */
class CustomSelect {
    static instances = [];

    static get(elementOrId) {
        if (typeof elementOrId === 'string') {
            return CustomSelect.instances.find(i => i.originalSelect.id === elementOrId);
        }
        return CustomSelect.instances.find(i => i.originalSelect === elementOrId);
    }

    constructor(selectElement) {
        if (selectElement.dataset.customSelectInitialized === 'true') return;
        selectElement.dataset.customSelectInitialized = 'true';

        this.originalSelect = selectElement;
        this.originalSelect.style.display = 'none';
        this.options = Array.from(this.originalSelect.options);
        
        // Determine Variant
        this.variant = this.originalSelect.dataset.variant || 'default';
        if (this.originalSelect.classList.contains('form-filter')) this.variant = 'filter';
        if (this.originalSelect.classList.contains('nav-select')) this.variant = 'nav';

        this.wrapper = document.createElement('div');
        this.buildWrapperClasses();
        
        this.init();
        CustomSelect.instances.push(this);
    }

    buildWrapperClasses() {
        let base = 'custom-select-wrapper relative active-select';
        
        // Copy width classes
        const widthClass = Array.from(this.originalSelect.classList).find(c => c.startsWith('w-') && c !== 'w-full');
        const isFullWidth = this.originalSelect.classList.contains('w-full') || 
                           this.originalSelect.classList.contains('form-control') || 
                           this.originalSelect.classList.contains('form-input');

        if (widthClass) base += ' ' + widthClass;
        else if (isFullWidth) base += ' w-full';
        else base += ' w-fit';
        
        this.wrapper.className = base;
    }

    init() {
        this.trigger = document.createElement('div');
        
        // Variant Styling
        let triggerClass = 'flex items-center justify-between cursor-pointer pr-3 transition-all duration-200';
        
        if (this.variant === 'filter') {
            triggerClass += ' form-filter'; 
        } else if (this.variant === 'nav') {
            // New Nav variant for transparent/header usage
            triggerClass += ' text-sm font-medium hover:bg-accents-2/50 rounded-lg px-2 py-1.5 border border-transparent hover:border-accents-2';
        } else {
            triggerClass += ' form-input';
        }

        // Inherit non-structural classes
        const inherited = Array.from(this.originalSelect.classList)
            .filter(c => !['custom-select', 'hidden', 'form-filter', 'form-input', 'w-full'].includes(c))
            .join(' ');
        if (inherited) triggerClass += ' ' + inherited;

        this.trigger.className = triggerClass;
        this.renderTrigger();
        
        // Dropdown Menu
        this.menu = document.createElement('div');
        this.menu.className = 'custom-select-dropdown';
        
        this.listContainer = document.createElement('div');
        this.listContainer.className = 'overflow-y-auto flex-1 py-1 custom-scrollbar';

        if (this.originalSelect.dataset.search === 'true') {
            this.buildSearch();
        }

        this.buildOptions();

        this.menu.appendChild(this.listContainer);
        this.wrapper.appendChild(this.trigger);
        this.wrapper.appendChild(this.menu);
        this.originalSelect.parentNode.insertBefore(this.wrapper, this.originalSelect);

        this.bindEvents();
        
        if (typeof lucide !== 'undefined') lucide.createIcons({ root: this.wrapper });
    }

    renderTrigger() {
        const option = this.originalSelect.options[this.originalSelect.selectedIndex];
        const text = option ? option.text : '';
        const icon = option?.dataset.icon;
        const image = option?.dataset.image;
        const flag = option?.dataset.flag;

        let html = '';
        if (image) html += `<img src="${image}" class="w-5 h-5 mr-2 rounded-full object-cover">`;
        else if (flag) html += `<span class="fi fi-${flag} mr-2 rounded-sm shadow-sm"></span>`;
        else if (icon) html += `<i data-lucide="${icon}" class="w-4 h-4 mr-2 opacity-70"></i>`;

        html += `<span class="truncate flex-1 text-left select-none">${text}</span>`;
        html += `<i data-lucide="chevron-down" class="custom-select-icon w-4 h-4 ml-2 opacity-70 transition-transform duration-200"></i>`;
        
        this.trigger.innerHTML = html;
        if (typeof lucide !== 'undefined') lucide.createIcons({ root: this.trigger });
    }

    buildSearch() {
        const div = document.createElement('div');
        div.className = 'p-2 bg-background z-10 border-b border-accents-2 rounded-t-xl sticky top-0';
        
        const input = document.createElement('input');
        input.type = 'text';
        input.className = 'w-full px-2 py-1.5 text-xs bg-accents-1 border border-accents-2 rounded-md focus:outline-none focus:ring-1 focus:ring-foreground transition-all';
        input.placeholder = 'Search...';
        
        input.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            Array.from(this.listContainer.children).forEach(el => {
                el.style.display = el.textContent.toLowerCase().includes(term) ? 'flex' : 'none';
            });
        });
        
        input.addEventListener('click', e => e.stopPropagation());
        
        div.appendChild(input);
        this.menu.appendChild(div);
        this.searchInput = input;
    }

    buildOptions() {
        this.listContainer.innerHTML = '';
        this.options.forEach((opt, idx) => {
            const el = document.createElement('div');
            el.className = 'px-3 py-2 text-sm cursor-pointer hover:bg-accents-1 transition-colors flex items-center gap-2 relative';
            if (opt.selected) el.classList.add('bg-accents-1', 'font-medium');

            // Icon/Image Logic
            if (opt.dataset.image) el.innerHTML += `<img src="${opt.dataset.image}" class="w-5 h-5 rounded-full object-cover">`;
            else if (opt.dataset.flag) el.innerHTML += `<span class="fi fi-${opt.dataset.flag} rounded-sm shadow-sm"></span>`;
            else if (opt.dataset.icon) el.innerHTML += `<i data-lucide="${opt.dataset.icon}" class="w-4 h-4 opacity-70"></i>`;
            
            el.innerHTML += `<span class="truncate">${opt.text}</span>`;
            
            // Selected Checkmark
            if (opt.selected) {
                el.innerHTML += `<i data-lucide="check" class="w-3 h-3 ml-auto text-foreground absolute right-3"></i>`;
            }

            el.addEventListener('click', () => this.select(idx));
            this.listContainer.appendChild(el);
        });
    }

    bindEvents() {
        this.trigger.addEventListener('click', e => {
            e.stopPropagation();
            this.toggle();
        });
        document.addEventListener('click', e => {
            if (!this.wrapper.contains(e.target)) this.close();
        });
    }

    toggle() {
        this.menu.classList.contains('open') ? this.close() : this.open();
    }

    open() {
        // Close others
        CustomSelect.instances.forEach(i => i !== this && i.close());
        
        // Smart Position
        const rect = this.wrapper.getBoundingClientRect();
        const menuHeight = 260; // Max-h-60 (240px) + padding + search if exists
        const spaceBelow = window.innerHeight - rect.bottom;
        const spaceAbove = rect.top;
        
        // Reset positioning classes
        this.menu.classList.remove(
            'right-0', 'left-0', 
            'origin-top-right', 'origin-top-left', 
            'origin-bottom-right', 'origin-bottom-left',
            'dropdown-up'
        );
        
        // Vertical check
        const goUp = spaceBelow < menuHeight && spaceAbove > spaceBelow;
        if (goUp) {
            this.menu.classList.add('dropdown-up');
        }

        // Horizontal check
        const isRightAligned = window.innerWidth - rect.left < 250;
        if (isRightAligned) {
            this.menu.classList.add('right-0');
        } else {
            this.menu.classList.add('left-0');
        }

        // Apply correct Origin for animation
        const originY = goUp ? 'bottom' : 'top';
        const originX = isRightAligned ? 'right' : 'left';
        this.menu.classList.add(`origin-${originY}-${originX}`);

        this.menu.classList.add('open');
        this.trigger.classList.add('ring-1', 'ring-foreground');
        this.trigger.querySelector('.custom-select-icon')?.classList.add('rotate-180');
        
        if (this.searchInput) setTimeout(() => this.searchInput.focus(), 50);
    }

    close() {
        this.menu.classList.remove('open');
        this.trigger.classList.remove('ring-1', 'ring-foreground');
        this.trigger.querySelector('.custom-select-icon')?.classList.remove('rotate-180');
    }

    select(index) {
        this.originalSelect.selectedIndex = index;
        this.renderTrigger();
        this.buildOptions(); // Rebuild to move checkmark
        this.close();
        this.originalSelect.dispatchEvent(new Event('change', { bubbles: true }));
        if (typeof lucide !== 'undefined') lucide.createIcons({ root: this.wrapper });
    }

    refresh() {
        this.options = Array.from(this.originalSelect.options);
        this.buildOptions();
        this.renderTrigger();
    }
}

// Register to Mivo Framework
if (window.Mivo) {
    window.Mivo.registerComponent('Select', CustomSelect);
    
    // Auto-init on load
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('select.custom-select').forEach(el => new CustomSelect(el));
    });
}
