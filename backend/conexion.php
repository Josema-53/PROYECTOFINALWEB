<?php
    $host     = getenv('DB_HOST') ?: 'localhost';
    $port     = getenv('DB_PORT') ?: '3306';
    $user     = getenv('DB_USER') ?: 'root';
    $password = getenv('DB_PASS') ?: '';
    $database = getenv('DB_NAME') ?: 'proyectofinal';
    $charset  = 'utf8mb4';

    $dns = "mysql:host=$host;port=$port;dbname=$database;charset=$charset";

    $opciones = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $caPath = getenv('DB_CA_PATH') ?: '';
    if (empty($caPath)) {
        $defaultCa = '/etc/ssl/certs/ca-certificates.crt';
        if (file_exists($defaultCa)) {
            $caPath = $defaultCa;
        }
    }
    if (!empty($caPath) && file_exists($caPath)) {
        $opciones[PDO::MYSQL_ATTR_SSL_CA] = $caPath;
    }

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
