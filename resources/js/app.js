import './alert.js';

const sidebar = document.getElementById('dashboard-sidebar');
const sidebarToggle = document.getElementById('sidebar-toggle');
const sidebarOverlay = document.getElementById('sidebar-overlay');

if (sidebar && sidebarToggle && sidebarOverlay) {
    const closeSidebar = () => {
        sidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
        sidebarToggle.setAttribute('aria-expanded', 'false');
    };

    sidebarToggle.addEventListener('click', () => {
        const isOpen = sidebarToggle.getAttribute('aria-expanded') === 'true';

        sidebar.classList.toggle('-translate-x-full', isOpen);
        sidebarOverlay.classList.toggle('hidden', isOpen);
        sidebarToggle.setAttribute('aria-expanded', String(!isOpen));
    });

    sidebarOverlay.addEventListener('click', closeSidebar);
}
