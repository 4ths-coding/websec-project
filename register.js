document.addEventListener('DOMContentLoaded', function() {
    const registrationForm = document.getElementById('registrationForm'); // Target the form by its ID
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const usernameInput = document.getElementById("username");
            const passwordInput = document.getElementById("password");
            const passwordErrorsDiv = document.getElementById("passwordErrors");
            const messageDiv = document.createElement('p'); // Create a paragraph for PHP messages
            messageDiv.id = 'php-message';
            registrationForm.insertBefore(messageDiv, registrationForm.querySelector('button')); // Insert before the button

            const username = usernameInput.value;
            const password = passwordInput.value;
            const strengthResult = checkPasswordStrength(password);

            passwordErrorsDiv.innerHTML = strengthResult.errors.length > 0
                ? `<ul>${strengthResult.errors.map(err => `<li>${err}</li>`).join('')}</ul>`
                : '';

            if (strengthResult.errors.length > 0) {
                return; // Don't submit if password is weak
            }

            fetch('register.php', { // Your PHP registration script
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded', // Or 'application/json'
                },
                body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
            })
            .then(response => response.text())
            .then(data => {
                messageDiv.textContent = data; // Display response from PHP
                if (data.includes('Registration successful')) {
                    // Optionally redirect or clear the form
                    registrationForm.reset();
                    passwordErrorsDiv.innerHTML = '';
                }
            })
            .catch(error => {
                console.error('Error sending registration data:', error);
                messageDiv.textContent = 'An unexpected error occurred.';
            });
        });

        const passwordInput = document.getElementById("password");
        const strengthBar = document.getElementById("strengthBar");
        const errorsDisplay = document.getElementById("passwordErrors");

        if (passwordInput && strengthBar && errorsDisplay) {
            passwordInput.addEventListener("input", function () {
                displayPasswordStrengthBar(passwordInput.value, strengthBar, errorsDisplay);
            });
        }
    }

    const loginForm = document.querySelector('.login-container h2:contains("Login")').parentNode; // Adjust selector if needed
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const usernameInput = document.getElementById("username");
            const passwordInput = document.getElementById("password");
            const messageDiv = document.createElement('p'); // Create a paragraph for PHP messages
            messageDiv.id = 'php-message';
            loginForm.insertBefore(messageDiv, loginForm.querySelector('button')); // Insert before the button

            const username = usernameInput.value;
            const password = passwordInput.value;

            fetch('login.php', { // Your PHP login script
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
            })
            .then(response => response.text())
            .then(data => {
                messageDiv.textContent = data; // Display response from PHP
                if (data.includes('Login successful')) {
                    // Optionally redirect
                    window.location.href = '/dashboard.php'; // Example
                }
            })
            .catch(error => {
                console.error('Error sending login data:', error);
                messageDiv.textContent = 'An unexpected error occurred.';
            });
        });
    }
});

function checkPasswordStrength(password) {
  let strength = 0;
  let errors = [];

  if (password.length < 8) {
    errors.push("Password must be at least 8 characters long.");
  } else {
    strength++;
  }

  if (password.match(/[a-z]/)) {
    strength++;
  } else {
    errors.push("Password must contain at least one lowercase letter.");
  }

  if (password.match(/[A-Z]/)) {
    strength++;
  } else {
    errors.push("Password must contain at least one uppercase letter.");
  }

  if (password.match(/[0-9]/)) {
    strength++;
  } else {
    errors.push("Password must contain at least one number.");
  }

  if (password.match(/[^a-zA-Z0-9]/)) {
    strength++;
  } else {
    errors.push("Password must contain at least one special character.");
  }

  return {
    strength: strength,
    errors: errors,
  };
}

function displayPasswordStrengthBar(password, strengthBar, errorsElement) {
  const result = checkPasswordStrength(password);
  const strength = result.strength;

  const barWidth = (strength / 5) * 100;

  strengthBar.style.width = `${barWidth}%`;

  if (strength <= 2) {
    strengthBar.style.backgroundColor = "red";
  } else if (strength <= 4) {
    strengthBar.style.backgroundColor = "orange";
  } else {
    strengthBar.style.backgroundColor = "green";
  }

  if (result.errors.length > 0) {
    errorsElement.innerHTML = `<ul>${result.errors.map((error) => `<li>${error}</li>`).join("")}</ul>`;
  } else {
    errorsElement.innerHTML = "";
  }
}
