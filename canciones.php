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
    <title>Canciones - Radio FM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="frontend/css/dashboard.css">
</head>
<body>
    <div class="d-flex">
        <?php include __DIR__ . '/backend/includes/sidebar.php'; ?>

        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-rock">
                <div class="container-fluid d-flex justify-content-between align-items-center">
                    <span class="navbar-brand mb-0 h4">&#127925; <span class="brand-accent">></span>CANCIONES</span>
                    <div class="d-flex align-items-center">
                        <span class="user-info me-3">&#128100; <?php echo strtoupper($usuario['nombre']); ?></span>
                        <a href="backend/logout.php" class="btn-logout-rock">Salir</a>
                    </div>
                </div>
            </nav>

            <div class="container-fluid px-4 py-4">
                <div class="d-flex justify-content-between mb-4">
                    <input type="text" id="input-busqueda" class="search-input-rock" placeholder="&#128269; Buscar por titulo, grupo o genero...">
                    <button class="btn-rock" onclick="abrirModal()">&#43; Nueva Cancion</button>
                </div>

                <div class="rock-card">
                    <div class="rock-card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-rock mb-0">
                                <thead>
                                    <tr>
                                        <th>Titulo</th>
                                        <th>Grupo</th>
                                        <th>Disco</th>
                                        <th>Duracion</th>
                                        <th>Genero</th>
                                        <th>Año</th>
                                        <th>Sencillo</th>
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

    <!-- Modal Cancion -->
    <div class="modal fade" id="modalCancion" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content modal-content-rock">
                <div class="modal-header modal-header-rock">
                    <h5 class="modal-title" id="modalTitulo">Gestionar Cancion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-rock">
                    <input type="hidden" id="can-id">
                    <div class="mb-3">
                        <label class="form-label-rock">Titulo *</label>
                        <input type="text" id="can-titulo" class="form-control form-control-dark" placeholder="Ej: Enter Sandman">
                    </div>
                    <div class="mb-3">
                        <label class="form-label-rock">Grupo *</label>
                        <select id="can-grupo_id" class="form-select form-control-dark">
                            <option value="">-- Seleccionar Grupo --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-rock">Disco</label>
                        <select id="can-disco_id" class="form-select form-control-dark">
                            <option value="">-- Sin disco (Sencillo) --</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label-rock">Duracion (segundos)</label>
                            <input type="number" id="can-duracion" class="form-control form-control-dark" placeholder="Ej: 301" min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label-rock">Genero</label>
                            <select id="can-genero" class="form-select form-control-dark">
                                <option value="">-- Seleccionar Genero --</option>
                                <option value="Rock Clasico">Rock Clasico</option>
                                <option value="Heavy Metal">Heavy Metal</option>
                                <option value="Thrash Metal">Thrash Metal</option>
                                <option value="Death Metal">Death Metal</option>
                                <option value="Black Metal">Black Metal</option>
                                <option value="Power Metal">Power Metal</option>
                                <option value="Doom Metal">Doom Metal</option>
                                <option value="Progressive Metal">Progressive Metal</option>
                                <option value="Hard Rock">Hard Rock</option>
                                <option value="Punk Rock">Punk Rock</option>
                                <option value="Hardcore Punk">Hardcore Punk</option>
                                <option value="Pop Punk">Pop Punk</option>
                                <option value="Grunge">Grunge</option>
                                <option value="Rock Alternativo">Rock Alternativo</option>
                                <option value="Indie Rock">Indie Rock</option>
                                <option value="Post Punk">Post Punk</option>
                                <option value="New Wave">New Wave</option>
                                <option value="Rock en Espanol">Rock en Espanol</option>
                                <option value="Rock Latino">Rock Latino</option>
                                <option value="Blues Rock">Blues Rock</option>
                                <option value="Psychedelic Rock">Psychedelic Rock</option>
                                <option value="Stoner Rock">Stoner Rock</option>
                                <option value="Nu Metal">Nu Metal</option>
                                <option value="Metalcore">Metalcore</option>
                                <option value="Gothic Rock">Gothic Rock</option>
                                <option value="Ska">Ska</option>
                                <option value="Reggae Rock">Reggae Rock</option>
                                <option value="Pop Rock">Pop Rock</option>
                                <option value="Folk Rock">Folk Rock</option>
                                <option value="Southern Rock">Southern Rock</option>
                                <option value="Glam Rock">Glam Rock</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label-rock">Año Lanzamiento</label>
                            <input type="number" id="can-ano" class="form-control form-control-dark" placeholder="Ej: 1991" min="1900" max="2030">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label-rock">Es Sencillo</label>
                            <select id="can-sencillo" class="form-select form-control-dark">
                                <option value="0">No</option>
                                <option value="1">Si</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer modal-footer-rock">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal" style="color:#aaa; border-color:#555;">Cancelar</button>
                    <button class="btn-rock" onclick="guardarCancion()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="frontend/js/canciones.js"></script>
</body>
</html>
