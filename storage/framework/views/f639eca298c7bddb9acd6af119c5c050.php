<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'resource' => '', // e.g. 'posts', 'users'
    'actions' => [
        'delete' => 'Delete'
    ], // Default action is Delete. Provide an array of value => label pairs.
    'class' => 'px-5 py-3 bg-navy-800/40 border border-navy-700/50 rounded-xl mb-3',
    'showBanner' => true
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'resource' => '', // e.g. 'posts', 'users'
    'actions' => [
        'delete' => 'Delete'
    ], // Default action is Delete. Provide an array of value => label pairs.
    'class' => 'px-5 py-3 bg-navy-800/40 border border-navy-700/50 rounded-xl mb-3',
    'showBanner' => true
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="flex items-center gap-2 bulk-actions-wrapper shadow-sm <?php echo e($class); ?>" data-resource="<?php echo e($resource); ?>" data-action-url="<?php echo e(route('admin.bulk.handle', $resource)); ?>">
    <select aria-label="Bulk actions" class="bulk-action-select w-40 px-3 py-1.5 rounded-lg bg-navy-900/50 border-none text-xs font-medium text-text-secondary focus:ring-1 focus:ring-electric cursor-pointer hover:bg-navy-800 transition-colors">
        <option value="">Bulk Actions</option>
        <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    
    <button type="button" class="bulk-action-apply px-4 py-1.5 rounded-lg bg-navy-700 hover:bg-navy-600 text-white text-xs font-semibold transition-all shadow-lg shrink-0" disabled>
        Apply
    </button>
</div>

<?php if($showBanner): ?>

<div class="bulk-select-all-banner hidden mb-4 bg-electric/10 border border-electric/30 text-electric px-4 py-2.5 rounded-xl text-sm text-center flex-col sm:flex-row items-center justify-center gap-2 transition-all">
    <span class="bulk-select-page-msg">
        All <strong class="bulk-page-count">0</strong> items on this page are selected.
    </span>
    <button type="button" class="bulk-select-all-btn hidden font-bold hover:underline transition-all">
        Select all <span class="bulk-total-count">0</span> items matching this search
    </button>
    <span class="bulk-select-all-msg hidden">
        All <strong class="bulk-total-count">0</strong> items are selected. 
        <button type="button" class="bulk-clear-btn font-bold text-rose-400 hover:text-rose-500 hover:underline ml-1 transition-all">Clear selection</button>
    </span>
</div>
<?php endif; ?>

<?php if (! $__env->hasRenderedOnce('2f7ff589-207b-4154-aaf6-fb110851835b')): $__env->markAsRenderedOnce('2f7ff589-207b-4154-aaf6-fb110851835b'); ?>
<?php $__env->startPush('scripts'); ?>
<script>
window.bulkSelectContext = window.bulkSelectContext || {};

function getUniqueCount(scope, selector) {
    let boxes = scope.querySelectorAll(selector);
    let unique = new Set(Array.from(boxes).map(cb => cb.value));
    return unique.size;
}

