<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN</title>
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css" />
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .login-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            max-width: 450px;
            width: 100%;
            margin: 0 auto;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        @media (max-width: 576px) {
            .container {
                padding: 1rem;
            }
            
            .login-wrapper {
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <header class="bg-success">
        <h1 class="text-center text-white py-3">LOGIN</h1>
    </header>
    
    <div class="login-wrapper">
        <div class="container bg-dark-subtle text-dark-emphasis">
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
            <div class="row justify-content-center">
                <div class="col-12">
                    <form method="post" action="./controlador/validarlogin.php">
                        <div class="mb-3">
                            <label for="documento" class="form-label fw-bold">Documento</label>
                            <input type="text" class="form-control" id="documento" name="documento" 
                                   placeholder="Ingresa tu Documento" required
                                   pattern="[0-9]+" title="Por favor ingresa solo números">
                            
                            <label for="pass" class="form-label mt-3 fw-bold">Contraseña</label>
                            <input type="password" id="pass" name="pass" class="form-control mb-3" 
                                   aria-describedby="passwordHelpBlock" required>
                            
                            <div id="passwordHelpBlock" class="form-text mb-3">
                                Tu contraseña debe contener letras y números.
                            </div>
                            
                            <button type="submit" class="btn btn-success w-100 py-2">Iniciar Sesión</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>