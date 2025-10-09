<?php
header('Content-Type: application/json; charset=utf-8');
function seleccionarDatos($idTabla){

    require_once './modelo/MYSQL.php';

    $mysql= new Mysql();
    $mysql->conectar();

    $consulta=$mysql->efectuarConsulta("SELECT * from empleados where id_empleado=$idTabla");
    $datoseditar=$consulta->fetch_assoc();

    echo json_encode($datoseditar);
    $mysql->desconectar();

}


// Determinar si se envio el formulario por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["IDempleado"]) && !empty($_POST["IDempleado"])) {
        // Capturar el ID
        $id = $_POST["IDempleado"];
        
        // Llamar a la funcion de seleccinar
        seleccionarDatos($id);
    }
}
?>