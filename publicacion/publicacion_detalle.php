<?php
include('../php/functions.php');
//include('../php/sesion.php');
// Inicia la sesión después de cerrar la conexión
session_start();

// Verificar si el usuario no ha iniciado sesión
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: index.php"); // Redirigir al usuario al inicio de sesión si no ha iniciado sesión
  exit;
}

// Verificar si se ha enviado una solicitud para cerrar sesión
if (isset($_GET["logout"]) && $_GET["logout"] === "true") {
  // Destruir todas las variables de sesión
  session_unset();

  // Destruir la sesión
  session_destroy();

  // Redirigir al usuario al inicio de sesión
  header("location: index.php");
  exit;
}

// Incluir el archivo de conexión a la base de datos
$link = include('../php/conexion.php');
$usuario =  $_SESSION['idU'];

if (isset($_GET["logout"]) && $_GET["logout"] === "true") {
    // Destruir todas las variables de sesión
    session_unset();

    // Destruir la sesión
    session_destroy();

    // Redirigir al usuario al inicio de sesión
    header("location: index.php");
    exit;
}

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
        $fechaRep = date("YYYY-MM-DD");

        $qRepPub = "INSERT INTO `reportepublicación`(`idPub`, `fecha_Report`, `motivo_Report`,`estado_Report`) 
                    VALUES ($idPub,CURDATE(),'$motivo',0);";

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
        $fechaRep = date("YYYY-MM-DD");

        $qRepCom = "INSERT INTO `reportecomentario`(`idComent`, `fecha_Report`, `motivo_Report`,`estado_Report`)
                    VALUES ($idComRep,CURDATE(),'$motivo',0)";

        $resR = mysqli_query($link, $qRepCom);
        if ($resR) {
            echo 'SI';
        } else {
            echo 'NO';
        }
    }
} else {
    // Si no se proporcionó un ID de publicación, redireccionar a la página principal
    header("Location: ../home.php");
    exit();
}


$notisquery = "SELECT n.*, p.titulo_Pub, d.desNoti FROM notificacion_usuario n
              JOIN publicacion p ON n.idPub = p.idPub
              JOIN notificaciones d ON d.idNoti = n.tipoNoti
              WHERE idUsuario = '$usuario' AND estadoNoti = 1
              ORDER BY n.idNotiUs DESC ";
$registrosNotis = mysqli_query($link, $notisquery); 

if (!$registrosNotis) {
  die('Error en la consulta: ' . mysqli_error($link));
}

$notisqueryCOUNT ="SELECT COUNT(*)  AS publicaciones_noleidas
                  FROM notificacion_usuario
                  WHERE idUsuario = '$usuario' AND estadoNoti = 1";

$registrosContarNotis = mysqli_query($link, $notisqueryCOUNT);

if (!$registrosContarNotis) {
  die('Error en la consulta: ' . mysqli_error($link));
}


$contador_notificaciones = mysqli_fetch_assoc($registrosContarNotis);

$total_notificaciones = $contador_notificaciones['publicaciones_noleidas'];


// Verifica si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST"  && isset($_POST['notisLeidas'])) {
  // Actualizar el estado de la publicación
  $actualizaNotis = "UPDATE notificacion_usuario
                      SET estadoNoti = 2
                      WHERE idUsuario = '$usuario'";

  if (mysqli_query($link, $actualizaNotis)) {
    header("Location: publicacion_detalle.php?id=".$idPub);
  } 
}


//contador de consultass
$fechaActual = date("Y-m-d");

if ($_SERVER["REQUEST_METHOD"] == "POST"  && isset($_POST['contador'])) {
        // Actualizar el estado de la publicación
        $queryCont = "INSERT INTO consultas(fechaConsulta) VALUES ('$fechaActual')";
        
        mysqli_query($link, $queryCont);
  }



