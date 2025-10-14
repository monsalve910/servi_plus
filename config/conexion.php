<?php
// config/conexion.php

// Se centraliza la conexión a la base de datos
class Conexion {
public static function conectar() {
$conn = new mysqli("localhost", "root", "", "gestion_empleados");
if ($conn->connect_error) {
die("Error de conexión: " . $conn->connect_error);
}
return $conn;
}
}