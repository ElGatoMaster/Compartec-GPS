<?php
include('../../php/functions.php');
$link = include('../../php/conexion.php'); // Incluye el archivo de conexión y obtén la conexión

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


$usuario =  $_SESSION['idU'];

if(isset($_GET['id']) && $usuario == $_GET['id']){
    header("Location: infoperfil.php");
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
    header("Location: home.php");
  } 
}



// Verificar si se proporcionó un ID de publicación
if (isset($_GET['id'])) {
    // Obtener el ID de la publicación desde el parámetro GET
    $idUs = $_GET['id'];

    // Consultar la base de datos para obtener la información completa de la publicación
    $query = "SELECT u.idUsuario, u.nom_Us, u.apell_Us, u.carrera_Us FROM usuario u WHERE u.idUsuario = $idUs";
    $consulta = "SELECT * FROM publicacion
                            WHERE id_Usuario = '$idUs' and estado_Pub = 1";
    $result = mysqli_query($link, $query);
    $registro = mysqli_query($link, $consulta);
    $usuario = mysqli_fetch_assoc($result);

    // Verificar si se encontró la publicación
    if (mysqli_num_rows($result) == 1) {
        $publicacion = mysqli_fetch_assoc($result);
    } else {
        // Si no se encontró la publicación, redireccionar a la página principal
        header("Location: ../../home.php");
        exit();
    }
} else {
    // Si no se proporcionó un ID de publicación, redireccionar a la página principal
    header("Location: ../../home.php");
    exit();
}

$registros = mysqli_query($link, $consulta); // Utiliza la conexión obtenida desde el archivo de conexión

// Verifica si la consulta se ejecutó correctamente
if (!$registros) {
    die('Error en la consulta: ' . mysqli_error($link));
}
$idUSesion = $_SESSION['idU'];
//Consulta e insercion de insignias
$qInsignias = "SELECT idInsignia,COUNT(idInsignia) as 'cant' FROM `usuario_insignia` WHERE idUsuario = $idUs GROUP BY idInsignia ORDER BY idInsignia";
$qAnime = "SELECT idInsignia,idCalif FROM `usuario_insignia` WHERE idUsuario = $idUs and idCalif = $idUSesion order by idInsignia";

$res = mysqli_query($link,$qInsignias);
$resAn = mysqli_query($link,$qAnime);

$porfavor = array();
$plisss = array();



if(isset($_POST["agregar"])){
    $idins = $_POST["idin"];
    $qExist = "SELECT * FROM `usuario_insignia` where idUsuario = $idUs and idInsignia=$idins and idCalif=$idUSesion";

    $res2 = mysqli_query($link,$qExist);
    if(mysqli_num_rows($res2) == 0){
        $meter = "INSERT INTO `usuario_insignia` VALUES ($idins,$idUs,$idUSesion)";

        if(mysqli_query($link,$meter)){
            echo 'todo bien';
            $res = mysqli_query($link,$qInsignias);
        }else{
            echo '<script>
            alert("valio madre");
            window.location = "UsuarioDetalle.php?=$idUs";
             </script>';
        }

    }
}

// Cerrar la conexión a la base de datos
mysqli_close($link);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ComparTec - Home</title>

    <!--En esta seccion se incluyen las hojas de estilos-->
    <link rel="icon" href="../../images\icons\tigerF.png"><!--Esta seccion de codigo agrega un icono a la pagina-->
    <link rel="stylesheet" href="../../css/normalizar.css">
    <link rel="stylesheet" href="../../css/estilos.css">
    <link rel="stylesheet" href="../../css/hover-min.css">
    <link rel="stylesheet" href="../../css/animate.css">
    <link rel="stylesheet" href="../../css/sidebars.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
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
        .contenedor {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .imagen {
            height: 180px;
            margin-right: 93px;
        }

        .material-symbols-outlined {
            font-variation-settings:
                'FILL' 0,
                'wght' 300,
                'GRAD' 0,
                'opsz' 22
        }
    </style>

    <style>

        .badge-content {
            margin-top: 20px;
        }

        .trophy-icon {
            width: 120px;
            height: 120px;
        }

        .badge-title {
            font-weight: bold;
            color: #007bff;
        }

        #guardarBtn,
        #cancelarBtn {
            background-color: blue;
            color: white;
            border: 2px solid blue;
            border-radius: 8px;
            padding: 2px 5px;
            margin-right: 5px;
            cursor: pointer;
        }

        #guardarBtn:hover,
        #cancelarBtn:hover {
            background-color: darkblue;
            border-color: darkblue;
        }
    </style>
