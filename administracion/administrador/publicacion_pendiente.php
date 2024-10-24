<?php
include('../../php/functions.php');
$link = include('../../php/conexion.php'); // Incluye el archivo de conexión y obtén la conexión

// Consulta a la base de datos

// Verifica si la consulta se ejecutó correctamente
if (isset($_GET['id'])) {
    // Obtener el ID de la publicación desde el parámetro GET
    $idPub = $_GET['id'];

    // Consultar la base de datos para obtener la información completa de la publicación
    $query = "SELECT p.*, u.nom_Us, u.apell_Us FROM publicacion p
              JOIN usuario u ON p.id_Usuario = u.idUsuario
              WHERE p.idPub = $idPub AND estado_Pub = 0";
    $result = mysqli_query($link, $query);


    // Verificar si se encontró la publicación
    if (mysqli_num_rows($result) == 1) {
        $publicacion = mysqli_fetch_assoc($result);
        
    //CONSULTAR LAS ETIQUETAS DE LA PUBLICACION
    $consultaEtiquetas = "SELECT nombreTag FROM tag_publicacion WHERE idPub = $idPub LIMIT 5";
    $resultEtiquetas = mysqli_query($link, $consultaEtiquetas);
    } else {
        exit();
    }
} else {
    exit();
}

//Actualización del estado

// Verifica si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST"  && isset($_POST['Enviar'])) {
    // Actualizar el estado de la publicación
    $query = "UPDATE publicacion SET estado_Pub = 1 WHERE idPub = $idPub";

    if (mysqli_query($link, $query)) {
        // Redirigir al usuario a la página de publicaciones pendientes
        header("Location: admin_home.php");
        exit;
    } else {
        header("Location: publicaciones_pendientes.php?error=true");
    }
}



if ($_SERVER["REQUEST_METHOD"] == "POST"  && isset($_POST['eliminar_archivo'])) {
    $rutaArchivo = "../".$publicacion['archivo_Pub'];
  
    if (file_exists($rutaArchivo)) {     
        if (unlink($rutaArchivo)) {
            $sql = "UPDATE publicacion SET estado_Pub = 2 WHERE idPub = $idPub";
           if (mysqli_query($link,$sql)) {
                // Eliminar los tags asociados a la publicación de la tabla 'tag_publicacion'
                $sqlTags = "DELETE FROM tag_publicacion WHERE idPub = $idPub";
  
                if (mysqli_query($link,$sqlTags)) {
                    echo "El archivo y la publicación se han eliminado correctamente.";
                    header("Location: admin_home.php");
                } else {
                    echo "Error al intentar eliminar los tags de la publicación: " . $conexion->error;
                }
            } else {
                echo "Error al intentar eliminar la publicación: " . $conexion->error;
            }
        } else {
            // Error al intentar eliminar el archivo
            echo "Error al intentar eliminar el archivo.";
        }
    } else {
        // El archivo no existe
        echo "El archivo no existe.";
    }
    exit;
  }


// Cierra la conexión después de realizar la consulta
mysqli_close($link);

