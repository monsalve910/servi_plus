<?php
// archivado: el contenido original fue movido a archive/removed_php_20250917_0001/
// Si necesita restaurarlo, recupere el archivo desde la carpeta archive.
http_response_code(410); 
echo "Script archivado: ver archive/removed_php_20250917_0001/diagnostico_password.php";
?>

<?php
require_once './modelo/MYSQL.php';

// Protección básica - eliminar después de usar
$access_key = $_GET['key'] ?? '';
if ($access_key !== 'diagnostico_secreto') {
    echo "Acceso no autorizado";
    exit;
}

echo "<h1>Diagnóstico de Contraseñas</h1>";

$mysql = new MySQL();
$mysql->conectar();

// Verificar si existen contraseñas en la base de datos
$sql = "SELECT id_empleado, nombre_empleado, documento, pass FROM empleados";
$result = $mysql->efectuarConsulta($sql);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Documento</th><th>Tiene Contraseña</th><th>Es Hash Válido</th><th>Acciones</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id_empleado'] . "</td>";
        echo "<td>" . $row['nombre_empleado'] . "</td>";
        echo "<td>" . $row['documento'] . "</td>";
        
        $tiene_pass = isset($row['pass']) && !empty($row['pass']);
        echo "<td>" . ($tiene_pass ? "Sí" : "No") . "</td>";
        
        $es_hash_valido = $tiene_pass && (strpos($row['pass'], '$2y$') === 0 || strpos($row['pass'], '$2a$') === 0);
        echo "<td>" . ($es_hash_valido ? "Parece válido" : "No es un hash válido") . "</td>";
        
        echo "<td>";
        echo "<form method='post' action='diagnostico_password.php?key=diagnostico_secreto'>";
        echo "<input type='hidden' name='id_empleado' value='" . $row['id_empleado'] . "'>";
        echo "<input type='hidden' name='action' value='reset_password'>";
        echo "<input type='password' name='new_password' placeholder='Nueva contraseña'>";
        echo "<input type='submit' value='Actualizar contraseña'>";
        echo "</form>";
        echo "</td>";
        
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No se encontraron empleados en la base de datos.</p>";
}

// Procesar actualización de contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_password') {
    $id_empleado = $_POST['id_empleado'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    
    if (!empty($id_empleado) && !empty($new_password)) {
        // Generar hash de la nueva contraseña
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Actualizar en la base de datos
        $update_sql = "UPDATE empleados SET pass = '" . $password_hash . "' WHERE id_empleado = " . $id_empleado;
        $update_result = $mysql->efectuarConsulta($update_sql);
        
        if ($update_result) {
            echo "<p style='color: green;'>Contraseña actualizada con éxito para el empleado ID: " . $id_empleado . "</p>";
        } else {
            echo "<p style='color: red;'>Error al actualizar la contraseña: " . mysqli_error($mysql->getConnection()) . "</p>";
        }
    } else {
        echo "<p style='color: red;'>Faltan datos para actualizar la contraseña</p>";
    }
}

// Verificar una contraseña específica
echo "<h2>Verificar Contraseña</h2>";
echo "<form method='post' action='diagnostico_password.php?key=diagnostico_secreto'>";
echo "Documento: <input type='text' name='documento' required><br>";
echo "Contraseña: <input type='password' name='password' required><br>";
echo "<input type='hidden' name='action' value='verify_password'>";
echo "<input type='submit' value='Verificar Contraseña'>";
echo "</form>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'verify_password') {
    $documento = $_POST['documento'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($documento) && !empty($password)) {
        $verify_sql = "SELECT id_empleado, nombre_empleado, pass FROM empleados WHERE documento = '$documento' LIMIT 1";
        $verify_result = $mysql->efectuarConsulta($verify_sql);
        
        if ($verify_result && mysqli_num_rows($verify_result) > 0) {
            $user_data = mysqli_fetch_assoc($verify_result);
            
            if (isset($user_data['pass']) && !empty($user_data['pass'])) {
                $verify_result = password_verify($password, $user_data['pass']);
                
                echo "<p><strong>Verificación de contraseña para " . $user_data['nombre_empleado'] . ":</strong> ";
                if ($verify_result) {
                    echo "<span style='color: green;'>CORRECTA</span>";
                } else {
                    echo "<span style='color: red;'>INCORRECTA</span>";
                }
                echo "</p>";
                
                echo "<p><strong>Hash almacenado:</strong> " . $user_data['pass'] . "</p>";
            } else {
                echo "<p style='color: red;'>El usuario no tiene contraseña establecida</p>";
            }
        } else {
            echo "<p style='color: red;'>No se encontró ningún usuario con ese documento</p>";
        }
    }
}

$mysql->desconectar();
?>
