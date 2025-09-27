<?php
session_start();
require_once '../modelo/MYSQL.php';

// Activar registro de errores en archivo
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '../debug_login.log');

// Función para depurar
function debug_to_file($message, $data = null) {
    $output = date('Y-m-d H:i:s') . " - " . $message;
    if ($data !== null) {
        $output .= " - " . print_r($data, true);
    }
    file_put_contents('../debug_login.log', $output . "\n", FILE_APPEND);
}

debug_to_file("Inicio de proceso de login");

// Limpiamos y validamos las entradas
$documento = isset($_POST['documento']) ? trim($_POST['documento']) : '';
$pass = isset($_POST['pass']) ? $_POST['pass'] : '';

debug_to_file("Datos recibidos", ['documento' => $documento, 'pass_length' => strlen($pass)]);

// Validar que no estén vacíos
if (empty($documento) || empty($pass)) {
    debug_to_file("Campos vacíos detectados");
    header("Location: ../login.php?error=2"); // Error: campos vacíos
    exit;
}

$mysql = new MySQL();
$mysql->conectar();

// Usamos una consulta preparada (simulada) para prevenir inyección SQL
$documento = mysqli_real_escape_string($mysql->getConnection(), $documento);

    $sql = "SELECT id_empleado, nombre_empleado, documento, pass, rol 
        FROM empleados 
        WHERE documento = '$documento' 
        LIMIT 1";debug_to_file("SQL ejecutado", $sql);

$result = $mysql->efectuarConsulta($sql);

// Verificar si hay resultados
if ($result) {
    $num_rows = mysqli_num_rows($result);
    debug_to_file("Resultados encontrados", $num_rows);
    
    if ($num_rows > 0) {
        $fila = mysqli_fetch_assoc($result);
        debug_to_file("Datos del usuario", ['id' => $fila['id_empleado'], 'nombre' => $fila['nombre_empleado'], 'has_pass' => isset($fila['pass']), 'pass_empty' => empty($fila['pass'])]);
        
        // Ver primeros caracteres del hash almacenado (para depuración)
        if (isset($fila['pass']) && !empty($fila['pass'])) {
            debug_to_file("Hash almacenado (primeros 20 caracteres)", substr($fila['pass'], 0, 20));
        }
        
        // Verificar la contraseña
        if (isset($fila['pass']) && !empty($fila['pass'])) {
            $verify_result = password_verify($pass, $fila['pass']);
            debug_to_file("Resultado de password_verify", $verify_result ? "CORRECTO" : "INCORRECTO");
            
            if ($verify_result) {
                // Login exitoso
                $_SESSION['user_id'] = $fila['id_empleado'];
                $_SESSION['nombre'] = $fila['nombre_empleado'];
                $_SESSION['documento'] = $fila['documento'];
                $_SESSION['rol'] = $fila['rol'];
                $_SESSION['acceso'] = true;
                $_SESSION['login_time'] = time();

                debug_to_file("Login exitoso - sesión iniciada", $_SESSION);
                
                // Usar ruta relativa para el redirect
                header("Location: ../index.php");
                exit;
            } else {
                debug_to_file("Verificación de contraseña falló");
                header("Location: ../login.php?error=1&reason=password"); // Contraseña incorrecta
                exit;
            }
        } else {
            debug_to_file("El usuario no tiene contraseña establecida");
            header("Location: ../login.php?error=1&reason=nopass"); // No hay contraseña almacenada
            exit;
        }
    } else {
        debug_to_file("No se encontró ningún usuario con ese documento");
        header("Location: ../login.php?error=1&reason=nodoc"); // Usuario no encontrado
        exit;
    }
} else {
    debug_to_file("Error en la consulta SQL", mysqli_error($mysql->getConnection()));
    header("Location: ../login.php?error=1&reason=sqlerror"); // Error en la consulta
    exit;
}

$mysql->desconectar();
debug_to_file("Fin del proceso de login - sin resultados positivos");

?>



