<?php
$link = include('../../php/conexion.php'); // Incluye el archivo de conexión y obtén la conexión
session_start();
if(!isset($_SESSION['correo_ch'])){
  $_SESSION['error_form'] = 'Error: Por favor, confirme el reenvío del formulario desde el mismo navegador.';
  header("Location: send_code.php");
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ComparTec - Home</title>
  <!--En esta seccion se incluyen las hojas de estilos-->
  <link rel="icon" href="../../images/icons/tigerF.png"><!--Esta seccion de codigo agrega un icono a la pagina-->
  <link rel="stylesheet" href="../../css/normalizar.css">
  <link rel="stylesheet" href="../../css/estilos.css">
  <link rel="stylesheet" href="../../css/hover-min.css">
  <link rel="stylesheet" href="../../css/animate.css">
  <link rel="stylesheet" href="../../css/sidebars.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <!--Inicia Bootstrap-->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!--Termmina Bootstrap-->
  <!--iconos-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,1,0" />
  <style>
    /* Estilo para centrar los campos de texto */
    .btn-custom {
      border-radius: 25px;
    }

    /* Estilo para el formulario */
    .card {
      border: none;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .form-control:focus {
      border-color: #80bdff;
      box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
    }

    /* Estilo para el contenedor principal */
    body {
      background-color: #f8f9fa;
    }

    /* Estilo para el contenedor del formulario */
    .container-form {
      margin-top: 80px;
    }
  </style>
</head>

<body>
  <!-- Encabezado -->
  <header class="bg-primary py-2">
    <div class="container d-flex align-items-center">
      <!-- Logo y título -->
      <div class="logo">
        <img src="../../images/icons/flamita.png" alt="Logo T - ComparTec" class="img-fluid mr-2">
        <h4 class="mb-0"><b><span class="col-1">Compar</span><span class="col-2">Tec</span></h4>
      </div>
    </div>
  </header>

  <div class="container container-form">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <!-- Formulario -->
        <div class="card p-4">
          <!-- Título del formulario -->
          <h2 class="text-center mb-4">Cambio de contraseña</h2>
          <form action="update_pass.php" method="post">
            <!-- Campo para la nueva contraseña -->
            <div class="mb-3">
              <label for="newPassword" class="form-label">Contraseña nueva</label>
              <input type="password" class="form-control" id="newPassword" placeholder="Ingrese su nueva contraseña">
              <div id="passwordRequirementsError" class="text-danger"></div>
            </div>
            <!-- Campo para confirmar la contraseña -->
            <div class="mb-3">
              <label for="confirmPassword" class="form-label">Confirmar contraseña</label>
              <input type="password" class="form-control" id="confirmPassword" name="newPassword" placeholder="Confirme su nueva contraseña" disabled>
              <div id="passwordError" class="text-danger"></div>
              <input type="hidden" name="email" value="<?php echo $_SESSION['correo_ch']; ?>">
            </div>
            <!-- Botón para enviar el formulario -->
            <button type="submit" class="btn btn-primary btn-lg btn-custom w-100" id="submitButton" disabled>Aceptar</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script src="../../js/validatepass.js"></script>
</body>

</html>