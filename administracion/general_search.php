<?php
include('../php/functions.php');
$link = include('../php/conexion.php'); // Incluye el archivo de conexión y obtén la conexión

// Inicia la sesión después de cerrar la conexión
session_start();
$usuario =  $_SESSION['idU'];




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
    header("Location: general_search.php?dataSearch=".$_GET["dataSearch"]);;
    } 
}



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

// Verificar si se ha enviado el formulario de búsqueda
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Obtener el término de búsqueda del formulario
    $searchTerm = $_GET["dataSearch"];


    // Consulta para buscar coincidencias con usuarios
    $consultaUsuarios = "SELECT * FROM usuario
    WHERE CONCAT(nom_Us, ' ', apell_Us) LIKE '%$searchTerm%'";

    $resultUsuarios = mysqli_query($link, $consultaUsuarios); // Utiliza la conexión obtenida desde el archivo de conexión

    // Consulta para buscar coincidencias en publicaciones
    $consultaPublicaciones = "SELECT p.*, u.nom_Us, u.apell_Us FROM publicacion p
     JOIN usuario u ON p.id_Usuario = u.idUsuario
     WHERE (p.titulo_Pub LIKE '%$searchTerm%'
     OR CONCAT(u.nom_Us, ' ', u.apell_Us) LIKE '%$searchTerm%'
     OR p.idPub IN (
         SELECT idPub FROM tag_publicacion WHERE nombreTag LIKE '%$searchTerm%'
     ) ) and p.estado_Pub = 1
     ORDER BY p.idPub DESC";

    $resultPublicaciones = mysqli_query($link, $consultaPublicaciones); // Utiliza la conexión obtenida desde el archivo de conexión

    // Verifica si alguna de las consultas se ejecutó correctamente
    if (!$resultUsuarios || !$resultPublicaciones) {
        die('Error en la consulta: ' . mysqli_error($link));
    }

    // Cierra la conexión después de realizar las consultas
    mysqli_close($link);
    $numResultadosUsuarios = mysqli_num_rows($resultUsuarios);
    $numResultadosPublicaciones = mysqli_num_rows($resultPublicaciones);
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ComparTec - Home</title>

    <!--En esta seccion se incluyen las hojas de estilos-->
    <link rel="icon" href="../images/icons/tigerF.png"><!--Esta seccion de codigo agrega un icono a la pagina-->
    <link rel="stylesheet" href="../css/normalizar.css">
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/hover-min.css">
    <link rel="stylesheet" href="../css/animate.css">
    <link rel="stylesheet" href="../css/sidebars.css">
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
      <img src="../images/icons/TecNM.png"  class="d-flex img-fluid" style="width: 145px; margin-right: 2.0vmax;">
      <img src="../images/icons/tec.png" class="d-flex img-fluid" style="width: 60px;  margin-right: 2.0vmax;">
      <a href="" class="logo d-flex align-items-center mb-3 mb-md-0 link-body-emphasis text-decoration-none">
        <img src="../images/icons/flamita.png" alt="Logo T - ComparTec" class="img-fluid">
        <h4><b><span class="col-1">Compar</span><span class="col-2">Tec</span></b></h4>
      </a>
    </div>
    <form action="general_search.php" method="GET" id="searchForm" class="d-flex search-field">
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
            <div class="flex-shrink-0 p-3 border-end border-terciary border-4 bg-body" style="width: 15%;">
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
                                <li><a href="search.php?carrera=Arquitectura" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres">Arquitectura</a></li>
                                <li><a href="search.php?carrera=Ing. Bioquimica" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres">Ingeniería Bioquímica</a></li>
                                <li><a href="search.php?carrera=Ing. Civil" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Ingeniería Civil</a></li>
                                <li><a href="search.php?carrera=Ing. Electrica" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Ingeniería Eléctrica</a></li>
                                <li><a href="search.php?carrera=Ing. Gestion Empresarial" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Ing. en Gestión Empresarial</a></li>
                                <li><a href="search.php?carrera=Ing. Sistemas Computacionales" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Ing. en Sistemas Computacionales</a></li>
                                <li><a href="search.php?carrera=Ing. Industrial" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Ingeniería Industrial</a></li>
                                <li><a href="search.php?carrera=Ing. Mecatronica" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Ingeniería Mecatrónica</a></li>
                                <li><a href="search.php?carrera=Ing. Quimica" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Ingeniería Química</a></li>
                                <li><a href="search.php?carrera=Lic. Administracion" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Licenciatura en Administración</a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="mb-1">
                        <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed" id="letrabardos" data-bs-toggle="collapse" data-bs-target="#contacto-collapse" aria-expanded="false" style="color: black; font-weight: bold;">
                            Contacto
                        </button>
                        <div class="collapse" id="contacto-collapse">
                            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                <li><a href="Perfil/info_del_contacto.php" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Información de contacto</a></li>
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
                                <li><a href="Perfil/infoperfil.php" class="link-body-emphasis d-inline-flex text-decoration-none rounded" id="letrabartres" style="color: black;">Mi Perfil</a></li>
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
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 main-content">
                <!-- Pestañas -->
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item bg-dark-subtle" role="presentation">
                        <button class="nav-link active" id="publicaciones-tab" data-bs-toggle="tab" data-bs-target="#publicaciones" type="button" role="tab" aria-controls="publicaciones" aria-selected="true">Publicaciones</button>
                    </li>
                    <li class="nav-item bg-dark-subtle" role="presentation">
                        <button class="nav-link" id="usuarios-tab" data-bs-toggle="tab" data-bs-target="#usuarios" type="button" role="tab" aria-controls="usuarios" aria-selected="false">Usuarios</button>
                    </li>
                </ul>

                <!-- Contenido de las pestañas -->
                <div class="tab-content" id="myTabContent">
                    <!-- Pestaña de Publicaciones -->
                    <div class="tab-pane fade show active" id="publicaciones" role="tabpanel" aria-labelledby="publicaciones-tab">
                        <div class="container mt-3">
                            <h2 style="user-select: none;font-size: 2vmax;"><b><?php echo functions::conversionTexto($numResultadosPublicaciones, "P") . "para: \"$searchTerm\"" ?></b></h2>
                            <div class="linea-delgada"></div>
                            <!-- Contenido de las publicaciones -->
                            <?php if (mysqli_num_rows($resultPublicaciones) > 0) : ?>
                                <?php while ($fila = mysqli_fetch_array($resultPublicaciones)) : ?>
                                    <div class="publicacion mt-4 card mb-4">
                                        <div class="card-body">
                                            <h3 class="card-title display-6"><b><?php echo $fila['titulo_Pub']; ?></b></h3>
                                            <p class="card-text lead"><?php echo $fila['descrip_Pub']; ?></p>
                                            <a name="fade" href="../publicacion/publicacion_detalle.php?id=<?php echo $fila['idPub']; ?>" class="btn btn-primary btn-sm"><b>Leer más</b></a>
                                        </div>
                                        <div class="card-footer d-flex text-muted justify-content-between align-items-end">
                                            <span class="card-text comment-date mb-0">Publicado por: <?php echo $fila['nom_Us'] . " " . $fila['apell_Us']; ?></span>
                                            <span class="card-text comment-date mb-0">Fecha de publicación: <?php echo functions::convertirFecha($fila['fecha_Pub']); ?></span>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <div class="alert alert-warning" role="alert">
                                    No se encontraron publicaciones.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

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

                    <!-- Pestaña de Usuarios -->
                    <div class="tab-pane fade" id="usuarios" role="tabpanel" aria-labelledby="usuarios-tab">
                        <div class="container mt-3">
                            <h2 style="user-select: none;font-size: 2vmax; margin-top: 0.5vmax;"><b><?php echo functions::conversionTexto($numResultadosUsuarios, "U") . "para: \"$searchTerm\"" ?></b></h2>
                            <!-- Contenido de los usuarios -->
                            <?php if (mysqli_num_rows($resultUsuarios) > 0) : ?>
                                <?php while ($fila = mysqli_fetch_array($resultUsuarios)) : ?>
                                    <div class="card mb-4 mt-4">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <!-- Mostrar imagen aleatoria -->
                                                    <img src="<?php echo $imagenes_aleatorias[array_rand($imagenes_aleatorias)]; ?>" alt="Imagen Aleatoria" class="rounded-circle" width="50">
                                                </div>
                                                <div class="col">
                                                    <h6 class="mb-1"><a class="link-dark link-underline link-underline-opacity-0" href="../administracion/Perfil/UsuarioDetalle.php?id=<?php echo $fila['idUsuario']; ?>"><?php echo $fila['nom_Us'] . " " . $fila['apell_Us']; ?></a></h6>
                                                </div>
                                                <div class="col-auto">
                                                    <!-- Botón para ir al perfil con icono de usuario -->
                                                    <a href="Perfil/UsuarioDetalle.php?id=<?php echo $fila['idUsuario']; ?>" class="btn btn-primary btn-sm">
                                                        <i class="bi bi-person"></i> Ver Perfil
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <div class="alert alert-warning" role="alert">
                                    No se encontraron usuarios.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>


        </div>
    </div>


    <script src="../js/fadeout.js"></script>
    <footer class="bg-primary py-2 bg-opacity-75 border-top border-terciary border-4 d-flex  align-items-center py-4.5 text-light bg-primary">
    <div></div>
    <div class="container mb-3 mt-3">
      <p class="mb-2 mt-2">&copy; 2024 ComparTec - Todos los derechos reservados</p>
    </div>
  </footer>

</body>

</html>