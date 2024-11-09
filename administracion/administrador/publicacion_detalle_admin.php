<?php
include('../../php/functions.php');
//include('../php/sesion.php');
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
    $result = mysqli_query($link, $query);

    // Verificar si se encontró la publicación
    if (mysqli_num_rows($result) == 1) {
        $publicacion = mysqli_fetch_assoc($result);

        // Consultar los comentarios asociados a esta publicación
        $query_comentarios = "SELECT c.*, u.nom_Us, u.apell_Us FROM comentario c
                              JOIN usuario u ON c.idUsuario = u.idUsuario
                              WHERE c.idPub = $idPub";
        $result_comentarios = mysqli_query($link, $query_comentarios);

        //CONSULTAR LAS ETIQUETAS DE LA PUBLICACION
        $consultaEtiquetas = "SELECT nombreTag FROM tag_publicacion WHERE idPub = $idPub LIMIT 5";
        $resultEtiquetas = mysqli_query($link, $consultaEtiquetas);
    } else {
        // Si no se encontró la publicación, redireccionar a la página principal
        header("Location: ../home.php");
        exit();
    }


    //Query para obtener el promedio de calificaciones de esta publicacion
    $qcalif = "SELECT AVG(`calificacion`) as 'promedio' from calificacion_detalle cd 
                join usuario u on u.idUsuario = cd.id_Usuario 
                join publicacion p on p.idPub = cd.idPub 
                where cd.idPub = $idPub;";
    $consulta = mysqli_query($link, $qcalif);

    $consultaCal = mysqli_fetch_assoc($consulta);

    if (isset($_POST["guardar"])) {  //Desmadre para guardar la calificacion
        $rate = $_POST["calificacion"];
        $idUser = $_SESSION['idU'];
        //verificar si es la primera vez que el usuario califica esta publicacion
        $ver = "SELECT * FROM `calificacion_detalle` 
                WHERE id_Usuario = $idUser AND idPub=$idPub;";

        $res = mysqli_query($link, $ver);
        if (mysqli_num_rows($res) == 1) {
            $sql = "UPDATE `calificacion_detalle` SET `calificacion`= $rate 
            WHERE id_Usuario = $idUser AND idPub=$idPub;";

            $res = mysqli_query($link, $sql);
            if ($res) {
                echo 'SI';
            } else {
                echo 'NO';
            }
        } else {
            $sql = "INSERT INTO `calificacion_detalle` VALUES($idUser,$idPub,$rate)";

            $res = mysqli_query($link, $sql);
            if ($res) {
                echo 'SI';
            } else {
                echo 'NO';
            }
        }
    }
    //exit(json_encode(array('id' => $idUser)));

    //PARA LOS REPORTES DE PUBLICACIONES Y COMENTARIOS
    if (isset($_POST["repub"])) {
        $motivo = $_POST["motivo"];
        $fechaRep = date("Y-m-d");

        $qRepPub = "INSERT INTO `reportepublicación`(`idPub`, `fecha_Report`, `motivo_Report`) 
                    VALUES ($idPub,CURDATE(),'$motivo');";

        $resR = mysqli_query($link, $qRepPub);
        if ($resR) {
            //header("Location: ../home.php");
            echo "Se ha guardado el reporte en la base de datos";
        } else {
            echo "NO";
        }
    }
    if (isset($_POST["repCom"])) {
        $motivo = $_POST["motivo"];
        $idComRep = $_POST["idComentario"];
        $fechaRep = date("Y-m-d");

        $qRepCom = "INSERT INTO `reportecomentario`(`idComent`, `fecha_Report`, `motivo_Report`)
                    VALUES ($idComRep,CURDATE(),'$motivo')";

        $resR = mysqli_query($link, $qRepCom);
        if ($resR) {
            echo 'SI';
        } else {
            echo 'NO';
        }
    }
} else {
    // Si no se proporcionó un ID de publicación, redireccionar a la página principal
    header("Location: ../../home.php");
    exit();
}

// Cerrar la conexión a la base de datos
mysqli_close($link);

// Iniciar sesión
//session_start();

