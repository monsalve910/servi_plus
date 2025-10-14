<?php
// controllers/EmpleadoController.php
require_once __DIR__ . '/../config/conexion.php';
class EmpleadoController {
public function obtenerEmpleados() {
$conexion = Conexion::conectar();
$consulta = "SELECT e.id_empleado, e.nombre_empleado, e.documento, c.cargo, d.departamento AS area, e.salario,e.estado FROM empleados e INNER JOIN cargo c ON e.cargo = c.id_cargo INNER JOIN departamento d ON e.area = d.id_departamento WHERE e.estado = 'Activo' ORDER BY id_empleado ASC";
$resultado = $conexion->query($consulta);
$empleados = [];

while ($fila = $resultado->fetch_assoc()) {
$empleados[] = $fila;
}

return $empleados;
}
public function obtenerEmpleadosPorDepartamento($idDepartamento) {
    $conexion = Conexion::conectar();
    $consulta = "SELECT e.id_empleado,e.nombre_empleado,e.documento,c.cargo,d.departamento AS area,e.salario,e.estado FROM empleados e INNER JOIN cargo c ON e.cargo = c.id_cargo INNER JOIN departamento d ON e.area = d.id_departamento WHERE d.id_departamento = $idDepartamento ORDER BY e.id_empleado ASC";
    $resultado = $conexion->query($consulta);
    $empleados = [];
    while ($fila = $resultado->fetch_assoc()) {
        $empleados[] = $fila;
    }
    return $empleados;
}
}


