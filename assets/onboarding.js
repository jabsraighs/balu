window.addEventListener("DOMContentLoaded", () => {
    const phoneInput = document.querySelector("#user_information_phone")

    function formatFrenchPhoneNumber(input) {
        // Remove all non-numeric characters from the input
        const phoneNumber = input.replace(/\D/g, '');
    
        // Check if the phone number has the correct length for a French number
        if (phoneNumber.length <= 14) {
            // Add the country code (+33) to the phone number
            let formattedPhoneNumber = phoneNumber.replace(/(\d{1})(\d{2})(\d{2})(\d{2})(\d{2})/, '+33 $1 $2 $3 $4 $5');
            
            // If the phone number has more than 9 digits, limit it to 9 digits
            if (formattedPhoneNumber.length > 17) {
                formattedPhoneNumber = formattedPhoneNumber.slice(0, 17);
            }
    
            return formattedPhoneNumber;
        } else {
            // If the phone number has more than 10 digits, return it as is
            return input;
        }
    }

    phoneInput.addEventListener('input', function(event) {
        const formattedPhoneNumber = formatFrenchPhoneNumber(event.target.value);
        event.target.value = formattedPhoneNumber;
    });
});