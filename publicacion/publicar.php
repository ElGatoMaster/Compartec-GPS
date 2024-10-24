<?php
include('../php/functions.php');
$link = include('../php/conexion.php'); // Incluye el archivo de conexión y obtén la conexión

// Inicia la sesión después de cerrar la conexión
session_start();
$nombreUS =  strstr($_SESSION['email'], '@', true);
$usuario =  $_SESSION['idU'];

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

$queryCarrera = "SELECT nomCarrera FROM carrera";
$res = mysqli_query($link, $queryCarrera); // Utiliza la conexión obtenida desde el archivo de conexión

// Verifica si la consulta se ejecutó correctamente
if (!$res) {
  die('Error en la consulta: ' . mysqli_error($link));
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
    header("Location: publicar.php");;
  } 
}


// Cierra la conexión después de realizar la consulta
mysqli_close($link);



?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ComparTec - Publicacion</title>

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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
  <!--Termmina Bootstrap-->

  <!--iconos-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,1,0" />
  <script src="tag.js"></script>


  <!--SCRIPT PARA ACTUALIZAR COMBO BOX-->
  <script language="javascript">
    $(document).ready(function() {
      $("#cbx_carrera").change(function() {

        $("#cbx_carrera option:selected").each(function() {
          nomCarrera = $(this).val();
          $.post("../php/materia.php", {
            nomCarrera: nomCarrera
          }, function(data) {
            $("#cbx_materia").html(data);
          });
        });
      })
    });
  </script>

</head>

