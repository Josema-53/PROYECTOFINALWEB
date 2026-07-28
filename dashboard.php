<?php
session_start();

if (!isset($_SESSION['usuario_activo'])) {
    header('Location: index.php');
    exit();
}
$usuario = $_SESSION['usuario_activo'];

require_once __DIR__ . '/backend/conexion.php';

$stmtDJs = $pdo->query("SELECT COUNT(*) FROM discjockey WHERE estado = 1");
$totalDJs = (int)$stmtDJs->fetchColumn();

$stmtGrupos = $pdo->query("SELECT COUNT(*) FROM grupo WHERE estado_activo = 1");
$totalGrupos = (int)$stmtGrupos->fetchColumn();

$stmtCanciones = $pdo->query("SELECT COUNT(*) FROM cancion");
$totalCanciones = (int)$stmtCanciones->fetchColumn();

$stmtReproducciones = $pdo->query("SELECT COUNT(*) FROM reproduccion");
$totalReproducciones = (int)$stmtReproducciones->fetchColumn();

$stmtUltimaRepro = $pdo->query("
    SELECT r.fecha_hora, c.titulo AS cancion, dj.nombre_artistico AS dj
    FROM reproduccion r
    INNER JOIN cancion c ON r.cancion_id = c.id
    INNER JOIN discjockey dj ON r.discjockey_id = dj.id
    ORDER BY r.fecha_hora DESC LIMIT 1
");
$ultimaRepro = $stmtUltimaRepro->fetch(PDO::FETCH_ASSOC);

$stmtMasRepro = $pdo->query("
    SELECT c.titulo, g.nombre_grupo, COUNT(*) AS veces
    FROM reproduccion r
    INNER JOIN cancion c ON r.cancion_id = c.id
    INNER JOIN grupo g ON c.grupo_id = g.id
    GROUP BY c.id, c.titulo, g.nombre_grupo
    ORDER BY veces DESC LIMIT 5
");
$masReproducidas = $stmtMasRepro->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Radio FM - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="frontend/css/dashboard.css">
</head>
<body>
    <div class="d-flex">
        <?php include __DIR__ . '/backend/includes/sidebar.php'; ?>

        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-rock">
                <div class="container-fluid d-flex justify-content-between align-items-center">
                    <span class="navbar-brand mb-0 h4">&#9889; <span class="brand-accent">></span>DASHBOARD</span>
                    <div class="d-flex align-items-center">
                        <span class="user-info me-3">
                            &#128100; <?php echo strtoupper($usuario['nombre']); ?>
                            <span class="user-role"><?php echo ucfirst($usuario['rol']); ?></span>
                        </span>
                        <a href="backend/logout.php" class="btn-logout-rock">Salir</a>
                    </div>
                </div>
            </nav>

            <div class="container-fluid px-4 py-4">

                <!-- Welcome -->
                <div class="dashboard-welcome mb-4">
                    <h2>&#9889; Bienvenido, <?php echo htmlspecialchars($usuario['nombre']); ?> &#127928;</h2>
                    <div class="welcome-subtitle">Panel de Control de la Emisora</div>
                    <p class="welcome-desc">Gestiona tus DJs, grupos, discos, canciones y reproducciones desde aqui.</p>
                </div>

                <!-- Stats -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="card-body d-flex align-items-center">
                                <div class="stat-icon stat-icon-red me-3">&#127908;</div>
                                <div>
                                    <div class="stat-label">Discjockeys</div>
                                    <div class="stat-valor"><?php echo $totalDJs; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="card-body d-flex align-items-center">
                                <div class="stat-icon stat-icon-orange me-3">&#127928;</div>
                                <div>
                                    <div class="stat-label">Grupos</div>
                                    <div class="stat-valor"><?php echo $totalGrupos; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="card-body d-flex align-items-center">
                                <div class="stat-icon stat-icon-purple me-3">&#127925;</div>
                                <div>
                                    <div class="stat-label">Canciones</div>
                                    <div class="stat-valor"><?php echo $totalCanciones; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="card-body d-flex align-items-center">
                                <div class="stat-icon stat-icon-yellow me-3">&#128225;</div>
                                <div>
                                    <div class="stat-label">Reproducciones</div>
                                    <div class="stat-valor"><?php echo $totalReproducciones; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Quick Access -->
                    <div class="col-md-6">
                        <div class="rock-card">
                            <div class="rock-card-header">
                                <h6 class="fw-bold mb-0" style="color: var(--rock-red); text-transform: uppercase; letter-spacing: 1px;">&#9889; Acceso Rapido</h6>
                            </div>
                            <div class="rock-card-body">
                                <div class="row g-3">
                                    <div class="col-4">
                                        <a href="discjockeys.php" class="quick-card">
                                            <span class="qc-icon">&#127908;</span>
                                            <div class="qc-title">DJs</div>
                                            <div class="qc-desc">Locutores</div>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="grupos.php" class="quick-card">
                                            <span class="qc-icon">&#127928;</span>
                                            <div class="qc-title">Grupos</div>
                                            <div class="qc-desc">Bandas</div>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="discos.php" class="quick-card">
                                            <span class="qc-icon">&#127926;</span>
                                            <div class="qc-title">Discos</div>
                                            <div class="qc-desc">Albumes</div>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="canciones.php" class="quick-card">
                                            <span class="qc-icon">&#127925;</span>
                                            <div class="qc-title">Canciones</div>
                                            <div class="qc-desc">Musica</div>
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <a href="reproducciones.php" class="quick-card">
                                            <span class="qc-icon">&#128225;</span>
                                            <div class="qc-title">On Air</div>
                                            <div class="qc-desc">Repros</div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Last Played & Top Songs -->
                    <div class="col-md-6">
                        <div class="rock-card mb-4">
                            <div class="rock-card-header">
                                <h6 class="fw-bold mb-0" style="color: var(--rock-orange); text-transform: uppercase; letter-spacing: 1px;">&#128225; Ultima Reproduccion</h6>
                            </div>
                            <div class="rock-card-body">
                                <?php if ($ultimaRepro): ?>
                                    <div class="d-flex align-items-center">
                                        <div class="on-air-badge me-3">
                                            <span class="on-air-dot"></span> ON AIR
                                        </div>
                                        <div>
                                            <div style="font-weight:700; color:#fff; font-size:1.1em;">
                                                <?php echo htmlspecialchars($ultimaRepro['cancion']); ?>
                                            </div>
                                            <div style="color: var(--rock-orange); font-size:0.9em;">
                                                <?php echo htmlspecialchars($ultimaRepro['dj']); ?>
                                            </div>
                                            <div style="color:#666; font-size:0.8em;">
                                                <?php echo date('d/m/Y H:i', strtotime($ultimaRepro['fecha_hora'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center text-muted py-3">No hay reproducciones aun</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="rock-card">
                            <div class="rock-card-header">
                                <h6 class="fw-bold mb-0" style="color: var(--rock-purple); text-transform: uppercase; letter-spacing: 1px;">&#11088; Top 5 Mas Reproducidas</h6>
                            </div>
                            <div class="rock-card-body p-0">
                                <table class="table table-rock mb-0">
                                    <tbody>
                                        <?php foreach ($masReproducidas as $i => $cancion): ?>
                                            <tr>
                                                <td style="width:40px; text-align:center; font-weight:800; color:var(--rock-red);">
                                                    #<?php echo $i + 1; ?>
                                                </td>
                                                <td>
                                                    <div style="font-weight:700; color:#fff;">
                                                        <?php echo htmlspecialchars($cancion['titulo']); ?>
                                                    </div>
                                                    <div style="font-size:0.8em; color:#888;">
                                                        <?php echo htmlspecialchars($cancion['nombre_grupo']); ?>
                                                    </div>
                                                </td>
                                                <td style="text-align:right;">
                                                    <span class="badge-rock-info">
                                                        <?php echo $cancion['veces']; ?> plays
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($masReproducidas)): ?>
                                            <tr><td colspan="3" class="text-center text-muted py-3">Sin datos</td></tr>
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
</body>
</html>
