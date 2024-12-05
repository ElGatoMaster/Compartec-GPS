<?php
include('../../php/functions.php');
$link = include('../../php/conexion.php'); // Incluye el archivo de conexi贸n y obt茅n la conexi贸n

$admin;

// Procesar formulario para agregar administrador
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = mysqli_real_escape_string($link, $_POST['correo_Admin']);
    $contrase帽a = mysqli_real_escape_string($link, $_POST['contra_Admin']);

        $insertar = "INSERT INTO administrador (correo_Admin, contra_Admin) VALUES ('$correo', '$contrase帽a')";
        if (mysqli_query($link, $insertar)) {
            header("Location: admin_addAdmin.php"); // Redirigir despu茅s de agregar
            exit;
        } else {
            die('Error al agregar administrador: ' . mysqli_error($link));
        }
}



// Procesar activaci贸n/desactivaci贸n
if (isset($_GET['toggle_estado'])) {
    $id = intval($_GET['id']);
    $nuevo_estado = intval($_GET['estado']) === 1 ? 0 : 1;
    $actualizar = "UPDATE administrador SET estado_Admin = $nuevo_estado WHERE idAdmin = $id";
    if (!mysqli_query($link, $actualizar)) {
        die('Error al actualizar estado: ' . mysqli_error($link));
    }
    header("Location: admin_addAdmin.php");
    exit;
}


// Consulta a la base de datos
$consulta = "SELECT idAdmin, correo_Admin, contra_Admin, estado_Admin FROM administrador";
$resultado = mysqli_query($link, $consulta);

// Verifica si la consulta se ejecut贸 correctamente
if (!$resultado) {
    die('Error en la consulta: ' . mysqli_error($link));
}


// Cierra la conexi贸n despu茅s de realizar la consulta
mysqli_close($link);

// Inicia la sesi贸n despu茅s de cerrar la conexi贸n
session_start();


// Verificar si el usuario no ha iniciado sesi贸n
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["rol"] !== "admin") {
  header("location: ../../index.php");
  exit;
}

// Verificar si se ha enviado una solicitud para cerrar sesi贸n
if(isset($_GET["logout"]) && $_GET["logout"] === "true") {
  // Destruir todas las variables de sesi贸n
  session_unset();
  
  // Destruir la sesi贸n
  session_destroy();
  
  // Redirigir al usuario al inicio de sesi贸n
  header("location: ../../index.php");
  exit;
}


?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BiblioTec - AdminHome</title>

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


