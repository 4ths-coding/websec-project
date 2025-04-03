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

        const passwordInput = document.getElementById("password");
        const strengthBar = document.getElementById("strengthBar");
        const errorsDisplay = document.getElementById("passwordErrors");

        if(passwordInput && strengthBar && errorsDisplay){
            passwordInput.addEventListener("input", function () {
              displayPasswordStrengthBar(passwordInput.value, strengthBar, errorsDisplay);
            });
        }

        function validateLogin() {
          let username = document.getElementById("username").value;
          let password = document.getElementById("password").value;

          if (username === "" || password === "") {
            alert("Please enter both username and password.");
            return;
          }

          if (password.length < 6) {
            alert("Password must be at least 6 characters long.");
            return;
          }

          if (username.length < 3) {
            alert("Username must be at least 3 characters long.");
            return;
          }

          console.log("Registration successful:", { username, password });
          alert("Registration Successful!");

          document.getElementById("username").value = "";
          document.getElementById("password").value = "";
        }
