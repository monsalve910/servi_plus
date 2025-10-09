<?php
header('Content-Type: application/json');

try {
    // Incluir sesión y control de acceso
    require_once './controlador/sesion.php';
    verificarAcceso();
    requerirAdmin();

    // Conectar base de datos
    require_once './modelo/MYSQL.php';
    $mysql = new MySQL();
    $mysql->conectar();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        // Validar que todos los campos obligatorios estén presentes
        $campos = ["nombre","documento","cargo","area","fecha","salario","correo","pass","rol"];
        foreach ($campos as $campo) {
            if (!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
                echo json_encode([
                    "success" => false,
                    "message" => "El campo $campo es obligatorio"
                ]);
                exit();
            }
        }

        // Sanitización de los datos
        $nombre = filter_var(trim($_POST["nombre"]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $documento = trim($_POST["documento"]);
        $cargo = filter_var(trim($_POST["cargo"]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $area = filter_var(trim($_POST["area"]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $fecha = trim($_POST["fecha"]);
        $salario = filter_var(trim($_POST["salario"]), FILTER_VALIDATE_FLOAT);
        $correo = filter_var(trim($_POST["correo"]), FILTER_SANITIZE_EMAIL);
        $telefono = !empty($_POST["tel"]) ? filter_var(trim($_POST["tel"]), FILTER_SANITIZE_NUMBER_INT) : null;
        $password = trim($_POST["pass"]);
        $rol = trim($_POST["rol"]);

        // Validar documento numérico
        if (!ctype_digit($documento)) {
            echo json_encode(["success"=>false,"message"=>"Documento inválido"]);
            exit();
        }

        // Verificar duplicados en documento
        $docDup = $mysql->efectuarConsulta("SELECT 1 FROM empleados WHERE documento = '$documento'");
        if (mysqli_num_rows($docDup) > 0) {
            echo json_encode(["success"=>false,"message"=>"El documento ya está registrado"]);
            exit();
        }

        // Validar correo
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["success"=>false,"message"=>"Correo inválido"]);
            exit();
        }
        $correoDup = $mysql->efectuarConsulta("SELECT 1 FROM empleados WHERE correo = '$correo'");
        if (mysqli_num_rows($correoDup) > 0) {
            echo json_encode(["success"=>false,"message"=>"El correo ya está registrado"]);
            exit();
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $nombreFoto = null;

        // Subida de imagen
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $permitidos = ['image/jpg','image/jpeg','image/png'];
            $info = finfo_open(FILEINFO_MIME_TYPE);
            $tipoArchivo = finfo_file($info, $_FILES['foto']['tmp_name']);
            finfo_close($info);

            if (!in_array($tipoArchivo, $permitidos)) {
                echo json_encode(["success"=>false,"message"=>"Tipo de archivo no válido"]);
                exit();
            }

            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $nombreFoto = uniqid() . '.' . $ext;

            $carpeta = __DIR__ . '/assets/fotos_empleados/';
            if (!is_dir($carpeta)) mkdir($carpeta, 0777, true);

            $destino = $carpeta . $nombreFoto;

            if (!move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
                echo json_encode(["success"=>false,"message"=>"Error al guardar la imagen"]);
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
            echo json_encode(["success"=>true,"message"=>"Empleado agregado exitosamente"]);
        } else {
            echo json_encode(["success"=>false,"message"=>"Error al agregar empleado"]);
        }

        $mysql->desconectar();
    }

} catch (Throwable $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error del servidor: " . $e->getMessage()
    ]);
    exit;
}
?>


