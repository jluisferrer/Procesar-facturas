<?php

require_once 'mindee-invoice-ocr.php';
require_once 'config.php'; // (Opcional)

// Obtiene el archivo PDF cargado por el usuario
$archivoFactura = $_FILES['archivo-factura']['tmp_name'];

if (!$archivoFactura) {
    echo json_encode(['exito' => false, 'mensaje' => 'Error al obtener el archivo de factura.']);
    exit;
}

// Extrae los datos de la factura usando la API de Mindee Invoice OCR
$mindee = new MindeeInvoiceOCR($apiKey);
$datosFactura = $mindee->extractData($archivoFactura);

if (!$datosFactura) {
    echo json_encode(['exito' => false, 'mensaje' => 'Error al extraer los datos de la factura.']);
    exit;
}

// Guarda los datos extraídos en la base de datos (opcional)
if ($databaseCredentials) {
    // Conéctate a la base de datos y guarda los datos
    // ...
}

// Envía la respuesta JSON con los datos extraídos
echo json_encode(['exito' => true, 'datos' => $datosFactura]);
