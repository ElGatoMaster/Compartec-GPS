<?php
$link = include('php/conexion.php'); // Incluye el archivo de conexión y obtén la conexión

// Consulta a la base de datos
$consulta = "SELECT titulo_Pub, descrip_Pub FROM publicacion";
$registros = mysqli_query($link, $consulta); // Utiliza la conexión obtenida desde el archivo de conexión

// Verifica si la consulta se ejecutó correctamente
if (!$registros) {
  die('Error en la consulta: ' . mysqli_error($link));
}

// Cierra la conexión después de realizar la consulta
mysqli_close($link);

// Inicia la sesión después de cerrar la conexión
session_start();
// Verificar si ya hay una sesión activa
if (!isset($_SESSION["rol"])) {
  if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    // Redirigir al usuario a la página de inicio
    header("location: home.php");
    exit;
  }
} else {
  header("location: administracion/administrador/admin_home.php");
  exit;
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <title>ComparTec - SingIn</title>
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <script>
                window.onpopstate = function () {
                    window.location.href = "index.php";
                };
                // Añade un estado al historial
                history.pushState(null, null, index.php);
                window.onpopstate = function () {
                    history.go(1);
                };
    </script>

  <!--En esta seccion se incluyen las hojas de estilos-->
  <link rel="icon" href="images/icons/tigerF.png"><!--Esta seccion de codigo agrega un icono a la pagina-->
  
  <link rel="stylesheet" href="css/estilosSigning.css">
  <link rel="stylesheet" href="css/normalizar.css">
  <link rel="stylesheet" href="css/hover-min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  
  <!--Inicia Bootstrap-->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!--Termmina Bootstrap-->
  <script src="js/loading.js"></script>
</head>

<body>
  <script>
    var alertMessage = "<?php echo isset($_SESSION['alert_message']) ? $_SESSION['alert_message'] : ''; ?>";
    if (alertMessage) {
      // Creamos un elemento de alerta con Bootstrap y el mensaje proporcionado
      var alertElement;
      if ("<?php echo $_SESSION['alert_message']; ?>" == "Contraseña actualizada exitosamente, por favor inicie sesion para continuar") {

        alertElement = '<div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">' +
          '<svg class="bi flex-shrink-0 me-2" style="width: 1em; height: 1em;" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>' +
          '<div>' + alertMessage + '</div>' +
          '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
          '</div>';
      } else {
        alertElement = '<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">' +
          '<svg class="bi flex-shrink-0 me-2" style="width: 1em; height: 1em;" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>' +
          '<div>' + alertMessage + '</div>' +
          '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
          '</div>';
      }

      // Agregamos el elemento de alerta al final del cuerpo del documento
      document.body.insertAdjacentHTML('beforeend', alertElement);

      // Eliminamos el mensaje de alerta de la sesión para que no se muestre nuevamente
      <?php unset($_SESSION['alert_message']); ?>
    }
  </script>


  <div class="content">
    <main>
      <form id="login-form" action="administracion/login_controlador.php" method="post">
        <h1 class="h1 mb-3vmax fw-normal" id="textoHola">¡Hola, <br />de nuevo!<br /> </h1>
        <div class="form-floating">
          <input type="email" class="form-control" id="floatingInput" placeholder="email" name="email" id="validationCustom05" required>
          <label for="floatingInput" id="letraform" style="font-size: 15px;">Correo Institucional</label>

        </div>
        <div class="form-floating">
          <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="password" id="validationCustom07" required>
          <label for="floatingPassword" id="letraform" style="font-size: 15px;">Contraseña</label>
        </div>
        <div class="contenedor-boton">
          <a href="administracion/change_passw/send_code.php"><button type="button" class="btn btn-link mb-2vmax mt-2vmax" id="letraform" style="font-size: 13px;">
              Olvide mi Contraseña
            </button>
            <a href="administracion/register.php"><button type="button" class="btn btn-link mb-auto mt-auto" id="letraform" style="font-size: 13px;">
                ¿No tienes una cuenta? Registrate aqui.
              </button></a>
        </div>
        <!-- <button class="btn btn-primary w-100 mt-2" type="submit" id="letraform" style="--bs-btn-padding-y: 0.6rem;  --bs-btn-font-size:15px;" value="Login">Iniciar sesión</button>  -->
        <button type="button" class="btn btn-primary w-100 mt-2" id="login-btn" style="--bs-btn-padding-y: 0.6rem;  --bs-btn-font-size:15px;" value="Login">Iniciar sesión</button>
        <p class="mt-2vmax mb-auto text-body-secondary" id="letraform">&copy; ComparTec 2024</p>
      </form>
    </main>

    <div class="v-line"> </div>

    <img src="images/icons/flamita.png" alt="Logo ComparTec flamita" width="20%" height="39%" class="logoflama">

    <label class="Biblio">Compar</label>
    <label class="Tec">Tec</label>
    <label class="Bienvenido">Bienvenido a</label>
    <label class="Saber">Conectate con el saber...</label>
  </div>

  <script>
    // Obtener una referencia al botón
    var boton = document.getElementById('login-btn');

    // Agregar un evento de clic al botón
    boton.addEventListener('click', function() {});
    // Agregar un evento de teclado al documento
    document.addEventListener('keypress', function(event) {
      // Verificar si la tecla presionada es Enter (código 13)
      if (event.keyCode === 13) {
        // Simular un clic en el botón
        boton.click();
      }
    });
  </script>


</body>

</html>