</head>

<!--IMAGEN DE CONTACTO-->
<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
    <symbol id="people-circle" viewBox="0 0 16 16">
        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
    </symbol>
</svg>

<body class="bg-body-secondary">
    <header class="bg-primary py-2 bg-opacity-75 border-bottom border-terciary border-4 d-flex flex-wrap align-items-center py-3 position-inherit">
    <div class="d-flex align-items-center">
      <!-- Logo y título -->
      <img src="../../images/icons/TecNM.png"  class="d-flex img-fluid" style="width: 145px; margin-right: 2.0vmax;">
      <img src="../../images/icons/tec.png" class="d-flex img-fluid" style="width: 60px;  margin-right: 2.0vmax;">
      <a href="" class="logo d-flex align-items-center mb-3 mb-md-0 link-body-emphasis text-decoration-none">
        <img src="../../images/icons/flamita.png" alt="Logo T - ComparTec" class="img-fluid">
        <h4><b><span class="col-1">Compar</span><span class="col-2">Tec</span></b></h4>
      </a>
    </div>
    <form action="../general_search.php" method="GET" id="searchForm" class="d-flex search-field">
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

    <div class="container-fluid">
        <div class="row">
            <!-- Barra de navegación izquierda -->
            <div class="flex-shrink-0 p-3 border-end border-terciary border-4 bg-body" style="width: 15%;">
                <ul class="list-unstyled" id="menu-lateral">
                    <li class="mb-2 mt-2">
                        <a class="nav-link align-items-center" href="../../home.php" id="letrabar" >Inicio</a>
                    </li>
                    <li class="mb-1">
                        <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed" id="letrabardos" data-bs-toggle="collapse" data-bs-target="#dashboard-collapse" aria-expanded="false" style="color: black; font-weight: bold;">
                            Carreras
                        </button>
                        <div class="collapse" id="dashboard-collapse">
                            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                <li><a href="../search.php?carrera=Arquitectura" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres">Arquitectura</a></li>
                                <li><a href="../search.php?carrera=Ing. Bioquimica" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres">Ingeniería Bioquímica</a></li>
                                <li><a href="../search.php?carrera=Ing. Civil" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Ingeniería Civil</a></li>
                                <li><a href="../search.php?carrera=Ing. Electrica" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Ingeniería Eléctrica</a></li>
                                <li><a href="../search.php?carrera=Ing. Gestion Empresarial" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Ing. en Gestión Empresarial</a></li>
                                <li><a href="../search.php?carrera=Ing. Sistemas Computacionales" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Ing. en Sistemas Computacionales</a></li>
                                <li><a href="../search.php?carrera=Ing. Industrial" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Ingeniería Industrial</a></li>
                                <li><a href="../search.php?carrera=Ing. Mecatronica" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Ingeniería Mecatrónica</a></li>
                                <li><a href="../search.php?carrera=Ing. Quimica" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Ingeniería Química</a></li>
                                <li><a href="../search.php?carrera=Lic. Administracion" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Licenciatura en Administración</a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="mb-1">
                        <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed" id="letrabardos" data-bs-toggle="collapse" data-bs-target="#contacto-collapse" aria-expanded="false" style="color: black; font-weight: bold;">
                            Contacto
                        </button>
                        <div class="collapse" id="contacto-collapse">
                            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                <li><a href="info_del_contacto.php?" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Información de contacto</a></li>
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
                                <li><a href="infoperfil.php" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Mi Perfil</a></li>
                                <a href="../../home.php?logout=true" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Cerrar Sesión</a>
                            </ul>
                        </div>
                    </li>
                    <hr class="my-2"> <!-- Línea divisora -->
                    <li class="mb-1 mt-3">
                        <a class="nav-link align-items-center" name="fade" href="../../publicacion/publicar.php" id="letrabardos" style="margin-left:10px">Nueva publicación</a>
                    </li>

                    <hr class="my-2"> <!-- Línea divisora -->
                    <li class="mb-1 mt-3">
                        <a class="nav-link align-items-center" href="#" id="letrabardos" style="margin-left:10px"><?php echo "Hola " . $_SESSION['nombre'] . " " . $_SESSION['apellido'] ?></a>
                    </li>
                </ul>
            </div>

            <!-- Contenido principal -->
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

            <!-- Contenido principal -->
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 main-content">
                <div class="contenedor">
                <div class="col-auto" style="margin-top: 2vmax;">
                    <?php if ($contador % 2 == 0) : ?>
                        <!-- Mostrar imagen aleatoria -->
                        <img src="<?php echo $imagenes_aleatorias[array_rand($imagenes_aleatorias)]; ?>" alt="Imagen Aleatoria" class="rounded-circle" width="200">
                    <?php else : ?>
                        <!-- Mostrar avatar -->
                        <img src="<?php echo $imagenes_aleatorias[array_rand($imagenes_aleatorias)]; ?>" alt="Imagen Aleatoria" class="rounded-circle" width="200">
                    <?php endif; ?>
                </div>

                    <h2>
                        <span id="nombreApellido"><?php echo $usuario['nom_Us'] . " " . $usuario['apell_Us'] ?></span>
                        </a>
                    </h2>

                    <h5>
                        <span id="nombreCarrera"><?php echo $usuario['carrera_Us'] ?></span>
                        </a>
                    </h5>

                    <!-- Modales de edición -->
                    <div class="modal fade" id="editarNombreModal" tabindex="-1" role="dialog" aria-labelledby="editarNombreModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editarNombreModalLabel">Editar Nombre</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <!-- Contenido del formulario de edición de nombre -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="editarCarreraModal" tabindex="-1" role="dialog" aria-labelledby="editarCarreraModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editarCarreraModalLabel">Editar Carrera</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <!-- Contenido del formulario de edición de carrera -->
                                    
                                </div>
                            </div>
                        </div>
                    </div>


                    <main role="main" class="col-md-9 ml-sm-auto col-lg-9 px-4">
                        <div class="container mt-2" >
                            <hr noshade="noshade">
                            <h3 class="text-center" style="margin-bottom: 20px;">Insignias</h3>
                            <div class="row row-cols-1 row-cols-md-4 g-6">

                                <div class="col">
                                <div class="card h-100 border-primary" style="width: 10rem;">
                                <button type="button" class="btn btn-sm mx-3 my-3" data-bs-toggle="popover" title="Descripcion" style="--bs-btn-padding-y: .01rem; --bs-btn-padding-x: 0rem; --bs-btn-font-size: .01rem; --bs-btn-color: var(--bs-blue)" data-bs-content="Para los Tigres que destacan en el aspecto académico.">
                                    <img class="img-fluid rounded-start" src="..\..\images\icons\tigre sabio.PNG" alt="Trophy Icon">
                                </button>
                                    <div class="card-body  text-center">
                                        <span class="card-title border-primary text-center">Tigre Sabio</span>
                                        <br>
                                        <?php
                                            $valedor = mysqli_fetch_array($resAn);
                                            if($valedor != null){
                                                if( $valedor['idInsignia'] == 1){
                                                    echo '<a class="btn btn-success btn-sm disabled" data-idin="1" disabled><i class="bi bi-plus-circle bi-sm"></i></a>';
                                                }else{
                                                    $plisss = $valedor;
                                                    echo '<a class="btn btn-success btn-sm" data-idin="1"><i class="bi bi-plus-circle bi-sm"></i></a>';
                                                }
                                            }else{
                                                echo '<a class="btn btn-success btn-sm" data-idin="1"><i class="bi bi-plus-circle bi-sm"></i></a>';
                                                
                                            }
                                        ?>
                                    </div>
                                    <div class="card-footer border-primary text-center">
                                        <?php   
                                            $usInsignias = mysqli_fetch_array($res);
                                            
                                            if($usInsignias!=null ){
                                                if($usInsignias[0]!=1){
                                                    $porfavor = $usInsignias;
                                                    echo 0;
                                                }else{
                                                    echo $usInsignias['cant'];
                                                }
                                            }else{
                                                echo 0;
                                            }     
                                        ?>
                                    </div>
                                </div>
                                </div>

                                <div class="col">
                                <div class="card h-100 border-primary" style="width: 10rem;">
                                <button type="button" class="btn btn-sm mx-3 my-3" data-bs-toggle="popover" title="Descripcion" style="--bs-btn-padding-y: .01rem; --bs-btn-padding-x: .0rem; --bs-btn-font-size: .1rem; --bs-btn-color: var(--bs-blue)" data-bs-content="Aquellos Tigres que dejan una huella de calidad con sus aportaciones.">
                                    <img class="img-fluid rounded-start" src="..\..\images\icons\huelladecalidad.PNG" alt="Trophy Icon">        
                                </button>
                                    <div class="card-body text-center">
                                        <span class="card-title  text-center">Huella de Calidad</span>
                                        <br>
                                        <?php
                                            if($plisss != null && $plisss['idInsignia'] == 2 ){
                                                echo '<a class="btn btn-success btn-sm disabled" data-idin="2" disabled><i class="bi bi-plus-circle bi-sm"></i></a>';
                                            }else{
                                                $valedor = mysqli_fetch_array($resAn);
                                                if($valedor != null){
                                                    if( $valedor['idInsignia'] == 2){
                                                        echo '<a class="btn btn-success btn-sm disabled" data-idin="2" disabled><i class="bi bi-plus-circle bi-sm"></i></a>';
                                                    }else{
                                                        $plisss = $valedor;
                                                        echo '<a class="btn btn-success btn-sm" data-idin="2"><i class="bi bi-plus-circle bi-sm"></i></a>';
                                                    }
                                                }else{
                                                    echo '<a class="btn btn-success btn-sm" data-idin="2"><i class="bi bi-plus-circle bi-sm"></i></a>';
                                                }
                                            }
                                            
                                        ?>
                                    </div>
                                    <div class ="card-footer border-primary text-center">
                                        <!-- seccion para agregar la cantidad de insignias -->
                                        <?php   
                                            if($porfavor!=null && $porfavor[0]==2){
                                                echo $porfavor[1];
                                            }else{
                                                $usInsignias = mysqli_fetch_array($res);
                                                if($usInsignias!=null ){
                                                    if($usInsignias[0]!=2){
                                                        $porfavor = $usInsignias;
                                                        echo 0;
                                                    }else{
                                                        echo $usInsignias[1];
                                                    }
                                                }else{
                                                    echo 0;
                                                } 
                                            }
                                        ?>
                                    </div>
                                </div>
                                </div>

                                <div class="col">
                                <div class="card h-100 border-primary" style="width: 10rem;">
                                <button type="button" class="btn btn-sm mx-3 my-3" data-bs-toggle="popover" title="Descripcion" style="--bs-btn-padding-y: .01rem; --bs-btn-padding-x: .0rem; --bs-btn-font-size: .1rem; --bs-btn-color: var(--bs-blue)" data-bs-content="Para aquellos Tigres que están dispuestos a ayudar y apoyan a la comunidad">
                                        <img class="img-fluid rounded-start " src="..\..\images\icons\tigre amigo.png" alt="Trophy Icon">
                                </button>
                                    <div class="card-body text-center">
                                        <span class="card-title">Tigre Amigo</span>
                                        <br>
                                        <?php
                                            if($plisss != null && $plisss['idInsignia'] == 3 ){
                                                echo '<a class="btn btn-success btn-sm disabled" data-idin="3" disabled><i class="bi bi-plus-circle bi-sm"></i></a>';
                                            }else{
                                                $valedor = mysqli_fetch_array($resAn);
                                                if($valedor != null){
                                                    if($valedor['idInsignia'] == 3){
                                                        echo '<a class="btn btn-success btn-sm disabled" data-idin="3" disabled><i class="bi bi-plus-circle bi-sm"></i></a>';
                                                    }else{
                                                        $plisss = $valedor;
                                                        echo '<a class="btn btn-success btn-sm" data-idin="3"><i class="bi bi-plus-circle bi-sm"></i></a>';
                                                    }
                                                }else{
                                                    echo '<a class="btn btn-success btn-sm" data-idin="3"><i class="bi bi-plus-circle bi-sm"></i></a>';
                                                }
                                            }
                                        ?>
                                        
                                    </div>
                                    <div class ="card-footer border-primary text-center">
                                        <?php   
                                            if($porfavor!=null && $porfavor[0]==3){
                                                echo $porfavor[1];
                                            }else{
                                                $usInsignias = mysqli_fetch_array($res);
                                                if($usInsignias!=null ){
                                                    if($usInsignias[0]!=3){
                                                        $porfavor = $usInsignias;
                                                        echo 0;
                                                    }else{
                                                        echo $usInsignias[1];
                                                    }
                                                }else{
                                                    echo 0;
                                                } 
                                            }    
                                        ?>
                                    </div>
                                </div>
                                </div>

                                <div class="col">
                                <div class="card h-100 border-primary" style="width: 10rem;">
                                <button type="button" class="btn btn-sm mx-3 my-3" data-bs-toggle="popover" title="Descripcion" style="--bs-btn-padding-y: .01rem; --bs-btn-padding-x: .0rem; --bs-btn-font-size: .1rem; --bs-btn-color: var(--bs-blue)" data-bs-content="Un Tigre que tiene mucha experiencia en la plataforma">
                                    <img class="img-fluid rounded-start " src="..\..\images\icons\tigre veterano.PNG" alt="Trophy Icon">
                                </button>
                                    <div class="card-body text-center">
                                        <span class="card-title">Tigre Veterano</span>
                                        <br>
                                        <?php
                                            if($plisss != null && $plisss['idInsignia'] == 4){
                                                echo '<a class="btn btn-success btn-sm disabled" data-idin="4" disabled><i class="bi bi-plus-circle bi-sm"></i></a>';
                                            }else{
                                                $valedor = mysqli_fetch_array($resAn);
                                                if($valedor != null){
                                                    if( $valedor['idInsignia'] == 4){
                                                        echo '<a class="btn btn-success btn-sm disabled" data-idin="4" disabled><i class="bi bi-plus-circle bi-sm"></i></a>';
                                                        
                                                    }else{
                                                        $plisss = $valedor;
                                                        echo '<a class="btn btn-success btn-sm" data-idin="4"><i class="bi bi-plus-circle bi-sm"></i></a>';
                                                         
                                                    }
                                                }else{
                                                    echo '<a class="btn btn-success btn-sm" data-idin="4"><i class="bi bi-plus-circle bi-sm"></i></a>';
                                                }
                                            }
                                        ?>
                                    </div>
                                    <div class ="card-footer border-primary text-center">
                                        <?php   
                                            if($porfavor!=null && $porfavor[0]==4){
                                                echo $porfavor[1];
                                            }else{
                                                $usInsignias = mysqli_fetch_array($res);
                                                if($usInsignias!=null ){
                                                    if($usInsignias[0]!=4){
                                                        $porfavor = $usInsignias;
                                                        echo 0;
                                                    }else{
                                                        echo $usInsignias[1];
                                                    }
                                                }else{
                                                    echo 0;
                                                } 
                                            }    
                                        ?>
                                    </div>
                                </div>
                                </div>

                            </div> <!-- row de las insignias(cards) -->
                        </div><!-- otras cosas -->
                        <script>
                            $(document).ready(function(){
                                $(".btn-success").click(function(){
                                    var idin = $(this).data('idin');
                                    var $boton = $(this);
                                    $.ajax({
                                        URL: "UsuarioDetalle.php",
                                        method:"POST",
                                        data: {idin:idin,agregar:1},
                                        success:function(r){
                                            $boton.addClass('disabled');
                                            $boton.prop('disabled',true);
                                            $boton.removeClass('.btn-success');
                                            console.log("SI");
                                            location.reload();
                                        }
                                    });

                                });
                            });
                        </script>
                        <script>
                            const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
                            const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))
                        </script>




                        <hr noshade="noshade"><br>
                        <h3 class="mb-5">Publicaciones de <?php echo $usuario['nom_Us'] . " " . $usuario['apell_Us'] ?></h3>
                </div>

                <?php while ($fila = mysqli_fetch_array($registro)) : ?>
                    <div class="publicacion card mb-3">
                        <div class="card-body">

                            <!-- Boton editar -->

                            <h3 class="card-title display-6"><b><?php echo $fila['titulo_Pub']; ?></b></h3>

                            <p class="card-text lead"><?php echo $fila['descrip_Pub']; ?></p>

                            <a name="fade" href="../../publicacion/publicacion_detalle.php?id=<?php echo $fila['idPub']; ?>" class="btn btn-primary btn-sm"><b>Leer más</b></a>
                        </div>
                        <div class="bg-primary py-2 bg-opacity-10 card-footer d-flex text-muted justify-content-between align-items-end">
                            <span class="card-text comment-date mb-0 ">Publicado por: <?php echo $usuario['nom_Us'] . " " . $usuario['apell_Us']; ?></span>
                            <span class="card-text comment-date mb-0"><?php echo functions::convertirFecha($fila['fecha_Pub']); ?></span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </main>
            </main>
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
                                <button class="btn btn-primary" id="ConfirmarEliminar">Eliminar</button>
                            </div>
                        </div>
                    </div>
                    <script src="../../publicacion/eliminar2.js">
                    </script>
                </div>
            </div>
        </div>

        <footer class="bg-primary py-2 bg-opacity-75 border-top border-terciary border-4  py-3 text-light bg-primary">
            <div class="container">
                <p class="mb-0">&copy; 2024 ComparTec - Todos los derechos reservados</p>
            </div>
        </footer>
</body>

</html>