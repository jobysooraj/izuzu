class FigCategoryTracker {
    constructor(formSelector, hiddenInputId) {
        this.form = document.querySelector(formSelector);
        this.figInput = document.getElementById(hiddenInputId);

        this.selected = new Set((this.figInput?.value || '').split(',').filter(Boolean));
        this.lastSubmitted = this.getSelectedKey(); // Save initial state
        this.checkboxes = document.querySelectorAll('input[name="fig[]"]');
        this.headings = document.querySelectorAll('.fig-heading');
        this.submitTimer = null;

        this.init();
    }

    init() {
        if (!this.form) {
            console.warn('Form not found:', this.form);
            return;
        }
        this.syncFromSelected();
        this.listenCheckboxes();
        this.listenHeadings();
        this.updateHidden();

        // Prevent default form submission
        this.form.addEventListener('submit', e => e.preventDefault());
    }

    syncFromSelected() {
        this.checkboxes.forEach(cb => {
            cb.checked = this.selected.has(cb.value);
        });

        const parentCheckboxes = document.querySelectorAll('.fig-parent-checkbox');
        parentCheckboxes.forEach(parent => {
            const children = parent.value.split(',').filter(Boolean);

            if (children.length === 0) {
                parent.checked = false;
                parent.indeterminate = false;
                return;
            }

            const allSelected = children.every(val => this.selected.has(val));
            const someSelected = children.some(val => this.selected.has(val));

            parent.checked = allSelected;
            parent.indeterminate = !allSelected && someSelected;
        });
    }



    listenCheckboxes() {
        this.checkboxes.forEach(cb =>
            cb.addEventListener('change', e => {
                // 1) let the checkbox itself toggle
                e.stopImmediatePropagation();

                // 2) handle parent‑checkbox specially
                if (cb.classList.contains('fig-parent-checkbox')) {
                    // parse its value into an array of child figs
                    const children = cb.value.split(',').filter(Boolean);
                    if (cb.checked) {
                        // select all children
                        children.forEach(fig => this.selected.add(fig));
                    } else {
                        // deselect all children
                        children.forEach(fig => this.selected.delete(fig));
                    }
                    // sync the actual child checkboxes in the DOM
                    this.checkboxes.forEach(childCb => {
                        if (childCb.classList.contains('fig-checkbox') &&
                            children.includes(childCb.value)) {
                            childCb.checked = cb.checked;
                        }
                    });
                } else {
                    // 3) normal child checkbox logic
                    const val = cb.value;
                    if (cb.checked) this.selected.add(val);
                    else this.selected.delete(val);

                    // also update the parent checkbox state if needed:
                    const parent = cb.closest('.fig-group')
                        .querySelector('.fig-parent-checkbox');
                    if (parent) {
                        // if *all* children now selected, check the parent; otherwise uncheck
                        const allKeys = parent.value.split(',').filter(Boolean);
                        const allSelected = allKeys.every(k => this.selected.has(k));
                        parent.checked = allSelected;
                    }
                }

                // 4) update hidden and submit
                this.updateHidden();
                this.showSpinner();
                this.debouncedSubmit();
            })
        );
    }


    listenHeadings() {
        this.headings.forEach(heading => {
            heading.addEventListener('click', e => {
                if (e.target.closest('input[type="checkbox"]')) return;

                const targetId = heading.dataset.target;
                const target = document.getElementById(targetId);
                if (!target) return;

                target.style.display = target.style.display === 'block' ? 'none' : 'block';
            });
        });
    }

    updateHidden() {
        if (!this.figInput) return;
        this.figInput.value = Array.from(this.selected).join(',');
    }

    getSelectedKey() {
        // Create a unique string representing current selection
        return Array.from(this.selected).sort().join(',');
    }

    debouncedSubmit() {
        if (this.submitTimer) clearTimeout(this.submitTimer);
        this.showSpinner(); // Show immediately on interaction

        this.submitTimer = setTimeout(() => {
            const newKey = this.getSelectedKey();

            // ✅ Always submit (even if same as last)
            this.submit();
            this.lastSubmitted = newKey;

        }, 250); // 250ms debounce delay
    }

    submit() {
        const formData = new FormData(this.form);

        if (this.selected.size === 0) {
            formData.delete('fig[]');
            formData.delete('fig');
            // 3) Serialize to a query string
            if (this.figInput) {
                this.figInput.value = '';
            }
            const params = new URLSearchParams(formData).toString();
            this.showSpinner(); // Redundant but safe
            setTimeout(() => {

                window.location.href = `${this.form.action}?${params}`;
            }, 200);

            return;
        }

        // ---- otherwise, proceed with “fig[] = …” AJAX ----


        formData.delete('fig[]');
        this.selected.forEach(val => formData.append('fig[]', val));

        const params = new URLSearchParams(formData).toString();
        const url = `${this.form.action}?${params}`;
        this.showSpinner();
        fetch(url, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(r => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then(json => {
                const wrapper = document.querySelector('.bb-product-items-wrapper');
                if (!wrapper) return;

                wrapper.innerHTML = json.data;
                history.replaceState(null, '', json.redirect_url || url);
                this.reinitializeFrontend?.();
            })
            .catch(err => console.error(err))
            .finally(() => {
                this.hideSpinner(); // ✅ Hide spinner after everything
            })
    }
    showSpinner() {
        const spinner = document.querySelector('.bb-loading-spinner');
        if (spinner) spinner.style.display = 'block';
    }

    hideSpinner() {
        const spinner = document.querySelector('.bb-loading-spinner');
        if (spinner) spinner.style.display = 'none';
    }
    reinitializeFrontend() {
        // ✅ Lazysizes: Trigger lazy loading on newly injected elements
        if (typeof window.lazySizes !== 'undefined' && lazySizes.loader) {
            lazySizes.loader.checkElems();
        }

        // ✅ Fallback: manually trigger data-src image load (for good measure)
        const imgs = document.querySelectorAll('img.lazyload[data-src]');
        imgs.forEach(img => {
            if (!img.src || img.src.includes('placeholder')) {
                img.src = img.dataset.src;
                img.classList.add('lazyloaded');
            }
        });

        // ✅ Theme-specific initializations (if any)
        if (typeof window.theme?.init === 'function') {
            window.theme.init();
        }

        // ✅ Reinitialize tooltips or sliders if used
        if (typeof $ === 'function') {
            $('.tooltip').tooltip(); // update as needed
        }

        console.log('✅ Frontend reinitialized after AJAX update');
    }



}

document.addEventListener('DOMContentLoaded', () => {
    const tracker = new FigCategoryTracker('.bb-product-form-filter', 'fig-input');

    // 🎯 Enhancement: sync fig ↔ pno/name
    const pnoInput = document.querySelector('input[name="pno"]');
    const nameInput = document.querySelector('input[name="name"]');

    // ✅ Clear fig filters when pno or name is typed
    function clearFigFilters() {
        if (!tracker) return;
        tracker.selected.clear();
        tracker.updateHidden();
        tracker.syncFromSelected();
    }

    if (pnoInput) {
        pnoInput.addEventListener('input', clearFigFilters);
    }

    if (nameInput) {
        nameInput.addEventListener('input', clearFigFilters);
    }

    // ✅ Clear pno & name when fig checkbox changes
    const figCheckboxes = document.querySelectorAll('input[name="fig[]"]');
    figCheckboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            if (pnoInput) pnoInput.value = '';
            if (nameInput) nameInput.value = '';
        });
    });
});
