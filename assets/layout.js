document.addEventListener('turbo:load', function () {

    let mobileMenuOpen = window.appConfig.mobileMenuOpen === true;
    let sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true'
        ? true
        : window.appConfig.sidebarCollapsed === true;
    if (sidebarCollapsed) {
        applySidebarCollapsed(true);
    }
    const toggleMobileMenu = (isOpen) => {
        mobileMenuOpen = isOpen;
        const sidebar = document.querySelector('aside');
        const overlay = document.querySelector('.fixed.inset-0.z-40.bg-gray-600');

        if (sidebar && overlay) {
            if (mobileMenuOpen) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                overlay.classList.add('block');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.remove('block');
                overlay.classList.add('hidden');
            }
        }
    };

    const toggleSidebar = () => {
        sidebarCollapsed = !sidebarCollapsed;
        localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
        applySidebarCollapsed(sidebarCollapsed);

    };

    function applySidebarCollapsed(isCollapsed) {
        const sidebar = document.querySelector('aside');
        const mainContent = document.querySelector('[class*="lg:pl-"]');

        if (sidebar && mainContent) {
            if (isCollapsed) {
                sidebar.classList.remove('lg:w-64');
                sidebar.classList.add('lg:w-20');
                mainContent.classList.remove('lg:pl-64');
                mainContent.classList.add('lg:pl-20');

                document.querySelectorAll('aside a span:not(:first-child), aside button span:not(:first-child)').forEach(function (el) {
                    el.classList.add('lg:hidden');
                });

                const logoContainer = document.querySelector('aside a.flex.items-center.space-x-3');
                if (logoContainer) {
                    logoContainer.classList.add('lg:justify-center');
                }
            } else {
                sidebar.classList.remove('lg:w-20');
                sidebar.classList.add('lg:w-64');
                mainContent.classList.remove('lg:pl-20');
                mainContent.classList.add('lg:pl-64');

                document.querySelectorAll('aside a span.lg\\:hidden, aside button span.lg\\:hidden').forEach(function (el) {
                    el.classList.remove('lg:hidden');
                });

                const logoContainer = document.querySelector('aside a.flex.items-center.space-x-3');
                if (logoContainer) {
                    logoContainer.classList.remove('lg:justify-center');
                }
            }
        }
    }

    const mobileMenuButton = document.querySelector('.mobile-menu-button');
    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', () => toggleMobileMenu(true));
    }

    const closeMobileButton = document.querySelector('.close-mobile-button');
    if (closeMobileButton) {
        closeMobileButton.addEventListener('click', () => toggleMobileMenu(false));
    }

    const collapseButton = document.querySelector('.collapse-sidebar-button');
    if (collapseButton) {
        collapseButton.addEventListener('click', toggleSidebar);
    }

    const logoutButton = document.querySelector('.logout-button');
    if (logoutButton) {
        logoutButton.addEventListener('click', function () {
            window.location.href = window.appConfig.logoutPath;
        });
    }
});