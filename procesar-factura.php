<?php
require_once 'vendor/autoload.php'; 

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
        // Extrae los datos relevantes de la respuesta
        $supplierName = $apiResponse->document->inference->prediction->supplierName->value ?? '';
        $supplierCompanyRegistrations = $apiResponse->document->inference->prediction->supplierCompanyRegistrations ?? [];

        // Construye la respuesta en formato JSON
        $response = [
            'supplierName' => $supplierName,
            'supplierCompanyRegistrations' => $supplierCompanyRegistrations,
        ];

        // Devuelve la respuesta como JSON
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        // Manejo de errores si no se obtuvo una respuesta válida
        echo json_encode(['error' => 'No se pudo extraer información de la factura']);
    }
} else {
    // Manejo de errores si no se puede subir el archivo
    echo json_encode(['error' => 'Error al subir el archivo']);
}
?>
