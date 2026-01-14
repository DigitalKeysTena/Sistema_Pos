<?php
session_start();

// ⭐ Cargar configuración y seguridad mejorada
require_once __DIR__ . '/../../../../src/config/app_config.php';
// Seguridad
require_once __DIR__ . '/../../../../src/security/auth.php';

// Verificar autenticación y rol
require_role([1]); // Solo ADMIN

// Plantilla superior
require_once __DIR__ . '/../../../utils/interfas/parte_superior.php';
?>

<!-- MAIN CONTENT -->
<div id="main-content"> 
    <div class="container mt-4" id="main-content-inner">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">
                            <i class="bi bi-speedometer2"></i> Panel de Administrador
                        </h3>
                    </div>
                    <div class="card-body">
                        <h5>Bienvenido, <?php echo htmlspecialchars($_SESSION['Nombre_Usuario'] ?? 'Administrador'); ?></h5>
                        <p class="text-muted">Sistema de gestión y control total</p>
                        
                        <div class="row mt-4">
                            <div class="col-md-4 mb-3">
                                <div class="card border-primary">
                                    <div class="card-body text-center">
                                        <i class="bi bi-people-fill fs-1 text-primary"></i>
                                        <h5 class="mt-3">Usuarios</h5>
                                        <p>Gestión de usuarios del sistema</p>
                                        <a href="#" class="btn btn-outline-primary">Administrar</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <i class="bi bi-box-seam fs-1 text-success"></i>
                                        <h5 class="mt-3">Inventario</h5>
                                        <p>Control total del inventario</p>
                                        <a href="../../inventario/php/inventario.php" class="btn btn-outline-success">Ver</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="card border-info">
                                    <div class="card-body text-center">
                                        <i class="bi bi-graph-up fs-1 text-info"></i>
                                        <h5 class="mt-3">Reportes</h5>
                                        <p>Estadísticas y análisis</p>
                                        <a href="./reporte.php" class="btn btn-outline-info">Ver</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../../../utils/interfas/parte_Inferior.php';
?>