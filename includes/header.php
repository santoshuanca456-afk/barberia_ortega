<?php
/**
 * Header común para todas las páginas
 * Sistema de Gestión - Barbería Ortega
 */
if (!isset($pageTitle)) {
    $pageTitle = SITE_NAME;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle . " - " . SITE_NAME; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>css/style.css">
    
    <style>
        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: linear-gradient(180deg, #4e73df 0%, #224abe 100%);
            padding-top: 20px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }
        .sidebar .logo {
            text-align: center;
            padding: 20px;
            color: white;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        .sidebar .logo i {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .sidebar .logo h4 {
            margin: 0;
            font-weight: 600;
        }
        .sidebar .nav {
            flex: 1;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 5px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            text-decoration: none;
            cursor: pointer;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .sidebar .nav-link i {
            width: 25px;
            margin-right: 10px;
        }
        
        /* Estilos para los dropdowns */
        .sidebar .dropdown {
            position: relative;
        }
        
        .sidebar .dropdown-menu {
            background: rgba(0, 0, 0, 0.25);
            border: none;
            box-shadow: none;
            padding: 0;
            margin: 0;
            width: calc(100% - 20px);
            margin-left: 10px;
            position: static;
            transform: none !important;
            border-radius: 0 0 8px 8px;
            overflow: hidden;
            display: none; /* Ocultos por defecto */
        }
        
        .sidebar .dropdown-menu.show {
            display: block;
            animation: slideDown 0.3s ease;
        }
        
        .sidebar .dropdown-item {
            color: rgba(255,255,255,0.8);
            padding: 10px 20px 10px 45px;
            margin: 0;
            border-radius: 0;
            display: flex;
            align-items: center;
            text-decoration: none;
            border: none;
            background: transparent;
            width: 100%;
            transition: all 0.2s;
        }
        
        .sidebar .dropdown-item:hover, .sidebar .dropdown-item.active {
            background: rgba(255,255,255,0.15);
            color: white;
            padding-left: 50px;
        }
        
        .sidebar .dropdown-toggle {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }
        
        .sidebar .dropdown-toggle::after {
            margin-left: auto;
            transition: transform 0.3s ease;
            border-top: 0.3em solid;
            border-right: 0.3em solid transparent;
            border-left: 0.3em solid transparent;
        }
        
        .sidebar .dropdown-toggle[aria-expanded="true"]::after {
            transform: rotate(180deg);
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                max-height: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                max-height: 500px;
                transform: translateY(0);
            }
        }
        
        /* Estilos para el botón de Cerrar Sesión */
        .sidebar .logout-section {
            margin-top: auto;
            padding: 20px 10px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar .logout-btn {
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px 20px;
            margin: 0 10px;
            border-radius: 8px;
            width: calc(100% - 20px);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
            cursor: pointer;
        }
        
        .sidebar .logout-btn:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .sidebar .logout-btn i {
            margin-right: 10px;
            font-size: 16px;
        }
        
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
        }
        
        .top-navbar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            position: relative;
            z-index: 999;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .card-header {
            border-radius: 10px 10px 0 0 !important;
        }
        
        /* Mejorar la legibilidad de los items activos */
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            font-weight: 600;
        }
        
        .sidebar .dropdown-item.active {
            background: rgba(255,255,255,0.2);
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-cut"></i>
            <h4><?php echo SITE_NAME; ?></h4>
            <small>Sistema de Gestión</small>
        </div>
        
        <nav class="nav flex-column">
            <!-- Módulo 1: Panel -->
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' && strpos($_SERVER['PHP_SELF'], 'dashboard') !== false ? 'active' : ''; ?>" 
               href="<?php echo SITE_URL; ?>modules/dashboard/">
                <i class="fas fa-tachometer-alt"></i> Panel
            </a>
            
            <!-- Módulo 2: Operación Diaria -->
            <div class="dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" aria-expanded="false">
                    <i class="fas fa-calendar-day"></i> Operación Diaria
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item <?php echo strpos($_SERVER['PHP_SELF'], 'reservas') !== false ? 'active' : ''; ?>" 
                           href="<?php echo SITE_URL; ?>modules/reservas/">
                            <i class="fas fa-calendar-check"></i> Reservas
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item <?php echo strpos($_SERVER['PHP_SELF'], 'clientes') !== false ? 'active' : ''; ?>" 
                           href="<?php echo SITE_URL; ?>modules/clientes/">
                            <i class="fas fa-users"></i> Clientes
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item <?php echo strpos($_SERVER['PHP_SELF'], 'servicios') !== false ? 'active' : ''; ?>" 
                           href="<?php echo SITE_URL; ?>modules/servicios/">
                            <i class="fas fa-scissors"></i> Servicios
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item <?php echo strpos($_SERVER['PHP_SELF'], 'pagos') !== false ? 'active' : ''; ?>" 
                           href="<?php echo SITE_URL; ?>modules/pagos/">
                            <i class="fas fa-cash-register"></i> Pagos de Servicios
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item <?php echo strpos($_SERVER['PHP_SELF'], 'notificaciones') !== false ? 'active' : ''; ?>" 
                           href="<?php echo SITE_URL; ?>modules/notificaciones/">
                            <i class="fas fa-bell"></i> Notificaciones
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Módulo 3: Gestión de Personal y Horarios -->
            <div class="dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" aria-expanded="false">
                    <i class="fas fa-user-clock"></i> Gestión de Personal
                </a>
                <ul class="dropdown-menu">
                    <?php if (hasRole(ROLE_ADMIN)): ?>
                    <li>
                        <a class="dropdown-item <?php echo strpos($_SERVER['PHP_SELF'], 'usuarios') !== false ? 'active' : ''; ?>" 
                           href="<?php echo SITE_URL; ?>modules/usuarios/">
                            <i class="fas fa-user-tie"></i> Usuarios
                        </a>
                    </li>
                    <?php endif; ?>
                    <li>
                        <a class="dropdown-item <?php echo strpos($_SERVER['PHP_SELF'], 'turnos') !== false ? 'active' : ''; ?>" 
                           href="<?php echo SITE_URL; ?>modules/turnos/">
                            <i class="fas fa-clock"></i> Turnos
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item <?php echo strpos($_SERVER['PHP_SELF'], 'estaciones') !== false ? 'active' : ''; ?>" 
                           href="<?php echo SITE_URL; ?>modules/estaciones/">
                            <i class="fas fa-chair"></i> Estaciones
                        </a>
                    </li>
                    <?php if (hasRole(ROLE_ADMIN)): ?>
                    <li>
                        <a class="dropdown-item <?php echo strpos($_SERVER['PHP_SELF'], 'sanciones') !== false ? 'active' : ''; ?>" 
                           href="<?php echo SITE_URL; ?>modules/sanciones/">
                            <i class="fas fa-exclamation-triangle"></i> Sanciones
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <!-- Módulo 4: Administración y Estrategia -->
            <?php if (hasRole(ROLE_ADMIN)): ?>
            <div class="dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" aria-expanded="false">
                    <i class="fas fa-chart-line"></i> Administración
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item <?php echo strpos($_SERVER['PHP_SELF'], 'alquileres') !== false ? 'active' : ''; ?>" 
                           href="<?php echo SITE_URL; ?>modules/estaciones/alquileres.php">
                            <i class="fas fa-file-invoice-dollar"></i> Gestión de Alquileres
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item <?php echo strpos($_SERVER['PHP_SELF'], 'reportes') !== false ? 'active' : ''; ?>" 
                           href="<?php echo SITE_URL; ?>modules/reportes/">
                            <i class="fas fa-chart-bar"></i> Reportes
                        </a>
                    </li>
                </ul>
            </div>
            <?php endif; ?>
        </nav>
        
        <!-- Sección de Cerrar Sesión al fondo -->
        <div class="logout-section">
            <a class="logout-btn" href="<?php echo SITE_URL; ?>modules/auth/logout.php">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-gray-800"><?php echo $pageTitle; ?></h5>
                <div class="d-flex align-items-center">
                    <span class="text-muted me-3">
                        <i class="fas fa-user-circle"></i> 
                        <?php echo $_SESSION['user_name']; ?>
                    </span>
                    <span class="badge bg-primary">
                        <?php echo ucfirst($_SESSION['user_role']); ?>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Page Content -->
        <div class="content-wrapper">
            <?php showAlert(); ?>

<!-- Bootstrap 5 JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Script mejorado para el comportamiento de los dropdowns
document.addEventListener('DOMContentLoaded', function() {
    const dropdowns = document.querySelectorAll('.sidebar .dropdown');
    
    // Inicializar todos los dropdowns como cerrados
    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        // Establecer estado inicial cerrado
        toggle.setAttribute('aria-expanded', 'false');
        menu.classList.remove('show');
        
        // Verificar si hay items activos en este dropdown
        const activeItem = dropdown.querySelector('.dropdown-item.active');
        
        // SOLO MODIFICACIÓN: No abrir automáticamente el dropdown de "Gestión de Personal"
        // cuando estamos en la página de alquileres
        const isAlquileresPage = window.location.pathname.includes('alquileres');
        const isGestionPersonal = toggle.textContent.includes('Gestión de Personal');
        
        if (activeItem && !(isAlquileresPage && isGestionPersonal)) {
            // Si hay un item activo Y no estamos en alquileres con gestión de personal, abrir este dropdown
            toggle.setAttribute('aria-expanded', 'true');
            toggle.classList.add('active');
            menu.classList.add('show');
        }
        
        // Event listener para el toggle
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            // Cerrar todos los otros dropdowns
            dropdowns.forEach(otherDropdown => {
                if (otherDropdown !== dropdown) {
                    const otherToggle = otherDropdown.querySelector('.dropdown-toggle');
                    const otherMenu = otherDropdown.querySelector('.dropdown-menu');
                    otherToggle.setAttribute('aria-expanded', 'false');
                    otherToggle.classList.remove('active');
                    otherMenu.classList.remove('show');
                }
            });
            
            // Alternar el dropdown actual
            this.setAttribute('aria-expanded', !isExpanded);
            this.classList.toggle('active', !isExpanded);
            menu.classList.toggle('show', !isExpanded);
        });
    });
    
    // Cerrar dropdowns al hacer clic fuera del sidebar
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.sidebar')) {
            dropdowns.forEach(dropdown => {
                const toggle = dropdown.querySelector('.dropdown-toggle');
                const menu = dropdown.querySelector('.dropdown-menu');
                
                // No cerrar si tiene un item activo (excepto para gestión de personal en alquileres)
                const activeItem = dropdown.querySelector('.dropdown-item.active');
                const isAlquileresPage = window.location.pathname.includes('alquileres');
                const isGestionPersonal = toggle.textContent.includes('Gestión de Personal');
                
                if (!activeItem || (isAlquileresPage && isGestionPersonal)) {
                    toggle.setAttribute('aria-expanded', 'false');
                    toggle.classList.remove('active');
                    menu.classList.remove('show');
                }
            });
        }
    });
    
    // Prevenir que los clicks en los dropdown items cierren el dropdown
    document.querySelectorAll('.sidebar .dropdown-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.stopPropagation();
            // Mantener el dropdown abierto después del click
            const dropdown = this.closest('.dropdown');
            const toggle = dropdown.querySelector('.dropdown-toggle');
            const menu = dropdown.querySelector('.dropdown-menu');
            
            toggle.setAttribute('aria-expanded', 'true');
            toggle.classList.add('active');
            menu.classList.add('show');
        });
    });
    
    console.log('Dropdowns inicializados correctamente');
});
</script>