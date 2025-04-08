// Patrick & Richard

function checkPasswordStrength(password) {
  let strength = 0;
  let errors = [];

  if (password.length < 8) {
      errors.push("Password must be at least 8 characters long.");
  } else {
      strength+=10;
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

  return {
      strength: strength,
      errors: errors,
  };
}

function displayPasswordStrengthBar(password, strengthBarElement, strengthTextElement) {
    const result = checkPasswordStrength(password);
    const strength = result.strength;
    const barWidth = (strength / 13) * 100; // Adjusted based on 4 strength criteria

    if (strengthBarElement) {
        strengthBarElement.style.width = `${barWidth}%`;
        if (strength <= 9) {
            strengthBarElement.style.backgroundColor = "red";
            strengthBarElement.style.boxShadow = "0 0 10px rgba(243, 89, 89, 0.8), 0 0 20px rgba(255, 0, 0, 0.5)";
        } else if (strength <= 12) {
            strengthBarElement.style.backgroundColor = "orange";
            strengthBarElement.style.boxShadow = "0 0 10px rgba(255, 165, 0, 0.8), 0 0 20px rgba(255, 165, 0, 0.5)";
        } else {
            strengthBarElement.style.backgroundColor = "green";
            strengthBarElement.style.boxShadow = "0 0 10px rgba(104, 253, 104, 0.8), 0 0 20px rgba(0, 255, 0, 0.5)";
        }
    }

    if (strengthTextElement) {
        if (password.length === 0) {
            strengthTextElement.textContent = "";
            strengthTextElement.className = "";
            return;
        }

        if (result.errors.length > 0 && password.length > 0 && strength <= 9) {
            strengthTextElement.textContent = "Weak";
            strengthTextElement.className = "weak";
        } else if (strength <= 12 && password.length > 0) {
            strengthTextElement.textContent = "Medium";
            strengthTextElement.className = "medium";
            document.getElementById("register").disabled = false;
        } else if (strength >= 13 && password.length > 0) {
            strengthTextElement.textContent = "Strong";
            strengthTextElement.className = "strong";
            document.getElementById("register").disabled = false;
        }
         // If no errors and strength is low, still show weak
        else if (result.errors.length === 0 && strength <= 1 && password.length > 0) {
            strengthTextElement.textContent = "Weak";
            strengthTextElement.className = "weak";
        }
    }
}


document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById("password");
    const strengthBar = document.getElementById("strengthBar");
    const passwordStrengthDiv = document.getElementById("password-strength"); // Corrected variable name

    if (passwordInput && strengthBar && passwordStrengthDiv) {
        passwordInput.addEventListener("input", function () {
            displayPasswordStrengthBar(this.value, strengthBar, passwordStrengthDiv);
        });
    }
});

