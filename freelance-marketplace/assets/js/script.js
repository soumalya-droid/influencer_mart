// Auto-dismiss alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    // Enable Bootstrap form validation
    const forms = document.querySelectorAll('.needs-validation');

    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Password strength meter
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const strengthMeter = document.getElementById('password-strength');
            if (strengthMeter) {
                const password = passwordInput.value;
                let strength = 0;

                if (password.length >= 8) strength++;
                if (password.match(/[a-z]+/)) strength++;
                if (password.match(/[A-Z]+/)) strength++;
                if (password.match(/[0-9]+/)) strength++;
                if (password.match(/[!@#$%^&*(),.?":{}|<>]+/)) strength++;

                const strengthText = ['Very Weak', 'Weak', 'Medium', 'Strong', 'Very Strong'];
                const strengthClass = ['danger', 'warning', 'info', 'success', 'success'];

                strengthMeter.textContent = strengthText[strength];
                strengthMeter.className = `text-${strengthClass[strength]}`;
            }
        });
    }

    // Chat auto-scroll
    const chatContainer = document.querySelector('.chat-container');
    if (chatContainer) {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    // Real-time notifications check
    if (typeof(EventSource) !== "undefined" && document.querySelector('.nav-link[href*="notifications.php"]')) {
        const source = new EventSource("api/notifications.php");
        source.onmessage = function(event) {
            const data = JSON.parse(event.data);
            if (data.count > 0) {
                const badge = document.querySelector('.nav-link[href*="notifications.php"] .badge');
                if (badge) {
                    badge.textContent = data.count;
                } else {
                    const link = document.querySelector('.nav-link[href*="notifications.php"]');
                    link.innerHTML += ` <span class="badge bg-danger">${data.count}</span>`;
                }
            }
        };
    }
});
