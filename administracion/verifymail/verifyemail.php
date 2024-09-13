<?php
$link = include('../../php/conexion.php'); // Incluye el archivo de conexión y obtén la conexión
// Cierra la conexión después de realizar la consulta
mysqli_close($link);

// Inicia la sesión después de cerrar la conexión
session_start();

$_SESSION['enviarCorreo']='';
$correo = $_GET['mail'];
$_SESSION['enviarCorreo']=$correo;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!--Required Meta tags-->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!--Bootstrap CSS-->
    <link rel="icon" href="../../images/icons/tigerF.png">
    <link rel="stylesheet" href="../../css\estiloReg.css">
    <link rel="stylesheet" href="../../css\normalizar.css">
    <link rel="stylesheet" href="../../css\estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <title>BiblioTec - Registro</title>
</head>
<body style="background-color: #EFF5FE;">
<header class="text-center ">
    <div class="cabeza navbar navbar-expand navbar-dark " >
        <div class="cabeza navbar-brand">
          <a  class="logor">
            <img src="../../images/icons/flamita.png" alt="Logo BiblioTec" class="img-fluid mr-1">
            <h4 class="mb-0"><b><span class="col-1">Biblio</span><span class="col-3 tec">Tec</span></h4>
          </a>
        </div>
    </div>
    <div class="row" >
      <div class="container-fl ">
        <h1 id="encabezado">Validar Correo</h1>
      </div>
    </div>
    <div class="card mx-auto my-5" style="width: 30rem;">
      <h6 class="mb-2 text-body-secondary">Se ha enviado un código de verificación a la dirección de correo proporcionada</h6>
    </div>
</header>

<section class="container py-6 my-1 w-50 text-ligth p-2" style="row-gap: 5%;"  id="campos">
  <form class="row my-auto py-2 mx-auto need-validation" id="form" action="verifyMail_Control.php" method="POST" style="background-color: #edfcf1;">

    <div class="col-md-6 mx-auto">
        <label for="validateCode" class="form-label mx-auto my-2">Ingrese el código de verificación:</label>
        <input type="text" id="validateCode" class="form-control my-3" name="codigoIn" required>
    </div>
    

    <div class="row">
      <div class="col-5 mb-1 mt-3 d-grid gap-2 mx-auto">
        <button type="submit" class="btn btn-success" >Validar</button>
      </div>
    </div>

  </form>
</section>


</body>
</html>