<body>

  <header class="bg-primary py-2 bg-opacity-75 border-bottom border-terciary border-4 py-2">
    <div class="container " style="margin-left:7.8vmax;" >
      <!-- Logo y t铆tulo -->
      <div class="logo">
        <img src="../../images/icons/flamita.png" alt="Logo T - BiblioTec" class="img-fluid mr-2">
        <h4 class="mb-0"><b><span class="col-1">Biblio</span><span class="col-2">Tec</span></h4>
    </div>
  </header>

  <div class="container-fluid" >
    <div class="row" >
        <!-- Barra de navegaci贸n izquierda -->
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
                <hr class="my-2"> <!-- L铆nea divisora -->
                <li class="mb-1">
                    <button class="btn d-inline-flex align-items-center rounded border-0 collapsed" id="letrabardos" style="color: black; font-weight: bold;">
                    <a class="nav-link align-items-center" href="admin_home.php?logout=true" id="letrabar">Cerrar sesión</a>
                    </button>
                </li>
                <hr class="my-2"> <!-- L铆nea divisora -->
                <li class="mb-1">
                  <a class="nav-link align-items-center" href="stats.php" id="letrabardos" style="margin-left:10px">Estadísticas de BiblioTec</a>
                </li>
            </ul>
        </div>
      
      <!-- Contenido principal -->
      <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 mt-2">
      <div class="container mt-2 mb-3">
      <h2 class="mb-4">Administrar Administradores</h2>
          
          <!-- Formulario para agregar nuevo administrador -->
          <form method="POST" class="mb-4">


                <div class="row">
                    <div class="col-md-4">
                        <label for="correo_Admin" class="form-label">Correo</label>
                        <input type="email" class="form-control" name="correo_Admin" id="correo_Admin" required 
                            value="<?php echo isset($admin) ? htmlspecialchars($admin['correo_Admin']) : ''; ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="contra_Admin" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="contra_Admin" id="contra_Admin" required
                                value="<?php echo isset($admin) ? htmlspecialchars($admin['contra_Admin']) : ''; ?>">
                            <button type="button" class="btn btn-secondary" onclick="togglePassword()">👁</button>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <!-- Bot贸n para agregar -->
                        <button type="submit" name="agregar_admin" class="btn btn-success">
                            Agregar Administrador
                        </button>
                    </div>
                </div>
            </form>


          <!-- Lista de administradores -->
          <table class="table table-bordered table-striped">
            <thead class="table-primary">
              <tr>
                <th>ID</th>
                <th>Correo</th>
                <th>Contraseña</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($fila = mysqli_fetch_assoc($resultado)) : ?>
                <tr>
                  <td><?php echo htmlspecialchars($fila['idAdmin']); ?></td>
                  <td><?php echo htmlspecialchars($fila['correo_Admin']); ?></td>
                  <td>
                    <input type="password" value="<?php echo htmlspecialchars($fila['contra_Admin']); ?>" class="form-control" readonly>
                  </td>
                  <td>
                    <?php echo $fila['estado_Admin'] ? 'Activo' : 'Inactivo'; ?>
                  </td>
                  <td>
                  <a href="#" data-bs-toggle="modal" data-bs-target="#editarModal" onclick="editarAdmin(<?php echo $fila['idAdmin']; ?>,
                   '<?php echo htmlspecialchars($fila['correo_Admin']); ?>', '<?php echo htmlspecialchars($fila['contra_Admin']); ?>')" class="btn btn-warning">Editar</a>
                    <a href="admin_addAdmin.php?toggle_estado=true&id=<?php echo $fila['idAdmin']; ?>&estado=<?php echo $fila['estado_Admin']; ?>" 
                               class="btn btn-<?php echo $fila['estado_Admin'] ? 'danger' : 'success'; ?>">
                               <?php echo $fila['estado_Admin'] ? 'Desactivar' : 'Activar'; ?>
                            </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>


                <!-- Modal -->
                <div class="modal fade" id="editarModal" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editarModalLabel">Editar Administrador</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" id="editarForm">
                                    <div class="mb-3">
                                        <label for="correo_Admin" class="form-label">Correo</label>
                                        <input type="email" class="form-control" name="correo_Admin" id="modal_correo_Admin" required value="<?php echo isset($admin) ? htmlspecialchars($admin['correo_Admin']) : ''; ?>">

                                    </div>
                                    <div class="mb-3">
                                        <label for="contra_Admin" class="form-label">Contraseña</label>
                                        <input type="password" class="form-control" name="contra_Admin" id="modal_contra_Admin" required value="<?php echo isset($admin) ? htmlspecialchars($admin['contra_Admin']) : ''; ?>">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                </form> 
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function editarAdmin(id, correo, contraseña) {
                        console.log("ID:", id, "Correo:", correo, "Contraseña:", contraseña); // Depurar los datos
                        document.getElementById('modal_correo_Admin').value = correo;
                        document.getElementById('modal_contra_Admin').value = contraseña;
                        document.getElementById('editarForm').action = 'admin_editAdmin.php?idAdmin=' + id;
                    }
                </script>

        </div>
      </main>
    </div>
  </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('contra_Admin');
            passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
        }
    </script>
  <script src ="../../js/fadeout.js">
  </script>

  <footer class="bg-primary py-2 bg-opacity-75 border-top border-terciary border-4 py-3 text-light bg-primary">
        <div class="container" >
            <p class="mb-1">&copy; 2024 BiblioTec - Todos los derechos reservados</p>
        </div>
    </footer>
  
</body>

</html>