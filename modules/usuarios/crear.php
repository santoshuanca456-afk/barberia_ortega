<?php
/**
 * Crear Nuevo Usuario
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();
requireRole(ROLE_ADMIN); // Solo administradores

$pageTitle = "Nuevo Usuario";
include '../../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-user-plus text-primary"></i> 
                    Nuevo Usuario
                </h1>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>

            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-wpforms"></i> Formulario de Registro
                    </h6>
                </div>
                <div class="card-body">
                    <form id="formUsuario" action="procesar.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="crear">

                        <div class="row">
                            <!-- Nombre -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user"></i> Nombre Completo *
                                </label>
                                <input type="text" 
                                       name="nombre" 
                                       id="nombre" 
                                       class="form-control" 
                                       placeholder="Ej: Carlos Méndez"
                                       required 
                                       autofocus>
                                <div class="invalid-feedback">El nombre es obligatorio</div>
                            </div>

                            <!-- Usuario -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user-circle"></i> Nombre de Usuario *
                                </label>
                                <input type="text" 
                                       name="usuario" 
                                       id="usuario" 
                                       class="form-control" 
                                       placeholder="usuario123"
                                       pattern="[a-zA-Z0-9_]{3,50}"
                                       required>
                                <small class="text-muted">3-50 caracteres, solo letras, números y guión bajo</small>
                                <div class="invalid-feedback">Usuario inválido (mínimo 3 caracteres)</div>
                            </div>

                            <!-- Contraseña -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-lock"></i> Contraseña *
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           name="contrasena" 
                                           id="contrasena" 
                                           class="form-control" 
                                           placeholder="Mínimo 6 caracteres"
                                           minlength="6"
                                           required>
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            onclick="togglePassword('contrasena')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Mínimo 6 caracteres</small>
                                <div class="invalid-feedback">La contraseña debe tener al menos 6 caracteres</div>
                            </div>

                            <!-- Teléfono -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-phone"></i> Teléfono
                                </label>
                                <input type="tel" 
                                       name="telefono" 
                                       id="telefono" 
                                       class="form-control" 
                                       placeholder="70000000"
                                       pattern="[67]\d{7}"
                                       maxlength="8">
                                <small class="text-muted">8 dígitos comenzando con 6 o 7</small>
                                <div class="invalid-feedback">Teléfono inválido</div>
                            </div>

                            <!-- Correo -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-envelope"></i> Correo Electrónico
                                </label>
                                <input type="email" 
                                       name="correo" 
                                       id="correo" 
                                       class="form-control" 
                                       placeholder="ejemplo@correo.com">
                                <div class="invalid-feedback">Correo electrónico inválido</div>
                            </div>

                            <!-- Rol -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user-tag"></i> Rol *
                                </label>
                                <select name="rol" id="rol" class="form-select" required>
                                    <option value="">Seleccione un rol</option>
                                    <option value="barbero">
                                        <i class="fas fa-cut"></i> Barbero
                                    </option>
                                    <option value="apoyo">Apoyo</option>
                                    <option value="administrador">Administrador</option>
                                </select>
                                <div class="invalid-feedback">Seleccione un rol</div>
                            </div>

                            <!-- Estado -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-toggle-on"></i> Estado
                                </label>
                                <select name="estado" class="form-select">
                                    <option value="activo" selected>Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <!-- Descripción del Rol Seleccionado -->
                        <div id="rolDescripcion" class="alert alert-info" style="display: none;">
                            <h6 id="rolTitulo"></h6>
                            <ul id="rolPermisos" class="mb-0 small"></ul>
                        </div>

                        <!-- Botones -->
                        <div class="text-end mt-4">
                            <a href="index.php" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Información de Seguridad -->
            <div class="card shadow mt-4">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-shield-alt"></i> Recomendaciones de Seguridad
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li><strong>Usuario único:</strong> Asegúrate de que cada usuario tenga un nombre de usuario diferente</li>
                        <li><strong>Contraseña segura:</strong> Usa contraseñas con letras, números y símbolos</li>
                        <li><strong>Roles apropiados:</strong> Asigna el rol correcto según las responsabilidades</li>
                        <li><strong>Actualización:</strong> Pide al usuario cambiar su contraseña en el primer inicio de sesión</li>
                        <li><strong>Desactivación:</strong> Si un usuario ya no trabaja, desactívalo en lugar de eliminarlo</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Mostrar/ocultar contraseña
    window.togglePassword = function(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = event.currentTarget.querySelector('i');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    };
    
    // Generar usuario automáticamente desde el nombre
    $('#nombre').on('blur', function() {
        if ($('#usuario').val() === '') {
            let nombre = $(this).val().toLowerCase();
            let usuario = nombre.split(' ')[0] + Math.floor(Math.random() * 100);
            $('#usuario').val(usuario.replace(/[^a-z0-9_]/g, ''));
        }
    });
    
    // Validar usuario en tiempo real
    $('#usuario').on('input', function() {
        let valor = $(this).val().replace(/[^a-zA-Z0-9_]/g, '');
        $(this).val(valor);
    });
    
    // Validar teléfono
    $('#telefono').on('input', function() {
        let valor = $(this).val().replace(/\D/g, '');
        $(this).val(valor);
    });
    
    // Mostrar descripción del rol
    const roles = {
        'administrador': {
            titulo: '<i class="fas fa-user-shield"></i> Administrador',
            permisos: [
                'Acceso total al sistema',
                'Gestión de usuarios',
                'Reportes financieros',
                'Configuración del sistema',
                'Auditoría completa'
            ],
            clase: 'alert-danger'
        },
        'barbero': {
            titulo: '<i class="fas fa-cut"></i> Barbero',
            permisos: [
                'Gestión de reservas propias',
                'Gestión de clientes',
                'Registro de servicios',
                'Ver agenda personal',
                'Alquiler de estaciones'
            ],
            clase: 'alert-success'
        },
        'apoyo': {
            titulo: '<i class="fas fa-hands-helping"></i> Apoyo',
            permisos: [
                'Gestión de turnos',
                'Limpieza y apertura/cierre',
                'Ver reservas del día',
                'Notificaciones internas',
                'Tareas operativas'
            ],
            clase: 'alert-warning'
        }
    };
    
    $('#rol').on('change', function() {
        const rolSeleccionado = $(this).val();
        
        if (rolSeleccionado && roles[rolSeleccionado]) {
            const rol = roles[rolSeleccionado];
            
            $('#rolTitulo').html(rol.titulo);
            
            let permisosHtml = '';
            rol.permisos.forEach(permiso => {
                permisosHtml += `<li>${permiso}</li>`;
            });
            $('#rolPermisos').html(permisosHtml);
            
            $('#rolDescripcion')
                .removeClass('alert-danger alert-success alert-warning')
                .addClass(rol.clase)
                .fadeIn();
        } else {
            $('#rolDescripcion').fadeOut();
        }
    });
    
    // Validación del formulario
    $('#formUsuario').on('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            
            Swal.fire({
                icon: 'warning',
                title: 'Campos incompletos',
                text: 'Por favor complete correctamente todos los campos obligatorios',
                confirmButtonColor: '#4e73df'
            });
        }
        $(this).addClass('was-validated');
    });
});
</script>

<?php include '../../includes/footer.php'; ?>