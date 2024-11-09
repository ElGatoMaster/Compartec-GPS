<?php
$link = include('../php/conexion.php'); // Incluye el archivo de conexión y obtén la conexión
// Cierra la conexión después de realizar la consulta
mysqli_close($link);

// Inicia la sesión después de cerrar la conexión
session_start();
?>
<!doctype html>
<html lang="es">
  <head>
    <!--Required Meta tags-->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!--Bootstrap CSS-->
    <link rel="icon" href="../images/icons/tigerF.png">
    <link rel="stylesheet" href="../css/estiloReg.css">
    <link rel="stylesheet" href="../css/normalizar.css">
    <link rel="stylesheet" href="../css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <title>ComparTec-Registro</title>
  </head>
  <body>
  <header class="bg-primary py-1">
    <div class="container">
      <!-- Logo y título -->
      <div class="logo">
        <img src="../images/icons/flamita.png" alt="Logo T - ComparTec" class="img-fluid mr-2">
        <h4 class="mb-0"><b><span class="col-1">Compar</span><span class="col-2">Tec</span></h4>
      </div>
    </div>
  </header>
  <div class="row mt-5" >
    <div class="container-fl ">
        <header class="text-center form">
            <h1 id="encabezado">Registrarse</h1>
        </header>
    </div>
  </div>
    <section class="container my-2 w-50 text-ligth p-2" id="campos">
        <form class="row g-3 py-2 need-validation" action="registro_controlador.php" method="post">
            <div class="col-6">
              <label for="inputAddress validationCustom01" class="form-label">Nombre(s)</label>
              <input type="text" class="form-control" id="inputAddress validationCustom01" name="nombre"required>
            </div>
            <div class="col-6">
                <label for="inputAddress" class="form-label">Apellidos</label>
                <input type="text" class="form-control" id="inputAddress" name="apellidos" required>
              </div>

            <div class="col-md-6 ">
              <label for="inputEmail4" class="form-label">Correo</label>
              <input type="email" class="form-control" id="inputEmail4 validationCustom01" placeholder="@ittepic.edu.mx" name="correo" required>
              
            </div>
            <div class="col-md-6">
              <label for="inputPassword4" class="form-label">Contraseña</label>
              <input type="password" class="form-control" id="inputPassword4 validationCustom01" name="contra" required>
              <div id="passwordHelpBlock" class="form-text lh-1" style="font-size: 1vmax">
              Debe contener al menos una letra mayúscula, una letra minúscula y un número, y 8 carácteres
              </div>
            </div>
            <div class="col-md-6">
                <label for="inputState validationCustom01" class="form-label">Carrera</label>
                <select id="inputState validationCustom01" class="form-select" name="carrera" required>
                  <option selected>Seleccionar</option>
                  <option>Arquitectura</option>
                  <option>Ing. Bioquimica</option>
                  <option>Ing. Civil</option>
                  <option>Ing. Electrica</option>
                  <option>Ing. Gestion Empresarial</option>
                  <option>Ing. Sistemas Computacionales</option>
                  <option>Ing. Industrial</option>
                  <option>Ing. Mecatronica</option>
                  <option>Ing. Quimica</option>
                  <option>Lic. Administracion</option>
                </select>
              </div>  

            <div class="col-md-6">
              <label for="inputPassword4" class="form-label">Confirmar Contraseña</label>
              <input type="password" class="form-control" id="inputPassword4 validationCustom01" name="repContra" required>
            </div>
            
            
              <div class="col-md-6 ">
                <label for="inputNumber validationCustom01" class="form-label">Semestre</label>
                <select id="inputNumber validationCustom01" class="form-select" name="semestre" required>
                  <option selected>Seleccionar</option>
                  <option>1</option>
                  <option>2</option>
                  <option>3</option>
                  <option>4</option>
                  <option>5</option>
                  <option>6</option>
                  <option>7</option>
                  <option>8</option>
                  <option>9</option>
                  <option>10</option>
                  <option>11</option>
                  <option>12</option>
                </select>
              </div>
            
          
            <div class="row ">
              <div class="col-5 mb-1 mt-3 d-grid gap-2 mx-auto">
                <button type="submit" class="btn btn-primary btn-lg ">Registrarse</button>
              </div>
  
              <div class="col-5  pt-2">
                <button type="button" class="btn btn-link mb-2 mt-3" id="letraform" style="font-size: 12px;">
                  <a href="../index.php">¿Ya tienes una cuenta? Inicia Sesion.</a>
              </div>
            </div>
            
          </form>
    </section>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
    var alertMessage = "<?php echo isset($_SESSION['alert_message']) ? $_SESSION['alert_message'] : ''; ?>";
    if(alertMessage) {
        // Creamos un elemento de alerta con Bootstrap y el mensaje proporcionado
        var alertElement = '<div class="alert alert-danger d-flex align-items-center" role="alert">' +
                               '<svg class="bi flex-shrink-0 me-2" style="width: 1em; height: 1em;" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>' +
                               '<div>' + alertMessage + '</div>' +
                           '</div>';

        // Agregamos el elemento de alerta al final del cuerpo del documento
        document.body.insertAdjacentHTML('beforeend', alertElement);

        // Eliminamos el mensaje de alerta de la sesión para que no se muestre nuevamente
        <?php unset($_SESSION['alert_message']); ?>
    }
  </script> 
  </body>
</html>