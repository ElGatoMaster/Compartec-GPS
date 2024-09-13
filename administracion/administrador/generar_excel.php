<?php

$link = include('../../php/conexion.php');
require '../../php/vendor/autoload.php'; // Carga el autoload de Composer


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Nombre del archivo Excel
$excelFile = 'Informe_BiblioTec.xlsx';
// Crear un nuevo objeto Spreadsheet
$spreadsheet = new Spreadsheet();

// Establecer propiedades del documento
$spreadsheet->getProperties()->setCreator("Tu Nombre")
                              ->setLastModifiedBy("Tu Nombre")
                              ->setTitle("Informe de Consultas")
                              ->setSubject("Informe de Consultas")
                              ->setDescription("Informe generado automáticamente con PhpSpreadsheet")
                              ->setKeywords("phpspreadsheet office php")
                              ->setCategory("Informe");


// Lista de nombres de procedimientos almacenados
$sp_names = array(
    "TotalUsuarios",
    "DistribucionUsuariosCarrera",
    "TotalPublicaciones",
    "PublicacionesPorTipo",
    "PublicacionesPorCarrera",
    "TotalComentarios",
    "ObtenerReportes",
    "ObtenerNumeroDeConsultasMes",
    "TotalPublicacionesPorAnio"
);

// Array para almacenar los datos de cada procedimiento
$stats = array();

// Ejecutar los procedimientos almacenados y almacenar los resultados
foreach ($sp_names as $sp_name) {
    $query = "CALL $sp_name();";
    $result = mysqli_multi_query($link, $query);

    if (!$result) {
        die('Error en la consulta: ' . mysqli_error($link));
    }

    $stats[$sp_name] = array();

    // Procesar cada conjunto de resultados
    do {
        if ($result = mysqli_store_result($link)) {
            $stats[$sp_name][] = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($link));
}

// Generar el informe en Excel
foreach ($stats as $sp_name => $resultados) {
    // Crear una nueva hoja
    $sheet = $spreadsheet->createSheet();
    
    // Establecer el título de la hoja
    $sheet->setTitle($sp_name);

    // Insertar los datos en la hoja
    $row = 1;
    foreach ($resultados as $resultado) {
        foreach ($resultado as $row_data) {
            $col = 1; // Comenzar desde la primera columna
            foreach ($row_data as $valor) {
                $sheet->setCellValueByColumnAndRow($col, $row, $valor);
                $col++;
            }
            $row++;
        }
    }
}

// Establecer la hoja activa como la primera
$spreadsheet->setActiveSheetIndex(0);


// Guardar el documento como un archivo Excel
if (file_exists($excelFile)) {
    unlink($excelFile); // Eliminar el archivo existente
}

$writer = new Xlsx($spreadsheet);
$writer->save($excelFile);

// Cerrar la conexión a la base de datos
mysqli_close($link);
    header("Location: stats.php?file=" . urlencode($excelFile));
exit;

?>