document.addEventListener('DOMContentLoaded', function() {
    function getScope(element) {
        return element.closest('.page-content, main, body');
    }

    // Master checkbox toggle
    document.querySelectorAll('.bulk-master-checkbox').forEach(master => {
        master.addEventListener('change', function() {
            const scope = getScope(this);
            if(scope) {
                // Sync all master checkboxes in this scope
                scope.querySelectorAll('.bulk-master-checkbox').forEach(m => {
                    if (m !== this) m.checked = this.checked;
                });

                const rowCheckboxes = scope.querySelectorAll('.bulk-item-checkbox');
                rowCheckboxes.forEach(cb => {
                    cb.checked = this.checked;
                });
                
                if(!this.checked) {
                    clearSelectAllState(scope);
                }
                
                updateApplyButton(scope);
                checkSelectAllBanner(scope, this.checked);
            }
        });
    });

    // Individual checkbox toggle
    document.querySelectorAll('.bulk-item-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            const scope = getScope(this);
            if(scope) {
                updateApplyButton(scope);
                
                // Update master checkbox states
                const masters = scope.querySelectorAll('.bulk-master-checkbox');
                const all = getUniqueCount(scope, '.bulk-item-checkbox');
                const checked = getUniqueCount(scope, '.bulk-item-checkbox:checked');
                
                masters.forEach(master => {
                    master.checked = (all > 0 && checked === all);
                    master.indeterminate = (checked > 0 && checked < all);
                });
                
                if(!this.checked) {
                    clearSelectAllState(scope);
                }
                
                checkSelectAllBanner(scope, checked === all);
            }
        });
    });

    // Banner logic for 'Select Everything'
    function checkSelectAllBanner(scope, isMasterChecked) {
        const banners = scope.querySelectorAll('.bulk-select-all-banner');
        if(!banners.length) return;
        
        const checkedCount = getUniqueCount(scope, '.bulk-item-checkbox:checked');
        const allItemsCount = getUniqueCount(scope, '.bulk-item-checkbox');
        
        banners.forEach(banner => {
            if (window.bulkSelectContext.isAllSelected) {
                // Already in "select all everything" state
                banner.classList.remove('hidden');
                banner.classList.add('flex');
                return;
            }

            if (isMasterChecked && checkedCount > 0 && checkedCount === allItemsCount) {
                banner.classList.remove('hidden');
                banner.classList.add('flex');
                banner.querySelector('.bulk-page-count').textContent = checkedCount;
                banner.querySelector('.bulk-select-page-msg').classList.remove('hidden');
                banner.querySelector('.bulk-select-all-msg').classList.add('hidden');
                
                // Check if there's pagination
                const pagination = scope.querySelector('nav[role="navigation"], .pagination');
                if (pagination) {
                    const btn = banner.querySelector('.bulk-select-all-btn');
                    btn.classList.remove('hidden');
                    
                    if (!window.bulkSelectContext.fetchedTotalCount) {
                        btn.textContent = 'Select everything matching this search';
                    }
                } else {
                    banner.querySelector('.bulk-select-all-btn').classList.add('hidden');
                }
            } else {
                banner.classList.add('hidden');
                banner.classList.remove('flex');
            }
        });
    }

    document.querySelectorAll('.bulk-select-all-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const scope = getScope(this);
            const banners = scope.querySelectorAll('.bulk-select-all-banner');
            
            // Show loading state
            const originalText = this.textContent;
            
            banners.forEach(banner => {
                const b = banner.querySelector('.bulk-select-all-btn');
                if(b) {
                    b.textContent = 'Fetching...';
                    b.disabled = true;
                }
            });
            
            try {
                const url = new URL(window.location.href);
                url.searchParams.set('fetch_all_ids', '1');
                
                const response = await fetch(url, {
                    headers: { 'Accept': 'application/json' }
                });
                
                if (response.ok) {
                    const ids = await response.json();
                    window.bulkSelectContext.isAllSelected = true;
                    window.bulkSelectContext.allIds = ids;
                    
                    banners.forEach(banner => {
                        banner.querySelectorAll('.bulk-total-count').forEach(el => el.textContent = ids.length);
                        banner.querySelector('.bulk-select-page-msg').classList.add('hidden');
                        banner.querySelector('.bulk-select-all-btn').classList.add('hidden');
                        banner.querySelector('.bulk-select-all-msg').classList.remove('hidden');
                    });
                    
                    // Force master checkboxes to checked
                    scope.querySelectorAll('.bulk-master-checkbox').forEach(master => {
                        master.checked = true;
                        master.indeterminate = false;
                    });
                    updateApplyButton(scope);
                } else {
                    alert('Could not fetch all matching items. Please try again.');
                    banners.forEach(banner => {
                        const b = banner.querySelector('.bulk-select-all-btn');
                        if(b) {
                            b.textContent = originalText;
                            b.disabled = false;
                        }
                    });
                }
            } catch (err) {
                console.error(err);
                alert('An error occurred. Please try again.');
                banners.forEach(banner => {
                    const b = banner.querySelector('.bulk-select-all-btn');
                    if(b) {
                        b.textContent = originalText;
                        b.disabled = false;
                    }
                });
            }
        });
    });

    document.querySelectorAll('.bulk-clear-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const scope = getScope(this);
            scope.querySelectorAll('.bulk-master-checkbox').forEach(master => {
                master.checked = false;
                master.dispatchEvent(new Event('change'));
            });
            clearSelectAllState(scope);
        });
    });

    function clearSelectAllState(scope) {
        window.bulkSelectContext.isAllSelected = false;
        window.bulkSelectContext.allIds = [];
        
        const banners = scope.querySelectorAll('.bulk-select-all-banner');
        
        banners.forEach(banner => {
            banner.querySelector('.bulk-select-page-msg').classList.remove('hidden');
            banner.querySelector('.bulk-select-all-msg').classList.add('hidden');
            const btn = banner.querySelector('.bulk-select-all-btn');
            btn.disabled = false;
            btn.textContent = 'Select everything matching this search';
        });
    }

    // Update apply button disabled state
    function updateApplyButton(scope) {
        const wrappers = scope.querySelectorAll('.bulk-actions-wrapper');
        if(!wrappers.length) return;
        
        let checkedCount = getUniqueCount(scope, '.bulk-item-checkbox:checked');
        if (window.bulkSelectContext.isAllSelected && window.bulkSelectContext.allIds) {
            checkedCount = window.bulkSelectContext.allIds.length;
        }

        wrappers.forEach(wrapper => {
            const applyBtn = wrapper.querySelector('.bulk-action-apply');
            
            if(applyBtn) {
                applyBtn.disabled = checkedCount === 0;
                applyBtn.textContent = checkedCount > 0 ? 'Apply (' + checkedCount + ')' : 'Apply';
                
                if(checkedCount > 0) {
                    applyBtn.classList.remove('bg-navy-700', 'text-white/50');
                    applyBtn.classList.add('bg-electric', 'hover:bg-electric-light', 'text-white');
                } else {
                    applyBtn.classList.add('bg-navy-700', 'text-white/50');
                    applyBtn.classList.remove('bg-electric', 'hover:bg-electric-light', 'text-white');
                }
            }
        });
    }

    // Handle Apply button click
    document.querySelectorAll('.bulk-action-apply').forEach(btn => {
        btn.addEventListener('click', function() {
            const wrapper = this.closest('.bulk-actions-wrapper');
            const select = wrapper.querySelector('.bulk-action-select');
            const action = select.value;
            
            if(!action) {
                alert('Please select a bulk action.');
                return;
            }

            const scope = getScope(this);

            let finalIds = [];
            if (window.bulkSelectContext.isAllSelected && window.bulkSelectContext.allIds) {
                finalIds = window.bulkSelectContext.allIds;
            } else {
                const checkedBoxes = scope.querySelectorAll('.bulk-item-checkbox:checked');
                const uniqueSet = new Set(Array.from(checkedBoxes).map(cb => cb.value));
                finalIds = Array.from(uniqueSet);
            }

            if(finalIds.length === 0) {
                alert('Please select at least one item.');
                return;
            }

            if(action === 'delete' && !confirm(`Are you sure you want to delete ${finalIds.length} selected items? This cannot be undone.`)) {
                return;
            }

            // Create a dynamic form to submit the bulk action
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = wrapper.dataset.actionUrl;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);

            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = action;
            form.appendChild(actionInput);

            finalIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\atomni-pro\resources\views/components/admin/bulk-actions.blade.php ENDPATH**/ ?>