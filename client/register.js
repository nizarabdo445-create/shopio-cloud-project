document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('registerForm');
    const usernameInput = document.getElementById('usernameInput');
    const emailInput = document.getElementById('emailInput');
    const passwordInput = document.getElementById('passwordInput');
    const conpassInput = document.getElementById('conpassInput');
    
    const usernameFeedback = document.getElementById('usernameFeedback');
    const emailFeedback = document.getElementById('emailFeedback');
    const passwordFeedback = document.getElementById('passwordFeedback');
    const conpassFeedback = document.getElementById('conpassFeedback');
    
    const registerAlert = document.getElementById('registerAlert');
    const registerBtn = document.getElementById('registerBtn');
    const registerSpinner = document.getElementById('registerSpinner');

    // Remove validation styles on input
    const resetValidation = (input) => {
        input.classList.remove('is-invalid');
    };

    usernameInput.addEventListener('input', () => resetValidation(usernameInput));
    emailInput.addEventListener('input', () => resetValidation(emailInput));
    passwordInput.addEventListener('input', () => resetValidation(passwordInput));
    conpassInput.addEventListener('input', () => resetValidation(conpassInput));

    registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Reset previous errors
        registerAlert.style.display = 'none';
        registerAlert.className = 'alert';
        registerAlert.textContent = '';
        
        [usernameInput, emailInput, passwordInput, conpassInput].forEach(resetValidation);

        const name = usernameInput.value.trim();
        const email = emailInput.value.trim();
        const password = passwordInput.value;
        const conpass = conpassInput.value;

        // Basic frontend validation
        let hasError = false;
        
        if (name.length < 3) {
            usernameInput.classList.add('is-invalid');
            usernameFeedback.textContent = 'Name must be at least 3 characters';
            hasError = true;
        }
        
        if (!email) {
            emailInput.classList.add('is-invalid');
            emailFeedback.textContent = 'Please enter your email';
            hasError = true;
        }
        
        if (password.length < 7) {
            passwordInput.classList.add('is-invalid');
            passwordFeedback.textContent = 'Password must be at least 7 characters';
            hasError = true;
        }
        
        if (password !== conpass) {
            conpassInput.classList.add('is-invalid');
            conpassFeedback.textContent = 'Passwords do not match';
            hasError = true;
        }

        if (hasError) return;

        // Loading state
        registerBtn.disabled = true;
        registerSpinner.classList.remove('d-none');

        try {
            const response = await fetch('/api/auth/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ name, email, password })
            });

            const result = await response.json();

            if (!response.ok) {
                // Handle API error
                let errorMsg = result.message || 'Registration failed';
                
                // If API returns specific validation array, show the first one or combine them
                if (result.errors && result.errors.length > 0) {
                    errorMsg = result.errors.join('<br>');
                }
                
                registerAlert.innerHTML = errorMsg;
                registerAlert.classList.add('alert-danger');
                registerAlert.style.display = 'block';
            } else if (result.status === 'success' || result.user_id) { // Checking status or presence of user_id depending on API format
                // Show success message
                registerAlert.textContent = 'Account created successfully! Redirecting to login...';
                registerAlert.classList.add('alert-success');
                registerAlert.style.display = 'block';
                
                // Redirect to login after 2 seconds
                setTimeout(() => {
                    window.location.href = 'login.html';
                }, 2000);
            }
        } catch (error) {
            console.error('Registration error:', error);
            registerAlert.textContent = 'A network error occurred. Please try again.';
            registerAlert.classList.add('alert-danger');
            registerAlert.style.display = 'block';
        } finally {
            // Remove loading state unless we are redirecting
            if (registerAlert.classList.contains('alert-danger')) {
                registerBtn.disabled = false;
                registerSpinner.classList.add('d-none');
            }
        }
    });
});
