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
    
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- jQuery (necesario para SweetAlert2 con Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>css/style.css">
    
    <!-- CSS adicional -->
    <?php if (isset($css_extra)): ?>
        <?php echo $css_extra; ?>
    <?php endif; ?>
    
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

        /* Estilos para SweetAlert2 personalizados */
        .swal2-popup {
            border-radius: 12px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .swal2-title {
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .swal2-confirm {
            border-radius: 8px;
            padding: 10px 25px;
        }
        
        .swal2-cancel {
            border-radius: 8px;
            padding: 10px 25px;
        }

        /* Estilos para SweetAlert2 en esquina superior derecha */
        .swal2-toast {
            border-radius: 8px !important;
            margin: 10px !important;
        }

        .swal2-popup.swal2-toast {
            padding: 15px 20px !important;
        }

        /* Asegurar que las alertas toast no sean muy grandes */
        .swal2-toast .swal2-title {
            font-size: 14px !important;
            margin: 0 !important;
        }

        /* Animación personalizada para entrada desde la derecha */
        @keyframes bounceInRight {
            from, 60%, 75%, 90%, to {
                animation-timing-function: cubic-bezier(0.215, 0.610, 0.355, 1.000);
            }
            from {
                opacity: 0;
                transform: translate3d(3000px, 0, 0);
            }
            60% {
                opacity: 1;
                transform: translate3d(-25px, 0, 0);
            }
            75% {
                transform: translate3d(10px, 0, 0);
            }
            90% {
                transform: translate3d(-5px, 0, 0);
            }
            to {
                transform: none;
            }
        }

        .animated.bounceInRight {
            animation-name: bounceInRight;
            animation-duration: 0.8s;
        }

        /* Estilos para el dropdown de notificaciones */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #e74a3b;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-item {
            border-left: 3px solid #4e73df;
            margin-bottom: 5px;
        }

        .notification-item.unread {
            background-color: #f8f9fc;
            border-left-color: #e74a3b;
        }

        .notification-time {
            font-size: 0.8rem;
            color: #6c757d;
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
                    <!-- NUEVO: Pago de Alquileres -->
                    <li>
                        <a class="dropdown-item <?php echo strpos($_SERVER['PHP_SELF'], 'pagoalquiler') !== false ? 'active' : ''; ?>" 
                           href="<?php echo SITE_URL; ?>modules/pagoalquiler/">
                            <i class="fas fa-receipt"></i> Pagos de Alquileres
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
            <a class="logout-btn" href="<?php echo SITE_URL; ?>modules/auth/logout.php" id="logoutBtn">
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
                    <!-- Dropdown de Notificaciones - VERSIÓN CORREGIDA -->
                    <div class="dropdown me-3">
                        <a class="btn btn-light position-relative" href="#" role="button" id="notificationsDropdown" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            <?php
                            // CÓDIGO SEGURO PARA NOTIFICACIONES - EVITA ERRORES
                            $unreadCount = 0;
                            try {
                                if (isset($_SESSION['user_id'])) {
                                    $db = getDB();
                                    
                                    // Verificar si la tabla existe
                                    $tableExists = $db->query("SHOW TABLES LIKE 'notificaciones'")->rowCount() > 0;
                                    
                                    if ($tableExists) {
                                        // Verificar columnas de la tabla
                                        $columns = $db->query("SHOW COLUMNS FROM notificaciones")->fetchAll(PDO::FETCH_COLUMN);
                                        
                                        // Determinar nombre correcto de las columnas
                                        $userColumn = in_array('id_usuario', $columns) ? 'id_usuario' : 
                                                     (in_array('user_id', $columns) ? 'user_id' : 
                                                     (in_array('usuario_id', $columns) ? 'usuario_id' : 'id'));
                                                     
                                        $readColumn = in_array('leido', $columns) ? 'leido' : 
                                                     (in_array('read', $columns) ? 'read' : 'leido');
                                        
                                        // Consulta segura
                                        $stmt = $db->prepare("SELECT COUNT(*) as count FROM notificaciones WHERE $userColumn = ? AND $readColumn = 0");
                                        $stmt->execute([$_SESSION['user_id']]);
                                        $result = $stmt->fetch();
                                        $unreadCount = $result ? $result['count'] : 0;
                                    }
                                }
                            } catch (Exception $e) {
                                // Silenciar el error para no romper la página
                                $unreadCount = 0;
                                error_log("Error en notificaciones: " . $e->getMessage());
                            }
                            
                            if ($unreadCount > 0): ?>
                                <span class="notification-badge"><?php echo $unreadCount; ?></span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown" style="width: 350px;">
                            <li><h6 class="dropdown-header">Notificaciones</h6></li>
                            <?php
                            $notifications = [];
                            try {
                                if (isset($_SESSION['user_id']) && isset($tableExists) && $tableExists && isset($userColumn)) {
                                    // Obtener últimas notificaciones
                                    $stmt = $db->prepare("
                                        SELECT * FROM notificaciones 
                                        WHERE $userColumn = ? 
                                        ORDER BY fecha_creacion DESC 
                                        LIMIT 5
                                    ");
                                    $stmt->execute([$_SESSION['user_id']]);
                                    $notifications = $stmt->fetchAll();
                                }
                            } catch (Exception $e) {
                                // Silenciar el error
                                error_log("Error cargando notificaciones: " . $e->getMessage());
                            }
                            
                            if (empty($notifications)): ?>
                                <li><span class="dropdown-item-text text-muted">No hay notificaciones</span></li>
                            <?php else: ?>
                                <?php foreach ($notifications as $notification): ?>
                                    <li>
                                        <a class="dropdown-item notification-item <?php echo (!$notification['leido'] && isset($notification['leido'])) ? 'unread' : ''; ?>" 
                                           href="<?php echo isset($notification['url']) ? $notification['url'] : '#'; ?>">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><?php echo isset($notification['titulo']) ? htmlspecialchars($notification['titulo']) : 'Sin título'; ?></h6>
                                                <small class="notification-time">
                                                    <?php echo isset($notification['fecha_creacion']) ? time_elapsed_string($notification['fecha_creacion']) : ''; ?>
                                                </small>
                                            </div>
                                            <p class="mb-1"><?php echo isset($notification['mensaje']) ? htmlspecialchars($notification['mensaje']) : ''; ?></p>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center" href="<?php echo SITE_URL; ?>modules/notificaciones/">Ver todas las notificaciones</a></li>
                        </ul>
                    </div>
                    
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
            <?php 
            // Sistema de alertas mejorado
            if (isset($_SESSION['success'])): ?>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        let message = "<?php echo addslashes($_SESSION['success']); ?>";
                        showToast(message, 'success', 3000);
                    });
                </script>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        let message = "<?php echo addslashes($_SESSION['error']); ?>";
                        showToast(message, 'error', 4000);
                    });
                </script>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['warning'])): ?>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        let message = "<?php echo addslashes($_SESSION['warning']); ?>";
                        showToast(message, 'warning', 3500);
                    });
                </script>
                <?php unset($_SESSION['warning']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['info'])): ?>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        let message = "<?php echo addslashes($_SESSION['info']); ?>";
                        showToast(message, 'info', 3000);
                    });
                </script>
                <?php unset($_SESSION['info']); ?>
            <?php endif; ?>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<!-- Bootstrap 5 JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

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
    
    // SweetAlert2 para confirmar cierre de sesión
    document.getElementById('logoutBtn').addEventListener('click', function(e) {
        e.preventDefault();
        const logoutUrl = this.href;
        
        Swal.fire({
            title: '¿Cerrar sesión?',
            text: '¿Estás seguro de que deseas salir del sistema?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '<i class="fas fa-sign-out-alt"></i> Sí, cerrar sesión',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
            reverseButtons: true,
            customClass: {
                popup: 'animated bounceIn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = logoutUrl;
            }
        });
    });

    // Sistema de notificaciones - Marcar como leídas
    const notificationIcon = document.getElementById('notificationsDropdown');
    
    if (notificationIcon) {
        notificationIcon.addEventListener('click', function() {
            fetch("<?php echo SITE_URL; ?>modules/notificaciones/marcar_leidas.php", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const badge = notificationIcon.querySelector('.notification-badge');
                        if (badge) badge.remove();
                    }
                })
                .catch(error => console.error('Error al marcar notificaciones como leídas:', error));
        });
    }
    
    console.log('Sistema inicializado correctamente');
});

