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
    <title>Discos - Radio FM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="frontend/css/dashboard.css">
</head>
<body>
    <div class="d-flex">
        <?php include __DIR__ . '/backend/includes/sidebar.php'; ?>

        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-rock">
                <div class="container-fluid d-flex justify-content-between align-items-center">
                    <span class="navbar-brand mb-0 h4">&#127926; <span class="brand-accent">></span>DISCOS</span>
                    <div class="d-flex align-items-center">
                        <span class="user-info me-3">&#128100; <?php echo strtoupper($usuario['nombre']); ?></span>
                        <a href="backend/logout.php" class="btn-logout-rock">Salir</a>
                    </div>
                </div>
            </nav>

            <div class="container-fluid px-4 py-4">
                <div class="d-flex justify-content-between mb-4">
                    <input type="text" id="input-busqueda" class="search-input-rock" placeholder="&#128269; Buscar por titulo o discografica...">
                    <button class="btn-rock" onclick="abrirModal()">&#43; Nuevo Disco</button>
                </div>

                <div class="rock-card">
                    <div class="rock-card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-rock mb-0">
                                <thead>
                                    <tr>
                                        <th>Titulo</th>
                                        <th>Grupo</th>
                                        <th>Año</th>
                                        <th>Discografica</th>
                                        <th>Canciones</th>
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

    <!-- Modal Disco -->
    <div class="modal fade" id="modalDisco" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content modal-content-rock">
                <div class="modal-header modal-header-rock">
                    <h5 class="modal-title" id="modalTitulo">Gestionar Disco</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-rock">
                    <input type="hidden" id="dis-id">
                    <div class="mb-3">
                        <label class="form-label-rock">Titulo del Disco *</label>
                        <input type="text" id="dis-titulo" class="form-control form-control-dark" placeholder="Ej: Master of Puppets">
                    </div>
                    <div class="mb-3">
                        <label class="form-label-rock">Grupo *</label>
                        <select id="dis-grupo_id" class="form-select form-control-dark">
                            <option value="">-- Seleccionar Grupo --</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label-rock">Año Lanzamiento</label>
                            <input type="number" id="dis-anio" class="form-control form-control-dark" placeholder="Ej: 1986" min="1900" max="2030">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label-rock">Num. Canciones</label>
                            <input type="number" id="dis-num_canciones" class="form-control form-control-dark" placeholder="Ej: 12" min="8">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-rock">Discografica</label>
                        <input type="text" id="dis-discografica" class="form-control form-control-dark" placeholder="Ej: EMI Records">
                    </div>
                </div>
                <div class="modal-footer modal-footer-rock">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal" style="color:#aaa; border-color:#555;">Cancelar</button>
                    <button class="btn-rock" onclick="guardarDisco()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="frontend/js/discos.js"></script>
</body>
</html>
