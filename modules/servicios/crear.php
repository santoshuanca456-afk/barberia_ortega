<?php
/**
 * Crear Nuevo Servicio
 * Sistema de Gestión - Barbería Ortega
 */

require_once '../../config/config.php';
requireLogin();

$pageTitle = "Nuevo Servicio";
include '../../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-plus-circle text-primary"></i> 
                    Nuevo Servicio
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
                    <form id="formServicio" action="procesar.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="crear">

                        <div class="row">
                            <!-- Nombre del Servicio -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-scissors"></i> Nombre del Servicio *
                                </label>
                                <input type="text" 
                                       name="nombre" 
                                       id="nombre" 
                                       class="form-control" 
                                       placeholder="Ej: Corte de Cabello, Afeitado, Tinte, etc."
                                       required 
                                       autofocus>
                                <div class="invalid-feedback">El nombre del servicio es obligatorio</div>
                            </div>

                            <!-- Duración -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-clock"></i> Duración (minutos) *
                                </label>
                                <input type="number" 
                                       name="duracion_minutos" 
                                       id="duracion_minutos" 
                                       class="form-control" 
                                       placeholder="30"
                                       min="5"
                                       max="300"
                                       step="5"
                                       required>
                                <small class="text-muted">Entre 5 y 300 minutos (5 horas)</small>
                                <div class="invalid-feedback">Ingrese una duración válida</div>
                            </div>

                            <!-- Precio -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-dollar-sign"></i> Precio (Bs) *
                                </label>
                                <input type="number" 
                                       name="precio" 
                                       id="precio" 
                                       class="form-control" 
                                       placeholder="50.00"
                                       min="0"
                                       step="0.01"
                                       required>
                                <small class="text-muted">Precio en Bolivianos</small>
                                <div class="invalid-feedback">Ingrese un precio válido</div>
                            </div>
                        </div>

                        <!-- Vista Previa -->
                        <div class="alert alert-info mt-3" id="preview" style="display: none;">
                            <h6><i class="fas fa-eye"></i> Vista Previa</h6>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5 id="previewNombre" class="mb-1">-</h5>
                                                    <span class="badge bg-info" id="previewDuracion">
                                                        <i class="fas fa-clock"></i> - min
                                                    </span>
                                                </div>
                                                <div class="text-end">
                                                    <h4 class="text-success mb-0" id="previewPrecio">Bs 0.00</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="text-end mt-4">
                            <a href="index.php" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Servicio
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Ejemplos de Servicios Comunes -->
            <div class="card shadow mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb text-warning"></i> Servicios Comunes en Barberías
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <div class="border rounded p-2">
                                <strong>Corte de Cabello</strong><br>
                                <small class="text-muted">30-45 min | Bs 30-50</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="border rounded p-2">
                                <strong>Corte + Barba</strong><br>
                                <small class="text-muted">45-60 min | Bs 50-70</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="border rounded p-2">
                                <strong>Afeitado Clásico</strong><br>
                                <small class="text-muted">20-30 min | Bs 25-40</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="border rounded p-2">
                                <strong>Tinte/Coloración</strong><br>
                                <small class="text-muted">60-90 min | Bs 80-120</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="border rounded p-2">
                                <strong>Diseño/Dibujo</strong><br>
                                <small class="text-muted">15-30 min | Bs 20-35</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="border rounded p-2">
                                <strong>Limpieza Facial</strong><br>
                                <small class="text-muted">30-45 min | Bs 40-60</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Actualizar vista previa en tiempo real
    function actualizarPreview() {
        const nombre = $('#nombre').val();
        const duracion = $('#duracion_minutos').val();
        const precio = $('#precio').val();
        
        if (nombre || duracion || precio) {
            $('#preview').fadeIn();
            $('#previewNombre').text(nombre || 'Nombre del Servicio');
            $('#previewDuracion').html(`<i class="fas fa-clock"></i> ${duracion || 0} min`);
            $('#previewPrecio').text(`Bs ${parseFloat(precio || 0).toFixed(2)}`);
        } else {
            $('#preview').fadeOut();
        }
    }
    
    $('#nombre, #duracion_minutos, #precio').on('input', actualizarPreview);
    
    // Validación del formulario
    $('#formServicio').on('submit', function(e) {
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
    
    // Capitalizar nombre automáticamente
    $('#nombre').on('blur', function() {
        let nombre = $(this).val();
        let nombreCapitalizado = nombre.split(' ')
            .map(palabra => palabra.charAt(0).toUpperCase() + palabra.slice(1).toLowerCase())
            .join(' ');
        $(this).val(nombreCapitalizado);
        actualizarPreview();
    });
    
    // Formatear precio automáticamente
    $('#precio').on('blur', function() {
        if ($(this).val()) {
            $(this).val(parseFloat($(this).val()).toFixed(2));
            actualizarPreview();
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>