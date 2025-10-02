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

/* // Verificar tiempo de inactividad (opcional)
function verificarTiempoSesion($minutos = 30) {
    if (isset($_SESSION['login_time'])) {
        // Si han pasado más de $minutos minutos, cerrar sesión
        if (time() - $_SESSION['login_time'] > $minutos * 60) {
            session_unset();
            session_destroy();
            header("Location: ./login.php?error=4");
            exit;
        } else {
            // Actualizar el tiempo de la última actividad
            $_SESSION['login_time'] = time();
        }
    }
} */

// Verificar si el usuario es administrador
function esAdministrador() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] == 1;
}

// Verificar si el usuario es administrador y redirigir si no lo es
function requerirAdmin() {
    if (!esAdministrador()) {
        header("Location: ./index.php?error=acceso_denegado");
        exit;
    }
}

// Cerrar la sesión
function cerrarSesion() {
    session_unset();
    session_destroy();
    header("Location: ./login.php");
    exit;
}
?>