// =============================================
// FUNCIONES GLOBALES SWEETALERT2
// =============================================

/**
 * Muestra alerta toast en esquina superior derecha
 * Ideal para notificaciones de éxito, información, etc.
 */
function showSweetAlert(title, message, icon = 'success', confirmButtonText = 'OK') {
    return Swal.fire({
        title: title,
        text: message,
        icon: icon,
        position: 'top-end',
        toast: true,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        customClass: {
            popup: 'animated bounceInRight'
        },
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
}

/**
 * Función específica para notificaciones toast simples
 */
function showToast(title, icon = 'success', timer = 3000) {
    return Swal.fire({
        title: title,
        icon: icon,
        position: 'top-end',
        toast: true,
        showConfirmButton: false,
        timer: timer,
        timerProgressBar: true,
        customClass: {
            popup: 'animated bounceInRight'
        },
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
}

/**
 * Muestra confirmación centrada (para acciones importantes)
 */
function confirmSweetAlert(title, text, confirmButtonText = 'Sí', cancelButtonText = 'Cancelar') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText,
        reverseButtons: true,
        customClass: {
            popup: 'animated bounceIn'
        }
    });
}

/**
 * Muestra errores de validación centrados
 */
function showValidationErrors(errors) {
    let errorList = '';
    errors.forEach(error => {
        errorList += `<li>${error}</li>`;
    });
    
    Swal.fire({
        title: 'Errores en el formulario',
        html: `
            <div class="text-start">
                <p>Por favor, corrige los siguientes errores:</p>
                <ul>${errorList}</ul>
            </div>
        `,
        icon: 'error',
        confirmButtonText: 'Entendido',
        customClass: {
            popup: 'animated shake'
        }
    });
}
</script>

<!-- JavaScript adicional -->
<?php if (isset($js_extra)): ?>
    <?php echo $js_extra; ?>
<?php endif; ?>