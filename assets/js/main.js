/**
 * JavaScript Principal
 * Sistema de Gestión - Barbería Ortega
 */

// Inicialización cuando el DOM está listo
$(document).ready(function() {
    
    // Agregar animación fade-in a las cards
    $('.card').addClass('fade-in');
    
    // Tooltip de Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Popover de Bootstrap
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});

/**
 * Confirmar acción con SweetAlert
 */
function confirmarAccion(mensaje, callback) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: mensaje,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4e73df',
        cancelButtonColor: '#858796',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            callback();
        }
    });
}

/**
 * Mostrar alerta de éxito
 */
function mostrarExito(titulo, mensaje) {
    Swal.fire({
        icon: 'success',
        title: titulo,
        text: mensaje,
        timer: 3000,
        showConfirmButton: false
    });
}

/**
 * Mostrar alerta de error
 */
function mostrarError(titulo, mensaje) {
    Swal.fire({
        icon: 'error',
        title: titulo,
        text: mensaje,
        confirmButtonColor: '#e74a3b'
    });
}

/**
 * Mostrar alerta de advertencia
 */
function mostrarAdvertencia(titulo, mensaje) {
    Swal.fire({
        icon: 'warning',
        title: titulo,
        text: mensaje,
        confirmButtonColor: '#f6c23e'
    });
}

/**
 * Mostrar loading
 */
function mostrarLoading(mensaje = 'Cargando...') {
    Swal.fire({
        title: mensaje,
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

/**
 * Cerrar loading
 */
function cerrarLoading() {
    Swal.close();
}

/**
 * Validar formulario
 */
function validarFormulario(formId) {
    const form = document.getElementById(formId);
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        mostrarAdvertencia('Campos incompletos', 'Por favor complete todos los campos requeridos');
        return false;
    }
    return true;
}

/**
 * Formatear fecha a formato DD/MM/YYYY
 */
function formatearFecha(fecha) {
    const date = new Date(fecha);
    const dia = String(date.getDate()).padStart(2, '0');
    const mes = String(date.getMonth() + 1).padStart(2, '0');
    const anio = date.getFullYear();
    return `${dia}/${mes}/${anio}`;
}

/**
 * Formatear fecha y hora
 */
function formatearFechaHora(fecha) {
    const date = new Date(fecha);
    const dia = String(date.getDate()).padStart(2, '0');
    const mes = String(date.getMonth() + 1).padStart(2, '0');
    const anio = date.getFullYear();
    const horas = String(date.getHours()).padStart(2, '0');
    const minutos = String(date.getMinutes()).padStart(2, '0');
    return `${dia}/${mes}/${anio} ${horas}:${minutos}`;
}

/**
 * Formatear moneda boliviana
 */
function formatearMoneda(monto) {
    return 'Bs ' + parseFloat(monto).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

/**
 * Sanitizar entrada de texto
 */
function sanitizarTexto(texto) {
    const div = document.createElement('div');
    div.textContent = texto;
    return div.innerHTML;
}

/**
 * Validar email
 */
function validarEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Validar teléfono boliviano
 */
function validarTelefono(telefono) {
    // Formato: 8 dígitos empezando con 6 o 7
    const re = /^[67]\d{7}$/;
    return re.test(telefono);
}

/**
 * Copiar al portapapeles
 */
function copiarAlPortapapeles(texto) {
    navigator.clipboard.writeText(texto).then(() => {
        mostrarExito('Copiado', 'Texto copiado al portapapeles');
    }).catch(() => {
        mostrarError('Error', 'No se pudo copiar el texto');
    });
}

/**
 * Imprimir sección
 */
function imprimirSeccion(elementId) {
    const contenido = document.getElementById(elementId).innerHTML;
    const ventana = window.open('', '', 'height=600,width=800');
    ventana.document.write('<html><head><title>Imprimir</title>');
    ventana.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">');
    ventana.document.write('</head><body>');
    ventana.document.write(contenido);
    ventana.document.write('</body></html>');
    ventana.document.close();
    ventana.print();
}

/**
 * Exportar tabla a CSV
 */
function exportarTablaCSV(tableId, filename = 'datos.csv') {
    const table = document.getElementById(tableId);
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = [], cols = rows[i].querySelectorAll('td, th');
        for (let j = 0; j < cols.length; j++) {
            row.push(cols[j].innerText);
        }
        csv.push(row.join(','));
    }
    
    const csvString = csv.join('\n');
    const link = document.createElement('a');
    link.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvString);
    link.download = filename;
    link.click();
}

/**
 * Toggle sidebar en móvil
 */
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('show');
}

/**
 * Búsqueda en tiempo real en tabla
 */
function buscarEnTabla(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    const tr = table.getElementsByTagName('tr');
    
    input.addEventListener('keyup', function() {
        const filter = input.value.toUpperCase();
        
        for (let i = 1; i < tr.length; i++) {
            let encontrado = false;
            const td = tr[i].getElementsByTagName('td');
            
            for (let j = 0; j < td.length; j++) {
                if (td[j]) {
                    const txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        encontrado = true;
                        break;
                    }
                }
            }
            
            tr[i].style.display = encontrado ? '' : 'none';
        }
    });
}

/**
 * Calcular edad desde fecha de nacimiento
 */
function calcularEdad(fechaNacimiento) {
    const hoy = new Date();
    const nacimiento = new Date(fechaNacimiento);
    let edad = hoy.getFullYear() - nacimiento.getFullYear();
    const mes = hoy.getMonth() - nacimiento.getMonth();
    
    if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
        edad--;
    }
    
    return edad;
}

/**
 * Generar código aleatorio
 */
function generarCodigo(longitud = 8) {
    const caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let codigo = '';
    for (let i = 0; i < longitud; i++) {
        codigo += caracteres.charAt(Math.floor(Math.random() * caracteres.length));
    }
    return codigo;
}

/**
 * Debounce para optimizar búsquedas
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Cargar contenido con AJAX
 */
function cargarContenido(url, contenedorId) {
    mostrarLoading('Cargando contenido...');
    
    fetch(url)
        .then(response => response.text())
        .then(data => {
            document.getElementById(contenedorId).innerHTML = data;
            cerrarLoading();
        })
        .catch(error => {
            mostrarError('Error', 'No se pudo cargar el contenido');
            console.error('Error:', error);
        });
}

/**
 * Actualizar contador en tiempo real
 */
function actualizarContador(elementId, valorFinal, duracion = 1000) {
    const elemento = document.getElementById(elementId);
    const valorInicial = parseInt(elemento.textContent) || 0;
    const incremento = (valorFinal - valorInicial) / (duracion / 16);
    let valorActual = valorInicial;
    
    const timer = setInterval(() => {
        valorActual += incremento;
        if ((incremento > 0 && valorActual >= valorFinal) || (incremento < 0 && valorActual <= valorFinal)) {
            elemento.textContent = valorFinal;
            clearInterval(timer);
        } else {
            elemento.textContent = Math.floor(valorActual);
        }
    }, 16);
}

/**
 * Verificar permisos de notificaciones
 */
function verificarPermisoNotificaciones() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
}

/**
 * Enviar notificación del navegador
 */
function enviarNotificacion(titulo, mensaje) {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(titulo, {
            body: mensaje,
            icon: '/assets/img/logo.png'
        });
    }
}

// Llamar al cargar la página
verificarPermisoNotificaciones();