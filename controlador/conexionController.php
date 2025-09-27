<?php
// Controlador para la conexión a la base de datos
$servidor = "localhost";
$usuario = "root";
$password = "";
$baseDatos = "gestion_empleados";

// Crear la conexión
$conexion = mysqli_connect($servidor, $usuario, $password, $baseDatos);

// Verificar la conexión
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Establecer codificación utf8
mysqli_set_charset($conexion, "utf8");

// Devolvemos la variable $conexion para que pueda ser utilizada por otros scripts
return $conexion;
?>