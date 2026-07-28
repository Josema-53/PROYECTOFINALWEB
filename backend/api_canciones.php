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
            $action = $_GET['action'] ?? '';

            if ($action === 'listas') {
                $stmtGrupos = $pdo->query("SELECT id, nombre_grupo FROM grupo ORDER BY nombre_grupo");
                $stmtDiscos = $pdo->query("SELECT d.id, d.titulo, d.grupo_id FROM disco d ORDER BY d.titulo");
                echo json_encode([
                    'grupos' => $stmtGrupos->fetchAll(PDO::FETCH_ASSOC),
                    'discos' => $stmtDiscos->fetchAll(PDO::FETCH_ASSOC),
                ]);
                exit();
            }

            $search = $_GET['q'] ?? '';
            $sql = "SELECT c.*, g.nombre_grupo, COALESCE(d.titulo, 'Sencillo') AS disco_titulo
                    FROM cancion c
                    INNER JOIN grupo g ON c.grupo_id = g.id
                    LEFT JOIN disco d ON c.disco_id = d.id
                    WHERE c.titulo LIKE ? OR g.nombre_grupo LIKE ? OR c.genero LIKE ?
                    ORDER BY c.titulo ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["%$search%", "%$search%", "%$search%"]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case 'POST':
        case 'PUT':
            $titulo = trim($input['titulo'] ?? '');
            $duracion = $input['duracion_segundos'] ?? null;
            $anio = $input['ano_lanzamiento'] ?? null;

            if (strlen($titulo) < 2) {
                http_response_code(400);
                echo json_encode(['error' => 'El titulo de la cancion debe tener al menos 2 caracteres']);
                exit;
            }
            if (empty($input['grupo_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Debe seleccionar un grupo']);
                exit;
            }
            if ($duracion !== null && (int)$duracion < 1) {
                http_response_code(400);
                echo json_encode(['error' => 'La duracion debe ser al menos 1 segundo']);
                exit;
            }
            if ($anio && (int)$anio < 1900) {
                http_response_code(400);
                echo json_encode(['error' => 'El año de lanzamiento no puede ser menor a 1900']);
                exit;
            }

            if ($method === 'POST') {
                $sql = "INSERT INTO cancion (titulo, grupo_id, disco_id, duracion_segundos, genero, ano_lanzamiento, es_sencillo) 
                        VALUES (:titulo, :grupo_id, :disco_id, :duracion_segundos, :genero, :ano_lanzamiento, :es_sencillo)";
            } else {
                $sql = "UPDATE cancion SET titulo = :titulo, grupo_id = :grupo_id, disco_id = :disco_id, 
                        duracion_segundos = :duracion_segundos, genero = :genero, ano_lanzamiento = :ano_lanzamiento, 
                        es_sencillo = :es_sencillo WHERE id = :id";
            }
            $stmt = $pdo->prepare($sql);
            $params = [
                ':titulo' => $titulo,
                ':grupo_id' => $input['grupo_id'],
                ':disco_id' => $input['disco_id'] ?: null,
                ':duracion_segundos' => $duracion ?: null,
                ':genero' => $input['genero'] ?: null,
                ':ano_lanzamiento' => $anio ?: null,
                ':es_sencillo' => $input['es_sencillo'] ?? 0,
            ];
            if ($method === 'PUT') $params[':id'] = $input['id'];
            $stmt->execute($params);
            echo json_encode(['message' => $method === 'POST' ? 'Cancion creada con exito' : 'Cancion actualizada con exito']);
            break;

        case 'DELETE':
            $sql = "DELETE FROM cancion WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $input['id']]);
            echo json_encode(['message' => 'Cancion eliminada con exito']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Metodo no permitido']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
