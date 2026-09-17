/**
 * Smart IT Helpdesk - Ticket Lifecycle & Operations (Developer 1)
 * Handles: Live Image Preview, 5-Star Rating, Action Modals, and AJAX Operations
 */

document.addEventListener('DOMContentLoaded', () => {
    initImagePreviews();
    initStarRating();
    initActionModals();
    initCommentForm();
    initSidebarToggle();
    initTableInteractions();
});

/**
 * 1. Image Preview & Dropzone Handler
 */
function initImagePreviews() {
    const fileInputs = document.querySelectorAll('input[type="file"].previewable');

    fileInputs.forEach(input => {
        const previewContainerId = input.dataset.preview;
        const previewContainer = document.getElementById(previewContainerId);
        if (!previewContainer) return;

        input.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) {
                previewContainer.innerHTML = '';
                previewContainer.style.display = 'none';
                return;
            }

            // Check if file is image
            if (!file.type.startsWith('image/')) {
                alert('กรุณาเลือกเฉพาะไฟล์รูปภาพ (JPG, PNG, WEBP)');
                this.value = '';
                previewContainer.innerHTML = '';
                previewContainer.style.display = 'none';
                return;
            }

            // Check size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                alert('ขนาดไฟล์ต้องไม่เกิน 5MB');
                this.value = '';
                previewContainer.innerHTML = '';
                previewContainer.style.display = 'none';
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                previewContainer.style.display = 'block';
                previewContainer.innerHTML = `
                    <div class="preview-box">
                        <img src="${e.target.result}" alt="Preview" class="preview-img">
                        <button type="button" class="btn-remove-img" title="ลบรูปภาพ">&times;</button>
                    </div>
                `;

                // Handle remove button
                previewContainer.querySelector('.btn-remove-img').addEventListener('click', () => {
                    input.value = '';
                    previewContainer.innerHTML = '';
                    previewContainer.style.display = 'none';
                });
            };
            reader.readAsDataURL(file);
        });
    });

    // Setup drag and drop styling
    const dropzones = document.querySelectorAll('.dropzone-container');
    dropzones.forEach(zone => {
        const input = zone.querySelector('input[type="file"]');
        if (!input) return;

        zone.addEventListener('click', (e) => {
            if (e.target !== input) {
                input.click();
            }
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            zone.addEventListener(eventName, (e) => {
                e.preventDefault();
                zone.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            zone.addEventListener(eventName, (e) => {
                e.preventDefault();
                zone.classList.remove('dragover');
            });
        });

        zone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            if (dt && dt.files && dt.files.length) {
                input.files = dt.files;
                input.dispatchEvent(new Event('change'));
            }
        });
    });
}

/**
 * 2. Interactive 5-Star Rating Component
 */
function initStarRating() {
    const starContainers = document.querySelectorAll('.star-rating');

    starContainers.forEach(container => {
        const inputId = container.dataset.targetInput;
        const targetInput = document.getElementById(inputId);
        const stars = container.querySelectorAll('.star');

        let selectedRating = targetInput ? parseInt(targetInput.value) || 0 : 0;

        function updateStars(val) {
            stars.forEach(star => {
                const starVal = parseInt(star.dataset.val);
                if (starVal <= val) {
                    star.classList.add('selected');
                    star.textContent = '★';
                } else {
                    star.classList.remove('selected');
                    star.textContent = '☆';
                }
            });
        }

        // Initial state
        if (selectedRating > 0) {
            updateStars(selectedRating);
        }

        stars.forEach(star => {
            star.addEventListener('mouseover', function () {
                const hoverVal = parseInt(this.dataset.val);
                stars.forEach(s => {
                    const v = parseInt(s.dataset.val);
                    if (v <= hoverVal) {
                        s.classList.add('hovered');
                        s.textContent = '★';
                    } else {
                        s.classList.remove('hovered');
                        s.textContent = '☆';
                    }
                });
            });

            star.addEventListener('mouseout', function () {
                stars.forEach(s => s.classList.remove('hovered'));
                updateStars(selectedRating);
            });

            star.addEventListener('click', function () {
                selectedRating = parseInt(this.dataset.val);
                if (targetInput) {
                    targetInput.value = selectedRating;
                }
                updateStars(selectedRating);
            });
        });
    });
}