// Inicia la sesión después de cerrar la conexión
session_start();


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

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ComparTec - Publicación pendiente</title>

   

    <!--En esta seccion se incluyen las hojas de estilos-->
    <link rel="icon" href="../../images/icons/tigerF.png"><!--Esta seccion de codigo agrega un icono a la pagina-->
    <link rel="stylesheet" href="../../css/normalizar.css">
    <link rel="stylesheet" href="../../css/estilos.css">
    <link rel="stylesheet" href="../../css/hover-min.css">
    <link rel="stylesheet" href="../../css/animate.css">
    <link rel="stylesheet" href="../../css/sidebars.css">
    <link rel="stylesheet" href="../../publicacion/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <!--Inicia Bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
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
                <ul class="list-unstyled" id="menu-lateral">
                    <li class="mb-1">
                        <a class="nav-link align-items-center p-3" href="admin_home.php" id="letrabardos"  style="color: black; font-weight: bold;">
                            Publicaciones Pendientes
                        </a>
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


            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 main-content bg-body-secondary" style="font-weight:normal; margin-left: 1%;">
                
                    <div class="container mt-4" >
                        <div class="row " >
                            <div class="col-md-12 ">
                                <!-- Decision de publicacion -->
                                <div class="card card-details mb-3 bg-warning-subtle">
                                    <div class="card-header bg-warning">
                                    </div>
                                    
                                    <!-- Botón para ver archivo adjunto -->
                                    <div class="row">
                                        <div class="col mt-1 mb-2" >
                                            <h3 class="pl-1 mb-2 mt-3" style="margin-left: 2vmax;"> <b>Publicación pendiente</b></h3> 
                                        </div>
                                        <div class="col text-end mt-4 mb-4  align-items-end" style="margin-right: 2vmax;">
                                            <a class="btn btn-outline-success btn-sm"  data-bs-target="#staticBackdrop"   data-bs-toggle="modal">
                                                <i class="bi bi-check2 mr-2"></i> Aprobar
                                            </a>
                                            <a  data-bs-target="#staticBackdrop2"   data-bs-toggle="modal" class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-trash3 mr-2"></i> Rechazar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!--CSS
                style="background-color: rgba(255, 166, 0, 0.281);
                style="background-color: rgb(252, 146, 7); -->

                    <!-- Detalles de la publicación -->
                    <div class="card mb-5">
                                    <div class="card-header bg-warning bg-opacity-75 text-light d-flex justify-content-between">
                                        <div>
                                            <h5 class="card-title mb-0"><?php echo $publicacion['titulo_Pub']; ?></h5>
                                            <p class="card-text mb-0">Por:<b> <?php echo $publicacion['nom_Us'] . " " . $publicacion['apell_Us']; ?></b></p>
                                        </div>
                                        <div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <!-- Descripción de la publicación -->
                                        <div class="row mb-4">
                                            <div class="col">
                                                <div class="bg-light p-3 rounded">
                                                    <p class="card-text"><?php echo $publicacion['descrip_Pub']; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Información adicional de la publicación -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="card-text"><b>Carrera:</b> <?php echo $publicacion['carrera_Pub']; ?></p>
                                                <p class="card-text"><b>Materia:</b> <?php echo $publicacion['materia_Pub']; ?></p>
                                                <p class="card-text"><b>Tipo de Publicación:</b> <?php echo $publicacion['tipo_pub']; ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <!-- Calificación con estrellas -->
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Botones para ver y descargar archivo adjunto -->
                                    <div class="card-footer d-flex justify-content-center align-items-center">
                                        <div class="mx-3">
                                            <a href="<?php echo "../". $publicacion['archivo_Pub']; ?>" target="_blank" class="btn btn-primary btn-lg">
                                                <i class="bi bi-file-pdf-fill mr-2"></i> Ver Archivo Adjunto
                                            </a>
                                        </div>
                                    </div>
                                    <!-- Etiquetas -->
                                    <div class="card-footer d-flex justify-content-between align-items-end">
                                        <?php
                                        // Colores predefinidos para las etiquetas
                                        $colores = array("bg-danger", "bg-success", "bg-info", "bg-warning", "bg-primary");

                                        // Contador para asignar colores
                                        $colorIndex = 0;

                                        // Mostrar las etiquetas obtenidas
                                        while ($etiqueta = mysqli_fetch_assoc($resultEtiquetas)) {
                                            echo '<span class="badge ' . $colores[$colorIndex] . ' mr-1">' . $etiqueta['nombreTag'] . '</span>';

                                            // Incrementar el índice de color (cíclico)
                                            $colorIndex = ($colorIndex + 1) % count($colores);
                                        }
                                        ?>
                                    <!-- Fecha de Publicación -->
                                    <p class="card-text comment-date mb-0 align-self-end">Fecha: <?php echo functions::convertirFecha($publicacion['fecha_Pub']); ?></p>
                                </div>
                            </div>
                        </div>
                     </div>
            </main>
                                    
             <!-- POPUP 1-->
             <form method="post"> 
             <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Publicación pendiente</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    ¿Estas seguro de aprobar esta publicación?
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" value="Enviar"  name="Enviar" class="btn btn-primary" data-bs-dismiss="modal">Sí, publicar</button>
                  </div>
                </div>
              </div>
            </div>
          </form>



          <!-- POPUP 2-->
          <form method="post" name="eliminar_archivo"> 
             <div class="modal fade" id="staticBackdrop2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Publicación pendiente</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    ¿Estas seguro de rechazar esta publicación?
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="eliminar_archivo"  value="eliminar_archivo" class="btn btn-primary" data-bs-dismiss="modal">Sí, rechazar y eliminar</button>
                  </div>
                </div>
              </div>
            </div>
          </form>


          <!-- PHP PARA MOSTAR MENSAJE DE ERROR-->


            <?php
            // Verificar si ha ocurrido un error durante la actualización del estado de la publicación
            if (isset($_GET['error']) && $_GET['error'] === 'true') {
                // Mostrar un script JavaScript para mostrar el toast de error
                echo "<script>$(document).ready(function() { 
                    toastr.error('Ha ocurrido un error al actualizar el estado de la publicación.');
                });</script>";
            }
            ?>


   
     <script src ="../../js/fadeout.js"></script>
            
     <footer class="bg-primary py-2 bg-opacity-75 border-top border-terciary border-4 py-3 text-light bg-primary">
        <div class="container" >
            <p class="mb-1">&copy; 2024 ComparTec - Todos los derechos reservados</p>
        </div>
    </footer>
</body>
</html>

