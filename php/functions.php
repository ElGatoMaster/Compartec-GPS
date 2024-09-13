<?php

use function PHPSTORM_META\type;

class functions
{
    public static function convertirFecha($fecha)
    {
        $fecha_timestamp = strtotime($fecha);
        $hoy = strtotime('today');
        $ayer = strtotime('yesterday');

        // Si la fecha es hoy
        if ($fecha_timestamp >= $hoy) {
            return 'Hoy';
        }
        // Si la fecha es ayer
        elseif ($fecha_timestamp >= $ayer) {
            return 'Ayer';
        }
        // Si la fecha es hace X días
        else {
            $diferencia = floor((time() - $fecha_timestamp) / (60 * 60 * 24));
            if ($diferencia <= 7) {
                return "Hace $diferencia días";
            } else {
                return self::fechaCompleta($fecha);
            }
        }
    }

    private static function fechaCompleta($fecha)
    {
        // Convertir la fecha al formato deseado
        /*$fecha = strftime("%d de %B del %Y", strtotime($fecha));
        $dateArr = explode(" ", $fecha); //SEPARA LA FECHA EN UN ARREGLO
        $dateArr[2] = self::mes($dateArr[2]); //REMPLAZA EL MES EN INGLES Y LOS TRADUCE A ESPAÑOL
        $fechaN = implode(" ", $dateArr); //VUELVE A UNIR EL ARREGLO EN UNA CADENA
        */
        $fecha = new DateTime($fecha);
        $fecha = $fecha->format('d-m-Y');
        $dateArr = explode("-", $fecha); //SEPARA LA FECHA EN UN ARREGLO
        $dateArr[1] = self::mes($dateArr[1]); //REMPLAZA EL MES EN INGLES Y LOS TRADUCE A ESPAÑOL
        $fechaN = implode(" de ", $dateArr); //VUELVE A UNIR EL ARREGLO EN UNA CADENA

        // Retornar la fecha formateada
        return $fechaN;
    }

    private static function mes($month)
    {
        switch ($month) {
            case '01':
                return 'Enero';
                break;
            case '02':
                return 'Febrero';
                break;
            case '03':
                return 'Marzo';
                break;
            case '04':
                return 'Abril';
                break;
            case '05':
                return 'Mayo';
                break;
            case '06':
                return 'Junio';
                break;
            case '07':
                return 'Julio';
                break;
            case '08':
                return 'Agosto';
                break;
            case '09':
                return 'Septiembre';
                break;
            case '10':
                return 'Octubre';
                break;
            case '11':
                return 'Noviembre';
                break;
            case '12':
                return 'Diciembre';
                break;
        }
    }

    public static function conversionTexto($numResultados, $typeR)
    {
        if ($typeR == "P") {
            if ($numResultados == 0) {
                return "Publicaciones encontradas ";
            } elseif ($numResultados > 1) {
                return "Se encontraron $numResultados publiaciones ";
            } else {
                return "Se encontró $numResultados publiación ";
            }
        } else {
            if ($numResultados < 1) {
                return "Usuarios encontrados ";
            }
            elseif ($numResultados > 1) {
                return "Se encontraron $numResultados usuarios ";
            } else {
                return "Se encontró $numResultados usuario ";
            }
        }
    }
}
