LOGIN PHP

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN</title>
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css" />
</head>

<body>
    <header class="bg-success">
        <h1 class="text-center text-white">LOGIN</h1>
    </header>
    <div class="container mt-5 bg-dark-subtle text-dark-emphasis">
        <?php 
        if (isset($_GET['error'])) {
            $reason = $_GET['reason'] ?? '';
            switch ($_GET['error']) {
                case 1:
                    switch ($reason) {
                        case 'password':
                            $mensaje = "La contraseña ingresada no coincide.";
                            break;
                        case 'nopass':
                            $mensaje = "El usuario no tiene contraseña establecida.";
                            break;
                        case 'nodoc':
                            $mensaje = "No se encontró ningún usuario con ese documento.";
                            break;
                        case 'sqlerror':
                            $mensaje = "Error al conectar con la base de datos.";
                            break;
                        default:
                            $mensaje = "Credenciales incorrectas. Intenta de nuevo.";
                    }
                    break;
                case 2:
                    $mensaje = "Por favor, completa todos los campos.";
                    break;
                case 3:
                    $mensaje = "No tienes permiso para acceder. Inicia sesión primero.";
                    break;
                default:
                    $mensaje = "Ha ocurrido un error. Por favor intenta de nuevo.";
            }
        ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php } ?>
        <div class="row">
            <form method="post" action="./controlador/validarlogin.php">
                <div class="mb-3">
                    <label for="documento" class="form-label">Documento</label>
                    <input type="text" class="form-control" id="documento" name="documento" 
                           placeholder="Ingresa tu Documento" required
                           pattern="[0-9]+" title="Por favor ingresa solo números">
                    
                    <label for="pass" class="form-label mt-3">Contraseña</label>
                    <input type="password" id="pass" name="pass" class="form-control mb-3" 
                           aria-describedby="passwordHelpBlock" required>
                    
                    <div id="passwordHelpBlock" class="form-text mb-3">
                        Tu contraseña debe contener letras y números.
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                </div>
        </div>
        </form>

    </div>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>