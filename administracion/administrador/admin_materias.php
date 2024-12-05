<?php
include('../../php/functions.php');
$link = include('../../php/conexion.php'); // Incluye el archivo de conexión y obtén la conexión

// Inicia la sesión después de cerrar la conexión
session_start();

$consulta = "SELECT * FROM materia
              WHERE estadoMateria = 1
              ORDER BY nomMateria ASC";
$registros = mysqli_query($link, $consulta);

//-------Agregar Materia
if(isset($_POST['pls'])){
  $nvMateria = $_POST['nuevaMateria'];
  $carrera = $_POST['nuevaCarrera'];
  $estado = $_POST['estadoMateria'];

  echo "<script>
  console.log('datos? ',$nvMateria,$carrera,$estado);
  </script>";

  $query = "INSERT INTO `materia`(`nomMateria`, `nomCarrera`, `estadoMateria`) 
  VALUES ('$nvMateria','$carrera','$estado')";

  $res = mysqli_query($link,$query);
  if($res){
      echo "<script>
      alert('se agregó la materia con exito');
      </script>";
      // Redirigir a la página anterior 
      //header('Location: ' . $_SERVER['HTTP_REFERER']);
  }else{
      echo "<script>
      alert('No se pudo agregar la materia');
      </script>";
      // Redirigir a la página anterior 
      //header('Location: ' . $_SERVER['HTTP_REFERER']);
  }
}

//----Editar Materia
if(isset($_POST['original'])){
  $original = $_POST['original'];
  $nomMateria = $_POST['nomMateria'];
  $carrera = $_POST['nomCarrera'];
  $est = $_POST['estadoMateria'];

 // Escapar las variables para evitar inyección SQL 
 $nomMateria = mysqli_real_escape_string($link, $nomMateria); 
 $carrera = mysqli_real_escape_string($link, $carrera);
 $est = mysqli_real_escape_string($link, $est); 
 $original_nomMateria = mysqli_real_escape_string($link, $original['nomMateria']); 
 $original_nomCarrera = mysqli_real_escape_string($link, $original['nomCarrera']);

  // $query = "UPDATE `materia` SET `nomMateria` = $nomMateria,`nomCarrera` = $carrera, `estadoMateria` = $est 
  //  WHERE `nomMateria` = '$original_nomMateria' AND `nomCarrera` = '$original_nomCarrera'";
  $query = "UPDATE `materia` SET `nomMateria` = '$nomMateria', `nomCarrera` = '$carrera', `estadoMateria` = '$est' 
  WHERE `nomMateria` = '$original_nomMateria' AND `nomCarrera` = '$original_nomCarrera'";

  $res = mysqli_query($link,$query);
  if($res){ 
    echo json_encode(["success" => true, "message" => "Se editó la materia con éxito"]); 
  }else{ 
    echo json_encode(["success" => false, "message" => "No se pudo editar la materia"]);
  }
}//Editar Materia

//Eliminar Materia
if(isset($_POST['eliminar'])){
  $materia = $_POST['materia'];

  $materia_nomMateria = mysqli_real_escape_string($link, $materia['nomMateria']); 
  $materia_nomCarrera = mysqli_real_escape_string($link, $materia['nomCarrera']);
  

  $query = "UPDATE `materia` SET `estadoMateria` = 0 
  WHERE `nomMateria` = '$materia_nomMateria' AND `nomCarrera` = '$materia_nomCarrera'";

  $res = mysqli_query($link,$query);
  
  if($res){ 
    echo json_encode(["success" => true, "message" => "Se eliminó la materia con éxito"]); 
  }else{ 
    echo json_encode(["success" => false, "message" => "No se pudo eliminar la materia"]);
  }

}//Eliminar Materia

// Verificar si el usuario no ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "admin") {
  header("location: ../../index.php");
  exit;
}

// Verificar si se ha enviado una solicitud para cerrar sesión
if(isset($_GET["logout"]) && $_GET["logout"] === "true") {
    // Destruir todas las variables de sesión
    session_unset();
    
    // Destruir la sesión
    session_destroy();
    
    // Redirigir al usuario al inicio de sesión
    header("location: ../../index.php");
    exit;
  }

