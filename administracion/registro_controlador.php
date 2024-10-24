<?php
//PAQUETES O LIBRERIAS PARA ENVIAR EL CORREO
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require 'verifymail/PHPMailer/PHPMailer.php';
require 'verifymail/PHPMailer/Exception.php';
require 'verifymail/PHPMailer/SMTP.php';  


$link = include('../php/conexion.php'); // Incluye el archivo de conexión y obtén la conexión

$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$correo = $_POST['correo'];
$contrasena = $_POST['contra'];
$repcon = $_POST['repContra'];
$semestre = $_POST['semestre'];
$carrera = $_POST['carrera'];

//VALIDAR LA EXTENSION DEL @CORREO ITTEPIC.EDU.MX
$regex = "/^([a-zA-Z0-9\.]+@ittepic(\.)edu(\.)mx)$/";
if(!preg_match($regex,$correo)){
    echo '<script>
        window.alert("Correo no válido");
        window.history.back();
    </script>';
    exit;
}

//VERIFICAR SI YA ESTA REGISTRADO ESE CORREO
$existe_correo = mysqli_query($link,"SELECT * FROM `usuario` WHERE `correo_Us` = '$correo'");
if(mysqli_num_rows($existe_correo) > 0){
    //$_SESSION["alert_message"]=;
    echo '<script>
        window.alert("Este correo ya esta registrado, intente con uno diferente o inicie sesión");
        window.history.back();
    </script>';
    exit;
}

//VALIDACION DE CONTRASEÑAS
$regex2 = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{8,}$/";  
if(strcmp($contrasena,$repcon) === 0){
    if(preg_match($regex2,$contrasena)){
        //SI AMBAS CONTRASEÑAS INGRESADAS COINCIDEN Y TIENEN AL MENOS 1 MAYUSCULA Y 1 NUMERO LAS ENCRIPTA
        $contrasena = password_hash($contrasena,PASSWORD_BCRYPT);
    }else{
        echo '<script>
            window.alert("Las contraseña debe cumplir con las caracteristicas");
            window.history.back();
            </script>';
    exit;
    }
}else{      
    echo '<script>
        window.alert("Las contraseñas no coinciden");
        window.history.back();
    </script>';
    exit;
}

//AQUI SE VAN A INSERTAR EN LA TABLA TEMPORAL LOS DATOS, MAS EL TOKEN Y SE ENVIA EL CORREO PARA VALIDACION DEL MISMO
//Generacion de un token
$token = uniqid("",true);
$token = substr($token,15,5);

$query = "INSERT INTO `usuario_temp`(`nom_us`, `apell_us`, `carrera_us`, `semestre_us`, `correo_us`, `contra_us`, `token`) 
VALUES ('$nombre','$apellidos','$carrera','$semestre','$correo','$contrasena','$token')";

$ejecutar = mysqli_query($link,$query);

//EJECUTA EL QUERY EN EL QUE SE GUARDAN LOS DATOS EN LA TABLA TEMPORAL ESPERANDO LA VALIDACION DEL CORREO
if($ejecutar){
    echo '<script>
        alert("Registro completado exitosamente");
        
    </script>';//window.location = "verifymail/verifyemail.php";
}else{
    echo '<script>
        alert("Ha ocurrido un error, intentelo de nuevo");
        
    </script>';//window.location = "register.php";
}

//ENVIO DE CORREO USANDO PHPMAILER :,v
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = 2;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'yvfeacostaca@ittepic.edu.mx';                     //SMTP username yvfeacostaca@ittepic.edu.mx
    $mail->Password   = 'AOCY030214HBCCRVA8';                               //SMTP password 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('yvfeacostaca@ittepic.edu.mx', 'Dev-ComparTec');
    $mail->addAddress($correo, $nombre." ".$apellidos);     //Add a recipient //Name is optional
    
    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = "Verificacion de correo para ComparTec.";
    $mail->Body    = "Se ha registrado este correo en la plataforma ComparTec <br>";
    $mail->Body   .= "Para validar su registro use el siguiente codigo: <br>";
    $mail->Body   .= "$token <br><br>";
    $mail->Body   .= "Si no es el caso, haga caso omiso de este correo.<br>";

    $mail->send();
    echo '<script>
        alert("Se ha enviado el correo");
        </script>';
        header('Location: verifymail\verifyemail.php?mail='.$_POST['correo']);
} catch (Exception $e) {
    echo "Ha ocurrido un error: ".$e;
    echo '<script>
        alert("Ha ocurrido un error: ");
        
    </script>';//window.location = "register.php";
}

mysqli_close($link);
?>
