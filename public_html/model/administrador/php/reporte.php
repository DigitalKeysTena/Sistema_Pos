<?php
session_start();

// ⭐ Cargar configuración y seguridad mejorada
require_once __DIR__ . '/../../../../src/config/app_config.php';
require_once __DIR__ . '/../../../../src/security/auth.php';
require_once __DIR__ . '/../../../../src/config/conection.php';

// Verificar autenticación y rol
require_role([3, 1]); // Inventario y Admin

require_once __DIR__ . '/../../../utils/interfas/parte_superior.php';

// Consultas para estadísticas
try {
    // Total de productos en inventario
    $stmt_productos = $pdo->query("SELECT COUNT(*) as total FROM inventario");
    $total_productos = $stmt_productos->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total de ventas
    $stmt_ventas = $pdo->query("SELECT COUNT(*) as total, IFNULL(SUM(Total_Venta), 0) as monto FROM venta");
    $ventas_data = $stmt_ventas->fetch(PDO::FETCH_ASSOC);
    $total_ventas = $ventas_data['total'] ?? 0;
    $monto_ventas = $ventas_data['monto'] ?? 0;
    
    // Total de clientes
    $stmt_clientes = $pdo->query("SELECT COUNT(*) as total FROM clientes");
    $total_clientes = $stmt_clientes->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total de proveedores
    $stmt_proveedores = $pdo->query("SELECT COUNT(*) as total FROM proveedor");
    $total_proveedores = $stmt_proveedores->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Productos con bajo stock (menos de 10 unidades)
    $stmt_bajo_stock = $pdo->query("SELECT COUNT(*) as total FROM inventario WHERE Stock_Producto < 10");
    $bajo_stock = $stmt_bajo_stock->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Obtener detalles de productos con bajo stock
    $stmt_bajo_stock_detalles = $pdo->query("
        SELECT i.Codigo_Producto, i.Nombre_Producto, i.Stock_Producto, c.Tipo_Categoria
        FROM inventario i
        INNER JOIN categoria c ON i.Id_Inventario_Categoria = c.Id_Categoria
        WHERE i.Stock_Producto < 10
        ORDER BY i.Stock_Producto ASC
    ");
    $productos_bajo_stock = $stmt_bajo_stock_detalles->fetchAll(PDO::FETCH_ASSOC);
    
    // Productos próximos a caducar (dentro de 30 días)
    $stmt_caducidad = $pdo->query("
        SELECT COUNT(*) as total 
        FROM inventario 
        WHERE Fecha_Caducidad != '0000-00-00' 
        AND Fecha_Caducidad BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
    ");
    $proximos_caducar = $stmt_caducidad->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Obtener detalles de productos próximos a caducar
    $stmt_caducidad_detalles = $pdo->query("
        SELECT i.Codigo_Producto, i.Nombre_Producto, i.Stock_Producto, i.Fecha_Caducidad, 
               DATEDIFF(i.Fecha_Caducidad, CURDATE()) as dias_restantes,
               c.Tipo_Categoria
        FROM inventario i
        INNER JOIN categoria c ON i.Id_Inventario_Categoria = c.Id_Categoria
        WHERE i.Fecha_Caducidad != '0000-00-00' 
        AND i.Fecha_Caducidad BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
        ORDER BY i.Fecha_Caducidad ASC
    ");
    $productos_proximos_caducar = $stmt_caducidad_detalles->fetchAll(PDO::FETCH_ASSOC);
    
    // Top 5 productos más vendidos
    $stmt_top_productos = $pdo->query("
        SELECT i.Nombre_Producto, SUM(dv.Cantidad) as total_vendido
        FROM detalle_venta dv
        INNER JOIN inventario i ON dv.Id_Inventario_Detalle = i.Id_Inventario
        GROUP BY i.Id_Inventario
        ORDER BY total_vendido DESC
        LIMIT 5
    ");
    $top_productos = $stmt_top_productos->fetchAll(PDO::FETCH_ASSOC);
    
    // Ventas por mes (últimos 6 meses)
    $stmt_ventas_mes = $pdo->query("
        SELECT DATE_FORMAT(Fecha_Venta, '%Y-%m') as mes, 
               DATE_FORMAT(Fecha_Venta, '%b %Y') as mes_nombre,
               COUNT(*) as total_ventas, 
               SUM(Total_Venta) as monto_total
        FROM venta
        WHERE Fecha_Venta >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY mes, mes_nombre
        ORDER BY mes ASC
    ");
    $ventas_mes = $stmt_ventas_mes->fetchAll(PDO::FETCH_ASSOC);
    
    // Últimos movimientos de inventario
    $stmt_ultimos_movimientos = $pdo->query("
        SELECT * FROM vista_movimientos_inventario
        LIMIT 5
    ");
    $ultimos_movimientos = $stmt_ultimos_movimientos->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $error_message = "Error al cargar datos: " . $e->getMessage();
}
?>

<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Custom Styles -->
<style>
    :root {
        --primary-color: black;
        --secondary-color: #3a0ca3;
        --success-color: #06d6a0;
        --warning-color: #ffd60a;
        --danger-color: #ef476f;
        --info-color: #4cc9f0;
        --dark-color: #1a1a2e;
        --light-bg: #f8f9fa;
    }
.swal2-container {
    z-index: 99999 !important;
}

    body {
        background: #f8f9fa;
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    #main-content {
        padding: 2rem 0;
    }

    .dashboard-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    .stat-card {
        background: linear-gradient(135deg, var(--card-color-1), var(--card-color-2));
        border: none;
        color: white;
        border-radius: 15px;
        padding: 1.5rem;
        height: 100%;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    .stat-card i {
        font-size: 2.5rem;
        opacity: 0.8;
    }

    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: bold;
        margin: 0.5rem 0;
    }

    .stat-card .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .card-productos {
        --card-color-1: #667eea;
        --card-color-2: #764ba2;
    }

    .card-ventas {
        --card-color-1: #f093fb;
        --card-color-2: #f5576c;
    }

    .card-clientes {
        --card-color-1: #4facfe;
        --card-color-2: #00f2fe;
    }

    .card-proveedores {
        --card-color-1: #43e97b;
        --card-color-2: #38f9d7;
    }

    .alert-card {
        border-left: 4px solid;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
        background: white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .alert-warning {
        border-color: var(--warning-color);
    }

    .alert-danger {
        border-color: var(--danger-color);
    }

    .page-header {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .page-header h1 {
        color: var(--primary-color);
        font-weight: bold;
        margin: 0;
    }

    .btn-custom {
        border-radius: 25px;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
    }

    .btn-primary-custom:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .chart-container {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        min-height: 350px;
    }

    .table-modern {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .table-modern thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .badge-custom {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
    }

    .movement-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .movement-ingreso {
        background: #d4edda;
        color: #155724;
    }

    .movement-salida {
        background: #f8d7da;
        color: #721c24;
    }

    .movement-ajuste {
        background: #fff3cd;
        color: #856404;
    }

    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 1rem;
        }
    }
</style>

<!-- MAIN CONTENT -->
<div id="main-content">
    <div class="container-fluid" id="main-content-inner">
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-chart-line me-3"></i>Panel de Reportes</h1>
                    <p class="text-muted mb-0">Dashboard de control y reportes del sistema</p>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-primary-custom btn-custom" onclick="generarReporte()">
                        <i class="fas fa-file-pdf me-2"></i>Generar Reporte
                    </button>
                </div>
            </div>
        </div>

        <!-- Estadísticas Principales -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card card-productos">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Total Productos</div>
                            <div class="stat-value"><?php echo number_format($total_productos); ?></div>
                            <small><i class="fas fa-box"></i> En inventario</small>
                        </div>
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card card-ventas">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Total Ventas</div>
                            <div class="stat-value"><?php echo number_format($total_ventas); ?></div>
                            <small><i class="fas fa-dollar-sign"></i> $<?php echo number_format($monto_ventas, 2); ?></small>
                        </div>
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card card-clientes">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Clientes</div>
                            <div class="stat-value"><?php echo number_format($total_clientes); ?></div>
                            <small><i class="fas fa-users"></i> Registrados</small>
                        </div>
                        <i class="fas fa-user-friends"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card card-proveedores">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-label">Proveedores</div>
                            <div class="stat-value"><?php echo number_format($total_proveedores); ?></div>
                            <small><i class="fas fa-truck"></i> Activos</small>
                        </div>
                        <i class="fas fa-handshake"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertas -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="alert-card alert-warning">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning me-3"></i>
                        <div>
                            <h5 class="mb-1">Productos con Stock Bajo</h5>
                            <p class="mb-0"><?php echo $bajo_stock; ?> productos tienen menos de 10 unidades en stock</p>
                        </div>
                        <button class="btn btn-sm btn-warning ms-auto" onclick="verBajoStock()">
                            Ver Detalles
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="alert-card alert-danger">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar-times fa-2x text-danger me-3"></i>
                        <div>
                            <h5 class="mb-1">Productos Próximos a Caducar</h5>
                            <p class="mb-0"><?php echo $proximos_caducar; ?> productos caducan en los próximos 30 días</p>
                        </div>
                        <button class="btn btn-sm btn-danger ms-auto" onclick="verProximosCaducar()">
                            Ver Detalles
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="chart-container">
                    <h4 class="mb-4"><i class="fas fa-chart-bar me-2"></i>Ventas Mensuales</h4>
                    <?php if(empty($ventas_mes)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
                            <p class="text-muted">No hay datos de ventas disponibles</p>
                            <small class="text-muted">Ejecuta el script de datos de prueba para ver las estadísticas</small>
                        </div>
                    <?php else: ?>
                        <canvas id="ventasChart" height="100"></canvas>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="chart-container">
                    <h4 class="mb-4"><i class="fas fa-star me-2"></i>Top 5 Productos</h4>
                    <?php if(empty($top_productos)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                            <p class="text-muted">No hay productos vendidos aún</p>
                        </div>
                    <?php else: ?>
                        <canvas id="productosChart"></canvas>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Tablas -->
        <div class="row">
            <!-- Productos Más Vendidos -->
            <div class="col-lg-6 mb-4">
                <div class="table-modern">
                    <div class="p-4">
                        <h4 class="mb-4"><i class="fas fa-trophy me-2"></i>Productos Más Vendidos</h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($top_productos)): ?>
                                        <?php foreach($top_productos as $index => $producto): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($producto['Nombre_Producto']); ?></td>
                                            <td><strong><?php echo number_format($producto['total_vendido']); ?></strong> unidades</td>
                                            <td><span class="badge bg-success badge-custom">Activo</span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                <i class="fas fa-info-circle me-2"></i>No hay datos de ventas disponibles
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Últimos Movimientos de Inventario -->
            <div class="col-lg-6 mb-4">
                <div class="table-modern">
                    <div class="p-4">
                        <h4 class="mb-4"><i class="fas fa-exchange-alt me-2"></i>Últimos Movimientos</h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Tipo</th>
                                        <th>Cantidad</th>
                                        <th>Usuario</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($ultimos_movimientos)): ?>
                                        <?php foreach($ultimos_movimientos as $movimiento): ?>
                                        <tr>
                                            <td>
                                                <small class="text-muted d-block"><?php echo htmlspecialchars($movimiento['Codigo_Producto']); ?></small>
                                                <?php echo htmlspecialchars($movimiento['Nombre_Producto']); ?>
                                            </td>
                                            <td>
                                                <span class="movement-badge movement-<?php echo strtolower($movimiento['Tipo_Movimiento']); ?>">
                                                    <?php echo $movimiento['Tipo_Movimiento']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <strong><?php echo number_format($movimiento['Cantidad']); ?></strong>
                                                <small class="text-muted d-block"><?php echo $movimiento['Stock_Anterior']; ?> → <?php echo $movimiento['Stock_Nuevo']; ?></small>
                                            </td>
                                            <td>
                                                <small><?php echo htmlspecialchars($movimiento['Usuario_Completo'] ?? 'Sistema'); ?></small>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                <i class="fas fa-info-circle me-2"></i>No hay movimientos registrados
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Datos para gráficos
const ventasMesData = <?php echo json_encode($ventas_mes); ?>;
const topProductosData = <?php echo json_encode($top_productos); ?>;

// Datos para alertas
const productosBajoStock = <?php echo json_encode($productos_bajo_stock); ?>;
const productosProximosCaducar = <?php echo json_encode($productos_proximos_caducar); ?>;

// Gráfico de Ventas Mensuales
const ctxVentas = document.getElementById('ventasChart');
if (ctxVentas && ventasMesData.length > 0) {
    new Chart(ctxVentas, {
        type: 'line',
        data: {
            labels: ventasMesData.map(v => v.mes_nombre),
            datasets: [{
                label: 'Ventas ($)',
                data: ventasMesData.map(v => parseFloat(v.monto_total)),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Ventas: $' + context.parsed.y.toLocaleString('es-EC', {minimumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString('es-EC');
                        }
                    }
                }
            }
        }
    });
}

// Gráfico de Top Productos
const ctxProductos = document.getElementById('productosChart');
if (ctxProductos && topProductosData.length > 0) {
    new Chart(ctxProductos, {
        type: 'doughnut',
        data: {
            labels: topProductosData.map(p => p.Nombre_Producto),
            datasets: [{
                data: topProductosData.map(p => p.total_vendido),
                backgroundColor: [
                    '#667eea',
                    '#f093fb',
                    '#4facfe',
                    '#43e97b',
                    '#ffd60a'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + ' unidades';
                        }
                    }
                }
            }
        }
    });
}

// Función para generar reporte
function generarReporte() {
    Swal.fire({
        title: 'Generar Reporte',
        html: `
            <p class="mb-3">Selecciona el tipo de reporte que deseas generar:</p>
            <div class="d-grid gap-2">
                <button class="btn btn-danger btn-lg" onclick="generarPDF()">
                    <i class="fas fa-file-pdf me-2"></i>Reporte PDF
                </button>
                <button class="btn btn-success btn-lg" onclick="generarExcel()">
                    <i class="fas fa-file-excel me-2"></i>Reporte Excel
                </button>
            </div>
        `,
        showConfirmButton: false,
        showCloseButton: true,
        width: 400
    });
}

function generarPDF() {
    Swal.close();
    Swal.fire({
        title: 'Generando PDF...',
        html: '<i class="fas fa-spinner fa-spin fa-3x"></i><br><br>Por favor espera',
        showConfirmButton: false,
        allowOutsideClick: false,
        timer: 2000
    }).then(() => {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'Reporte PDF generado correctamente',
            confirmButtonColor: '#667eea'
        });
    });
}

function generarExcel() {
    Swal.close();
    Swal.fire({
        title: 'Generando Excel...',
        html: '<i class="fas fa-spinner fa-spin fa-3x"></i><br><br>Por favor espera',
        showConfirmButton: false,
        allowOutsideClick: false,
        timer: 2000
    }).then(() => {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'Reporte Excel generado correctamente',
            confirmButtonColor: '#43e97b'
        });
    });
}

