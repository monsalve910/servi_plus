<?php
require_once './controlador/sesion.php';
verificarAcceso();
if (!esAdministrador()) {
    header('Location: index.php?error=acceso_denegado');
    exit;
}
require_once './modelo/MYSQL.php';
require_once './FPDF/fpdf.php';
require_once './controlador/empleadocontroller.php';

$mysql = new MySQL();
$mysql->conectar();

// Si se solicita generar PDF general
if (isset($_GET['tipo']) && $_GET['tipo'] === 'general') {
    $controlador = new EmpleadoController();
    $empleados = $controlador->obtenerEmpleados();

    class PDF extends FPDF
    {
        function Header()
        {
            $this->SetFont('Arial', 'B', 14);
            $this->Cell(0, 10, 'Listado de Empleados', 0, 1, 'C');
            $this->Ln(5);
        }

        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Página ' . $this->PageNo(), 0, 0, 'C');
        }
    }
    $pdf = new PDF();
    $pdf->AddPage('L');
    $pdf->SetFont('Arial', 'B', 12);

    $pdf->Cell(10, 10, 'ID', 1);
    $pdf->Cell(60, 10, 'Nombre', 1);
    $pdf->Cell(35, 10, 'Documento', 1);
    $pdf->Cell(40, 10, 'Cargo', 1);
    $pdf->Cell(40, 10, 'Area', 1);
    $pdf->Cell(40, 10, 'Salario', 1);
    $pdf->Cell(40, 10, 'Estado', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 12);
    foreach ($empleados as $emp) {
        $pdf->Cell(10, 10, $emp['id_empleado'], 1);
        $pdf->Cell(60, 10, $emp['nombre_empleado'], 1);
        $pdf->Cell(35, 10, $emp['documento'], 1);
        $pdf->Cell(40, 10, $emp['cargo'], 1);
        $pdf->Cell(40, 10, $emp['area'], 1);
        $pdf->Cell(40, 10, $emp['salario'], 1);
        $pdf->Cell(40, 10, $emp['estado'], 1);
        $pdf->Ln();
    }
    $pdf->Output('I', 'Listado_Empleados.pdf');
}

$departamentos = $mysql->efectuarConsulta("SELECT id_departamento, departamento FROM departamento");
if (isset($_POST['tipo']) && $_POST['tipo'] === 'departamento' && isset($_POST['id_departamento'])) {
    $idDepto = intval($_POST['id_departamento']);
    $controlador = new EmpleadoController();

    // Método nuevo para obtener empleados por departamento
    $empleados = $controlador->obtenerEmpleadosPorDepartamento($idDepto);

    class PDF extends FPDF
    {
        function Header()
        {
            $this->SetFont('Arial', 'B', 14);
            $this->Cell(0, 10, 'Listado de Empleados', 0, 1, 'C');
            $this->Ln(5);
        }
        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Página ' . $this->PageNo(), 0, 0, 'C');
        }
    }

    $pdf = new PDF();
    $pdf->AddPage('L');
    $pdf->SetFont('Arial', 'B', 12);

    // Encabezados
    $pdf->Cell(20, 10, 'ID', 1);
    $pdf->Cell(60, 10, 'Nombre', 1);
    $pdf->Cell(35, 10, 'Documento', 1);
    $pdf->Cell(40, 10, 'Cargo', 1);
    $pdf->Cell(40, 10, 'Area', 1);
    $pdf->Cell(25, 10, 'Salario', 1);
    $pdf->Cell(25, 10, 'Estado', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 9);
    foreach ($empleados as $emp) {
        $pdf->Cell(20, 10, $emp['id_empleado'], 1);
        $pdf->Cell(60, 10, $emp['nombre_empleado'], 1);
        $pdf->Cell(35, 10, $emp['documento'], 1);
        $pdf->Cell(40, 10, $emp['cargo'], 1);
        $pdf->Cell(40, 10, $emp['area'], 1);
        $pdf->Cell(25, 10, $emp['salario'], 1);
        $pdf->Cell(25, 10, $emp['estado'], 1);
        $pdf->Ln();
    }

    $pdf->Output('I', 'Listado_Empleados_Departamento.pdf');
    exit;
}


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Reportes - ServiPlus</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/reportes.css">
</head>

<body class="p-4">
    <div class="container text-white py-4" style="background-color: #008000;">
        <h1 class="text-center mb-3">Generar Reportes (Administradores)</h1>
        <p class="text-center">Seleccione el tipo de reporte que desea generar.</p>

        <div class="mb-3 text-center">
            <a class="btn btn-primary btn-custom" href="?tipo=general">
                Reporte general de empleados activos (PDF)
            </a>
        </div>

        <div class="mb-3">
            <form method="post" class="row g-2 align-items-center justify-content-center text-center text-md-start">
                <input type="hidden" name="tipo" value="departamento">

                <div class="col-12 col-md-auto">
                    <label for="id_departamento" class="form-label mb-0">Departamento:</label>
                </div>

                <div class="col-12 col-md-4">
                    <select name="id_departamento" id="id_departamento" class="form-select" required>
                        <option value="">-- Seleccione --</option>
                        <?php while ($d = mysqli_fetch_assoc($departamentos)): ?>
                            <option value="<?php echo $d['id_departamento']; ?>">
                                <?php echo htmlspecialchars($d['departamento']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-12 col-md-auto">
                    <button class="btn btn-secondary btn-custom text-white" type="submit" style="background-color: #007bff;">
                        Generar PDF por departamento
                    </button>
                </div>
            </form>
        </div>

        <div class="text-center mt-4">
            <a href="index.php" class="btn fw-bold text-white btn-custom" style="background-color: #007bff;">
                Volver
            </a>
        </div>
    </div>

    <script src="./assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>