<?php
require '../php/sesion.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Comprobar si los campos de correo electrónico y contraseña no están vacíos
    if (!empty($_POST["email"]) && !empty($_POST["password"])) {
        // Obtener las credenciales del formulario
        $email = $_POST["email"];
        $password = $_POST["password"];

        // Conectar a la base de datos (reemplaza estos valores con los tuyos)
        $host = "localhost";
        $dbUsername = "root";
        $dbPassword = "root";//BiBliotec0027
        $dbName = "bibliotec";

        // Crear conexión
        $conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

        // Verificar la conexión
        if ($conn->connect_error) {
            die("Error de conexión: " . $conn->connect_error);
        }

        // Preparar la consulta para la tabla 'usuario'
        $sql_usuario = "SELECT contra_Us FROM usuario WHERE correo_Us = ?";
        
        if($stmt = $conn->prepare($sql_usuario)){
            // Vincular variables a la declaración preparada como parámetros
            $stmt->bind_param("s", $param_email);
            
            // Establecer parámetros
            $param_email = $email;
            
            // Ejecutar la declaración
            if($stmt->execute()){
                // Almacenar resultado
                $stmt->store_result();
                
                // Verificar si se encontró el correo electrónico en la tabla 'usuario'
                if($stmt->num_rows == 1){                    
                    // Vincular variables de resultado
                    $stmt->bind_result($hashed_password);
                    if($stmt->fetch()){
                        if(password_verify($password, $hashed_password)){
                            // La contraseña es correcta, inicia una nueva sesión
                            $_SESSION["loggedin"] = true;
                            $_SESSION["email"] = $email;
                            Login($conn, $email);
                            header("location: ../home.php"); // Redirigir al usuario a la página de inicio
                            exit;
                        } else{
                            // Mostrar un mensaje de error si la contraseña no es válida
                            $_SESSION["alert_message"] = "Contraseña o Correo incorrecto";
                            header("location: ../index.php"); // Redirigir de vuelta al formulario de inicio de sesión
                            exit;
                        }
                    }
                }
            }
            $stmt->close();
        }

        // Preparar la consulta para la tabla 'administrador'
        $sql_administrador = "SELECT contra_Admin FROM administrador WHERE correo_Admin = ?";
        
        if($stmt = $conn->prepare($sql_administrador)){
            // Vincular variables a la declaración preparada como parámetros
            $stmt->bind_param("s", $param_email);
            
            // Establecer parámetros
            $param_email = $email;
            
            // Ejecutar la declaración
            if($stmt->execute()){
                // Almacenar resultado
                $stmt->store_result();
                
                // Verificar si se encontró el correo electrónico en la tabla 'administrador'
                if($stmt->num_rows == 1){                    
                    // Vincular variables de resultado
                    $stmt->bind_result($admin_password);
                    if($stmt->fetch()){
                        if($password == $admin_password){
                            // La contraseña es correcta, inicia una nueva sesión como administrador
                            $_SESSION["loggedin"] = true;
                            $_SESSION["email"] = $email;
                            $_SESSION['rol'] = "admin";
                            header("location: administrador/admin_home.php"); // Redirigir al administrador a la página de inicio de sesión de administrador
                            exit;
                        } else{
                            // Mostrar un mensaje de error si la contraseña no es válida
                            $_SESSION["alert_message"] = "Contraseña o Correo incorrecto";
                            header("location: ../index.php"); // Redirigir de vuelta al formulario de inicio de sesión
                            exit;
                        }
                    }
                }
            }
            $stmt->close();
        }
        
        // Si el correo electrónico no se encuentra en ninguna de las tablas, mostrar un mensaje de error
        $_SESSION["alert_message"] = "Contraseña o Correo incorrecto";
        header("location: ../index.php"); // Redirigir de vuelta al formulario de inicio de sesión
        exit;
        
        // Cerrar conexión
        $conn->close();
    } else {
        // Si no se proporcionaron credenciales, redirigir de vuelta al formulario de inicio de sesión
        $_SESSION["alert_message"] = "Por favor, ingresa tu correo electrónico y contraseña.";
        header("location: ../index.php");
        exit;
    }
}
?>