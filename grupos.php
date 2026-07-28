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
    <title>Grupos - Radio FM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="frontend/css/dashboard.css">
</head>
<body>
    <div class="d-flex">
        <?php include __DIR__ . '/backend/includes/sidebar.php'; ?>

        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-rock">
                <div class="container-fluid d-flex justify-content-between align-items-center">
                    <span class="navbar-brand mb-0 h4">&#127928; <span class="brand-accent">></span>GRUPOS</span>
                    <div class="d-flex align-items-center">
                        <span class="user-info me-3">&#128100; <?php echo strtoupper($usuario['nombre']); ?></span>
                        <a href="backend/logout.php" class="btn-logout-rock">Salir</a>
                    </div>
                </div>
            </nav>

            <div class="container-fluid px-4 py-4">
                <div class="d-flex justify-content-between mb-4">
                    <input type="text" id="input-busqueda" class="search-input-rock" placeholder="&#128269; Buscar por nombre, pais o genero...">
                    <button class="btn-rock" onclick="abrirModal()">&#43; Nuevo Grupo</button>
                </div>

                <div class="rock-card">
                    <div class="rock-card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-rock mb-0">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Pais</th>
                                        <th>Año Formacion</th>
                                        <th>Genero</th>
                                        <th>Integrantes</th>
                                        <th>Estado</th>
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

    <!-- Modal Grupo -->
    <div class="modal fade" id="modalGrupo" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content modal-content-rock">
                <div class="modal-header modal-header-rock">
                    <h5 class="modal-title" id="modalTitulo">Gestionar Grupo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-rock">
                    <input type="hidden" id="gr-id">
                    <div class="mb-3">
                        <label class="form-label-rock">Nombre del Grupo *</label>
                        <input type="text" id="gr-nombre" class="form-control form-control-dark" placeholder="Ej: Metallica">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label-rock">Pais de Origen</label>
                            <select id="gr-pais" class="form-select form-control-dark" onchange="togglePaisOtro()">
                                <option value="">-- Seleccionar Pais --</option>
                                <option value="Estados Unidos">Estados Unidos</option>
                                <option value="Reino Unido">Reino Unido</option>
                                <option value="Argentina">Argentina</option>
                                <option value="Mexico">Mexico</option>
                                <option value="Espana">Espana</option>
                                <option value="Colombia">Colombia</option>
                                <option value="Chile">Chile</option>
                                <option value="Peru">Peru</option>
                                <option value="Venezuela">Venezuela</option>
                                <option value="Brasil">Brasil</option>
                                <option value="Alemania">Alemania</option>
                                <option value="Francia">Francia</option>
                                <option value="Italia">Italia</option>
                                <option value="Suecia">Suecia</option>
                                <option value="Finlandia">Finlandia</option>
                                <option value="Noruega">Noruega</option>
                                <option value="Canada">Canada</option>
                                <option value="Australia">Australia</option>
                                <option value="Japon">Japon</option>
                                <option value="Irlanda">Irlanda</option>
                                <option value="Paises Bajos">Paises Bajos</option>
                                <option value="__otro__">Otro (especificar)</option>
                            </select>
                            <input type="text" id="gr-pais-otro" class="form-control form-control-dark mt-2" style="display:none;" placeholder="Escribe el pais...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label-rock">Año Formacion</label>
                            <input type="number" id="gr-anio" class="form-control form-control-dark" placeholder="Ej: 1981" min="1900" max="2030">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label-rock">Genero Musical</label>
                            <select id="gr-genero" class="form-select form-control-dark">
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
                        <div class="col-md-6 mb-3">
                            <label class="form-label-rock">Integrantes</label>
                            <input type="number" id="gr-integrantes" class="form-control form-control-dark" placeholder="Ej: 4" min="1" max="20">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-rock">Biografia</label>
                        <textarea id="gr-biografia" class="form-control form-control-dark" rows="3" placeholder="Breve historia del grupo..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-rock">Estado</label>
                        <select id="gr-estado" class="form-select form-control-dark">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer modal-footer-rock">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal" style="color:#aaa; border-color:#555;">Cancelar</button>
                    <button class="btn-rock" onclick="guardarGrupo()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="frontend/js/grupos.js"></script>
</body>
</html>