mysqli_close($link);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ComparTec - AdminHome</title>

  <!--En esta seccion se incluyen las hojas de estilos-->
  <link rel="icon" href="../../images\icons\tigerF.png"><!--Esta seccion de codigo agrega un icono a la pagina-->
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
    <!--ICONOS -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,1,0" />

</head>
<style>
  .material-symbols-outlined {
    color: #F87200;
    text-shadow: 2px 2px 4px rgba(134, 134, 134, 0.2);
    font-variation-settings:
      'FILL' 1,
      'wght' 900,
      'GRAD' 100,
      'opsz' 424
  }
</style>

<body>
<header class="bg-primary py-2 bg-opacity-75 border-bottom border-terciary border-4 d-flex flex-wrap align-items-center py-3 position-inherit">
    <div class="d-flex align-items-center">
      <!-- Logo y título -->
      <img src="../../images/icons/TecNM.png"  class="d-flex img-fluid" style="width: 145px; margin-right: 2.0vmax;">
      <img src="../../images/icons/tec.png" class="d-flex img-fluid" style="width: 60px;  margin-right: 2.0vmax;">
      <a href="" class="logo d-flex align-items-center mb-3 mb-md-0 link-body-emphasis text-decoration-none">
        <img src="../../images/icons/flamita.png" alt="Logo T - ComparTec" class="img-fluid" style = "margin-right: 0.5vmax;">
        <h4><span class="col-1 ">Compar</span>
        <span class="col-2">Tec<span class="col-1"> - Administrador</span></span></h4>
      </a>
    </div>
</header>

