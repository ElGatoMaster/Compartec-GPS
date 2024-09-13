<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../verifymail/PHPMailer/PHPMailer.php';
require '../verifymail/PHPMailer/Exception.php';
require '../verifymail/PHPMailer/SMTP.php';

$link = include('../../php/conexion.php'); // Incluye el archivo de conexión y obtén la conexión
$_SESSION["correo_ch"] = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Comprobar si los campos de correo electrónico no están vacíos
    if (!empty($_POST["correo"])) {
        // Obtener el correo electrónico del formulario
        $email = $_POST["correo"];

        // Preparar y ejecutar la consulta
        $stmt = $link->prepare("SELECT correo_Us FROM usuario WHERE correo_Us = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // Verificar si se encontró el correo electrónico
        if ($stmt->num_rows == 1) {
            // Se encontró el correo, puedes enviar el código de confirmación aquí
            // Aquí deberías enviar el código al correo electrónico proporcionado
            // Generar un código aleatorio de 6 dígitos
            //ENVIO DE CORREO USANDO PHPMAILER :,v
            $mail = new PHPMailer(true);
            $site = 'https://bibliotec.website/administracion/change_passw/change_password.php';

            try {
                //Server settings
                $mail->SMTPDebug = 0;                      //Enable verbose debug output
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host       = 'smtp.hostinger.com';                     //Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                $mail->Username   = 'administracion@bibliotec.website';                     //SMTP username
                $mail->Password   = 'BiBliotec0027#';                               //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
                $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                //Recipients
                $mail->setFrom('administracion@bibliotec.website', 'Dev-Bibliotec');
                $mail->addAddress($email, $nombre." ".$apellidos);       //Add a recipient //Name is optional

                //Content
                // Content
                $mail->isHTML(true); // Set email format to HTML
                $mail->Subject = "Confirmación de cambio de contraseña para usuario BiblioTec";

                // Cuerpo del correo con estilos CSS
                $mail->Body =  "<div style='font-family: Arial, sans-serif; padding: 7px;'>
        <h2 style='color: #007bff; margin-bottom: 20px;'>Confirmación de Cambio de Contraseña</h2>
        <p>Se ha solicitado un cambio de contraseña para la cuenta ligada a este correo.</p>
        <p>Para confirmar el cambio de contraseña, haga clic en el siguiente botón:</p>
        <p style='text-align: center;'>
            <a href='$site' style='
                display: inline-block;
                background-color: #007bff;
                color: #fff;
                text-decoration: none;
                padding: 10px 20px;
                border-radius: 5px;
                font-weight: bold;
                transition: background-color 0.3s ease;
            '>Confirmar Cambio de Contraseña</a>
        </p>
        <p>Si no solicitó este cambio de contraseña, puede ignorar este correo.</p>
        <hr style='border: 1px solid #ccc; margin-top: 40px;'>
        <p style='text-align: center; color: #888;'>BiblioTec - Conéctate con el Saber</p>
    </div>
";

                // Estilos CSS para el cuerpo del correo
                $mail->AltBody = "Se ha solicitado un cambio de contraseña para la cuenta ligada a este correo. Para confirmar el cambio de contraseña, visite: $site";

                // Enviar el correo
                $mail->send();
                $_SESSION["confirmation_message"] = "Se ha enviado el código de verificación al correo electrónico: $email.";
            } catch (Exception $e) {
                $_SESSION["alert_message"] = "Hubo un error al enviar el correo.";
            }
        } else {
            // El correo no está registrado, mostrar un mensaje de alerta
            $_SESSION["alert_message"] = "El correo proporcionado no está registrado.";
        }

        // Redirigir de vuelta al formulario
        $_SESSION["correo_ch"] = $email;
        header("Location: send_code.php");
        exit();
    }
}
