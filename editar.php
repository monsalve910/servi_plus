<?php
header('Content-Type: application/json');
// Incluir sesión y control de acceso
require_once './controlador/sesion.php';
verificarAcceso();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (
        isset($_POST["nombre"]) && !empty($_POST["nombre"])
        &&  isset($_POST["documento"]) && !empty($_POST["documento"])
        &&  isset($_POST["cargo"]) && !empty($_POST["cargo"])
        &&  isset($_POST["area"]) && !empty($_POST["area"])
        &&  isset($_POST["fecha"]) && !empty($_POST["fecha"])
        &&  isset($_POST["salario"]) && !empty($_POST["salario"])
        &&  isset($_POST["correo"]) && !empty($_POST["correo"])
        &&  isset($_POST["tel"]) && !empty($_POST["tel"])
        &&  isset($_POST["rol"]) && !empty($_POST["rol"])
    ) {
        require_once './modelo/MYSQL.php';
        $mysql = new MySQL();
        $mysql->conectar();

        $id=$_POST["IDempleado"];

        // Sanitización de los datos
        $nombre = filter_var(trim($_POST["nombre"]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $documento = trim($_POST["documento"]);
        $cargo = filter_var(trim($_POST["cargo"]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $area = filter_var(trim($_POST["area"]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $fecha = trim($_POST["fecha"]);
        $salario = filter_var(trim($_POST["salario"]), FILTER_VALIDATE_FLOAT);
        $correo = filter_var(trim($_POST["correo"]), FILTER_SANITIZE_EMAIL);
        $telefono = !empty($_POST["tel"]) ? filter_var(trim($_POST["tel"]), FILTER_SANITIZE_NUMBER_INT) : null;
        //$password = trim($_POST["pass"]);
        $rol = trim($_POST["rol"]);

        if (!ctype_digit($documento)) {
            echo json_encode([
                "success" => false,
                "message" => "Documento inválido"
            ]);
            exit();
        }
        // Verificar que no exista el documento en la BD
        $verificacionEmail = $mysql->efectuarConsulta("SELECT 1 FROM empleados WHERE documento = '$documento' AND id_empleado != $id");

        if (mysqli_num_rows($verificacionEmail) > 0) {
            echo json_encode([
                "success" => false,
                "message" => "Error el documento ya se encuentra registrado"
            ]);
            exit();
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                "success" => false,
                "message" => "Correo inválido"
            ]);
            exit();
        }
        // Verificar que no exista el correo en la BD
        $verificacionEmail = $mysql->efectuarConsulta("SELECT 1 FROM empleados WHERE correo = '$correo' AND id_empleado != $id");

        if (mysqli_num_rows($verificacionEmail) > 0) {
            echo json_encode([
                "success" => false,
                "message" => "Error el correo ya se encuentra registrado"
            ]);
            exit();
        }
        // Consulta para traer la contraseña actual de la BD
        $passwordBD = $mysql->efectuarConsulta("SELECT pass FROM empleados WHERE id_empleado = $id");
        $passwordBD = $passwordBD->fetch_assoc()["pass"];

        // Verificar si el usuario ingresó una nueva contraseña
        if (isset($_POST["newPass"]) && !empty($_POST["newPass"])) {
            // Encriptar la nueva contraseña
            $newPassword = password_hash($_POST["newPass"], PASSWORD_DEFAULT);
        } else {
            // Si no ingresó nada, mantener la contraseña actual
            $newPassword = $passwordBD;
        }


        // Se obtiene el nombre actual de la foto desde la BD
        $consultaFoto = $mysql->efectuarConsulta("SELECT foto_empleado FROM empleados WHERE id_empleado = $id");
        $fotoActual = $consultaFoto->fetch_assoc()["foto_empleado"];

        // Variable que guardará el nombre de la foto final
        $nombreFoto = $fotoActual;

        // Subida de imagen (solo si el usuario seleccionó un archivo)
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {

            $permitidos = ['image/jpg', 'image/jpeg', 'image/png'];
            $info = finfo_open(FILEINFO_MIME_TYPE);
            $tipoArchivo = finfo_file($info, $_FILES['foto']['tmp_name']);
            finfo_close($info);

            if (!in_array($tipoArchivo, $permitidos)) {
                echo json_encode([
                    "success" => false,
                    "message" => "Tipo de archivo no válido"
                ]);
                exit();
            }

            // Generar un nombre único para evitar colisiones
            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $nombreFoto = uniqid() . '.' . $ext;

            $carpeta = __DIR__ . '/assets/fotos_empleados/';
            if (!is_dir($carpeta)) mkdir($carpeta, 0777, true);

            $destino = $carpeta . $nombreFoto;

            // Subir nueva foto
            if (!move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
                echo json_encode([
                    "success" => false,
                    "message" => "Error al guardar la imagen"
                ]);
                exit();
            }

            // Eliminar la foto anterior (si existe y no es la misma)
            if (!empty($fotoActual) && file_exists($carpeta . $fotoActual)) {
                unlink($carpeta . $fotoActual);
            }
        }

        $actualizar = $mysql->efectuarConsulta("UPDATE empleados SET nombre_empleado='$nombre',documento='$documento',cargo='$cargo',area='$area',fecha_ingreso='$fecha',salario='$salario',correo='$correo',telefono='$telefono',pass='$newPassword',foto_empleado='$nombreFoto',rol='$rol' WHERE id_empleado='$id'");

        //si la consulta se ejecuta se envia en JSON para la alerta de confirmacion
        if($actualizar){
            echo json_encode([
                "success"=>true,
                "message"=>"Usuario Editado Exitosamente"
            ]);
        }else{
            echo json_encode([
                "success"=>false,
                "message"=>"Error al Editar"
            ]);
        }
        $mysql->desconectar();
    }
}
