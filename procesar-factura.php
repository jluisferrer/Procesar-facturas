<?php

require_once 'vendor/autoload.php';
require_once 'db.php'; // Incluye la conexión a la base de datos

use Mindee\Client;
use Mindee\Product\Invoice\InvoiceV4;

// Verifica si se ha enviado un archivo
if ($_FILES['factura']['error'] === UPLOAD_ERR_OK) {
    // Ruta temporal del archivo subido
    $fileTmpPath = $_FILES['factura']['tmp_name'];

    // Inicializa el cliente Mindee con tu clave API
    $mindeeClient = new Client("680a9fe5955b4232f292ce6d3876226a");

    // Carga el archivo desde el sistema de archivos
    $inputSource = $mindeeClient->sourceFromPath($fileTmpPath);

    // Parsea el archivo con el modelo InvoiceV4
    $apiResponse = $mindeeClient->parse(InvoiceV4::class, $inputSource);

    // Verifica si se obtuvo una respuesta válida
    if ($apiResponse->document !== null && $apiResponse->document->inference !== null && $apiResponse->document->inference->prediction !== null) {

        // Procesar supplierName
        $supplierName = $apiResponse->document->inference->prediction->supplierName->value ?? '';

        // Procesar supplierCompanyRegistrations
        $supplierCompanyRegistrations = [];
        foreach ($apiResponse->document->inference->prediction->supplierCompanyRegistrations as $registration) {
            $supplierCompanyRegistrations[] = $registration->value;
        }

        //Procesar invoiceNumber
        $invoiceNumber = $apiResponse->document->inference->prediction->invoiceNumber->value ?? '';

        //Procesar totalAmount
        $totalAmount = $apiResponse->document->inference->prediction->totalAmount->value ?? 0;

        //Procesar totalTax
        $totalTax = $apiResponse->document->inference->prediction->totalTax->value ?? 0;

        //Procesar número de paginas del documento
        $numPages = $apiResponse->document->inference->prediction->totalPages ?? 0;
        if ($numPages <= 0) {
            $numPages = 1; // Asegurarse de que al menos se reporte una página
        }
        //Calcular el hash del archivo
        $hash = hash_file('sha256', $fileTmpPath);

        // Inserta los datos en la base de datos
        try {
            $stmt = $pdo->prepare("
                INSERT INTO facturas (nif, nombre_empresa, numero_factura, importe_total, iva_total, numero_paginas, hash) 
                VALUES (:nif, :nombreEmpresa, :numeroFactura, :importeTotal, :ivaTotal, :numeroPaginas, :hash)
            ");

            $stmt->execute([
                ':nif' => json_encode($supplierCompanyRegistrations),
                ':nombreEmpresa' => $supplierName,
                ':numeroFactura' => $invoiceNumber,
                ':importeTotal' => $totalAmount,
                ':ivaTotal' => $totalTax,
                ':numeroPaginas' => $numPages,
                ':hash' => $hash,
            ]);

            // Construye la respuesta en formato JSON
            $response = [
                'supplierName' => $supplierName,
                'supplierCompanyRegistrations' => $supplierCompanyRegistrations,
                'invoiceNumber' => $invoiceNumber,
                'totalAmount' => $totalAmount,
                'totalTax' => $totalTax,
                'numPages' => $numPages,
                'hash' => $hash
            ];

            // Devuelve la respuesta como JSON
            header('Content-Type: application/json');
            echo json_encode($response);
        } catch (PDOException $e) {
            // Manejo de errores si no se pudo guardar en la base de datos
            echo json_encode(['error' => 'Error al guardar en la base de datos: ' . $e->getMessage()]);
        }
    } else {
        // Manejo de errores si no se obtuvo una respuesta válida
        echo json_encode(['error' => 'No se pudo extraer información de la factura']);
    }
} else {
    // Manejo de errores si no se puede subir el archivo
    echo json_encode(['error' => 'Error al subir el archivo']);
}
