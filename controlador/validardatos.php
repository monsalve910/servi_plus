<?php
class Validador
{
    public static function limpiar($dato)
    {
        return htmlspecialchars(strip_tags(trim($dato)));
    }
    public static function validarNombre($nombre)
    {
        $nombre = self::limpiar($nombre);
        if (empty($nombre)) {
            return "El nombre es obligatorio.";
        }
        if (strlen($nombre) < 3) {
            return "El nombre debe tener al menos 3 caracteres.";
        }
        return null;
    }
    public static function validarDocumento($documento)
    {
        $documento = self::limpiar($documento);
        if (empty($documento)) {
            return "El documento es obligatorio.";
        }
        if (!ctype_digit($documento)) {
            return "El documento debe contener solo números.";
        }
        return null;
    }
    public static function validarCorreo($correo)
    {
        $correo = self::limpiar($correo);
        if (empty($correo)) {
            return "El correo es obligatorio.";
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return "El correo no tiene un formato válido.";
        }
        return null;
    }
    public static function validarCargo($cargo)
    {
        $cargo = self::limpiar($cargo);
        if (empty($cargo)) {
            return "Debe seleccionar un cargo.";
        }
        return null;
    }
    public static function validarArea($area)
    {
        $area = self::limpiar($area);
        if (empty($area)) {
            return "Debe seleccionar un área.";
        }
        return null;
    }
    public static function validarFecha($fecha)
    {
        $fecha = self::limpiar($fecha);
        $fechaValida = DateTime::createFromFormat("Y-m-d", $fecha);
        if (!$fechaValida || $fechaValida->format("Y-m-d") !== $fecha) {
            return "La fecha no es válida (formato esperado YYYY-MM-DD).";
        }
        return null;
    }
    public static function validarSalario($salario)
    {
        $salario = self::limpiar($salario);
        if (!is_numeric($salario) || $salario <= 0) {
            return "El salario debe ser un número mayor a 0.";
        }
        return null;
    }
    public static function validarTelefono($telefono)
    {
        $telefono = self::limpiar($telefono);

        if (empty($telefono)) {
            return "El teléfono es obligatorio.";
        }
        
        // Permitir cualquier formato de teléfono, considerando solo los dígitos para la validación
        $telefonoLimpio = preg_replace('/[^0-9]/', '', $telefono);
        
        // Verificar si tiene suficientes dígitos (entre 7 y 15)
        if (strlen($telefonoLimpio) < 7 || strlen($telefonoLimpio) > 15) {
            return "El teléfono debe tener entre 7 y 15 dígitos (tiene " . strlen($telefonoLimpio) . ").";
        }
        
        return null;
    }
    public static function validarPassword($password)
    {
        // No aplicar limpiar() ya que podría modificar caracteres especiales válidos en contraseñas
        if (empty($password)) {
            return "La contraseña es obligatoria.";
        }
        
        $tieneLetras = preg_match('/[A-Za-z]/', $password);
        $tieneNumeros = preg_match('/[0-9]/', $password);
        
        // Información detallada sobre qué falta en la contraseña
        $problemas = [];
        if (!$tieneLetras) $problemas[] = "letras";
        if (!$tieneNumeros) $problemas[] = "números";
        
        if (!empty($problemas)) {
            return "La contraseña debe contener " . implode(" y ", $problemas) . ".";
        }
        
        if (strlen($password) < 6) {
            return "La contraseña debe tener al menos 6 caracteres (tiene " . strlen($password) . ").";
        }
        
        return null;
    }
}
