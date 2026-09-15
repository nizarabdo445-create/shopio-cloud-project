document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('emailInput');
    const passwordInput = document.getElementById('passwordInput');
    const emailFeedback = document.getElementById('emailFeedback');
    const passwordFeedback = document.getElementById('passwordFeedback');
    const loginAlert = document.getElementById('loginAlert');
    const loginBtn = document.getElementById('loginBtn');
    const loginSpinner = document.getElementById('loginSpinner');

    // Remove validation styles on input
    const resetValidation = (input) => {
        input.classList.remove('is-invalid');
    };

    emailInput.addEventListener('input', () => resetValidation(emailInput));
    passwordInput.addEventListener('input', () => resetValidation(passwordInput));

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Reset previous errors
        loginAlert.style.display = 'none';
        loginAlert.textContent = '';
        emailInput.classList.remove('is-invalid');
        passwordInput.classList.remove('is-invalid');

        const email = emailInput.value.trim();
        const password = passwordInput.value;

        // Basic frontend validation
        let hasError = false;
        if (!email) {
            emailInput.classList.add('is-invalid');
            emailFeedback.textContent = 'Please enter your Email';
            hasError = true;
        }
        if (!password) {
            passwordInput.classList.add('is-invalid');
            passwordFeedback.textContent = 'Please enter your password';
            hasError = true;
        }

        if (hasError) return;

        // Loading state
        loginBtn.disabled = true;
        loginSpinner.classList.remove('d-none');

        try {
            const response = await fetch('/api/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email, password })
            });

            const result = await response.json();

            if (!response.ok) {
                // Handle API error
                loginAlert.textContent = result.message || 'Invalid email or password';
                loginAlert.style.display = 'block';
            } else if (result.status === 'success') {
                // Success! Save token and redirect
                const token = result.data.token;
                if (token) {
                    localStorage.setItem('token', token);
                    // Also store user info for convenience if needed
                    localStorage.setItem('user', JSON.stringify({
                        id: result.data.user_id,
                        name: result.data.name,
                        email: result.data.email,
                        role: result.data.role
                    }));
                }
                
                // Redirect to shop
                window.location.href = 'shop.html';
            }
        } catch (error) {
            console.error('Login error:', error);
            loginAlert.textContent = 'A network error occurred. Please try again.';
            loginAlert.style.display = 'block';
        } finally {
            // Remove loading state
            loginBtn.disabled = false;
            loginSpinner.classList.add('d-none');
        }
    });
});
