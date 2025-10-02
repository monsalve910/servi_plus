<?php
require_once './controlador/sesion.php';
verificarAcceso();
function permisoBadge($cond) {
    if ($cond) return '<span class="badge bg-primary text-white" aria-label="Sí" title="Sí">✓</span>';
    return '<span class="badge bg-light text-muted" aria-label="No" title="No">—</span>';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <title>Verificar Permisos</title>
    <style>
        /* Light, high-contrast theme for readability */
        body { background: #f8fafc; color: #0f1724; padding: 20px; font-size: 1.05rem; }
        .card-contrast { background: #ffffff; border: 1px solid rgba(15,23,36,0.06); }
        .muted { color: #475569; }
        .big { font-size: 1.14rem; }
        .perm-list .list-group-item { background: transparent; border: 0; padding-left: 0; }
        .perm-list li { display: flex; align-items: center; gap: 0.75rem; padding: 0.45rem 0; }
        .perm-key { min-width: 210px; color: #0f1724; font-weight: 600; }
        .perm-indicator .badge { font-size: 1rem; padding: .45rem .6rem; }
        a { color: #0f1724; }
    </style>
</head>
<body>
    <div class="container-fluid py-4" style="background-color: #008000;">
        <div class="row gy-3">
            <div class="col-12 col-md-6">
                <div class="card card-contrast p-4 shadow-sm" style="width:100%; background-color: #f8f9fa; color:#212529;">
                    <h2 class="mb-1">Verificar permisos</h2>
                    <p class="muted mb-3">Información del usuario autenticado</p>

                    <h5 class="mb-2">Usuario</h5>
                    <p class="mb-1 big"><strong>Nombre:</strong> <?php echo htmlspecialchars($_SESSION['nombre']); ?></p>
                    <p class="mb-1 big"><strong>Documento:</strong> <?php echo htmlspecialchars($_SESSION['documento']); ?></p>
                    <p class="mb-0"><strong>Rol:</strong>
                        <?php if ($_SESSION['rol'] == 1): ?>
                            <span class="badge ms-2" style="background-color: #007bff;">Administrador</span>
                        <?php else: ?>
                            <span class="badge ms-2" style="background-color: #007bff;">Usuario</span>
                        <?php endif; ?>
                    </p>

                    <hr>
                    <h5 class="mb-2">Permisos</h5>
                    <ul class="list-unstyled perm-list mb-3">
                        <li><span class="perm-key">Ver empleados</span><span class="perm-indicator"><?php echo permisoBadge(true); ?></span></li>
                        <li><span class="perm-key">Agregar empleados</span><span class="perm-indicator"><?php echo permisoBadge(esAdministrador()); ?></span></li>
                        <li><span class="perm-key">Editar empleados</span><span class="perm-indicator"><?php echo permisoBadge(esAdministrador()); ?></span></li>
                        <li><span class="perm-key">Eliminar empleados</span><span class="perm-indicator"><?php echo permisoBadge(esAdministrador()); ?></span></li>
                        <li><span class="perm-key">Gestionar roles</span><span class="perm-indicator"><?php echo permisoBadge(esAdministrador()); ?></span></li>
                    </ul>

                    <a href="index.php" class="btn btn-light text-white" style="background-color: #007bff;">Volver al inicio</a>
                    <?php if (esAdministrador()): ?>
                        <a href="gestion_roles.php" class="btn btn-light text-white" style="background-color: #007bff;">Gestionar Roles</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-12 col-md-6" style="background-color: #f8f9fa; color:#212529;">
                <div class="card card-contrast p-4 shadow-sm" style="width:100%; background-color: #f8f9fa; color:#212529;">
                    <h5 class="mb-2">Acciones disponibles</h5>
                    <?php if (esAdministrador()): ?>
                        <div class="alert alert-dark text-black p-3">
                            <p class="mb-1">Como <strong>Administrador</strong> podés:</p>
                            <ul class="mb-0">
                                <li>Agregar empleados (<a href="agregar.php">Ir</a>)</li>
                                <li>Gestionar roles (<a href="gestion_roles.php">Ir</a>)</li>
                                <li>Editar y eliminar empleados desde la lista</li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-secondary text-dark p-3">
                            <p class="mb-1">Como <strong>Usuario</strong> puedes:</p>
                            <ul class="mb-0">
                                <li>Ver la lista de empleados</li>
                                <li>Consultar tus permisos</li>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="mt-3 muted">Última sesión: <?php echo isset($_SESSION['ultimo_login']) ? htmlspecialchars($_SESSION['ultimo_login']) : '—'; ?></div>
                </div>
            </div>
        </div>
    </div>

    <script src="./assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
