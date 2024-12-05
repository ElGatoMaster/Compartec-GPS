<?php
include('../../php/functions.php');
$link = include('../../php/conexion.php');

// Verificar si se pasa el idAdmin para editar
if (isset($_GET['idAdmin'])) {
    $idAdmin = intval($_GET['idAdmin']);

    // Consulta para obtener los datos del administrador a editar
    $consulta_editar = "SELECT idAdmin, correo_Admin, contra_Admin FROM administrador WHERE idAdmin = $idAdmin";
    $resultado_editar = mysqli_query($link, $consulta_editar);

    if ($resultado_editar && mysqli_num_rows($resultado_editar) > 0) {
        $admin = mysqli_fetch_assoc($resultado_editar);
        echo json_encode($admin); // Devuelve los datos como JSON
    } else {
        echo json_encode(['error' => 'Administrador no encontrado.']);
    }
} else {
    echo json_encode(['error' => 'ID de administrador no proporcionado.']);
}

// Cierra la conexión
mysqli_close($link);
?>