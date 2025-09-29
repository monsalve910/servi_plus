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
    <header style="background-color: #008000;">
        <h1 class="text-center py-3" style:"#212529">LOGIN</h1>
    </header>
    
    <div class="login-wrapper">
        <div class="container text-dark-emphasis" style="background-color: #f8f9fa;">
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
                    <form id="formLogin" style="background-color: #f8f9fa;">
    <div class="mb-3">
        <label for="documento" class="form-label fw-bold">Documento</label>
        <input type="text" class="form-control" id="documento" name="documento" 
               placeholder="Ingresa tu Documento" required
               pattern="[0-9]+" title="Por favor ingresa solo números">
        <label for="pass" class="form-label mt-3 fw-bold">Contraseña</label>
        <input type="password" id="pass" name="pass" class="form-control mb-3" required>

        <button type="submit" class=" w-100 py-2" style="background-color: #28a745; color:#212529;">Iniciar Sesión</button>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelector('#formLogin').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('./controlador/validarlogin.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.text())
    .then(resp => {
        resp = resp.trim();
        if (resp === "ok") {
            Swal.fire({
                icon: 'success',
                title: 'Bienvenido',
                text: 'Inicio de sesión exitoso',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = "index.php";
            });
        } else if (resp === "error:password") {
            Swal.fire('Error', 'Contraseña incorrecta', 'error');
        } else if (resp === "error:usuario") {
            Swal.fire('Error', 'Usuario no encontrado', 'error');
        } else if (resp === "error:campos_vacios") {
            Swal.fire('Atención', 'Completa todos los campos', 'warning');
        } else {
            Swal.fire('Error', 'Error desconocido: ' + resp, 'error');
        }
    })
    .catch(() => {
        Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
    });
});
</script>

                </div>
            </div>
        </div>
    </div>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>