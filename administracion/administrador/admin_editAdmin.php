<?php
include('../../php/functions.php');
$link = include('../../php/conexion.php'); // Incluye el archivo de conexión y obtén la conexión

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar si se pasa el idAdmin para editar
if (isset($_GET['idAdmin'])) {
    $idAdmin = intval($_GET['idAdmin']);
    
    // Consulta para obtener los datos del administrador a editar
    $consulta_editar = "SELECT idAdmin, correo_Admin, contra_Admin FROM administrador WHERE idAdmin = $idAdmin";
    $resultado_editar = mysqli_query($link, $consulta_editar);
    
    if ($resultado_editar && mysqli_num_rows($resultado_editar) > 0) {
        $admin = mysqli_fetch_assoc($resultado_editar);
    } else {
        die('Administrador no encontrado.');
    }
}

// Procesar el formulario para actualizar los datos del administrador
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = mysqli_real_escape_string($link, $_POST['correo_Admin']);
    $contraseña = mysqli_real_escape_string($link, $_POST['contra_Admin']);
    
    $actualizar = "UPDATE administrador SET correo_Admin = '$correo', contra_Admin = '$contraseña' WHERE idAdmin = $idAdmin";
    
    if (mysqli_query($link, $actualizar)) {
        header("Location: admin_addAdmin.php"); // Redirigir después de actualizar
        exit;
    } else {
        die('Error al actualizar administrador: ' . mysqli_error($link));
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
    <title>Editar Administrador</title>
    <!-- Agregar las hojas de estilo necesarias -->
    <link rel="stylesheet" href="../../css/normalizar.css">
    <link rel="stylesheet" href="../../css/estilos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Editar Administrador</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="correo_Admin" class="form-label">Correo</label>
            <input type="email" class="form-control" name="correo_Admin" id="correo_Admin" required value="<?php echo htmlspecialchars($admin['correo_Admin']); ?>">
        </div>
        <div class="mb-3">
            <label for="contra_Admin" class="form-label">Contraseña</label>
            <input type="password" class="form-control" name="contra_Admin" id="contra_Admin" required value="<?php echo htmlspecialchars($admin['contra_Admin']); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
</div>

</body>
</html>