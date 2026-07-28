<?php
session_start();

if (!isset($_SESSION['usuario_activo'])) {
    header('Location: index.php');
    exit();
}
$usuario = $_SESSION['usuario_activo'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reproducciones - Radio FM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="frontend/css/dashboard.css">
</head>
<body>
    <div class="d-flex">
        <?php include __DIR__ . '/backend/includes/sidebar.php'; ?>

        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-rock">
                <div class="container-fluid d-flex justify-content-between align-items-center">
                    <span class="navbar-brand mb-0 h4">&#128225; <span class="brand-accent">></span>REPRODUCCIONES</span>
                    <div class="d-flex align-items-center">
                        <span class="user-info me-3">&#128100; <?php echo strtoupper($usuario['nombre']); ?></span>
                        <a href="backend/logout.php" class="btn-logout-rock">Salir</a>
                    </div>
                </div>
            </nav>

            <div class="container-fluid px-4 py-4">
                <div class="d-flex justify-content-between mb-4">
                    <div class="d-flex gap-3 align-items-center">
                        <input type="date" id="filtro-fecha" class="form-control" style="width:auto; border-radius:10px;">
                        <input type="text" id="filtro-dj" class="search-input-rock" placeholder="&#127908; Buscar DJ..." style="width:220px;">
                        <input type="text" id="filtro-cancion" class="search-input-rock" placeholder="&#127925; Buscar cancion..." style="width:220px;">
                        <button class="btn-rock" id="btn-buscar" onclick="buscarReproducciones()">&#128269; Filtrar</button>
                        <button class="btn btn-sm btn-outline-secondary" id="btn-limpiar" onclick="limpiarFiltros()">Limpiar</button>
                    </div>
                    <button class="btn-rock" onclick="abrirModal()">&#43; Nueva Reproduccion</button>
                </div>

                <!-- Stats -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="card-body d-flex align-items-center">
                                <div class="stat-icon stat-icon-red me-3">&#128225;</div>
                                <div>
                                    <div class="stat-label">Total Reproducciones</div>
                                    <div class="stat-valor" id="stat-total">0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="card-body d-flex align-items-center">
                                <div class="stat-icon stat-icon-orange me-3">&#127908;</div>
                                <div>
                                    <div class="stat-label">DJs Activos Hoy</div>
                                    <div class="stat-valor" id="stat-djs">0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="card-body d-flex align-items-center">
                                <div class="stat-icon stat-icon-purple me-3">&#127925;</div>
                                <div>
                                    <div class="stat-label">Canciones Hoy</div>
                                    <div class="stat-valor" id="stat-canciones">0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="card-body d-flex align-items-center">
                                <div class="stat-icon stat-icon-yellow me-3">&#128337;</div>
                                <div>
                                    <div class="stat-label">Horas Emitidas</div>
                                    <div class="stat-valor" id="stat-horas">0h</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rock-card">
                    <div class="rock-card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-rock mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha/Hora</th>
                                        <th>Cancion</th>
                                        <th>Grupo</th>
                                        <th>Discjockey</th>
                                        <th>Duracion</th>
                                        <th>Observaciones</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="cuerpo-tabla"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Reproduccion -->
    <div class="modal fade" id="modalRepro" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content modal-content-rock">
                <div class="modal-header modal-header-rock">
                    <h5 class="modal-title" id="modalTitulo">Nueva Reproduccion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-rock">
                    <input type="hidden" id="rep-id">
                    <div class="mb-3">
                        <label class="form-label-rock">Cancion *</label>
                        <select id="rep-cancion_id" class="form-select form-control-dark">
                            <option value="">-- Seleccionar Cancion --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-rock">Discjockey *</label>
                        <select id="rep-dj_id" class="form-select form-control-dark">
                            <option value="">-- Seleccionar DJ --</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label-rock">Fecha/Hora</label>
                            <input type="datetime-local" id="rep-fecha" class="form-control form-control-dark">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label-rock">Duracion Real (seg)</label>
                            <input type="number" id="rep-duracion" class="form-control form-control-dark" placeholder="Ej: 300" min="1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-rock">Observaciones</label>
                        <input type="text" id="rep-obs" class="form-control form-control-dark" placeholder="Ej: Apertura de programa">
                    </div>
                </div>
                <div class="modal-footer modal-footer-rock">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal" style="color:#aaa; border-color:#555;">Cancelar</button>
                    <button class="btn-rock" onclick="guardarRepro()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="frontend/js/reproducciones.js"></script>
</body>
</html>
