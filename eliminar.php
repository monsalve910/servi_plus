<?php

// Funcion para ejecutar el cambio de estado de un usuario
// Ya sea para eliminar (INACTIVO) o Reintegrar(ACTIVO)
function cambiarEstado($id, $estado)
{
    // Requerimos utilizar el modelo
    require_once './modelo/MYSQL.php';

    //Instancia del modelo
    $mysql = new MySQL();
    // Conexion con la base de datos
    $mysql->conectar();

    //Efectuar la consulta
    $cambiarEstado = $mysql->efectuarConsulta("UPDATE empleados set estado = '$estado' WHERE id_empleado = $id");

    // Determinar si eliminar o reintegrar empleado 
    if ($estado == "Activo") {
        $mensaje = "Empleado reintegrado nuevamente";
    } else {
        $mensaje =  "Empleado eliminado";
    }

    // En caso de que la consulta se realice correctmente envie un mensaje de confirmacion
    if ($cambiarEstado) {
        echo json_encode([
            "success" => true,
            "message" => $mensaje
        ]);
    } else {
        // Si no es porque ocurrio un error
        echo json_encode([
            "success" => false,
            "message" => "Ocurrio un error..."
        ]);
    }
    // Desconectamos la conexion
    $mysql->desconectar();
}


// Si el metodo de envio es POST ejecuta la accion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Confirmar de que el ID contenga un dato valido y que no este vacio
    if (isset($_POST["id_empleado"]) && !empty($_POST["id_empleado"])) {
        // Capturar el ID 
        $id = $_POST["id_empleado"];
        // Capturar el estado
        $estado = $_POST["estado"];

        // valida si se quiere eliminar o reintegrar un usuario
        if ($estado == "Activo") {
            cambiarEstado($id, "Inactivo");
        } else {
            cambiarEstado($id, "Activo");
        }
    }
}
