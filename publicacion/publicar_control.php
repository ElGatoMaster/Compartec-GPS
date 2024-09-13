<?php
include('../php/conexion.php');

session_start();

$carpetaUsuario = "../repo_archivos/" . $_SESSION['idU'];
// Crear la carpeta si no existe
if (!is_dir($carpetaUsuario)) {
    if (!mkdir($carpetaUsuario, 0777, true)) {
        die('Error al crear la carpeta');
    }
}


    // Insertar en la base de datos solo si el archivo se ha subido correctamente
    $titulo = $_POST['titulo'];
    $idUsuario = $_SESSION['idU'];
    $fechaActual = date("Y-m-d");
    $carrera = $_POST['cbx_carrera']; 
    $descripcion = $_POST['descripcion']; 
    $materia = $_POST['cbx_materia'];
    $tipo = $_POST['tipo'];
    $nulo =  0;
    $null='';

    // Obtener las etiquetas del formulario
    $etiquetas = $_POST['etiquetas'];
    // Separar las etiquetas por el espacio en blanco
    $arrayEtiquetas = explode(' ', $etiquetas);

    // insercion
    $inserta = "INSERT INTO publicacion (id_Usuario, titulo_Pub, fecha_pub, descrip_Pub, calif_Pub, carrera_Pub, materia_Pub, tipo_pub, estado_Pub, archivo_Pub) VALUES (?,?,?,?,?,?,?,?,?,?)";
    if ($stmt = $link->prepare($inserta)) {
        $stmt->bind_param("isssssssss", $idUsuario, $titulo, $fechaActual ,$descripcion, $nulo, $carrera, $materia, $tipo, $nulo, $null);

        if ($stmt->execute()) {
            $idPublicacion = $stmt->insert_id;
            // Renombrar el archivo en el servidor
            $archivoTemporal = $_FILES['archivo']['tmp_name'];
            $nombreArchivo = $idPublicacion . "_" . $_FILES['archivo']['name'];

            $rutaArchivo = $carpetaUsuario . '/' . $nombreArchivo;
                if (move_uploaded_file($archivoTemporal, $rutaArchivo)) {
                    // Actualizar la ruta del archivo en la base de datos
                    $rutaUpdate = "UPDATE publicacion SET archivo_Pub = '$rutaArchivo' WHERE idPub = $idPublicacion";
                    if ($update = $link->prepare($rutaUpdate)) {
                        $update->execute();
    
                        // Insertar etiquetas en la tabla tag_publicacion
                        foreach ($arrayEtiquetas as $etiqueta) {
                            if (!empty($etiqueta)) {
                                $etiquetaSinSimbolo = substr($etiqueta, 1);
                                $insertarEtiqueta = "INSERT INTO tag_publicacion (idPub, nombreTag) VALUES (?, ?)";
                                if ($stmtEtiqueta = $link->prepare($insertarEtiqueta)) {
                                    $stmtEtiqueta->bind_param("is", $idPublicacion, $etiquetaSinSimbolo);
                                    $stmtEtiqueta->execute();
                                }
                            }
                        }
                        $_SESSION["alert_message"] = "La publicación se ha mandado a revisión.";
                        header("location: publicar.php");
                        exit;
                    } else {
                        $_SESSION["alert_message"] = "Error al actualizar la ruta del archivo en la base de datos.";
                        header("location: publicar.php");
                        exit;
                    }
                } else {
                    $_SESSION["alert_message"] = "Error al mover el archivo a la nueva ubicación.";
                    header("location: publicar.php");
                    exit;
                }
           
        } else {
            $_SESSION["alert_message"] = "Error al ejecutar la consulta SQL: " . $link->error;
            header("location: publicar.php");
            exit;
        }
        $stmt->close();
    } else {
        $_SESSION["alert_message"] = "Error en la preparación de la consulta SQL: " . $link->error;
        header("location: publicar.php");
        exit;
    }
    
    $link->close();
    ?>