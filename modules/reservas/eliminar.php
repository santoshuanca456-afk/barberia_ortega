<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Reserva - Barbería Ortega</title>
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
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
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
        
        .alert-info {
            border-left: 4px solid #17a2b8;
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
                <h1 class="page-title">Gestionar Reserva</h1>
                
                <div class="row">
                    <div class="col-md-8 mx-auto">
                        <div class="card shadow">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-exchange-alt"></i> Cambiar Estado de Reserva
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Alerta dinámica según el estado -->
                                <div id="alertContainer">
                                    <div class="alert alert-warning">
                                        <h6><i class="fas fa-exclamation-triangle"></i> ¿Está seguro de cambiar el estado de esta reserva?</h6>
                                        <p class="mb-0">Esta acción modificará el estado actual de la reserva.</p>
                                    </div>
                                </div>
                                
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6>Detalles de la Reserva</h6>
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
                                
                                <!-- Selector de estado -->
                                <div class="mb-4">
                                    <label class="form-label"><strong>Cambiar estado a:</strong></label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button" class="btn btn-outline-primary estado-btn" data-estado="pendiente">
                                            <i class="fas fa-clock"></i> Pendiente
                                        </button>
                                        <button type="button" class="btn btn-outline-success estado-btn" data-estado="confirmada">
                                            <i class="fas fa-check"></i> Confirmada
                                        </button>
                                        <button type="button" class="btn btn-outline-danger estado-btn" data-estado="cancelada">
                                            <i class="fas fa-times"></i> Cancelada
                                        </button>
                                        <button type="button" class="btn btn-outline-info estado-btn" data-estado="completada">
                                            <i class="fas fa-check-double"></i> Completada
                                        </button>
                                    </div>
                                </div>
                                
                                <form method="POST" id="statusForm">
                                    <input type="hidden" name="csrf_token" value="abc123xyz">
                                    <input type="hidden" name="nuevo_estado" id="nuevoEstado" value="">
                                    
                                    <div class="d-flex justify-content-between">
                                        <a href="index.php" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Volver
                                        </a>
                                        <button type="submit" class="btn btn-danger" id="submitBtn" disabled>
                                            <i class="fas fa-sync-alt"></i> <span id="btnText">Seleccione un estado</span>
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
            const estadoBtns = document.querySelectorAll('.estado-btn');
            const nuevoEstadoInput = document.getElementById('nuevoEstado');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const currentStatus = document.getElementById('currentStatus');
            const alertContainer = document.getElementById('alertContainer');
            
            // Estados y sus configuraciones
            const estados = {
                'pendiente': {
                    texto: 'Marcar como Pendiente',
                    clase: 'btn-warning',
                    icono: 'fa-clock',
                    alerta: 'alert-warning',
                    mensaje: 'La reserva se marcará como pendiente.'
                },
                'confirmada': {
                    texto: 'Confirmar Reserva',
                    clase: 'btn-success',
                    icono: 'fa-check',
                    alerta: 'alert-success',
                    mensaje: 'La reserva se confirmará.'
                },
                'cancelada': {
                    texto: 'Cancelar Reserva',
                    clase: 'btn-danger',
                    icono: 'fa-times',
                    alerta: 'alert-danger',
                    mensaje: 'La reserva se cancelará.'
                },
                'completada': {
                    texto: 'Marcar como Completada',
                    clase: 'btn-info',
                    icono: 'fa-check-double',
                    alerta: 'alert-info',
                    mensaje: 'La reserva se marcará como completada.'
                }
            };
            
            // Manejar clic en botones de estado
            estadoBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const estado = this.getAttribute('data-estado');
                    const config = estados[estado];
                    
                    // Actualizar el formulario
                    nuevoEstadoInput.value = estado;
                    
                    // Activar el botón de envío
                    submitBtn.disabled = false;
                    submitBtn.className = 'btn ' + config.clase;
                    btnText.innerHTML = `<i class="fas ${config.icono}"></i> ${config.texto}`;
                    
                    // Actualizar la alerta
                    alertContainer.innerHTML = `
                        <div class="alert ${config.alerta}">
                            <h6><i class="fas ${config.icono}"></i> ¿Está seguro de cambiar el estado de esta reserva?</h6>
                            <p class="mb-0">${config.mensaje}</p>
                        </div>
                    `;
                    
                    // Resaltar el botón seleccionado
                    estadoBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                });
            });
            
            // Manejar envío del formulario
            document.getElementById('statusForm').addEventListener('submit', function(e) {
                if (!nuevoEstadoInput.value) {
                    e.preventDefault();
                    alert('Por favor, seleccione un estado para la reserva.');
                    return;
                }
                
                const estado = nuevoEstadoInput.value;
                const config = estados[estado];
                const confirmMessage = `¿Está completamente seguro de que desea ${config.texto.toLowerCase()}?`;
                
                if (!confirm(confirmMessage)) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>