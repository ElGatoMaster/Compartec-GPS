<?php
include('../../php/functions.php');
$link = include('../../php/conexion.php');

session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "admin") {
    header("location: ../../index.php");
    exit;
}

if (isset($_GET["logout"]) && $_GET["logout"] === "true") {
    session_unset();
    session_destroy();
    header("location: ../../index.php");
    exit;
}

// Obtener estadísticas
$stats = array();
$sp_names = array(
    "TotalUsuarios",
    "DistribucionUsuariosCarrera",
    "TotalPublicaciones",
    "PublicacionesPorTipo",
    "PublicacionesPorCarrera",
    "TotalComentarios",
    "ObtenerReportes",
    "ObtenerNumeroDeConsultasMes",
    "TotalPublicacionesPorAnio"
);

foreach ($sp_names as $sp_name) {
    $query = "CALL $sp_name();";
    $result = mysqli_multi_query($link, $query);

    if (!$result) {
        die('Error en la consulta: ' . mysqli_error($link));
    }

    $stats[$sp_name] = array();

    // Procesar cada conjunto de resultados
    do {
        if ($result = mysqli_store_result($link)) {
            $stats[$sp_name][] = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($link));
}



if (isset($_GET['file'])) {
    // Obtener el nombre del archivo del parámetro GET
    $fileName = $_GET['file'];

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . basename($fileName) . '"');
        header('Content-Length: ' . filesize($fileName));
        
        // Leer el archivo y enviarlo al navegador
        readfile($fileName);
        exit;
}



mysqli_close($link);
?>



<!DOCTYPE html>
<html lang="es">

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

  <!--Termmina Bootstrap-->
  <!--iconos-->
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


<body class="bg-body-secondary">
<header class="bg-primary py-2 bg-opacity-75 border-bottom border-terciary border-4 d-flex flex-wrap align-items-center py-3 position-inherit">
    <div class="d-flex align-items-center">
      <!-- Logo y título -->
      <img src="../../images/icons/TecNM.png"  class="d-flex img-fluid" style="width: 145px; margin-right: 2.0vmax;">
      <img src="../../images/icons/tec.png" class="d-flex img-fluid" style="width: 60px;  margin-right: 2.0vmax;">
      <a href="" class="logo d-flex align-items-center mb-3 mb-md-0 link-body-emphasis text-decoration-none">
        <img src="../../images/icons/flamita.png" alt="Logo T - ComparTec" class="img-fluid" style = "margin-right: 0.5vmax;">
        <h4><span class="col-1 ">Biblio</span>
        <span class="col-2">Tec<span class="col-1"> - Administrador</span></span></h4>
      </a>
    </div>
  </header>

  <div class="container-fluid" >
    <div class="row" >
        <!-- Barra de navegación izquierda -->
        <div class="flex-shrink-0 p-3 border-end border-terciary border-4 bg-body p-3" style="width: 15%;">
            <ul class="list-unstyled" id="menu-lateral" style="padding-bottom: 300px">
            <li class="mb-1">
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
                <hr class="my-2"> <!-- Línea divisora -->
                <li class="mb-1">
                    <button class="btn d-inline-flex align-items-center rounded border-0 collapsed" id="letrabardos" style="color: black; font-weight: bold;">
                    <a class="nav-link align-items-center" href="admin_home.php?logout=true" id="letrabar">Cerrar sesión</a>
                    </button>
                </li>
                <hr class="my-2"> <!-- Línea divisora -->
                <li class="mb-1">
                  <a class="nav-link align-items-center" href="stats.php" id="letrabardos" style="margin-left:10px">Estadísticas de ComparTec</a>
                </li>
            </ul>
        </div>
      
      <!-- Contenido principal -->
      <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 main-content bg-body-secondary">
    <div class="container mt-4">
        <h2 class="mb-4">Estadísticas
        <form action="generar_excel.php" method="post">
                <button type="submit" class="btn btn-success" >
                        <i class="bi bi-filetype-xml" style="font-size: 1vmax"></i>  Descargar estadísticas
                </button>
        </form>
        </h2>

        <?php foreach ($stats as $sp_name => $data) : ?>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white justify-content-between d-flex">
                    <h5 class="card-title mb-0"><?php echo $sp_name; ?></h5>
                </div>
                <div class="card-body">
                    <?php foreach ($data as $result) : ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <?php foreach ($result[0] as $key => $value) : ?>
                                            <th><?php echo ucwords(str_replace('_', ' ', $key)); ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($result as $row) : ?>
                                        <tr>
                                            <?php foreach ($row as $value) : ?>
                                                <td><?php echo $value; ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>


    </div>
  </div>

  <script src ="../../js/fadeout.js">
  </script>


  <footer class="bg-primary py-2 bg-opacity-75 border-top border-terciary border-4 py-3 text-light bg-primary">
        <div class="container" >
            <p class="mb-1">&copy; 2024 ComparTec - Todos los derechos reservados</p>
        </div>
    </footer>
  
</body>

</html>