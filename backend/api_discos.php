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
            $sql = "SELECT d.*, g.nombre_grupo 
                    FROM disco d
                    INNER JOIN grupo g ON d.grupo_id = g.id
                    WHERE d.titulo LIKE ? OR g.nombre_grupo LIKE ? OR d.discografica LIKE ?
                    ORDER BY d.titulo ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["%$search%", "%$search%", "%$search%"]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case 'POST':
        case 'PUT':
            $titulo = trim($input['titulo'] ?? '');
            $anio = $input['anio_lanzamiento'] ?? null;
            $numCanciones = $input['num_canciones'] ?? null;

            if (strlen($titulo) < 2) {
                http_response_code(400);
                echo json_encode(['error' => 'El titulo del disco debe tener al menos 2 caracteres']);
                exit;
            }
            if (empty($input['grupo_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Debe seleccionar un grupo']);
                exit;
            }
            if ($anio && (int)$anio < 1900) {
                http_response_code(400);
                echo json_encode(['error' => 'El año de lanzamiento no puede ser menor a 1900']);
                exit;
            }
            if ($numCanciones === null || (int)$numCanciones < 8) {
                http_response_code(400);
                echo json_encode(['error' => 'El disco debe tener al menos 8 canciones']);
                exit;
            }

            if ($method === 'POST') {
                $sql = "INSERT INTO disco (titulo, grupo_id, anio_lanzamiento, discografica, num_canciones) 
                        VALUES (:titulo, :grupo_id, :anio_lanzamiento, :discografica, :num_canciones)";
            } else {
                $sql = "UPDATE disco SET titulo = :titulo, grupo_id = :grupo_id, anio_lanzamiento = :anio_lanzamiento, 
                        discografica = :discografica, num_canciones = :num_canciones WHERE id = :id";
            }
            $stmt = $pdo->prepare($sql);
            $params = [
                ':titulo' => $titulo,
                ':grupo_id' => $input['grupo_id'],
                ':anio_lanzamiento' => $anio ?: null,
                ':discografica' => trim($input['discografica'] ?? '') ?: null,
                ':num_canciones' => $numCanciones ?? 0,
            ];
            if ($method === 'PUT') $params[':id'] = $input['id'];
            $stmt->execute($params);
            echo json_encode(['message' => $method === 'POST' ? 'Disco creado con exito' : 'Disco actualizado con exito']);
            break;

        case 'DELETE':
            $sql = "DELETE FROM disco WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $input['id']]);
            echo json_encode(['message' => 'Disco eliminado con exito']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Metodo no permitido']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
