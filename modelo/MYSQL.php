<?php
// Clase para gestionar la conexión a la base de datos
class MySQL {

    // Datos de conexión
    private $ipServidor = "localhost";
    private $usuarioBase = "root";
    private $contrasena = "";
    private $nombreBaseDatos = "gestion_empleados";

    private $conexion;

    // Método para conectar a la base de datos
    public function conectar() {
        $this->conexion = mysqli_connect($this->ipServidor, $this->usuarioBase, $this->contrasena, $this->nombreBaseDatos);

        // Validar si hubo un error en la conexión
        if (!$this->conexion) {
            die("Error al conectar a la base de datos: " . mysqli_connect_error());
        }

        // Establecer codificación utf8
        mysqli_set_charset($this->conexion, "utf8");
    }

    // Método para desconectar la base de datos
    public function desconectar() {
        if ($this->conexion) {
            mysqli_close($this->conexion);
        }
    }
    
    // Método para obtener la conexión (útil para funciones como mysqli_real_escape_string)
    public function getConnection() {
        return $this->conexion;
    }

    // Método para ejecutar una consulta y devolver su resultado
    public function efectuarConsulta($consulta) {
        // Verificar que la codificación sea utf8 antes de ejecutar
        mysqli_query($this->conexion, "SET NAMES 'utf8'");
        mysqli_query($this->conexion, "SET CHARACTER SET 'utf8'");

        $resultado = mysqli_query($this->conexion, $consulta);

        if (!$resultado) {
            $error = "Error en la consulta: " . mysqli_error($this->conexion) . "<br>";
            $error .= "Consulta ejecutada: " . htmlspecialchars($consulta) . "<br>";
            echo "<div style='background-color: #ffdddd; color: #ff0000; padding: 10px; margin: 10px 0; border: 1px solid #ff0000;'>" . $error . "</div>";
        }

        return $resultado;
    }
}
?>