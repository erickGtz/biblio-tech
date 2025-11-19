// auth.js - Frontend JavaScript para autenticación

// Función para mostrar mensajes
function showMessage(message, type) {
    const existingMsg = document.querySelector('.message');
    if (existingMsg) {
        existingMsg.remove();
    }

    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;

    const form = document.querySelector('form');
    form.parentNode.insertBefore(messageDiv, form);

    setTimeout(() => {
        messageDiv.remove();
    }, 5000);
}

// Login
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(loginForm);
        const data = {
            correo: formData.get('correo'),
            contrasena: formData.get('contrasena')
        };

        try {
            const response = await fetch('../backend/controllers/authController.php?action=login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            const userId = result.data.idUsuario;

            if (result.success) {
                showMessage('Inicio de sesión exitoso', 'success');
                localStorage.setItem('user_id', userId);
                
                // Redirigir al dashboard o página principal
                setTimeout(() => {
                    window.location.href = 'dashboard.html';
                }, 1500);
            } else {
                showMessage(result.message || 'Error al iniciar sesión', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showMessage('Error de conexión con el servidor', 'error');
        }
    });
}

// Registro
const registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(registerForm);
        const data = {
            nombre: formData.get('nombre'),
            apellido_paterno: formData.get('apellido_paterno'),
            apellido_materno: formData.get('apellido_materno'),
            fecha_nacimiento: formData.get('fecha_nacimiento'),
            escuela: formData.get('escuela'),
            grado_academico: formData.get('grado_academico'),
            correo: formData.get('correo'),
            contrasena: formData.get('contrasena')
        };

        // Validación básica
        if (!data.nombre || !data.apellido_paterno || !data.apellido_materno) {
            showMessage('Por favor completa todos los campos', 'error');
            return;
        }

        if (data.contrasena.length < 6) {
            showMessage('La contraseña debe tener al menos 6 caracteres', 'error');
            return;
        }

        try {
            
            const response = await fetch('../backend/controllers/authController.php?action=register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                showMessage('Registro exitoso. Redirigiendo...', 'success');
                setTimeout(() => {
                    window.location.href = 'login.html';
                }, 2000);
            } else {
                showMessage(result.message || 'Error al registrar usuario', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showMessage('Error de conexión con el servidor', 'error');
        }
    });
}