<?php
require_once 'database.php';

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }
    
    // Configurar charset para UTF8
    $conn->set_charset("utf8mb4");
    
} catch(Exception $e) {
    die("Erro na conexão: " . $e->getMessage());
}
