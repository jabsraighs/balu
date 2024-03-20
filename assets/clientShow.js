window.addEventListener("DOMContentLoaded", () => {
    const tabButtons = document.querySelectorAll("[data-tabTitle]");
    const tabContents = document.querySelectorAll("[data-tab]");
    const trigger = document.querySelector('[data-popover-target="popover-delete"]');
    console.log("🚀 ~ window.addEventListener ~ trigger:", trigger)
    const popover = document.querySelector('#popover-delete');
    console.log("🚀 ~ window.addEventListener ~ popover:", popover)
    
    tabButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const tabTitle = button.getAttribute("data-tabTitle");
            setActiveTab(tabTitle);
        });
    });

    function setActiveTab(tabTitle) {
        // Hide all tab contents
        tabContents.forEach((content) => {
            content.classList.add("hidden");
        });

        tabButtons.forEach((button) => {
            button.classList.remove("text-white", "bg-secondary");
        });

        const selectedTab = document.querySelector(`[data-tab="${tabTitle}"]`);
        selectedTab.classList.remove("hidden");

        const clickedButton = document.querySelector(`[data-tabTitle="${tabTitle}"]`);
        clickedButton.classList.add("text-white", "bg-secondary");
    }

    trigger.addEventListener('click', function() {
        if (popover.classList.contains('invisible')) {
          popover.classList.remove('invisible', 'opacity-0');
          popover.classList.add('visible', 'opacity-100');
        } else {
          popover.classList.remove('visible');
          popover.classList.add('invisible');
        }
    })


});
