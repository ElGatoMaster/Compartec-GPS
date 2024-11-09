<?php
// Incluir el archivo de conexión a la base de datos
$link = include('../../php/conexion.php');

// Verificar si se proporcionó un ID de publicación
if (isset($_GET['id'])) {
    // Obtener el ID de la publicación desde el parámetro GET
    $idPub = $_GET['id'];

    // Consultar la base de datos para obtener la información completa de la publicación
    $query = "SELECT p.*, u.nom_Us, u.apell_Us FROM publicacion p
              JOIN usuario u ON p.id_Usuario = u.idUsuario
              WHERE p.idPub = $idPub";

    $query2 = "SELECT * FROM reportepublicación WHERE idPub = $idPub ORDER BY `idReporte` DESC";
    $result = mysqli_query($link, $query);
    $registro = mysqli_query($link, $query2);
    $fila = mysqli_fetch_array($registro);
    $publicacion = mysqli_fetch_array($result);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['ConfirmarEliminar2'])) {
        $rutaArchivo = "../" . $publicacion['archivo_Pub'];
        if (file_exists($rutaArchivo)) {
            if (unlink($rutaArchivo)) {
                $sql = "UPDATE publicacion SET estado_Pub = 3  WHERE idPub = $idPub";
                if (mysqli_query($link, $sql)) {
                    // Eliminar los tags asociados a la publicación de la tabla 'tag_publicacion'
                    $sqlTags = "UPDATE reportepublicación SET estado_Report = 1 WHERE idPub = $idPub";

                    if (mysqli_query($link, $sqlTags)) {
                        echo "El archivo y la publicación se han eliminado correctamente.";
                        header("Location: rep_publicacion_pendiente.php");
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
    if(isset(($_POST['RechazarReporte']))){
        $sql = "UPDATE reportepublicación SET estado_Report= 1 WHERE idPub = $idPub";
        mysqli_query($link, $sql);
        header("Location: rep_publicacion_pendiente.php");
    }
}

mysqli_close($link);

// Inicia la sesión después de cerrar la conexión
session_start();

// Verificar si el usuario no ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "admin") {
    header("location: ../../index.php");
    exit;
}

// Verificar si se ha enviado una solicitud para cerrar sesión
if (isset($_GET["logout"]) && $_GET["logout"] === "true") {
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
    <title>ComparTec - Reporte publicación</title>

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
                            <a class="nav-link align-items-center" href="admin_home.php?logout=true" id="letrabar" name="id">Cerrar sesión</a>
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

                <div class="container mt-5 mb-5">
                    <div class="row">
                        <div class="col-md-11" style="margin-left: 4%;">
                            <!-- Detalles de la publicación -->
                            <div class="card card-details bg-danger-subtle">
                                <div class="card-header bg-danger">
                                </div>

                                <!-- Botón para ver archivo adjunto -->
                                <div class="row">
                                    <div class="col mt-1 mb-2">
                                        <h3 class="pl-1  mb-2" style="margin-left: 2vmax;"> Reporte de publicación</h3>
                                        <h5 class="row"><Span style="margin-left: 2vmax;"><b>Motivo de reporte: </b><?php echo $fila['motivo_Report']; ?></Span><span></span></h5>
                                    </div>
                                    <div class="col text-end mt-4 mb-3  align-items-end" style="margin-right: 2vmax;">
                                        <a id="Rechazar" data-bs-toggle="modal" data-bs-target="#reg-modalR" href="#" class="btn btn-outline-success btn-sm">
                                            <i class="bi bi-check2 mr-2"></i> Rechazar reporte
                                        </a>
                                        <a id="idEliminar2" data-bs-toggle="modal" data-bs-target="#reg-modal" href="#" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-trash3 mr-2"></i> Eliminar publicacion
                                        </a>

                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>


                <!--PUBLICACIÓN-->


                <div class="container mt-4 mb-5">
                    <div class="row">
                        <div class="col-md-8" style="width: 92%; margin-left: 4%;">
                            <!-- Detalles de la publicación -->
                            <div class="card card-details">
                                <div class="card-header text-light bg-danger">
                                    <h5 class="card-title mb-0"><?php echo $publicacion['titulo_Pub']; ?></h5>
                                    <p class="card-text mb-0">Por:<b> <?php echo $publicacion['nom_Us'] . " " . $publicacion['apell_Us']; ?></b></p>
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
                                            <div class="rating mt-3">
                                                <p class="card-text"><b>Calificación:</b></p>
                                                <?php
                                                // Calificación actual de la publicación
                                                $calificacion = $publicacion['calif_Pub'];

                                                // Convertir calificación del rango 1-10 a 1-5
                                                $calificacion_estrellas = ceil($calificacion / 2);

                                                // Mostrar estrellas llenas según la calificación
                                                for ($i = 1; $i <= 5; $i++) {
                                                    if ($i <= $calificacion_estrellas) {
                                                        echo '<i class="bi bi-star-fill text-warning"></i>';
                                                    } else {
                                                        echo '<i class="bi bi-star text-warning"></i>';
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Botón para ver archivo adjunto -->
                                <div class="row">
                                    <div class="col text-center mt-3 mb-3">
                                        <a href="../<?php echo $publicacion['archivo_Pub']; ?>" target="_blank" class="btn btn-primary btn-lg">
                                            <i class="bi bi-file-pdf-fill mr-2"></i> Ver Archivo Adjunto
                                        </a>
                                    </div>
                                </div>
                                <!-- Etiquetas -->
                                <div class="card-footer d-flex justify-content-between align-items-end">
                                    <?php
                                    // Etiquetas por defecto y colores asignados
                                    $etiquetas = array("Etiqueta1", "Etiqueta2", "Etiqueta3");
                                    $colores = array("bg-danger", "bg-success", "bg-info");

                                    // Iterar sobre las etiquetas y mostrarlas
                                    foreach ($etiquetas as $index => $etiqueta) {
                                        echo '<span class="badge ' . $colores[$index % count($colores)] . ' mr-1">' . $etiqueta . '</span>';
                                    }
                                    ?>
                                    <!-- Fecha de Publicación -->
                                    <p class="card-text comment-date mb-0 align-self-end">Fecha de Reporte: <?php echo $fila['fecha_Report']; ?></p>
                                </div>
                            </div>


            </main>
            <!-- Modal para BORRAR LA PUBLICACION -->
            <form method="POST" name="ConfirmarEliminar2">
                <div class="modal fade" id="reg-modal" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
                    <div class="modal.dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-title">Confirmar Eliminar</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p> ¿Estas seguro de querer eliminar esta publicación?</p>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" name="ConfirmarEliminar2">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Modal para RECHAZAR EL REPORTE -->
            <form method="POST" name="RechazarReporte">
                <div class="modal fade" id="reg-modalR" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
                    <div class="modalR.dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-title">Confirmar Rechazo</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modalR" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p> ¿Estas seguro de querer rechazar el reporte de esta publicación?</br>
                                Esta acción no se podrá deshacer</p>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" name="RechazarReporte">Aceptar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <script src="../../js/fadeout.js"></script>

            <footer class="bg-primary py-2 bg-opacity-75 border-top border-terciary border-4 py-3 text-light bg-primary">
        <div class="container" >
            <p class="mb-1">&copy; 2024 ComparTec - Todos los derechos reservados</p>
        </div>
    </footer>
</body>

</html>