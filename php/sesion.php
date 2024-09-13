<?php

function Login($link, $correo)
{
    $consulta = "SELECT * FROM usuario WHERE correo_Us = '$correo'";
    
    // Ejecutar la consulta
    $registros = mysqli_query($link, $consulta);

    // Verificar si la consulta se ejecutó correctamente
    if ($registros) {
        // Obtener el primer registro (suponiendo que el correo es único)
        $fila = mysqli_fetch_assoc($registros);

        // Almacenar los datos en variables de sesión
        $_SESSION['idU'] = $fila['idUsuario'];
        $_SESSION['nombre'] = $fila['nom_Us'];
        $_SESSION['apellido'] = $fila['apell_Us'];
        $_SESSION['carrera'] = $fila['carrera_Us'];
        $_SESSION['semestre'] = $fila['semestre_Us'];

        // Liberar los resultados de la memoria
        mysqli_free_result($registros);
    } else {   
    }
    // Cerrar la conexión después de usarla
    mysqli_close($link);
}

function ReloadLogin($link, $idUser)
{
    $consulta = "SELECT * FROM usuario WHERE idUsuario = $idUser";
    
    // Ejecutar la consulta
    $registros = mysqli_query($link, $consulta);

    // Verificar si la consulta se ejecutó correctamente
    if ($registros) {
        // Obtener el primer registro (suponiendo que el correo es único)
        $fila = mysqli_fetch_assoc($registros);

        // Almacenar los datos en variables de sesión
        $_SESSION['nombre'] = $fila['nom_Us'];
        $_SESSION['apellido'] = $fila['apell_Us'];
        $_SESSION['carrera'] = $fila['carrera_Us'];
        $_SESSION['semestre'] = $fila['semestre_Us'];

        // Liberar los resultados de la memoria
        mysqli_free_result($registros);
    } else {   
    }
    // Cerrar la conexión después de usarla
    mysqli_close($link);
}
?>