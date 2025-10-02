<?php
// Proteger la página - requiere autenticación y rol de administrador
require_once './controlador/sesion.php';
verificarAcceso();
requerirAdmin();

require_once './modelo/MYSQL.php';
$mysql = new MySQL();
$mysql->conectar();

$id = (int)$_GET["id"];

$resultado = $mysql->efectuarConsulta("UPDATE empleados SET estado = 0 WHERE id_empleado = $id");

if ($resultado) {
    echo "ok";
} else {
    echo "error al eliminar";
}

$mysql->desconectar();
exit();
