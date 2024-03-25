window.addEventListener("DOMContentLoaded", () =>
{
    const selectClient = document.querySelector("#quote_client");
    const changeClient = document.querySelector("#client_change");
    const showClientName = document.querySelector("#current_client_name");
    const showClientAddress = document.querySelector("#current_client_address");
    const showClient = document.querySelector("#client_show")

    selectClient.addEventListener("change", (e) =>
    {
        const selectedOption = e.target.options[e.target.selectedIndex];
        const selectedValue = e.target.value;

        if (selectedValue.trim() !== '') {
            selectClient.classList.add("hidden")
            changeClient.classList.remove("hidden")
            showClientName.textContent = selectedOption.dataset.clientName;
            showClientAddress.textContent = selectedOption.dataset.clientAddress;
            showClient.classList.remove("hidden")
        }
    });

    changeClient.addEventListener("click", () => {
        selectClient.value = "";
    
        showClientName.textContent = "";
        showClientAddress.textContent = "";
        selectClient.classList.remove("hidden")
        changeClient.classList.add("hidden")
        showClient.classList.add("hidden")
    });
})