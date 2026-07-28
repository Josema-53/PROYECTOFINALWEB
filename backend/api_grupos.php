<?php

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'conexion.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    switch ($method) {
        case 'GET':
            $search = $_GET['q'] ?? '';
            $sql = "SELECT * FROM grupo 
                    WHERE nombre_grupo LIKE ? OR pais_origen LIKE ? OR genero_musical LIKE ?
                    ORDER BY nombre_grupo ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["%$search%", "%$search%", "%$search%"]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case 'POST':
        case 'PUT':
            $nombre_grupo = trim($input['nombre_grupo'] ?? '');
            $anio_formacion = $input['anio_formacion'] ?? null;
            $integrantes = $input['integrantes'] ?? null;

            if (strlen($nombre_grupo) < 2) {
                http_response_code(400);
                echo json_encode(['error' => 'El nombre del grupo debe tener al menos 2 caracteres']);
                exit;
            }
            if ($anio_formacion && ((int)$anio_formacion < 1900)) {
                http_response_code(400);
                echo json_encode(['error' => 'El año de formacion no puede ser menor a 1900']);
                exit;
            }
            if ($integrantes !== null && (int)$integrantes < 1) {
                http_response_code(400);
                echo json_encode(['error' => 'El numero de integrantes debe ser al menos 1']);
                exit;
            }

            if ($method === 'POST') {
                $sql = "INSERT INTO grupo (nombre_grupo, pais_origen, anio_formacion, genero_musical, integrantes, biografia, estado_activo) 
                        VALUES (:nombre_grupo, :pais_origen, :anio_formacion, :genero_musical, :integrantes, :biografia, :estado_activo)";
            } else {
                $sql = "UPDATE grupo SET nombre_grupo = :nombre_grupo, pais_origen = :pais_origen, anio_formacion = :anio_formacion, 
                        genero_musical = :genero_musical, integrantes = :integrantes, biografia = :biografia, 
                        estado_activo = :estado_activo WHERE id = :id";
            }
            $stmt = $pdo->prepare($sql);
            $params = [
                ':nombre_grupo' => $nombre_grupo,
                ':pais_origen' => trim($input['pais_origen'] ?? '') ?: null,
                ':anio_formacion' => $anio_formacion ?: null,
                ':genero_musical' => $input['genero_musical'] ?: null,
                ':integrantes' => $integrantes ?: null,
                ':biografia' => trim($input['biografia'] ?? '') ?: null,
                ':estado_activo' => $input['estado_activo'] ?? 1,
            ];
            if ($method === 'PUT') $params[':id'] = $input['id'];
            $stmt->execute($params);
            echo json_encode(['message' => $method === 'POST' ? 'Grupo creado con exito' : 'Grupo actualizado con exito']);
            break;

        case 'DELETE':
            $sql = "DELETE FROM grupo WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $input['id']]);
            echo json_encode(['message' => 'Grupo eliminado con exito']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Metodo no permitido']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
