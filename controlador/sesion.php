<?php
// Este archivo debe ser incluido al inicio de cada página que requiera autenticación
session_start();

// Verificar si el usuario está autenticado
function verificarAcceso() {
    if (!isset($_SESSION['acceso']) || $_SESSION['acceso'] !== true) {
        // Redirigir al login con mensaje de error
        header("Location: ./login.php?error=3");
        exit;
    }
}

// Verificar si el usuario es administrador
function esAdministrador() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] == 1;
}


// Cerrar la sesión
function cerrarSesion() {
    session_unset();
    session_destroy();
    header("Location: ./login.php");
    exit;
}
?>
