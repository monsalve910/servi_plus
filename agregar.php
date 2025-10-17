<?php
header('Content-Type: application/json');
// Incluir sesión y control de acceso
require_once './controlador/sesion.php';
verificarAcceso();

// Conectar base de datos
require_once './modelo/MYSQL.php';
$mysql = new MySQL();
$mysql->conectar();

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
        &&  isset($_POST["pass"]) && !empty($_POST["pass"])
        &&  isset($_POST["rol"]) && !empty($_POST["rol"])
    ) {

        // Sanitización de los datos
        $nombre = filter_var(trim($_POST["nombre"]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $documento = trim($_POST["documento"]);
        $cargo = filter_var(trim($_POST["cargo"]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $area = filter_var(trim($_POST["area"]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $fecha = trim($_POST["fecha"]);
        $salario = filter_var(trim($_POST["salario"]), FILTER_SANITIZE_NUMBER_FLOAT);
        $correo = filter_var(trim($_POST["correo"]), FILTER_SANITIZE_EMAIL);
        $telefono = !empty($_POST["tel"]) ? filter_var(trim($_POST["tel"]), FILTER_SANITIZE_NUMBER_INT) : null;
        $password = trim($_POST["pass"]);
        $rol = trim($_POST["rol"]);

        // Validar documento numérico
        if (!ctype_digit($documento)) {
            echo json_encode([
                "success" => false,
                "message" => "Documento inválido"
            ]);
            exit();
        }
         if (!filter_var($salario, FILTER_VALIDATE_FLOAT)) {
            echo json_encode([
                "success" => false,
                "message" => "Salario inválido"
            ]);
            exit();
        }

        // Verificar duplicados en documento
        $docDup = $mysql->efectuarConsulta("SELECT 1 FROM empleados WHERE documento = '$documento'");
        if (mysqli_num_rows($docDup) > 0) {
            echo json_encode([
                "success" => false,
                "message" => "El documento ya está registrado"
            ]);
            exit();
        }

        // Validar correo
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                "success" => false,
                "message" => "Correo inválido"
            ]);
            exit();
        }
        $correoDup = $mysql->efectuarConsulta("SELECT 1 FROM empleados WHERE correo = '$correo'");
        if (mysqli_num_rows($correoDup) > 0) {
            echo json_encode([
                "success" => false,
                "message" => "El correo ya está registrado"
            ]);
            exit();
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $nombreFoto = null;

        // Subida de imagen
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

            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $nombreFoto = uniqid() . '.' . $ext;

            $carpeta = __DIR__ . '/assets/fotos_empleados/';
            if (!is_dir($carpeta)) mkdir($carpeta, 0777, true);

            $destino = $carpeta . $nombreFoto;

            if (!move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
                echo json_encode([
                    "success" => false,
                    "message" => "Error al guardar la imagen"
                ]);
                exit();
            }
        }

        // Insertar en la base de datos
        $consulta = $mysql->efectuarConsulta("
            INSERT INTO empleados 
            (nombre_empleado, documento, cargo, area, fecha_ingreso, salario, correo, telefono, pass, foto_empleado, rol, estado) 
            VALUES 
            ('$nombre','$documento','$cargo','$area','$fecha','$salario','$correo','$telefono','$passwordHash','$nombreFoto','$rol','Activo')
        ");

        if ($consulta) {
            echo json_encode([
                "success" => true,
                "message" => "Empleado agregado exitosamente"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Error al agregar empleado"
            ]);
        }


        $mysql->desconectar();
    }
}
