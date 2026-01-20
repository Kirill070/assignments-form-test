import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('registration-form');
    if (!form) {
        return;
    }

    const errorAlert = document.getElementById('register-error');
    const successAlert = document.getElementById('register-success');
    const submitButton = form.querySelector('button[type="submit"]');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const showError = (message) => {
        errorAlert.textContent = message;
        errorAlert.classList.remove('d-none');
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        errorAlert.classList.add('d-none');
        errorAlert.textContent = '';

        const formData = new FormData(form);
        const payload = Object.fromEntries(formData.entries());

        const validations = [
            [!payload.first_name, 'First name is required.'],
            [!payload.last_name, 'Last name is required.'],
            [!payload.email, 'Email is required.'],
            [payload.email && !payload.email.includes('@'), 'Email must contain "@".'],
            [!payload.password, 'Password is required.'],
            [payload.password !== payload.password_confirmation, 'Passwords do not match.'],
        ];

        for (const [hasError, message] of validations) {
            if (hasError) {
                showError(message);
                return;
            }
        }

        submitButton.disabled = true;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
                body: formData,
            });

            if (response.status === 419) {
                showError('Session expired. Refresh the page and try again.');
                return;
            }

            const data = await response.json().catch(() => null);

            if (!response.ok || !data) {
                showError('Registration failed. Try again.');
                return;
            }

            if (!data.success) {
                showError(data.message || 'Registration failed.');
                return;
            }

            form.classList.add('d-none');
            successAlert.textContent = data.message || 'Registration successful.';
            successAlert.classList.remove('d-none');
        } catch (error) {
            showError('Registration failed. Try again.');
        } finally {
            submitButton.disabled = false;
        }
    });
});