// Verificar si el usuario no ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../../index.php"); // Redirigir al usuario al inicio de sesión si no ha iniciado sesión
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ComparTec - Detalles de la Publicación</title>

    <!--En esta seccion se incluyen las hojas de estilos-->
    <link rel="icon" href="../../images/icons/tigerF.png"><!--Esta seccion de codigo agrega un icono a la pagina-->
    <link rel="stylesheet" href="../../css/normalizar.css">
    <link rel="stylesheet" href="../../css/estilos.css">
    <link rel="stylesheet" href="../../css/hover-min.css">
    <link rel="stylesheet" href="../../css/animate.css">
    <link rel="stylesheet" href="../../css/sidebars.css">
    <link rel="stylesheet" href="style.css">
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

    <style>
        @font-face {
            font-family: 'Agrandir';
            src: url('../../css/Agrandir.otf') format('otf');
        }

        pre {
            font-family: 'Agrandir', sans-serif;
            font-size: 16px;
        }
    </style>
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
    <!--Aqui se muestra un apartado para los productos que se venderan-->

    <!--IMAGEN DE CONTACTO-->
    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
        <symbol id="people-circle" viewBox="0 0 16 16">
            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
        </symbol>
    </svg>


    <div class="container-fluid">
        <div class="row">
            <!-- Barra de navegación izquierda -->
            <div class="flex-shrink-0 p-3 border-end border-terciary border-4 bg-body p-3" style="width: 15%;">
                <ul class="list-unstyled" id="menu-lateral">
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
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4" style="font-weight:normal; margin-left: 1.5%">
                <div class="col">
                <h3 style=" margin-left: 1.5% ; margin-top: 35px;">
                <b class="textogran" style="font-size: 2vmax; text-shadow: 2px 2px 4px rgba(114, 114, 114, 0.4);
                    margin-top: 0.5vmax;">Detalles de la Publicación</b>
                    </h3>
                    <span style=" margin-left: 82%;">
                        <a href="javascript:history.back()" class="btn btn-info btn-sm">
                            <i class="bi bi-box-arrow-up-right mr-2" style="font-size: 1em;"></i> Regresar al reporte
                        </a>
                    </span>
                </div>
                    <div class="container mt-4 mb-5">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Detalles de la publicación -->
                                <div class="card card-details">
                                    <div class="card-header bg-primary text-light d-flex justify-content-between">
                                        <div>
                                            <h5 class="card-title mb-0"><?php echo $publicacion['titulo_Pub']; ?></h5>
                                            <p class="card-text mb-0">Por: <a class="link-light link-underline link-underline-opacity-0"><b><?php echo $publicacion['nom_Us'] . " " . $publicacion['apell_Us']; ?></b></a></p>
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
                                                <div class="rating mt-3">
                                                    <p class="card-text"><b>Calificación:

                                                            <?php
                                                            // Calificación actual de la publicación
                                                            if ($consultaCal['promedio'] === null) {
                                                                $calificacion = 0;
                                                                echo '<span> ' . 'NA' . '</span> </b></p>';
                                                            } else {
                                                                $calificacion = $consultaCal['promedio'];
                                                                echo '<span> ' . round($calificacion, 2) . '</span> / 5 </b></p>';
                                                            }
                                                            

                                                            // Convertir calificación de 1-5
                                                            $calificacion_estrellas = ceil($calificacion / 1);
                                                            ?>
                                                            <p class="calificar">
                                                                <?php

                                                                // Mostrar estrellas llenas según la calificación
                                                                for ($i = 1; $i <= 5; $i++) {
                                                                    if ($i <= $calificacion_estrellas) {
                                                                        echo '<i class="bi bi-star-fill estrella" data-rating="' . $i . '"></i>';
                                                                    } else {
                                                                        echo '<i class="bi bi-star-fill" data-rating="' . $i . '"></i>';
                                                                    }
                                                                }
                                                                ?>
                                                            </p>
                                                            <!--Script para las estrelas -->
                                                            <script src="../../publicacion/valoracion.js"></script>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <!-- Botones para ver y descargar archivo adjunto -->
                                    <div class="card-footer d-flex justify-content-center align-items-center">
                                        <div class="mx-3">
                                            <a href="../<?php echo $publicacion['archivo_Pub']; ?>" target="_blank" class="btn btn-primary btn-lg">
                                                <i class="bi bi-file-pdf-fill mr-2"></i> Ver Archivo Adjunto
                                            </a>
                                        </div>
                                        <div class="mx-3">
                                            <a href="../<?php echo $publicacion['archivo_Pub']; ?>" download class="btn btn-success">
                                                <i class="bi bi-cloud-arrow-down mr-2" style="font-size: 1.5em;"></i> <!-- Cambiar a otro icono de descarga -->
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




                            <!-- Sección de comentarios -->
                            <div class="mt-5">
                                <h5 class="mb-4">Comentarios</h5>


                                <!-- Arreglo de rutas de imágenes aleatorias -->
                                <?php
                                $imagenes_aleatorias = array();

                                // Ruta base de las imágenes
                                $ruta_base = "../../images/tigers/";

                                // Generar el arreglo de rutas de imágenes
                                for ($i = 1; $i <= 15; $i++) {
                                    $ruta_imagen = $ruta_base . "a" . $i . ".png";
                                    $imagenes_aleatorias[] = $ruta_imagen;
                                }

                                // Contador para alternar entre las imágenes aleatorias y los avatares
                                $contador = 0;
                                ?>

                                <!-- Mostrar comentarios existentes -->
                                <?php while ($comentario = mysqli_fetch_assoc($result_comentarios)) : ?>
                                    <div class="card mt-4">
                                        <div class="card-header">
                                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                                <!-- Botón de reportar -->
                                                
                                                <!-- Botón para ver perfil -->
                                                
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <?php if ($contador % 2 == 0) : ?>
                                                        <!-- Mostrar imagen aleatoria -->
                                                        <img src="<?php echo $imagenes_aleatorias[array_rand($imagenes_aleatorias)]; ?>" alt="Imagen Aleatoria" class="rounded-circle" width="50">
                                                    <?php else : ?>
                                                        <!-- Mostrar avatar -->
                                                        <img src="<?php echo $imagenes_aleatorias[array_rand($imagenes_aleatorias)]; ?>" alt="Imagen Aleatoria" class="rounded-circle" width="50">
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col">
                                                    <h6 class="mb-1"><a class="link-dark link-underline link-underline-opacity-0" ><?php echo $comentario['nom_Us'] . " " . $comentario['apell_Us']; ?></a></h6>
                                                    <pre class="mb-1"><?php echo $comentario['text_Coment']; ?></pre>
                                                    <small class="text-muted"><?php echo functions::convertirFecha($comentario['fecha_Coment']); ?></small>
                                                </div>
                                                <div class="col-auto">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    // Incrementar el contador
                                    $contador++;
                                    ?>
                                <?php endwhile; ?>

                            </div>
                        </div>
                    </div>
        </div>
        </main>


        
    </div>
    </div>

    <script src="../js/fadeout.js"></script>

    <footer class="bg-primary py-2 bg-opacity-75 border-top border-terciary border-4 py-3 text-light bg-primary">
        <div class="container" >
            <p class="mb-1">&copy; 2024 ComparTec - Todos los derechos reservados</p>
        </div>
    </footer>
</body>

</html>