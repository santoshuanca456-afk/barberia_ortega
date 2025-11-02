<script>
function abrirModalCrear() {
    document.querySelector('#modalCrearEstacion form').reset();
    document.getElementById('form_action').value = 'crear';
    document.getElementById('form_id_estacion').value = '';
    document.getElementById('modalTitulo').innerHTML = '<i class="fas fa-plus-circle"></i> Nueva Estación';
    document.getElementById('form_disponible').checked = true;
    
    var modal = new bootstrap.Modal(document.getElementById('modalCrearEstacion'));
    modal.show();
}

function editarEstacion(estacion) {
    document.getElementById('form_action').value = 'editar';
    document.getElementById('form_id_estacion').value = estacion.id_estacion;
    document.getElementById('form_nombre').value = estacion.nombre;
    document.getElementById('form_descripcion').value = estacion.descripcion || '';
    document.getElementById('form_disponible').checked = estacion.disponible == 1;
    document.getElementById('modalTitulo').innerHTML = '<i class="fas fa-edit"></i> Editar Estación';
    
    new bootstrap.Modal(document.getElementById('modalCrearEstacion')).show();
}

function asignarAlquiler(id_estacion, nombre) {
    document.getElementById('alquiler_id_estacion').value = id_estacion;
    document.getElementById('alquiler_estacion_nombre').textContent = nombre;
    
    new bootstrap.Modal(document.getElementById('modalAsignarAlquiler')).show();
}

function toggleDisponibilidad(id_estacion, disponible) {
    const mensaje = disponible ? '¿Marcar esta estación como disponible?' : '¿Marcar esta estación como no disponible?';
    
    Swal.fire({
        title: '¿Estás seguro?',
        text: mensaje,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4e73df',
        cancelButtonColor: '#858796',
        confirmButtonText: 'Sí, cambiar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `procesar.php?action=toggle_disponibilidad&id=${id_estacion}&disponible=${disponible}`;
        }
    });
}

// Resetear formularios al cerrar modales
document.getElementById('modalCrearEstacion').addEventListener('hidden.bs.modal', function() {
    document.querySelector('#modalCrearEstacion form').reset();
    document.getElementById('form_action').value = 'crear';
    document.getElementById('form_id_estacion').value = '';
    document.getElementById('modalTitulo').innerHTML = '<i class="fas fa-plus-circle"></i> Nueva Estación';
});
</script>