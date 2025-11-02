/**
 * Validación del Formulario de Login
 * Sistema de Gestión - Barbería Ortega
 */

// Validación del formulario de login
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const usuario = document.getElementById('usuario').value.trim();
    const contrasena = document.getElementById('contrasena').value;
    
    // Validar campos vacíos
    if (usuario === '' || contrasena === '') {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Campos vacíos',
            text: 'Por favor complete todos los campos',
            confirmButtonColor: '#667eea'
        });
        return false;
    }
    
    // Validar longitud mínima del usuario
    if (usuario.length < 3) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Usuario inválido',
            text: 'El usuario debe tener al menos 3 caracteres',
            confirmButtonColor: '#667eea'
        });
        return false;
    }
    
    // Validar longitud mínima de la contraseña
    if (contrasena.length < 6) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Contraseña inválida',
            text: 'La contraseña debe tener al menos 6 caracteres',
            confirmButtonColor: '#667eea'
        });
        return false;
    }
    
    // Mostrar loading mientras procesa
    Swal.fire({
        title: 'Verificando...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    return true;
});

// Limpiar espacios en blanco al escribir en el campo usuario
document.getElementById('usuario').addEventListener('input', function(e) {
    this.value = this.value.trim();
});

// Prevenir copiar/pegar en el campo de contraseña (opcional, aumenta seguridad)
document.getElementById('contrasena').addEventListener('paste', function(e) {
    e.preventDefault();
    Swal.fire({
        icon: 'info',
        title: 'Acción no permitida',
        text: 'Por seguridad, no se permite pegar en el campo de contraseña',
        timer: 2000,
        showConfirmButton: false
    });
});

// Agregar efecto visual al focus de inputs
document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('focus', function() {
        this.parentElement.parentElement.classList.add('focused');
    });
    
    input.addEventListener('blur', function() {
        this.parentElement.parentElement.classList.remove('focused');
    });
});

// Mostrar/ocultar contraseña (si se agrega el botón)
function togglePassword() {
    const passwordField = document.getElementById('contrasena');
    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);
}

// Detectar tecla Enter en los inputs
document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('loginForm').dispatchEvent(new Event('submit'));
        }
    });
});