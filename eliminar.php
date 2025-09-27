<?php
// Proteger la página - requiere autenticación y rol de administrador
require_once './controlador/sesion.php';
verificarAcceso();
requerirAdmin();

// Conexión a la base de datos
require_once './modelo/MYSQL.php';

$mysql = new MySQL();
$mysql->conectar();

// Verificar que se recibió un ID válido
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php?error=id_invalido");
    exit();
}

$id = (int)$_GET["id"];

// Verificar que el empleado existe antes de eliminarlo
$check_sql = "SELECT nombre_empleado FROM empleados WHERE id_empleado = $id AND estado = 1";
$check_result = $mysql->efectuarConsulta($check_sql);

if ($check_result && mysqli_num_rows($check_result) > 0) {
    // Eliminar (marcar como inactivo)
    $resultado = $mysql->efectuarConsulta("UPDATE empleados SET estado = 0 WHERE id_empleado = $id");

    if ($resultado) {
        header("Location: index.php?msg=eliminado");
    } else {
        header("Location: index.php?error=eliminar_fallido");
    }
} else {
    header("Location: index.php?error=empleado_no_encontrado");
}

$mysql->desconectar();
exit();
?>