<div class="container-fluid">
  <div class="row">
    <!-- Barra de navegación izquierda -->
    <div class="flex-shrink-0 p-3 border-end border-terciary border-4 bg-body p-3" style="width: 15%;">
            <ul class="list-unstyled" id="menu-lateral">
            <li class="mb-2 mt-2">
                  <a class="nav-link align-items-center" href="admin_home.php" id="letrabardos" style="margin-left:10px">Publicaciones Pendiendes</a>
                </li>
                <li class="mb-1">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed" id="letrabardos" data-bs-toggle="collapse" data-bs-target="#dashboard-collapse" aria-expanded="false" style="color: black; font-weight: bold;">
                      Reportes
                    </button>
                    <div class="collapse" id="dashboard-collapse">
                      <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                        <li><a href="rep_comentario_pendiente.php" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres">Comentarios</a></li>
                        <li><a href="rep_publicacion_pendiente.php" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres">Publicaciones</a></li>
                      </ul>
                    </div>
                </li>
                <li class="mb-2 mx-3">
                  <a href="admin_materias.php" class="nav-link align-items-center" id="letrabardos" style="margin: left 10px;">
                    Administrar Materias
                  </a>
                </li>
                <li class="mb-1">
                    <button class="btn d-inline-flex align-items-center rounded border-0 collapsed" id="letrabardos" style="color: black; font-weight: bold;">
                    <a class="nav-link align-items-center" href="admin_addAdmin.php" id="letrabar">Administradores</a>
                    </button>
                </li>
                <hr class="my-2"> <!-- Línea divisora -->
                <li class="mb-1">
                    <button class="btn d-inline-flex align-items-center rounded collapsed" id="letrabardos" style="color: black;">
                    <a class="nav-link align-items-center" href="admin_home.php?logout=true" id="letrabar">Cerrar sesión</a>
                    </button>
                </li>
                <hr class="my-2"> <!-- Línea divisora -->
                <li class="mb-1">
                  <a class="nav-link align-items-center" href="stats.php" id="letrabardos" style="margin-left:10px">Estadísticas de ComparTec</a>
                </li>
            </ul>
        </div><!--BARA LATERAL FIN -->

  <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 main-content bg-body-secondary">
  <div class="conteiner mt-3 mb-4">
  <h2 style="user-select: none;font-size: 2vmax;"><b>Administrar Materias</b></h2>
  </div>
  <div class="align-top mb-3 position-relative">
  <button type="submit" class="btn btn-success position-absolute end-0 bottom-100 " data-bs-toggle="modal" data-bs-target="#modal_agregar">
    <i class="bi bi-plus-circle-fill" style="font-size: 1vmax"></i> Agregar Materia
  </button>
  </div>

  <div class="table-responsive mt-3">
    <table class="table table-bordered table-hover mt-3">
    <thead class="thead-dark">
      <tr>
        <th>Materia</th>
        <th>Carrera</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($registros as $row) : $contador=0;?>
        <tr>
          <?php foreach ($row as $value) : ?>
            <td><?php 
              if($contador<2){
                echo $value;
                $contador++;
              }else if($value == 1){
                echo 'Activo';
              }
              ?></td>
          <?php endforeach; $jsonRow = json_encode($row);?>
          <td>
            <a class="btn btn-info btn-sm shadow" id='editarMateria' data-bs-toggle="modal" data-bs-target="#modal_editar"
            data-materia='<?php echo htmlspecialchars($jsonRow, ENT_QUOTES, 'UTF-8');?>'>
            <i class="bi bi-pencil-fill"></i>
            </a>
            <a class="btn btn-danger btn-sm shadow" id="eliminarMateria" data-bs-toggle="modal" data-bs-target="#modal_eliminar"
            data-eliminar='<?php echo htmlspecialchars($jsonRow, ENT_QUOTES, 'UTF-8');?>'>
            <i class="bi bi-trash3-fill "></i>
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    <script src="control_mat.js"></script>
    </table>
  </div>
  </main>

  <!-- Ventana de Agregar -->
  <div class="modal fade" id="modal_agregar" tabindex="-1" aria-labelledby="modal_agregar_mat" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
               <h1 class="modal-title fs-5" id="exampleModalLabel">Agregar Materia</h1>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="textMateria" class="form-label">Materia:</label>
              <input type="text" id="textMateria" class="form-control" >
            <br>
              <label for="selectCarrera" class="form-label">Carrera:</label>
              <select id="inputState validationCustom01 selectCarrera" class="form-select" name="carrera" required>
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
            <br>
              <label for="" class="form-label">Estado</label><br>
              <input type="checkbox" id="checkEstado" class="form-check-input" checked>
              <label for="checkEstado" class="form-check-label">Activa</label>
            </div>
          </div><!--MODAL BODY -->
          <div class="modal-footer">  
            <button type="button" class="btn btn-secondary" id="agregar_materia">Aceptar</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          </div>
        </div>
      </div>
    </div><!--MODAL COMPLETO DE Agregar -->

  <!-- Ventana de editar -->
    <div class="modal fade" id="modal_editar" tabindex="-1" aria-labelledby="modal_editar_mat" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
               <h1 class="modal-title fs-5" id="exampleModalLabel">Editar Materia</h1>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="textMateria" class="form-label">Materia:</label>
              <input type="text" id="textMateriaEd" class="form-control" >
            <br>
            <label for="selectCarrera" class="form-label">Carrera:</label>
              <select id="selectCarreraEd" class="form-select" name="carrera" required>
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
            <br>
              <label for="" class="form-label">Estado</label><br>
              <input type="checkbox" id="checkEstado" class="form-check-input">
              <label for="checkEstado" class="form-check-label">Activa</label>
            </div>
          </div><!--MODAL BODY -->
          <div class="modal-footer">  
            <button type="button" class="btn btn-primary" id="modificar_materia">Guardar</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          </div>
        </div>
      </div>
    </div><!--MODAL COMPLETO DE MODIFICAR -->

    <!-- Ventana de ELIMINAR -->
    <div class="modal fade" id="modal_eliminar" tabindex="-1" aria-labelledby="modal_eliminar_mat" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
               <h1 class="modal-title fs-5" id="exampleModalLabel">Eliminar Materia</h1>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
          <div class="modal-body">
            <div class="mb-3">
              <h3>¿Está seguro de que desea eliminar la materia <b id="elMateria"></b>?</h3>
            </div>
          </div><!--MODAL BODY -->
          <div class="modal-footer">  
            <button type="button" class="btn btn-danger" id="eliminar_materia">Eliminar</button>
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancelar</button>
          </div>
        </div>
      </div>
    </div><!--MODAL COMPLETO DE MODIFICAR -->

  </div><!--row creo que para que se pongan las cosas como renglones -->
</div><!--Container fluid, toda la pagina -->



<script src ="../../js/fadeout.js">
</script>

<footer class="bg-primary py-2 bg-opacity-75 border-top border-terciary border-4 py-3 text-light bg-primary">
    <div class="container" >
        <p class="mb-1">&copy; 2024 ComparTec - Todos los derechos reservados</p>
    </div>
</footer>

</body>
</html>