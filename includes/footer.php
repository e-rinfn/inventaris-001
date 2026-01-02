</div><!-- /wrapper -->

<script>
    // Sidebar Toggle Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebarToggleBtn');
        const mainContent = document.querySelector('.main-content');
        const toggleIcon = toggleBtn ? toggleBtn.querySelector('.toggle-icon') : null;

        // Fungsi update icon
        function updateIcon() {
            if (toggleIcon) {
                if (sidebar.classList.contains('collapsed')) {
                    toggleIcon.textContent = '☰';
                    toggleBtn.title = 'Tampilkan Sidebar';
                } else {
                    toggleIcon.textContent = '✕';
                    toggleBtn.title = 'Sembunyikan Sidebar';
                }
            }
        }

        // Cek status sidebar dari localStorage
        const sidebarState = localStorage.getItem('sidebarCollapsed');
        if (sidebarState === 'true') {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
            document.body.classList.add('sidebar-collapsed');
        }
        updateIcon();

        // Fungsi toggle sidebar
        function toggleSidebar() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            document.body.classList.toggle('sidebar-collapsed');

            // Simpan status ke localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);

            updateIcon();
        }

        // Event listener untuk tombol toggle di navbar
        if (toggleBtn) {
            toggleBtn.addEventListener('click', toggleSidebar);
        }
    });
</script>
</body>

</html>