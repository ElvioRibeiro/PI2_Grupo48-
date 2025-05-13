// Basic Frontend Validations and Interactions
document.addEventListener('DOMContentLoaded', function () {
    // --- Form Validation Example (Bootstrap 5 style) ---
    // This is a generic example. Specific forms might need more tailored validation.
    const formsToValidate = document.querySelectorAll('form'); // Or more specific selectors like '#registerForm', '#loginForm'

    Array.from(formsToValidate).forEach(form => {
        form.addEventListener('submit', event => {
            let formIsValid = true;

            // --- Custom Validations (add as needed) ---

            // Example: Password confirmation for registration form
            if (form.id === 'registerForm') {
                const password = form.querySelector('#senha');
                const confirmPassword = form.querySelector('#confirm_senha');
                if (password && confirmPassword && password.value !== confirmPassword.value) {
                    confirmPassword.classList.add('is-invalid');
                    // Ensure feedback div exists or create one dynamically if preferred
                    const feedbackDiv = confirmPassword.nextElementSibling;
                    if (feedbackDiv && feedbackDiv.classList.contains('invalid-feedback')) {
                        feedbackDiv.textContent = 'As senhas não coincidem.';
                    }
                    formIsValid = false;
                } else if (confirmPassword) {
                    confirmPassword.classList.remove('is-invalid');
                    confirmPassword.classList.add('is-valid'); // Optional: show valid state
                }
            }
            
            // Example: Check required fields (Bootstrap handles this with 'required' attribute, but you can add more)
            form.querySelectorAll('[required]').forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    formIsValid = false;
                } else {
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid'); // Optional
                }
            });


            // If custom validation fails, prevent submission
            if (!formIsValid) {
                event.preventDefault();
                event.stopPropagation();
                // Optionally, scroll to the first invalid field or show a general error message.
            }
            
            // Add Bootstrap's default validation classes if using their system
            // form.classList.add('was-validated'); // This line can be used if you rely more on Bootstrap's built-in validation styles after submit

        }, false);
    });

    // --- Delete Confirmation ---
    // This is handled inline in ad-list.php via onclick="return confirm(...)"
    // If you wanted to make it more generic via JS:
    /*
    const deleteButtons = document.querySelectorAll('a[href*="delete-ad"]'); // Example selector
    deleteButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            if (!confirm('Tem certeza que deseja excluir este item? Esta ação não pode ser desfeita.')) {
                event.preventDefault();
            }
        });
    });
    */

    // --- Auto-dismiss alerts ---
    const alertList = document.querySelectorAll('.alert-dismissible');
    alertList.forEach(function (alert) {
        // Ensure bootstrap's Alert component is available if you want to use its methods
        // For simple timeout:
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getInstance(alert);
            if (bsAlert) {
                bsAlert.close();
            } else {
                // Fallback if Bootstrap's JS isn't fully initialized or alert is not a BS alert
                alert.style.display = 'none';
            }
        }, 5000); // Dismiss after 5 seconds
    });

    // --- Preview image on file input change (for create/edit ad forms) ---
    const photoInput = document.getElementById('foto');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer'); // Add this div in your HTML
    const imagePreview = document.getElementById('imagePreview'); // Add an <img> tag with this ID

    if (photoInput && imagePreviewContainer && imagePreview) {
        photoInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreviewContainer.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                imagePreview.src = '#';
                imagePreviewContainer.style.display = 'none';
            }
        });
    }
});
