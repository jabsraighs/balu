const setupHeaderFonctions = () => {
    console.log('Header functions initialized');
    // User menu toggle
    const userMenuToggle = document.querySelector('.user-menu-toggle');
    const userMenuDropdown = document.querySelector('.user-menu-dropdown');

    if (userMenuToggle && userMenuDropdown) {
        let userMenuOpen = false;

        const toggleUserMenu = () => {
            userMenuOpen = !userMenuOpen;

            if (userMenuOpen) {
                userMenuDropdown.classList.remove('hidden');
                userMenuToggle.setAttribute('aria-expanded', 'true');
            } else {
                userMenuDropdown.classList.add('hidden');
                userMenuToggle.setAttribute('aria-expanded', 'false');
            }
        };

        userMenuToggle.addEventListener('click', toggleUserMenu);

        // Close menu when clicking outside
        document.addEventListener('click', function (event) {
            const userMenuContainer = document.querySelector('.user-menu-container');
            if (userMenuContainer && !userMenuContainer.contains(event.target) && userMenuOpen) {
                toggleUserMenu();
            }
        });
    }

    // Mobile menu button
    const mobileMenuButton = document.querySelector('.mobile-menu-button');
    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', function () {
            // This fires the mobile menu open event that's handled in the layout.html.twig
            const event = new CustomEvent('openMobileMenu');
            document.dispatchEvent(event);
        });
    }

    // Theme toggle
    const themeToggle = document.querySelector('.theme-toggle');
    const darkIcon = document.querySelector('.dark-icon');
    const lightIcon = document.querySelector('.light-icon');

    if (themeToggle && darkIcon && lightIcon) {
        const updateThemeIcons = () => {
            if (document.documentElement.classList.contains('dark')) {
                darkIcon.classList.remove('hidden');
                lightIcon.classList.add('hidden');
            } else {
                darkIcon.classList.add('hidden');
                lightIcon.classList.remove('hidden');
            }
        };

        updateThemeIcons();

        document.addEventListener('themeChanged', updateThemeIcons);

        themeToggle.addEventListener('click', function () {
            const newTheme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
            
            document.dispatchEvent(new CustomEvent('themeChanged', { 
                detail: { theme: newTheme } 
            }));
        });
    }
}

document.addEventListener('turbo:load',setupHeaderFonctions);
document.addEventListener('DOMContentLoaded', setupHeaderFonctions);