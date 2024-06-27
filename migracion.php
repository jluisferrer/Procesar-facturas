<?php

require_once 'db.php';

try {
    $sql = "
    CREATE TABLE IF NOT EXISTS facturas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nif JSON NOT NULL,
        nombre_empresa VARCHAR(255) NOT NULL,
        numero_factura VARCHAR(255) NOT NULL,
        importe_total DECIMAL(10, 2) NOT NULL,
        iva_total DECIMAL(10, 2) NOT NULL,
        numero_paginas INT NOT NULL,
        hash CHAR(64) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    $pdo->exec($sql);
    echo "Tabla `facturas` creada con éxito.";
} catch (PDOException $e) {
    echo "Error al crear la tabla: " . $e->getMessage();
}
