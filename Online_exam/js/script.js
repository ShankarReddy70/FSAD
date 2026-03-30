// script.js - General utility scripts

document.addEventListener('DOMContentLoaded', () => {
    // Form validation
    const forms = document.querySelectorAll('.validate-form');
    
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                    // Add simple visual cue
                    field.style.borderColor = 'var(--danger)';
                } else {
                    field.classList.remove('is-invalid');
                    field.style.borderColor = '#ddd';
                }
            });

            // Specific check for passwords match if register mode
            const pwd = form.querySelector('input[name="password"]');
            const confirmPwd = form.querySelector('input[name="confirm_password"]');
            
            if (pwd && confirmPwd) {
                if (pwd.value !== confirmPwd.value) {
                    isValid = false;
                    alert("Passwords do not match!");
                    confirmPwd.style.borderColor = 'var(--danger)';
                }
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    });

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});
