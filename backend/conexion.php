<?php
    $host     = getenv('DB_HOST') ?: 'localhost';
    $user     = getenv('DB_USER') ?: 'root';
    $password = getenv('DB_PASS') ?: '';
    $database = getenv('DB_NAME') ?: 'proyectofinal';
    $charset  = 'utf8mb4';

    $dns = "mysql:host=$host;dbname=$database;charset=$charset";

    $opciones = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_SSL_CA => getenv('DB_CA_PATH') ?: null,
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
