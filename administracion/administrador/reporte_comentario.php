<?php
// Incluir el archivo de conexión a la base de datos
$link = include('../../php/conexion.php');

// Verificar si se proporcionó un ID de publicación
if (isset($_GET['id'])) {
    // Obtener el ID de la publicación desde el parámetro GET
    $idComent = $_GET['id'];

    // Consultar la base de datos para obtener la información completa de la publicación
    $query = "SELECT c.*, u.nom_Us, u.apell_Us FROM comentario c
              JOIN usuario u ON c.idUsuario = u.idUsuario
              WHERE c.idComent = $idComent";
    $query2 = "SELECT * FROM reportecomentario WHERE idComent = $idComent";
    $result = mysqli_query($link, $query);
    $registro = mysqli_query($link, $query2);
    $fila = mysqli_fetch_array($registro);

    // Verificar si se encontró la publicación
    if (mysqli_num_rows($result) == 1) {
        $comentario = mysqli_fetch_assoc($result);
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
// Verificar si se proporcionó un ID de comentario a eliminar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_comentario'])) {
    if (isset($_POST['idComentario'])) {
        $idComentarioEliminar = $_POST['idComentario'];

        // Eliminar los registros relacionados en la tabla reportecomentario
        $queryEliminarReportes = "DELETE FROM reportecomentario WHERE idComent = $idComentarioEliminar";
        mysqli_query($link, $queryEliminarReportes);

        // Luego puedes eliminar el comentario de la tabla comentario
        $queryEliminarComentario = "DELETE FROM comentario WHERE idComent = $idComentarioEliminar";
        mysqli_query($link, $queryEliminarComentario);

        // Redirigir a la página rep_comentario_pendiente.php
        header("Location: rep_comentario_pendiente.php");
        exit(); // Terminar el script para evitar que se ejecute más código después de la redirección
    } else {
        echo "Error al intentar eliminar el comentario.";
    }
}
// Agregar condición para manejar el rechazo del reporte
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['RechazarReporte'])) {
    // Eliminar el reporte de la base de datos
    $queryEliminarReporte = "DELETE FROM reportecomentario WHERE idComent = $idComent";
    if (mysqli_query($link, $queryEliminarReporte)) {
        // Redirigir a la página rep_comentario_pendiente.php
        header("Location: rep_comentario_pendiente.php");
        exit(); // Terminar el script para evitar que se ejecute más código después de la redirección
    } else {
        echo "Error al eliminar el reporte: " . mysqli_error($link);
    }
}




// Cerrar la conexión a la base de datos
mysqli_close($link);

// Iniciar sesión
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
    <title>ComparTec - Reporte comentario</title>

   

    <!--En esta seccion se incluyen las hojas de estilos-->
    <link rel="icon" href="../../images/icons/tigerF.png"><!--Esta seccion de codigo agrega un icono a la pagina-->
    <link rel="stylesheet" href="../../css/normalizar.css">
    <link rel="stylesheet" href="../../css/estilos.css">
    <link rel="stylesheet" href="../../css/hover-min.css">
    <link rel="stylesheet" href="../../css/animate.css">
    <link rel="stylesheet" href="../../css/sidebars.css">
    <link rel="stylesheet" href="../../publicacion/style.css">
    <link rel="stylesheet" href="../../css\estiloReg.css">
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


            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4" style="font-weight:normal; margin-left: 1.5%;">
                
                    <div class="container mt-5 mb-6" >
                        <div class="row" >
                            <div class="col-md-11" style="margin-left: 4%;">
                                <!-- Detalles de la publicación -->
                                <div class="card card-details bg-danger-subtle">
                                    <div class="card-header bg-danger">
                                    </div>
                                    
                                    <!-- Botón para ver archivo adjunto -->
                                    <div class="row"  >
                                        <div class="col  mt-1 mb-2" style="color: black;">
                                            <h3 class="pl-5 mb-2 ml-2" style="margin-left: 2vmax;">Reporte de comentario</h3>
                                            <h5><Span style="margin-left: 2vmax;"><b>Tipo de reporte: </b></Span><span><?php echo $fila['motivo_Report'];?></span></h5> 
                                        </div>
                                        <div class="col text-end mt-4 mb-3  align-items-end" style="margin-right: 2vmax;">
                                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#reg-modalR">
                                         <i class="bi bi-check-all mr-2"></i> Rechazar reporte
                                        </button>
                                            <a href="#" class="btn btn-outline-danger btn-sm" onclick="eliminarComentario(<?php echo $comentario['idComent']; ?>)">
                                                <i class="bi bi-trash3 mr-2"></i> Eliminar comentario
                                            </a>
                                            <a href="publicacion_detalle_admin.php?id=<?php echo $comentario['idPub']; ?>" class="btn btn-outline-info btn-sm">
                                            <i class="bi bi-file-earmark mr-2"></i> Ir a la publicacion
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
   
   <!-- borrar comentario -->
   <script>
    function eliminarComentario(idComentario) {
        if (confirm("¿Estás seguro de que quieres eliminar este comentario?")) {
            // Crear un formulario para enviar el ID del comentario a eliminar
            var form = document.createElement("form");
            form.setAttribute("method", "post");
            form.setAttribute("action", "");

            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", "idComentario");
            hiddenField.setAttribute("value", idComentario);

            var hiddenField2 = document.createElement("input");
            hiddenField2.setAttribute("type", "hidden");
            hiddenField2.setAttribute("name", "eliminar_comentario");
            hiddenField2.setAttribute("value", "true");

            form.appendChild(hiddenField);
            form.appendChild(hiddenField2);

            document.body.appendChild(form);
            form.submit();
        }
    }

    //borrar comentario -->
    //rechazar reporte -->
    

</script>
<!-- /rechazar reporte -->

                                    
                                    <!-- COMENTARIO REPORTADO -->
                                        <div class="card mt-5 mb-5" style="height: 40%; width: 92%; margin-left: 4%;">
                                            <div class="card-header bg-danger text-light">
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-auto">
                                                        
                                                    </div>
                                                    <div class="col">
                                                        <h3 class="mb-4"><b>Usuario: <?php echo $comentario['nom_Us']; ?>  <?php echo $comentario['apell_Us'];?></b></h3>
                                                        <textarea class="form-control mb-4" id="comentario" name="comentario" rows="3" disabled><?php echo $comentario['text_Coment']; ?></textarea>
                                                        <small class="text-muted mb-2">Fecha de Reporte: <?php echo $fila['fecha_Report']; ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <form method="POST">
                                        <div class="modal fade" id="reg-modalR" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
                                           <div class="modal-dialog">
                                           <div class="modal-content">
                                                <div class="modal-header">
                                                   <h5 class="modal-title" id="modal-title">Confirmar Rechazo</h5>
                                                   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                              <div class="modal-body">
                                              <p>¿Estás seguro de querer rechazar el reporte de este comentario?</br>
                                               Esta acción no se podrá deshacer.</p>
                                            </div>
                                            <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary" name="RechazarReporte">Aceptar</button>
                                           </div>
                                           </div>
                                          </div>
                                        </div>
                                        </form>

                            
                    
            </main>
        </div>
    </div>

   

    <script src ="../../js/fadeout.js"></script>
            
    <footer class="bg-primary py-2 bg-opacity-75 border-top border-terciary border-4 py-3 text-light bg-primary">
        <div class="container" >
            <p class="mb-1">&copy; 2024 ComparTec - Todos los derechos reservados</p>
        </div>
    </footer>
</body>
</html>