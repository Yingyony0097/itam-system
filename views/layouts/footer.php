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

<!-- Auto-hide Toast Notifications -->
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