/**
 * 3. Action Modals Management
 */
function initActionModals() {
    // Open modal triggers
    document.querySelectorAll('[data-modal-target]').forEach(button => {
        button.addEventListener('click', () => {
            const modalId = button.dataset.modalTarget;
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
            }
        });
    });

    // Close modal triggers
    document.querySelectorAll('.modal-close, [data-modal-close]').forEach(button => {
        button.addEventListener('click', () => {
            const modal = button.closest('.modal-overlay');
            if (modal) {
                modal.classList.remove('active');
            }
        });
    });

    // Close on clicking backdrop
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.classList.remove('active');
            }
        });
    });
}

/**
 * 4. Comment Form Submissions
 */
function initCommentForm() {
    const commentForm = document.getElementById('comment-form');
    if (!commentForm) return;

    commentForm.addEventListener('submit', async function (e) {
        // Normal form submission or AJAX can be handled smoothly
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'กำลังบันทึก...';
        }
    });
}

/**
 * 5. Mobile Sidebar Toggle & Drawer Handler
 */
function initSidebarToggle() {
    const toggleBtn = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('app-sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    const closeBtn = document.getElementById('sidebar-close-btn');

    function openSidebar() {
        if (sidebar) sidebar.classList.add('open');
        if (backdrop) backdrop.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('open');
        if (backdrop) backdrop.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (sidebar && sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', closeSidebar);
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeSidebar);
    }

    // Close sidebar on ESC key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('open')) {
            closeSidebar();
        }
    });

    // Close on mobile link click if navigating on same page
    if (sidebar) {
        sidebar.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 1024) {
                    closeSidebar();
                }
            });
        });
    }
}

/**
 * 6. Interactive Table: Clickable Rows & Bulk Checkbox Selection
 */
function initTableInteractions() {
    // 1. Clickable Rows
    document.querySelectorAll('tr.clickable-row').forEach(row => {
        row.addEventListener('click', (e) => {
            // Ignore click if originating from checkbox, button, or link
            if (e.target.closest('input[type="checkbox"], a, button, .checkbox-cell')) {
                return;
            }
            const href = row.dataset.href;
            if (href) {
                window.location.href = href;
            }
        });
    });

    // 2. Select All & Individual Checkboxes
    const selectAllCheckbox = document.getElementById('select-all-tickets');
    const ticketCheckboxes = document.querySelectorAll('.ticket-checkbox');
    const bulkActionBar = document.getElementById('bulk-action-bar');
    const selectedCountSpan = document.getElementById('selected-count');

    function updateBulkBar() {
        const checkedBoxes = document.querySelectorAll('.ticket-checkbox:checked');
        const count = checkedBoxes.length;

        if (selectedCountSpan) {
            selectedCountSpan.textContent = count;
        }

        if (bulkActionBar) {
            if (count > 0) {
                bulkActionBar.classList.add('active');
            } else {
                bulkActionBar.classList.remove('active');
            }
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.checked = (ticketCheckboxes.length > 0 && count === ticketCheckboxes.length);
            selectAllCheckbox.indeterminate = (count > 0 && count < ticketCheckboxes.length);
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            const isChecked = this.checked;
            ticketCheckboxes.forEach(cb => {
                cb.checked = isChecked;
                const row = cb.closest('tr');
                if (row) {
                    if (isChecked) {
                        row.classList.add('row-selected');
                    } else {
                        row.classList.remove('row-selected');
                    }
                }
            });
            updateBulkBar();
        });
    }

    ticketCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            const row = this.closest('tr');
            if (row) {
                if (this.checked) {
                    row.classList.add('row-selected');
                } else {
                    row.classList.remove('row-selected');
                }
            }
            updateBulkBar();
        });
    });
}


