<?php
$conexion = require "./controlador/conexionController.php"; // <-- así obtienes la conexión

// Consulta de departamentos (área)
$consultaArea = "SELECT d.departamento AS nombre, COUNT(*) AS total FROM empleados e INNER JOIN departamento d ON e.area = d.id_departamento WHERE e.estado = 1 GROUP BY d.departamento";
$resultArea = mysqli_query($conexion, $consultaArea);
$area = [];
while ($row = mysqli_fetch_assoc($resultArea)) {
    $area[] = $row;
}

// Consulta de cargos
$consultaCargo = "SELECT c.cargo AS nombre, COUNT(*) AS total FROM empleados e INNER JOIN cargo c ON e.cargo = c.id_cargo WHERE e.estado = 1 GROUP BY c.cargo";
$resultCargo = mysqli_query($conexion, $consultaCargo);
$cargo = [];
while ($row = mysqli_fetch_assoc($resultCargo)) {
    $cargo[] = $row;
}

header('Content-Type: application/json');
echo json_encode([
    "area" => $area,
    "cargo" => $cargo
]);
?>