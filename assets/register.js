window.addEventListener("DOMContentLoaded", () => {
    const password = document.querySelector('#registration_form_plainPassword_first');
    const confirmPassword = document.querySelector('#registration_form_plainPassword_second');
    const submitButton = document.querySelector('#create_btn');
    const errorMessage = document.querySelector("#error_confirm_password");
    const errorMessagePassword = document.querySelector("#error_password");

    submitButton.disabled = true;

    const validateForm = () => {
        const passwordRegex = /^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])/;
        if (!passwordRegex.test(password.value) || password.value !== confirmPassword.value) {
            // Password does not meet the requirements or passwords do not match
            submitButton.disabled = true;
        } else {
            // Password meets the requirements and passwords match
            submitButton.disabled = false;
        }
    }

    password.addEventListener('input', function () {
        const passwordRegex = /^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*]).{8,}$/;
        if (!passwordRegex.test(this.value)) {
            // Password does not meet the requirements
            errorMessagePassword.classList.remove("hidden");
            if (this.value.length < 8) {
                errorMessagePassword.textContent = "Le mot de passe doit comporter au moins 8 caractères.";
            } else {
                errorMessagePassword.textContent = "Le mot de passe doit contenir au moins une lettre majuscule, un chiffre et un caractère spécial.";
            }
        } else {
            // Password meets the requirements
            errorMessagePassword.classList.add("hidden");
        }
        validateForm();
    });

    confirmPassword.addEventListener("input", () => {
        if(confirmPassword.value.length === 0 ) {
            return
        }
        if(password.value !== confirmPassword.value) {
            errorMessage.classList.remove("hidden");
        } else {
            errorMessage.classList.add("hidden");
        }
        validateForm();
    });
});