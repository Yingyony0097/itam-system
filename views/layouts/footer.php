</main>
    <!-- End Page Content -->
    
</div>
<!-- End Main Wrapper -->

<!-- Footer -->
<footer class="glass-card text-center" style="margin: 0 16px 16px 272px; padding: 16px; border-radius: 12px;">
    <p class="m-0" style="font-size: 14px; color: var(--gray-600);">
        &copy; <?= date('Y') ?> ITAM System - P-line Company | Vientiane, Laos
    </p>
</footer>

<!-- SweetAlert2 for Toast Notifications & Confirmations -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- jQuery -->
<script src="/public/assets/js/jquery.min.js"></script>

<!-- Bootstrap 5 JS Bundle -->
<script src="/public/assets/js/bootstrap.bundle.min.js"></script>

<!-- Lucide Icons Initialization -->
<script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Re-initialize icons after dynamic content loads
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>

<!-- Custom JavaScript -->
<script src="/public/assets/js/custom.js"></script>

<!-- SweetAlert2 Toast Notifications -->
<script>
// Configure SweetAlert2 Toast
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 4000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

// Show flash messages automatically
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($flash = get_flash_message()): ?>
        Toast.fire({
            icon: '<?= $flash['type'] === 'success' ? 'success' : 'error' ?>',
            title: <?= json_encode($flash['message']) ?>
        });
    <?php endif; ?>
});

// Delete Confirmation Function
window.confirmDelete = function(form, itemName = 'this item') {
    event.preventDefault();

    Swal.fire({
        title: 'Are you sure?',
        html: `You are about to delete <strong>${itemName}</strong>.<br>This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="lucide-trash-2" style="width: 16px; height: 16px; display: inline-block; margin-right: 5px;"></i> Yes, delete it!',
        cancelButtonText: '<i class="lucide-x" style="width: 16px; height: 16px; display: inline-block; margin-right: 5px;"></i> Cancel',
        customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false,
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Submit form
            form.submit();
        }
    });

    // Re-initialize Lucide icons in SweetAlert
    setTimeout(() => lucide.createIcons(), 100);

    return false;
};

// Status Toggle Confirmation Function
window.confirmToggleStatus = function(form, userName, currentStatus) {
    event.preventDefault();

    const action = currentStatus === '1' ? 'deactivate' : 'activate';
    const actionColor = currentStatus === '1' ? '#ffc107' : '#28a745';

    Swal.fire({
        title: 'Confirm Action',
        html: `Are you sure you want to <strong>${action}</strong> user <strong>${userName}</strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: actionColor,
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Yes, ${action}!`,
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'btn',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false,
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });

    return false;
};
</script>

<!-- Auto-hide Bootstrap Toast Notifications (Fallback) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toastElList = [].slice.call(document.querySelectorAll('.toast'));
    const toastList = toastElList.map(function (toastEl) {
        return new bootstrap.Toast(toastEl, {
            autohide: true,
            delay: 5000
        });
    });

    // Show toasts
    toastList.forEach(toast => toast.show());
});
</script>

<!-- Responsive Sidebar for Mobile -->
<style>
@media (max-width: 991px) {
    .main-wrapper {
        margin-left: 0 !important;
    }
    
    footer {
        margin-left: 16px !important;
    }
}
</style>

</body>
</html>