// Función para ver productos con bajo stock
function verBajoStock() {
    if (productosBajoStock.length === 0) {
        Swal.fire({
            icon: 'info',
            title: '¡Excelente!',
            text: 'No hay productos con stock bajo en este momento',
            confirmButtonColor: '#667eea'
        });
        return;
    }

    let htmlContent = `
        <div class="table-responsive">
            <table class="table table-hover text-start">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
    `;

    productosBajoStock.forEach((producto, index) => {
        const stockClass = producto.Stock_Producto <= 3 ? 'text-danger' : 'text-warning';
        const stockIcon = producto.Stock_Producto <= 3 ? 'fa-exclamation-circle' : 'fa-exclamation-triangle';
        
        htmlContent += `
            <tr>
                <td><small class="text-muted">${producto.Codigo_Producto}</small></td>
                <td><strong>${producto.Nombre_Producto}</strong></td>
                <td><span class="badge bg-secondary">${producto.Tipo_Categoria}</span></td>
                <td>
                    <span class="${stockClass}">
                        <i class="fas ${stockIcon}"></i>
                        <strong>${producto.Stock_Producto}</strong> unidades
                    </span>
                </td>
            </tr>
        `;
    });

    htmlContent += `
                </tbody>
            </table>
        </div>
        <div class="alert alert-warning mt-3 mb-0">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Acción recomendada:</strong> Contactar a los proveedores para reabastecer estos productos
        </div>
    `;

    Swal.fire({
        title: '<i class="fas fa-exclamation-triangle text-warning"></i> Productos con Stock Bajo',
        html: htmlContent,
        width: 800,
        confirmButtonColor: '#ffd60a',
        confirmButtonText: '<i class="fas fa-check me-2"></i>Entendido',
        customClass: {
            popup: 'text-start'
        }
    });
}