<style>
  .material-symbols-outlined {
    font-variation-settings:
      'FILL' 0,
      'wght' 400,
      'GRAD' -25,
      'opsz' 2
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
            <a class="nav-link align-items-center" name="fade" href="publicar.php" id="letrabardos" style="margin-left:10px">Nueva publicación</a>
          </li>

          <hr class="my-2"> <!-- Línea divisora -->
          <li class="mb-1 mt-3">
            <a class="nav-link align-items-center" href="#" id="letrabardos" style="margin-left:10px"><?php echo "Hola " . $_SESSION['nombre'] . " " . $_SESSION['apellido'] ?></a>
          </li>
        </ul>
      </div>
      <!-- Contenido principal -->


      <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 bg-body-secondary" style="margin-left: 0.9%;">
        <div class="container" style="margin-top:1vmax; align-items: center;">
          <h2 style="margin-left: 0.1%; margin-bottom: 1vmax;">
            <span class="material-symbols-outlined"> library_add </span>
            <b style="margin-left:0.5vmax;" class="textogran">Nueva Publicación</b>
          </h2>
          <div class="linea-delgada"></div>

          <form class="row g-3 mt-2 mb-2vmax needs-validation" method="POST" action="publicar_control.php" enctype="multipart/form-data" novalidate>
            <!-- Título -->
            <div class="col-md-8 mt-0">
              <label for="inputEmail4" class="form-label" id="letraform"><b>Título * :</b></label>
              <input type="text" autocomplete="off" name="titulo" id="validationCustom07" class="form-control bs-primary-rgb" style="border-color: rgb(179, 179, 179);" required>
              <div class="invalid-feedback">
                Ingrese un título para esta publicación.
              </div>
            </div>
            <!-- Usuario -->
            <div class="col-md-4 mt-0 mb-1">
              <label for="inputPassword4" class="form-label" id="letraform"><b>Usuario * :</b></label>
              <input class="form-control" style="border-color: rgb(179, 179, 179); font-weight: 500;" type="text" placeholder="@<?php echo $nombreUS ?>" aria-label="Disabled input example" disabled>
            </div>

            <!-- Descripción -->
            <div class="mb-2 mt-3">
              <label for="exampleFormControlTextarea1" class="form-label" id="letraform"><b>Descripción (en caso de ser Recurso Bibliográfico agregar aquí los datos de referencia) :</b></label>
              <textarea class="form-control" name="descripcion" autocomplete="off" placeholder="Ej. Autor del libro: Ramirez, M. (2008)" id="floatingTextarea2" style="height: 100px; resize: none;"></textarea>
            </div>

            <!-- Carrera -->
            <div class="col-md-5 mt-0">
              <label for="colFormLabelSm" class="col-sm-3 col-form-label col-form-label-sm" id="letraform"><b>Carrera * :</b></label>
              <div class="col-sm-11">
                <select class="form-select" id="cbx_carrera" name="cbx_carrera" aria-label="Default select example" style="border-color: rgb(179, 179, 179);" required>
                  <option selected disabled value="">Selecciona carrera...</option>
                  <?php while ($ROW = $res->fetch_assoc()) { ?>
                    <option value="<?php echo $ROW['nomCarrera']; ?>"><?php echo $ROW['nomCarrera']; ?></option>
                  <?php } ?>
                </select>
                <div class="invalid-feedback">
                  Seleccione una carrera.
                </div>
              </div>
            </div>

            <!-- Materia -->
            <div class="col-md-7 mt-0 mb-1">
              <label for="colFormLabelSm" class="col-sm-2 col-form-label col-form-label-sm" id="letraform"><b>Materia * :</b></label>
              <div class="col-sm-12">
                <select class="form-select" id="cbx_materia" name="cbx_materia" aria-label="Default select example" style="border-color: rgb(179, 179, 179);" required>
                  <option selected disabled value="">Selecciona materia...</option>
                </select>
                <div class="invalid-feedback">
                  Seleccione una materia
                </div>
              </div>
            </div>

            <!-- Etiquetas -->
            <div class="col-md-8 mt-3 mb-2">
              <label for="inputEmail4" class="form-label" id="letraform"><b>Etiquetas (hasta 5, separadas por #) * :</b></label>
              <input value="#" type="text" name="etiquetas" autocomplete="off" class="form-control bs-primary-rgb" style="border-color: rgb(179, 179, 179);" required>
              <div class="invalid-feedback">
                Debes ingresar al menos una etiqueta y no más de 5, separadas por '#'.
              </div>
            </div>

            <!-- Tipo de Publicación -->
            <fieldset class="row g-2 mb-2 mt-2">
              <legend class="col-form-label col-sm-2 pt-0 " id="letraradio" style="margin-left: 0.2%;"><b>Tipo de Publicación* :</b></legend>
              <div class="col-md-6">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="tipo" id="gridRadios1" style="border-color: rgb(179, 179, 179); margin-left: 1%;" value="Recurso Bibliografico" required>
                  <label class="form-check-label" for="gridRadios1" id="letraradio">
                    Recurso Bibliográfico
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" style="border-color: rgb(179, 179, 179); margin-left: 1%;" name="tipo" id="gridRadios2" value="Trabajos y tareas" required>
                  <label class="form-check-label" for="gridRadios2" id="letraradio">
                    Trabajos y tareas
                  </label>
                  <div class="invalid-feedback">
                    Seleccione el tipo de publicación
                  </div>
                </div>
              </div>
            </fieldset>

            <!-- Documento -->
            <div class="mb-3 mt-1">
              <label for="formFileLg" class="form-label" id="letraform"><b>Documento* :</b>
                <button type="button" class="btn" data-bs-toggle="popover" title="Restricciones de documentos" style="--bs-btn-padding-y: .01rem; --bs-btn-padding-x: .0rem; --bs-btn-font-size: .1rem; --bs-btn-color: var(--bs-blue)" data-bs-content="Solo los archivos .pdf, .doc, .docx, .ppt, .ccv están permitidos. No más de 100MB">
                  <span class="material-symbols-outlined">help </span>
                </button>
              </label>
              <input class="form-control form-control-lg" onchange="checkFileSize(this)" accept=".pdf, .doc, .docx, .pptx, .ccv" name="archivo" type="file" id="formFileLg" style="border-color: rgb(179, 179, 179);" required>
              <div class="invalid-feedback">
                Por favor selecciona un archivo.
              </div>
              <div id="errorMessage" style="color: red;"></div>
            </div>

            <!-- Botón Publicar -->
            <div class="d-grid gap-1 col-6 mx-auto mb-2">
              <input class="btn btn-primary btn mb-3" type="button" value="Publicar" id="letrabuton" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
            </div>

            <!-- POPUP-->
            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Confirmación de publicación</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    ¿Estás seguro de realizar la publicación?
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" value="Enviar" class="btn btn-primary" data-bs-dismiss="modal">Sí, publicar</button>
                  </div>
                </div>
              </div>
            </div>
          </form>

          <!-- Script para validación -->
          <script>
            (() => {
              'use strict'
              const forms = document.querySelectorAll('.needs-validation')

              Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                  if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                  }

                  form.classList.add('was-validated')
                }, false)
              })
            })()
          </script>

          <!--Script para POPOVERS-->
          <script>
            const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
            const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))
          </script>

          <!--Script para popup confirmación-->
          <script>
            const myModal = document.getElementById('myModal')
            const myInput = document.getElementById('myInput')

            myModal.addEventListener('shown.bs.modal', () => {
              myInput.focus()
            })
          </script>

          <!--Script para validar tamaño-->
          <script>
            function checkFileSize(input) {
              var maxSize = 100 * 1024 * 1024; // Tamaño máximo permitido en bytes (2 MB)
              if (input.files && input.files[0]) {
                var fileSize = input.files[0].size;
                if (fileSize > maxSize) {
                  document.getElementById('errorMessage').innerText = 'El tamaño del archivo excede el límite permitido (100 MB).';
                  input.value = ''; // Limpiar el valor del input file
                } else {
                  document.getElementById('errorMessage').innerText = '';
                }
              }
            }
          </script>
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



  <!-- TOAST -->
  <div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="alertToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header">
        <strong class="me-auto">Mensaje de Alerta</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body"></div>
    </div>
  </div>

  <script>
    var alertMessage = "<?php echo isset($_SESSION['alert_message']) ? $_SESSION['alert_message'] : ''; ?>";
    if (alertMessage) {
      var alertToast = document.getElementById('alertToast');
      var toastBody = alertToast.querySelector('.toast-body');
      toastBody.textContent = alertMessage;
      var bsAlert = new bootstrap.Toast(alertToast);
      bsAlert.show();
      <?php unset($_SESSION['alert_message']); ?>
    }

    //VALIDAR ETIQUETAS
    $(document).ready(function() {
      $('input[name="etiquetas"]').on('keydown', function(event) {
        if ($(this).val().trim() === '' && event.key === 'Backspace') {
          event.preventDefault();
        } else if ($(this).val().trim() === '' && event.key === '#') {
          return;
        } else if ($(this).val().trim() === '#' && event.key === 'Backspace') {
          event.preventDefault();
        } else if ($(this).val().trim() === '#' && event.key === ' ') {
          event.preventDefault();
        } else if (event.key === ' ' && $(this).val().trim() !== '') {
          var etiquetas = $(this).val().split(' ');
          var ultimaEtiqueta = etiquetas[etiquetas.length - 1];
          if (ultimaEtiqueta.startsWith('#') && /^[a-zA-Z]+$/.test(ultimaEtiqueta.substring(1)) && etiquetas.length < 5) {
            $(this).val($(this).val() + ' #');
            event.preventDefault();
          } else {
            event.preventDefault();
          }
        } else if (event.key === ' ' && $(this).val().split('#').length >= 6) {
          event.preventDefault();
        }
      });

      // Validar campo de etiquetas después de enviar el formulario
      $('.needs-validation').on('submit', function() {
        const etiquetasInput = $('input[name="etiquetas"]');
        const etiquetasValue = etiquetasInput.val().trim();
        const etiquetasError = $('#etiquetasError');
        const etiquetasIcon = $('#etiquetasIcon');

        if (etiquetasValue === '#') {
          etiquetasInput.addClass('is-invalid');
          etiquetasError.show();
          etiquetasIcon.removeClass('bi-check-circle');
          etiquetasIcon.addClass('bi-exclamation-circle-fill text-danger');
          return false;
        } else {
          etiquetasInput.removeClass('is-invalid');
          etiquetasError.hide();
          etiquetasIcon.removeClass('bi-exclamation-circle-fill text-danger');
          etiquetasIcon.addClass('bi-check-circle text-success');
          return true;
        }
      });


    });
  </script>

</body>

</html>