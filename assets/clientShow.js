window.addEventListener("DOMContentLoaded", () => {
    const tabButtons = document.querySelectorAll("[data-tabTitle]");
    const tabContents = document.querySelectorAll("[data-tab]");

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
});
