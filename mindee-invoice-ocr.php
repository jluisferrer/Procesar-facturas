<?php

class MindeeInvoiceOCR {

    private $apiKey;

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }

    public function extractData($filePath) {
        // Reemplaza 'https://api.mindee.ai/v1/invoice-ocr' con la URL real de la API
        $apiUrl = 'https://api.mindee.ai/v1/invoice-ocr';

        // Prepara la solicitud HTTP
        $data = ['file' => new CURLFile($filePath)];
        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: multipart/form-data'
        ];

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Ejecuta la solicitud y obtiene la respuesta
        $response = curl_exec($ch);
        curl_close($ch);

        // Decodifica la respuesta JSON
        $responseData = json_decode($response, true);

        // Verifica si la solicitud fue exitosa
        if (isset($responseData['status']) && $responseData['status'] === 'success') {
            return $responseData['data'];
        } else {
            return null;
        }
    }
}
