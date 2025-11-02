<?php
/**
 * Sistema de alertas con SweetAlert2
 * Compatible con el sistema existente
 */

// Alertas de éxito
if (isset($_SESSION['success'])): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let message = "<?php echo addslashes($_SESSION['success']); ?>";
        showToast(message, 'success', 3000);
    });
</script>
<?php 
unset($_SESSION['success']);
endif; 

// Alertas de error
if (isset($_SESSION['error'])): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let message = "<?php echo addslashes($_SESSION['error']); ?>";
        showToast(message, 'error', 4000);
    });
</script>
<?php 
unset($_SESSION['error']);
endif; 

// Alertas de login (especiales)
if (isset($_SESSION['login'])): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let message = "<?php echo addslashes($_SESSION['login']); ?>";
        Swal.fire({
            title: '¡Bienvenido!',
            text: message,
            icon: 'success',
            confirmButtonText: 'Continuar',
            customClass: {
                popup: 'animated bounceIn'
            }
        });
    });
</script>
<?php 
unset($_SESSION['login']);
endif; 

// Alertas de warning
if (isset($_SESSION['warning'])): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let message = "<?php echo addslashes($_SESSION['warning']); ?>";
        showToast(message, 'warning', 3500);
    });
</script>
<?php 
unset($_SESSION['warning']);
endif; 

// Alertas de info
if (isset($_SESSION['info'])): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let message = "<?php echo addslashes($_SESSION['info']); ?>";
        showToast(message, 'info', 3000);
    });
</script>
<?php 
unset($_SESSION['info']);
endif; 
?>