<?php
$pagina_actual = basename($_SERVER['PHP_SELF']);
?>
<nav id="sidebar" class="d-flex flex-column p-3 text-white">
    <div class="text-center mb-4 mt-2">
        <div class="sidebar-logo">
            <span class="logo-icon">&#9889;</span>
            <h3 class="fw-bold m-0 text-light">RADIO FM</h3>
        </div>
        <small class="sidebar-subtitle">SISTEMA DE GESTION MUSICAL</small>
    </div>
    <hr class="sidebar-divider">

    <ul class="nav nav-pills flex-column mb-auto mt-2">
        <li class="nav-item mb-2">
            <a href="dashboard.php"
                class="nav-link text-white fw-semibold menu-item <?php echo $pagina_actual === 'dashboard.php' ? 'active-menu' : ''; ?>">
                &#9889; INICIO
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="discjockeys.php"
                class="nav-link text-white fw-semibold menu-item <?php echo $pagina_actual === 'discjockeys.php' ? 'active-menu' : ''; ?>">
                &#127908; DISCJOCKEYS
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="grupos.php"
                class="nav-link text-white fw-semibold menu-item <?php echo $pagina_actual === 'grupos.php' ? 'active-menu' : ''; ?>">
                &#127928; GRUPOS
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="discos.php"
                class="nav-link text-white fw-semibold menu-item <?php echo $pagina_actual === 'discos.php' ? 'active-menu' : ''; ?>">
                &#127926; DISCOS
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="canciones.php"
                class="nav-link text-white fw-semibold menu-item <?php echo $pagina_actual === 'canciones.php' ? 'active-menu' : ''; ?>">
                &#127925; CANCIONES
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="reproducciones.php"
                class="nav-link text-white fw-semibold menu-item <?php echo $pagina_actual === 'reproducciones.php' ? 'active-menu' : ''; ?>">
                &#128225; REPRODUCCIONES
            </a>
        </li>
    </ul>

    <hr class="sidebar-divider">
    <div class="text-center pb-2">
        <small class="text-white-50">Rock Radio FM v1.0 &copy; 2026</small>
    </div>
</nav>
