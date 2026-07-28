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
                $stmtCanciones = $pdo->query("SELECT c.id, c.titulo, c.duracion_segundos, g.nombre_grupo 
                    FROM cancion c INNER JOIN grupo g ON c.grupo_id = g.id ORDER BY c.titulo");
                $stmtDJs = $pdo->query("SELECT id, nombre_artistico FROM discjockey WHERE estado = 1 ORDER BY nombre_artistico");
                echo json_encode([
                    'canciones' => $stmtCanciones->fetchAll(PDO::FETCH_ASSOC),
                    'djs' => $stmtDJs->fetchAll(PDO::FETCH_ASSOC),
                ]);
                exit();
            }

            if ($action === 'stats') {
                $fecha = $_GET['fecha'] ?? '';
                $dj = $_GET['dj'] ?? '';
                $cancion = $_GET['cancion'] ?? '';

                $conditions = [];
                $params = [];

                if (!empty($fecha)) {
                    $conditions[] = "DATE(r.fecha_hora) = :fecha";
                    $params[':fecha'] = $fecha;
                }
                if (!empty($dj)) {
                    $conditions[] = "dj.nombre_artistico LIKE :dj";
                    $params[':dj'] = "%$dj%";
                }
                if (!empty($cancion)) {
                    $conditions[] = "c.titulo LIKE :cancion";
                    $params[':cancion'] = "%$cancion%";
                }

                $where = count($conditions) > 0 ? 'WHERE ' . implode(' AND ', $conditions) : '';

                $from = "FROM reproduccion r
                         INNER JOIN cancion c ON r.cancion_id = c.id
                         INNER JOIN grupo g ON c.grupo_id = g.id
                         INNER JOIN discjockey dj ON r.discjockey_id = dj.id
                         $where";

                $stmtTotal = $pdo->prepare("SELECT COUNT(*) $from");
                $stmtTotal->execute($params);
                $total = (int)$stmtTotal->fetchColumn();

                $stmtDJs = $pdo->prepare("SELECT COUNT(DISTINCT r.discjockey_id) $from");
                $stmtDJs->execute($params);
                $djs = (int)$stmtDJs->fetchColumn();

                $stmtCanciones = $pdo->prepare("SELECT COUNT(DISTINCT r.cancion_id) $from");
                $stmtCanciones->execute($params);
                $canciones = (int)$stmtCanciones->fetchColumn();

                $stmtHoras = $pdo->prepare("SELECT COALESCE(SUM(r.duracion_real), 0) $from");
                $stmtHoras->execute($params);
                $segundos = (int)$stmtHoras->fetchColumn();
                $horas = round($segundos / 3600, 1);

                echo json_encode([
                    'total' => $total,
                    'djs' => $djs,
                    'canciones' => $canciones,
                    'horas' => $horas,
                ]);
                exit();
            }

            $fecha = $_GET['fecha'] ?? '';
            $dj = $_GET['dj'] ?? '';
            $cancion = $_GET['cancion'] ?? '';

            $conditions = [];
            $params = [];

            if (!empty($fecha)) {
                $conditions[] = "DATE(r.fecha_hora) = :fecha";
                $params[':fecha'] = $fecha;
            }
            if (!empty($dj)) {
                $conditions[] = "dj.nombre_artistico LIKE :dj";
                $params[':dj'] = "%$dj%";
            }
            if (!empty($cancion)) {
                $conditions[] = "c.titulo LIKE :cancion";
                $params[':cancion'] = "%$cancion%";
            }

            $where = count($conditions) > 0 ? 'WHERE ' . implode(' AND ', $conditions) : '';

            $sql = "SELECT r.*, c.titulo AS cancion_titulo, c.duracion_segundos, c.genero,
                           g.nombre_grupo, dj.nombre_artistico AS dj_nombre
                    FROM reproduccion r
                    INNER JOIN cancion c ON r.cancion_id = c.id
                    INNER JOIN grupo g ON c.grupo_id = g.id
                    INNER JOIN discjockey dj ON r.discjockey_id = dj.id
                    $where
                    ORDER BY r.fecha_hora DESC
                    LIMIT 200";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case 'POST':
            $sql = "INSERT INTO reproduccion (cancion_id, discjockey_id, fecha_hora, duracion_real, observaciones) 
                    VALUES (:cancion_id, :discjockey_id, :fecha_hora, :duracion_real, :observaciones)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':cancion_id' => $input['cancion_id'],
                ':discjockey_id' => $input['discjockey_id'],
                ':fecha_hora' => $input['fecha_hora'] ?: date('Y-m-d H:i:s'),
                ':duracion_real' => $input['duracion_real'] ?: null,
                ':observaciones' => $input['observaciones'] ?: null,
            ]);
            echo json_encode(['message' => 'Reproduccion registrada con exito']);
            break;

        case 'PUT':
            $sql = "UPDATE reproduccion SET cancion_id = :cancion_id, discjockey_id = :discjockey_id, 
                    fecha_hora = :fecha_hora, duracion_real = :duracion_real, observaciones = :observaciones WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id' => $input['id'],
                ':cancion_id' => $input['cancion_id'],
                ':discjockey_id' => $input['discjockey_id'],
                ':fecha_hora' => $input['fecha_hora'],
                ':duracion_real' => $input['duracion_real'] ?: null,
                ':observaciones' => $input['observaciones'] ?: null,
            ]);
            echo json_encode(['message' => 'Reproduccion actualizada con exito']);
            break;

        case 'DELETE':
            $sql = "DELETE FROM reproduccion WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $input['id']]);
            echo json_encode(['message' => 'Reproduccion eliminada con exito']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Metodo no permitido']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
