<?php
/**
 * Crear Nuevo Cliente
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();

// Verificar si hay redirección (viene desde módulo de reservas)
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';

$pageTitle = "Nuevo Cliente";
include '../../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-user-plus text-primary"></i> 
                    Nuevo Cliente
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
                    <form id="formCliente" action="procesar.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="crear">
                        <?php if ($redirect): ?>
                            <input type="hidden" name="redirect" value="<?php echo $redirect; ?>">
                        <?php endif; ?>

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
                                       placeholder="Ej: Juan Pérez García"
                                       required 
                                       autofocus>
                                <div class="invalid-feedback">El nombre es obligatorio</div>
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
                                <small class="text-muted">Formato: 8 dígitos (ej: 70123456)</small>
                                <div class="invalid-feedback">Teléfono inválido. Debe ser 8 dígitos comenzando con 6 o 7</div>
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

                            <!-- Notas -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-sticky-note"></i> Notas / Observaciones
                                </label>
                                <textarea name="notas" 
                                          class="form-control" 
                                          rows="4" 
                                          placeholder="Preferencias del cliente, alergias, observaciones especiales, etc."></textarea>
                                <small class="text-muted">Información adicional que pueda ser útil para el servicio</small>
                            </div>
                        </div>

                        <!-- Información adicional -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Nota:</strong> Los campos marcados con (*) son obligatorios. 
                            El teléfono y correo son opcionales pero recomendados para comunicación.
                        </div>

                        <!-- Botones -->
                        <div class="text-end mt-4">
                            <a href="index.php" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cliente
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tips para un buen registro -->
            <div class="card shadow mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb text-warning"></i> Tips para un Buen Registro
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li><strong>Nombre completo:</strong> Registra nombres y apellidos para evitar confusiones</li>
                        <li><strong>Teléfono verificado:</strong> Confirma que el número sea correcto para recordatorios</li>
                        <li><strong>Preferencias:</strong> Anota estilos de corte, alergias o preferencias especiales</li>
                        <li><strong>Actualización:</strong> Mantén los datos actualizados en cada visita</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Validación del teléfono en tiempo real
    $('#telefono').on('input', function() {
        let valor = $(this).val().replace(/\D/g, '');
        $(this).val(valor);
        
        if (valor.length > 0 && valor.length < 8) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    
    // Capitalizar nombre automáticamente
    $('#nombre').on('blur', function() {
        let nombre = $(this).val();
        let nombreCapitalizado = nombre.split(' ')
            .map(palabra => palabra.charAt(0).toUpperCase() + palabra.slice(1).toLowerCase())
            .join(' ');
        $(this).val(nombreCapitalizado);
    });
    
    // Convertir correo a minúsculas
    $('#correo').on('blur', function() {
        $(this).val($(this).val().toLowerCase());
    });
    
    // Validación del formulario
    $('#formCliente').on('submit', function(e) {
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