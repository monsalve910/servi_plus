<?php
require_once './controlador/sesion.php';
verificarAcceso();
if (!esAdministrador()) {
    header('Location: index.php?error=acceso_denegado');
    exit;
}
require_once './modelo/MYSQL.php';
// Asegurar que FPDF busque las fuentes en la carpeta /font del proyecto (no en modelo/font)
if (!defined('FPDF_FONTPATH')) {
    define('FPDF_FONTPATH', __DIR__ . '/font/');
}
require_once './modelo/fpdf.php';

$mysql = new MySQL();
$mysql->conectar();

// Si se solicita generar PDF general
if (isset($_GET['tipo']) && $_GET['tipo'] === 'general') {
    $sql = "SELECT e.nombre_empleado, e.documento, c.cargo, d.departamento, e.salario, e.fecha_ingreso, e.estado FROM empleados e INNER JOIN cargo c ON e.cargo = c.id_cargo INNER JOIN departamento d ON e.area = d.id_departamento WHERE e.estado = 'Activo'";
    $res = $mysql->efectuarConsulta($sql);

    $pdf = new FPDF('L','mm','A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,10,'Reporte general de empleados activos',0,1,'C');
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(0,8,'Fecha: '.date('Y-m-d H:i:s'),0,1,'R');
    $pdf->Ln(4);

    // Cabecera tabla
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(60,8,'Nombre',1);
    $pdf->Cell(35,8,'Documento',1);
    $pdf->Cell(45,8,'Cargo',1);
    $pdf->Cell(45,8,'Departamento',1);
    $pdf->Cell(25,8,'Salario',1);
    $pdf->Cell(35,8,'Fecha ingreso',1);
    $pdf->Cell(25,8,'Estado',1);
    $pdf->Ln();

    $pdf->SetFont('Arial','',9);
    while ($row = mysqli_fetch_assoc($res)) {
        $pdf->Cell(60,7,utf8_decode($row['nombre_empleado']),1);
        $pdf->Cell(35,7,$row['documento'],1);
        $pdf->Cell(45,7,utf8_decode($row['cargo']),1);
        $pdf->Cell(45,7,utf8_decode($row['departamento']),1);
        $pdf->Cell(25,7,$row['salario'],1,0,'R');
        $pdf->Cell(35,7,$row['fecha_ingreso'],1);
        $pdf->Cell(25,7,$row['estado'],1);
        $pdf->Ln();
    }

    $pdf->Output('I','reporte_general_empleados.pdf');
    exit;
}

// Si se solicita generar PDF por departamento
if (isset($_GET['tipo']) && $_GET['tipo'] === 'departamento' && isset($_GET['id_departamento'])) {
    $id = $_GET['id_departamento'];
    $sql = "SELECT e.nombre_empleado, e.documento, c.cargo, d.departamento, e.salario, e.fecha_ingreso, e.estado FROM empleados e INNER JOIN cargo c ON e.cargo = c.id_cargo INNER JOIN departamento d ON e.area = d.id_departamento WHERE d.id_departamento = $id";
    $res = $mysql->efectuarConsulta($sql);

    // Obtener nombre del departamento
    $drow = mysqli_fetch_assoc($mysql->efectuarConsulta("SELECT departamento FROM departamento WHERE id_departamento = $id"));
    $nombreDept = $drow ? $drow['departamento'] : 'Desconocido';

    $pdf = new FPDF('L','mm','A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,10,'Reporte de empleados - Departamento: '.utf8_decode($nombreDept),0,1,'C');
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(0,8,'Fecha: '.date('Y-m-d H:i:s'),0,1,'R');
    $pdf->Ln(4);

    // Cabecera tabla
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(60,8,'Nombre',1);
    $pdf->Cell(35,8,'Documento',1);
    $pdf->Cell(45,8,'Cargo',1);
    $pdf->Cell(45,8,'Departamento',1);
    $pdf->Cell(25,8,'Salario',1);
    $pdf->Cell(35,8,'Fecha ingreso',1);
    $pdf->Cell(25,8,'Estado',1);
    $pdf->Ln();

    $pdf->SetFont('Arial','',9);
    while ($row = mysqli_fetch_assoc($res)) {
        $pdf->Cell(60,7,utf8_decode($row['nombre_empleado']),1);
        $pdf->Cell(35,7,$row['documento'],1);
        $pdf->Cell(45,7,utf8_decode($row['cargo']),1);
        $pdf->Cell(45,7,utf8_decode($row['departamento']),1);
        $pdf->Cell(25,7,$row['salario'],1,0,'R');
        $pdf->Cell(35,7,$row['fecha_ingreso'],1);
        $pdf->Cell(25,7,$row['estado'],1);
        $pdf->Ln();
    }

    $pdf->Output('I','reporte_departamento_'.$id.'.pdf');
    exit;
}

// Página HTML: formulario para generar reportes
$departamentos = $mysql->efectuarConsulta("SELECT id_departamento, departamento FROM departamento ORDER BY departamento");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reportes - ServiPlus</title>
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
</head>
<body class="p-4">
    <div class="container text-white" style="background-color: #008000;">
        <h1>Generar Reportes (Administradores)</h1>
        <p>Seleccione el tipo de reporte que desea generar.</p>
        <div class="mb-3">
            <a class="btn btn-primary" href="?tipo=general">Reporte general de empleados activos (PDF)</a>
        </div>
        <div class="mb-3">
            <form method="get" class="d-flex gap-2 align-items-center">
                <input type="hidden" name="tipo" value="departamento">
                <label for="id_departamento">Departamento:</label>
                <select name="id_departamento" id="id_departamento" class="form-select ms-2" required>
                    <option value="">-- Seleccione --</option>
                    <?php while($d = mysqli_fetch_assoc($departamentos)): ?>
                        <option value="<?php echo $d['id_departamento']; ?>"><?php echo htmlspecialchars($d['departamento']); ?></option>
                    <?php endwhile; ?>
                </select>
                <button class="btn btn-secondary" type="submit" style="background-color: #007bff;">Generar PDF por departamento</button>
            </form>
        </div>
        <a href="index.php" class="text-decoration-none btn fw-bold text-white" style="background-color: #007bff;">Volver</a>
    </div>
</body>
</html>
