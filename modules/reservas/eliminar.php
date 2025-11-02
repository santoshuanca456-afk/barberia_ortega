<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Reserva - Barbería Ortega</title>
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
                <h1 class="page-title">Eliminar Reserva</h1>
                
                <div class="row">
                    <div class="col-md-8 mx-auto">
                        <div class="card shadow">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-trash"></i> Eliminar Reserva
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning">
                                    <h6><i class="fas fa-exclamation-triangle"></i> ¿Está seguro de eliminar esta reserva?</h6>
                                    <p class="mb-0">Esta acción no se puede deshacer.</p>
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
                                                <p><strong>Estado:</strong> 
                                                    <span class="badge status-confirmada">
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
                                
                                <form method="POST" id="deleteForm">
                                    <input type="hidden" name="csrf_token" value="abc123xyz">
                                    
                                    <div class="d-flex justify-content-between">
                                        <a href="index.php" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Confirmar Eliminación
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
        document.getElementById('deleteForm').addEventListener('submit', function(e) {
            if (!confirm('¿Está completamente seguro de que desea eliminar esta reserva? Esta acción no se puede deshacer.')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>