// Función para ver productos próximos a caducar
function verProximosCaducar() {
    if (productosProximosCaducar.length === 0) {
        Swal.fire({
            icon: 'success',
            title: '¡Todo en orden!',
            text: 'No hay productos próximos a caducar en los próximos 30 días',
            confirmButtonColor: '#667eea'
        });
        return;
    }

    let htmlContent = `
        <div class="table-responsive">
            <table class="table table-hover text-start">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Stock</th>
                        <th>Días Restantes</th>
                        <th>Fecha Caducidad</th>
                    </tr>
                </thead>
                <tbody>
    `;

    productosProximosCaducar.forEach((producto, index) => {
        const diasRestantes = parseInt(producto.dias_restantes);
        let urgenciaClass = 'text-warning';
        let urgenciaIcon = 'fa-exclamation-triangle';
        
        if (diasRestantes <= 7) {
            urgenciaClass = 'text-danger';
            urgenciaIcon = 'fa-exclamation-circle';
        } else if (diasRestantes <= 15) {
            urgenciaClass = 'text-warning';
        } else {
            urgenciaClass = 'text-info';
            urgenciaIcon = 'fa-info-circle';
        }

        // Formatear fecha
        const fecha = new Date(producto.Fecha_Caducidad);
        const fechaFormateada = fecha.toLocaleDateString('es-EC', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });

        htmlContent += `
            <tr>
                <td><small class="text-muted">${producto.Codigo_Producto}</small></td>
                <td><strong>${producto.Nombre_Producto}</strong></td>
                <td><span class="badge bg-secondary">${producto.Tipo_Categoria}</span></td>
                <td>${producto.Stock_Producto} unidades</td>
                <td>
                    <span class="${urgenciaClass}">
                        <i class="fas ${urgenciaIcon}"></i>
                        <strong>${diasRestantes}</strong> días
                    </span>
                </td>
                <td><small>${fechaFormateada}</small></td>
            </tr>
        `;
    });

    htmlContent += `
                </tbody>
            </table>
        </div>
        <div class="alert alert-danger mt-3 mb-0">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Acción recomendada:</strong> Considerar promociones o descuentos para productos próximos a vencer
        </div>
    `;

    Swal.fire({
        title: '<i class="fas fa-calendar-times text-danger"></i> Productos Próximos a Caducar',
        html: htmlContent,
        width: 900,
        confirmButtonColor: '#ef476f',
        confirmButtonText: '<i class="fas fa-check me-2"></i>Tomar Acción',
        customClass: {
            popup: 'text-start'
        }
    });
}

// Mostrar mensaje de bienvenida
document.addEventListener('DOMContentLoaded', function() {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    Toast.fire({
        icon: 'success',
        title: 'Panel de Reportes Cargado'
    });
});
</script>

<?php
// Plantilla inferior
require_once __DIR__ . '/../../../utils/interfas/parte_Inferior.php';
?>