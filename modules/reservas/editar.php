<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelar/Reactivar Reserva - Barbería Ortega</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #8B4513;
            --secondary-color: #D2691E;
            --accent-color: #A0522D;
            --light-color: #F5F5DC;
            --dark-color: #3E2723;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            border-radius: 10px 10px 0 0 !important;
            font-weight: bold;
        }
        
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }
        
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        
        .status-pendiente {
            background-color: #ffc107;
            color: #212529;
        }
        
        .status-confirmada {
            background-color: #28a745;
            color: white;
        }
        
        .status-cancelada {
            background-color: #dc3545;
            color: white;
        }
        
        .status-completada {
            background-color: #17a2b8;
            color: white;
        }
        
        .badge {
            font-size: 0.8rem;
            padding: 0.4em 0.6em;
        }
        
        .alert-warning {
            border-left: 4px solid #ffc107;
        }
        
        .alert-danger {
            border-left: 4px solid #dc3545;
        }
        
        .alert-success {
            border-left: 4px solid #28a745;
        }
        
        .sidebar {
            background-color: var(--dark-color);
            min-height: 100vh;
            color: white;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.8rem 1rem;
            border-radius: 0.25rem;
            margin-bottom: 0.2rem;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-link i {
            margin-right: 0.5rem;
            width: 1.2rem;
            text-align: center;
        }
        
        .main-content {
            padding: 2rem;
        }
        
        .page-title {
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--secondary-color);
        }
        
        .reservation-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
        }
        
        .action-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .action-card.selected {
            border: 2px solid;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
            }
            
            .main-content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: var(--dark-color);">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-cut"></i> Barbería Ortega
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> Administrador
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Configuración</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-2 col-md-3 sidebar p-0">
                <div class="d-flex flex-column p-3">
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="#" class="nav-link active">
                                <i class="fas fa-calendar-alt"></i> Reservas
                            </a>
                        </li>
                        <li>
                            <a href="#" class="nav-link">
                                <i class="fas fa-users"></i> Clientes
                            </a>
                        </li>
                        <li>
                            <a href="#" class="nav-link">
                                <i class="fas fa-user-tie"></i> Barberos
                            </a>
                        </li>
                        <li>
                            <a href="#" class="nav-link">
                                <i class="fas fa-chart-bar"></i> Reportes
                            </a>
                        </li>
                        <li>
                            <a href="#" class="nav-link">
                                <i class="fas fa-cog"></i> Configuración
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-10 col-md-9 main-content">
                <h1 class="page-title">Cancelar/Reactivar Reserva</h1>
                
                <div class="row">
                    <div class="col-md-8 mx-auto">
                        <div class="card shadow">
                            <div class="card-header bg-warning text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-exchange-alt"></i> Cambiar Estado de Reserva
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Información de la reserva -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h6>Detalles de la Reserva #123</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Cliente:</strong> Juan Pérez</p>
                                                <p><strong>Teléfono:</strong> +34 612 345 678</p>
                                                <p><strong>Email:</strong> juan.perez@example.com</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Barbero:</strong> Carlos Ortega</p>
                                                <p><strong>Fecha:</strong> 15/06/2023 10:30</p>
                                                <p><strong>Servicio:</strong> Corte de cabello y afeitado</p>
                                                <p><strong>Estado Actual:</strong> 
                                                    <span id="currentStatus" class="badge status-confirmada">
                                                        Confirmada
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <p><strong>Notas:</strong> Cliente prefiere corte clásico con degradado.</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Selección de acción -->
                                <div class="mb-4">
                                    <h6 class="mb-3">Seleccione la acción a realizar:</h6>
                                    <div class="row">
                                        <!-- Cancelar Reserva -->
                                        <div class="col-md-6 mb-3">
                                            <div class="card action-card h-100" id="cancelCard" data-action="cancelar">
                                                <div class="card-body text-center">
                                                    <div class="mb-3">
                                                        <i class="fas fa-times-circle fa-3x text-danger"></i>
                                                    </div>
                                                    <h5 class="card-title">Cancelar Reserva</h5>
                                                    <p class="card-text">Marca la reserva como cancelada. El cliente será notificado.</p>
                                                    <div class="mt-3">
                                                        <span class="badge bg-danger">Estado: Cancelada</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Reactivar Reserva -->
                                        <div class="col-md-6 mb-3">
                                            <div class="card action-card h-100" id="reactivateCard" data-action="reactivar">
                                                <div class="card-body text-center">
                                                    <div class="mb-3">
                                                        <i class="fas fa-redo fa-3x text-success"></i>
                                                    </div>
                                                    <h5 class="card-title">Reactivar Reserva</h5>
                                                    <p class="card-text">Vuelve a poner la reserva en estado pendiente para confirmación.</p>
                                                    <div class="mt-3">
                                                        <span class="badge bg-warning text-dark">Estado: Pendiente</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Alerta dinámica -->
                                <div id="alertContainer" class="mb-4" style="display: none;">
                                    <div class="alert alert-warning">
                                        <h6><i class="fas fa-exclamation-triangle"></i> ¿Está seguro de realizar esta acción?</h6>
                                        <p class="mb-0" id="alertMessage">Seleccione una acción para ver los detalles.</p>
                                    </div>
                                </div>
                                
                                <!-- Formulario -->
                                <form method="POST" id="statusForm" action="procesar_estado.php">
                                    <input type="hidden" name="csrf_token" value="abc123xyz">
                                    <input type="hidden" name="id_reserva" value="123">
                                    <input type="hidden" name="action" id="formAction" value="">
                                    <input type="hidden" name="nuevo_estado" id="nuevoEstado" value="">
                                    
                                    <div class="d-flex justify-content-between">
                                        <a href="index.php" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Volver
                                        </a>
                                        <button type="submit" class="btn btn-warning" id="submitBtn" disabled>
                                            <i class="fas fa-sync-alt"></i> <span id="btnText">Seleccione una acción</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cancelCard = document.getElementById('cancelCard');
            const reactivateCard = document.getElementById('reactivateCard');
            const formAction = document.getElementById('formAction');
            const nuevoEstado = document.getElementById('nuevoEstado');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const alertContainer = document.getElementById('alertContainer');
            const alertMessage = document.getElementById('alertMessage');
            const currentStatus = document.getElementById('currentStatus');
            
            // Configuración de acciones
            const acciones = {
                'cancelar': {
                    texto: 'Cancelar Reserva',
                    clase: 'btn-danger',
                    icono: 'fa-times',
                    alerta: 'alert-danger',
                    mensaje: 'La reserva será marcada como cancelada. Esta acción notificará al cliente.',
                    estado: 'cancelada'
                },
                'reactivar': {
                    texto: 'Reactivar Reserva',
                    clase: 'btn-success',
                    icono: 'fa-redo',
                    alerta: 'alert-success',
                    mensaje: 'La reserva volverá a estado pendiente para su confirmación.',
                    estado: 'pendiente'
                }
            };
            
            // Manejar selección de cancelar
            cancelCard.addEventListener('click', function() {
                seleccionarAccion('cancelar');
            });
            
            // Manejar selección de reactivar
            reactivateCard.addEventListener('click', function() {
                seleccionarAccion('reactivar');
            });
            
            function seleccionarAccion(accion) {
                const config = acciones[accion];
                
                // Quitar selección anterior
                cancelCard.classList.remove('selected', 'border-danger');
                reactivateCard.classList.remove('selected', 'border-success');
                
                // Aplicar selección actual
                if (accion === 'cancelar') {
                    cancelCard.classList.add('selected', 'border-danger');
                } else {
                    reactivateCard.classList.add('selected', 'border-success');
                }
                
                // Actualizar el formulario
                formAction.value = accion;
                nuevoEstado.value = config.estado;
                
                // Activar el botón de envío
                submitBtn.disabled = false;
                submitBtn.className = 'btn ' + config.clase;
                btnText.innerHTML = `<i class="fas ${config.icono}"></i> ${config.texto}`;
                
                // Actualizar la alerta
                alertContainer.style.display = 'block';
                alertContainer.innerHTML = `
                    <div class="alert ${config.alerta}">
                        <h6><i class="fas ${config.icono}"></i> ¿Está seguro de realizar esta acción?</h6>
                        <p class="mb-0">${config.mensaje}</p>
                    </div>
                `;
            }
            
            // Manejar envío del formulario
            document.getElementById('statusForm').addEventListener('submit', function(e) {
                if (!formAction.value) {
                    e.preventDefault();
                    alert('Por favor, seleccione una acción para la reserva.');
                    return;
                }
                
                const accion = formAction.value;
                const config = acciones[accion];
                const confirmMessage = `¿Está completamente seguro de que desea ${config.texto.toLowerCase()}?`;
                
                if (!confirm(confirmMessage)) {
                    e.preventDefault();
                } else {
                    // Simular cambio de estado (en un caso real esto se haría en el backend)
                    currentStatus.textContent = config.estado.charAt(0).toUpperCase() + config.estado.slice(1);
                    currentStatus.className = 'badge status-' + config.estado;
                    
                    // Mostrar mensaje de éxito
                    alert('¡Estado de reserva actualizado correctamente!');
                }
            });
        });
    </script>
</body>
</html>