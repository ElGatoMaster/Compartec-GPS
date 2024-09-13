// Obtener referencias a los elementos del formulario
const newPasswordInput = document.getElementById('newPassword');
const confirmPasswordInput = document.getElementById('confirmPassword');
const submitButton = document.getElementById('submitButton');
const passwordError = document.getElementById('passwordError');
const passwordRequirementsError = document.getElementById('passwordRequirementsError');

// Agregar un event listener para validar los requisitos de la contraseña cuando se escribe
newPasswordInput.addEventListener('input', validatePasswordRequirements);
confirmPasswordInput.addEventListener('input', validatePassword);

function validatePasswordRequirements() {
  // Obtener el valor de la nueva contraseña
  const newPassword = newPasswordInput.value;

  // Validar la contraseña con una expresión regular
  const passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{8,}$/;
  const isValidPassword = passwordRegex.test(newPassword);

  // Mostrar un mensaje de error si la contraseña no cumple con los requisitos
  if (!isValidPassword) {
    passwordRequirementsError.textContent = 'La contraseña debe tener al menos 8 caracteres, una letra mayuscula y un número.';
    confirmPasswordInput.disabled = true; // Desactivar el segundo campo de contraseña
    submitButton.disabled = true; // Desactivar el botón de enviar
  } else {
    passwordRequirementsError.textContent = '';
    confirmPasswordInput.disabled = false; // Activar el segundo campo de contraseña
    submitButton.disabled = false; // Activar el botón de enviar
  }
}

// Agregar un event listener para verificar las contraseñas cuando se escriben
newPasswordInput.addEventListener('input', validatePassword);
confirmPasswordInput.addEventListener('input', validatePassword);

function validatePassword() {
    // Obtener los valores de las contraseñas
    const newPassword = newPasswordInput.value;
    const confirmPassword = confirmPasswordInput.value;

    // Verificar si las contraseñas coinciden
    if (newPassword === confirmPassword) {
        // Habilitar el botón de enviar y borrar el mensaje de error
        submitButton.disabled = false;
        passwordError.textContent = '';
    } else {
        // Deshabilitar el botón de enviar y mostrar un mensaje de error
        submitButton.disabled = true;
        passwordError.textContent = 'Las contraseñas no coinciden.';
    }
}