// Cerrar la conexión a la base de datos
mysqli_close($link);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiblioTec - Detalles de la Publicación</title>

    <!--En esta seccion se incluyen las hojas de estilos-->
    <link rel="icon" href="../images/icons/tigerF.png"><!--Esta seccion de codigo agrega un icono a la pagina-->
    <link rel="stylesheet" href="../css/normalizar.css">
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/hover-min.css">
    <link rel="stylesheet" href="../css/animate.css">
    <link rel="stylesheet" href="../css/sidebars.css">
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
            src: url('../css/Agrandir.otf') format('otf');
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
      <img src="../images/icons/TecNM.png"  class="d-flex img-fluid" style="width: 145px; margin-right: 2.0vmax;">
      <img src="../images/icons/tec.png" class="d-flex img-fluid" style="width: 60px;  margin-right: 2.0vmax;">
      <a href="" class="logo d-flex align-items-center mb-3 mb-md-0 link-body-emphasis text-decoration-none">
        <img src="../images/icons/flamita.png" alt="Logo T - BiblioTec" class="img-fluid">
        <h4><b><span class="col-1">Biblio</span><span class="col-2">Tec</span></b></h4>
      </a>
    </div>
    <form action="../administracion/general_search.php" method="GET" id="searchForm" class="d-flex search-field">
      <input id="searchInput" name="dataSearch" class="form-control me-2" type="search" autocomplete="off" required placeholder="Buscar" aria-label="Search">
      <button id="searchButton" type="button">
      <i class="bi bi-search search-icon"></i>
      </button>
    </form>

        <div class="d-flex mt-2">
                <button class="btn btn-warning"  id="notis"  data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                <i class="bi bi-bell-fill"></i><span class="badge text-bg-danger"><?php echo $total_notificaciones;?></span>
                  </button>
                <div class="row">
                </div>
              <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
                <div class="offcanvas-header">
                  <h5 class="offcanvas-title " id="offcanvasRightLabel" style="mr-2">Notificaciones</h5> 
                  <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                  </div>
                  <div class="offcanvas-header justify-content-between">
                  <form method="post" name="notisLeidas" > 
                      <button name="notisLeidas" type="submit" class="btn btn-outline-primary btn-sm" name="notisLeidas" >Marcar como leidas</button>
                  </form>
                </div>
                <div class="offcanvas-body">
                <div class="list-group list-group-flush border-bottom scrollarea"> 
                <?php while ($fila = mysqli_fetch_array($registrosNotis)) : ?> 
                    <a href="#" class="list-group-item list-group-item-action  py-3 lh-sm" aria-current="true">
                      <div class="d-flex w-100 align-items-center justify-content-between">
                        <strong class="mb-1"><?php echo $fila['titulo_Pub']; ?></strong>
                        <small><?php echo functions::convertirFecha($fila['fechaNoti']);?></small>
                      </div>
                      <div class="col-10 mb-1 small"><?php echo $fila['desNoti']; ?></div>
                    </a>
                <?php endwhile; ?>
                </div>
              </div>
            </div>
          </div>
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
            <div class="flex-shrink-0 p-3 border-end border-terciary border-4 bg-body" style="width: 15%; background-color: #F07B12;">
                <ul class="list-unstyled" id="menu-lateral">
                    <li class="mb-2 mt-2">
                        <a class="nav-link align-items-center" href="../home.php" id="letrabar">Inicio</a>
                    </li>
                    <li class="mb-1">
                        <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed" id="letrabardos" data-bs-toggle="collapse" data-bs-target="#dashboard-collapse" aria-expanded="false" style="color: black; font-weight: bold;">
                            Carreras
                        </button>
                        <div class="collapse" id="dashboard-collapse">
                            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                <li><a href="../administracion/search.php?carrera=Arquitectura" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres">Arquitectura</a></li>
                                <li><a href="../administracion/search.php?carrera=Ing. Bioquimica" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres">Ingeniería Bioquímica</a></li>
                                <li><a href="../administracion/search.php?carrera=Ing. Civil" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Ingeniería Civil</a></li>
                                <li><a href="../administracion/search.php?carrera=Ing. Electrica" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Ingeniería Eléctrica</a></li>
                                <li><a href="../administracion/search.php?carrera=Ing. Gestion Empresarial" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Ing. en Gestión Empresarial</a></li>
                                <li><a href="../administracion/search.php?carrera=Ing. Sistemas Computacionales" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Ing. en Sistemas Computacionales</a></li>
                                <li><a href="../administracion/search.php?carrera=Ing. Industrial" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Ingeniería Industrial</a></li>
                                <li><a href="../administracion/search.php?carrera=Ing. Mecatronica" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Ingeniería Mecatrónica</a></li>
                                <li><a href="../administracion/search.php?carrera=Ing. Quimica" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Ingeniería Química</a></li>
                                <li><a href="../administracion/search.php?carrera=Lic. Administracion" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Licenciatura en Administración</a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="mb-1">
                        <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed" id="letrabardos" data-bs-toggle="collapse" data-bs-target="#contacto-collapse" aria-expanded="false" style="color: black; font-weight: bold;">
                            Contacto
                        </button>
                        <div class="collapse" id="contacto-collapse">
                            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                <li><a href="../administracion/Perfil/info_del_contacto.php?" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Información de contacto</a></li>
                            </ul>
                        </div>
                    </li>
                    <hr class="my-2"> <!-- Línea divisora -->
                    <li class="mb-1">
                        <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed" id="letrabardos" data-bs-toggle="collapse" data-bs-target="#cuenta-collapse" aria-expanded="false" style="color: black; font-weight: bold;">
                            <svg class="bi pe-none" width="1.3vmax" height="1.3vmax">
                                <use xlink:href="#people-circle" />
                            </svg>
                            <span style="margin-top:0.3vmax; margin-left: 0.4vmax;">Cuenta</span>
                        </button>
                        <div class="collapse" id="cuenta-collapse">
                            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                <li><a href="../administracion/Perfil/infoperfil.php" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Mi Perfil</a></li>
                                <a href="../home.php?logout=true" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Cerrar Sesión</a>
                            </ul>
                        </div>
                    </li>
                    <hr class="my-2"> <!-- Línea divisora -->
                    <li class="mb-1 mt-3">
                        <a class="nav-link align-items-center" name="fade" href="../publicacion/publicar.php" id="letrabardos" style="margin-left:10px">Nueva publicación</a>
                    </li>

                    <hr class="my-2"> <!-- Línea divisora -->
                    <li class="mb-1 mt-3">
                        <a class="nav-link align-items-center" href="#" id="letrabardos" style="margin-left:10px"><?php echo "Hola " . $_SESSION['nombre'] . " " . $_SESSION['apellido'] ?></a>
                    </li>
                </ul>
            </div>
            <!-- Contenido principal -->


            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 bg-body-secondary main-content" style="font-weight:normal; margin-left: 1.5%">
                <h3 style=" margin-left: 1.5% ; margin-top: 35px;">
                    <b class="textogran" style="font-size: 2vmax; 
                    margin-top: 0.5vmax;">Detalles de la Publicación</b>
                    </h2>
                    <div class="linea-delgada"></div>
                    <div class="container mt-4 mb-5">
                        <div class="row">
                            <div class="col-md-11">
                                <!-- Detalles de la publicación -->
                                <div class="card card-details">
                                    <div class="card-header bg-primary bg-opacity-75 text-light d-flex justify-content-between">
                                        <div>
                                            <h5 class="card-title mb-0"><?php echo $publicacion['titulo_Pub']; ?></h5>
                                            <p class="card-text mb-0">Por: <a class="link-light link-underline link-underline-opacity-0" href="../administracion/Perfil/UsuarioDetalle.php?id=<?php echo $publicacion['id_Usuario']; ?>"><b><?php echo $publicacion['nom_Us'] . " " . $publicacion['apell_Us']; ?></b></a></p>
                                        </div>
                                        <div>
                                            <!-- Botón de reportar -->
                                            <?php
                                                if($usuario != $publicacion['id_Usuario']){
                                                    echo '<a class="btn btn-danger btn-sm shadow" id="reportarPub" data-bs-toggle="modal" data-bs-target="#modal_report_p">
                                                    <i class="bi bi-flag-fill"></i>
                                                </a>';
                                                }
                                            ?>
                                            
                                            <a class="btn btn-info btn-sm shadow" href = "../administracion/Perfil/UsuarioDetalle.php?id=<?php echo $publicacion['id_Usuario']; ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Ver Perfil">
                                                <i class="bi bi-person-fill"></i>
                                            </a>
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
                                                            <script src="valoracion.js"></script>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <!-- Botones para ver y descargar archivo adjunto -->
                                    <form id="consultaForm" method="post">
                                        <input type="hidden" name="contador" value="1">
                                    </form>
                                    <div class="card-footer d-flex justify-content-center align-items-center">
                                        <div class="mx-3">
                                            <a href="<?php echo $publicacion['archivo_Pub']; ?>" target="_blank" class="btn btn-primary btn-lg"  onclick="enviarFormulario()" >
                                                <i class="bi bi-file-pdf-fill mr-2"  ></i> Ver Archivo Adjunto
                                            </a>
                                        </div>
                                        <div class="mx-3">
                                            <a href="<?php echo $publicacion['archivo_Pub']; ?>" download class="btn btn-success"   onclick="enviarFormulario()"  >
                                                <i class="bi bi-cloud-arrow-down mr-2" style="font-size: 1.5em;"  ></i> <!-- Cambiar a otro icono de descarga -->
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

                                <!-- Formulario para agregar comentarios -->
                                <form action="comentar.php" method="POST">
                                    <div class="mb-3">
                                        <label for="comentario" class="form-label">Agregar Comentario:</label>
                                        <textarea class="form-control" id="comentario" name="comentario" rows="3" required></textarea>
                                    </div>
                                    <input type="hidden" name="id_publicacion" value="<?php echo $publicacion['idPub']; ?>">
                                    <button type="submit" class="btn btn-primary">Enviar Comentario</button>
                                </form>

                                <!-- Arreglo de rutas de imágenes aleatorias -->
                                <?php
                                $imagenes_aleatorias = array();

                                // Ruta base de las imágenes
                                $ruta_base = "../images/tigers/";

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
                                                <?php if($usuario != $comentario['idUsuario']){
                                                    echo '<a class="btn btn-sm btn-outline-danger " id="reportarCom" data-bs-toggle="modal" data-bs-target="#modal_report_c" data-comid="'.$comentario['idComent'].'">
                                                    <i class="bi bi-flag-fill"></i>
                                                </a>';
                                                } 
                                                ?>
                                                
                                                <!-- Botón para ver perfil -->
                                                <a class="btn btn-outline-info btn-sm shadow" href = "../administracion/Perfil/UsuarioDetalle.php?id=<?php echo $comentario['idUsuario']; ?>" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Ver Perfil">
                                                    <i class="bi bi-person-fill"></i>
                                                </a>
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
                                                    <h6 class="mb-1"><a class="link-dark link-underline link-underline-opacity-0" href="../administracion/Perfil/UsuarioDetalle.php?id=<?php echo $comentario['idUsuario']; ?>"><?php echo $comentario['nom_Us'] . " " . $comentario['apell_Us']; ?></a></h6>
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

        <!-- Ventana de reportar P -->
        <div class="modal fade" id="modal_report_p" tabindex="-1" aria-labelledby="modal_report_pl" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Reportar Publicacion</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="inputState validationCustom01" class="form-label">Motivo</label>
                        <select id="inputState validationCustom01 " class="form-select selepub" name="motivo">
                            <option selected>Seleccionar</option>
                            <option>Contenido inapropiado</option>
                            <option>Spam</option>
                            <option>No cumple con las normas de referenciado</option>
                        </select>
                        <br>
                        <label for="textAreaRp">Comentario</label>
                        <textarea name="Comentario" id="textAreaRp" cols="60" rows="2" placeholder="(Opcional)"></textarea>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="subir_reporte_p">Aceptar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <?php


        ?>

        <!-- VEntana reportar C -->
        <div class="modal fade" id="modal_report_c" tabindex="-1" aria-labelledby="modal_report_cl" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Reportar Comentario</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="inputState validationCustom01 " class="form-label">Motivo</label>
                        <select id="inputState validationCustom01 " class="form-select selecom" name="motivo">
                            <option selected>Seleccionar</option>
                            <option>Spam</option>
                            <option>Violencia</option>
                            <option>Acoso</option>
                            <option>Lenguaje inapropiado</option>
                        </select>
                        <br>
                        <label for="textAreaRp">Comentario</label>
                        <textarea name="Comentario" id="comentarioRc" cols="60" rows="2" placeholder="(Opcional)"></textarea>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="subir_reporte_c">Aceptar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <script src="reportar_pub.js"></script>
        <script>
        function enviarFormulario() {
            document.getElementById('consultaForm').submit();
        }
        </script>

    </div>
    </div>

    <script src="../js/fadeout.js"></script>

    <footer class="bg-primary py-2 bg-opacity-75 border-top border-terciary border-4 d-flex  align-items-center py-4 text-light bg-primary">
    <div></div>
    <div class="container mb-3 mt-2">
      <p class="mb-2 mt-2">&copy; 2024 BiblioTec - Todos los derechos reservados</p>
    </div>
  </footer>
</body>

</html>