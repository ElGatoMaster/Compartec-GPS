<?php
include('../php/functions.php');
$link = include('../php/conexion.php');

// Verifica si 'carrera' está definido en $_GET antes de intentar acceder a él
if (isset($_GET['carrera'])) {
    $carrera = $_GET['carrera'];

    // Verifica si 'materia' está definido en $_GET
    $materia = isset($_GET['materia']) ? $_GET['materia'] : '';
    $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';

    // Construye la consulta SQL
    $consulta = "SELECT p.*, u.nom_Us, u.apell_Us 
             FROM publicacion p
             JOIN usuario u ON p.id_Usuario = u.idUsuario
             WHERE carrera_Pub = '$carrera' and estado_Pub = 1";

    if ($materia != '') {
        $consulta .= " AND materia_Pub LIKE '%$materia%'";
    }

    if ($tipo != '') {
        $consulta .= " AND tipo_Pub = '$tipo'";
    }

    $consulta .= " ORDER BY p.idPub";


    // Ejecuta la consulta
    $result = mysqli_query($link, $consulta);

    if (mysqli_num_rows($result) > 0) {
        // Si se encontraron resultados, muestra las publicaciones
        while ($fila = mysqli_fetch_array($result)) {
            // Construye el HTML para mostrar cada publicación
            echo '<div class="publicacion card mb-4">';
            echo '<div class="card-body">';
            echo '<h3 class="card-title display-6"><b>' . $fila['titulo_Pub'] . '</b></h3>';
            echo '<p class="card-text lead">' . $fila['descrip_Pub'] . '</p>';
            echo '<a name="fade" href="../publicacion/publicacion_detalle.php?id=' . $fila['idPub'] . '" class="btn btn-primary btn-sm"><b>Leer más</b></a>';
            echo '</div>';
            echo '<div class="card-footer d-flex text-muted justify-content-between align-items-end">';
            echo '<span class="card-text comment-date mb-0">Publicado por: ' . $fila['nom_Us'] . ' ' . $fila['apell_Us'] . '</span>';
            echo '<span class="card-text comment-date mb-0">Fecha de publicación: ' . functions::convertirFecha($fila['fecha_Pub']) . '</span>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        // Si no se encontraron resultados, muestra un mensaje
        echo '<div class="alert alert-warning" role="alert">
                No se encontraron resultados para la búsqueda.
              </div>';
    }
} else {
    // Si 'carrera' no está definido en $_GET, muestra un mensaje de error
    echo '<div class="alert alert-danger" role="alert">
            Error: No se proporcionó la carrera.
          </div>';
}

// Cierra la conexión a la base de datos
mysqli_close($link);
