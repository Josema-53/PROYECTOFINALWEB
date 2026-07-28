<?php
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $database = 'proyectofinal';
    $charset = 'utf8mb4';

    $dns = "mysql:host=$host;dbname=$database;charset=$charset";

    $opciones = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    try {
        $pdo= new PDO($dns, $user, $password, $opciones);
    } catch (PDOException $e) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'estado' => 'error',
            'mensaje' => 'Error de conexion a la base de datos'
        ]);
        exit;
    }

?>
