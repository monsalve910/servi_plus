<?php
$carpeta = __DIR__ . '/ASSETS/fotos_empleados/';
echo "Ruta absoluta: $carpeta <br>";
echo "is_dir: " . (is_dir($carpeta) ? "SI" : "NO") . "<br>";
echo "is_writable: " . (is_writable($carpeta) ? "SI" : "NO") . "<br>";
