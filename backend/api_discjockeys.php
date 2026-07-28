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
            $sql = "SELECT * FROM discjockey 
                    WHERE nombre_artistico LIKE ? OR nombre_real LIKE ? OR cedula LIKE ? OR genero_favorito LIKE ?
                    ORDER BY nombre_artistico ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["%$search%", "%$search%", "%$search%", "%$search%"]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case 'POST':
        case 'PUT':
            $nombre_artistico = trim($input['nombre_artistico'] ?? '');
            $nombre_real = trim($input['nombre_real'] ?? '');
            $cedula = trim($input['cedula'] ?? '');
            $telefono = trim($input['telefono'] ?? '');
            $correo = trim($input['correo'] ?? '');

            if (strlen($nombre_artistico) < 2 || !preg_match('/[a-zA-ZÀ-ÿÑñ]{2,}/', $nombre_artistico)) {
                http_response_code(400);
                echo json_encode(['error' => 'El nombre artistico debe tener al menos 2 letras']);
                exit;
            }
            if ($nombre_real && (strlen($nombre_real) < 2 || !preg_match('/[a-zA-ZÀ-ÿÑñ]{2,}/', $nombre_real))) {
                http_response_code(400);
                echo json_encode(['error' => 'El nombre real debe tener al menos 2 letras']);
                exit;
            }
            if (!preg_match('/^\d{10}$/', $cedula)) {
                http_response_code(400);
                echo json_encode(['error' => 'La cedula debe tener 10 digitos']);
                exit;
            }
            $dv = (int)$cedula[9];
            $suma = 0;
            for ($i = 0; $i < 9; $i++) {
                $mult = ($i % 2 === 0) ? 2 : 1;
                $valor = (int)$cedula[$i] * $mult;
                $suma += ($valor >= 10) ? $valor - 9 : $valor;
            }
            $esperado = ($suma % 10 === 0) ? 0 : 10 - ($suma % 10);
            if ($dv !== $esperado) {
                http_response_code(400);
                echo json_encode(['error' => 'La cedula ingresada no es valida']);
                exit;
            }
            if ($telefono && !preg_match('/^0\d{9}$/', $telefono)) {
                http_response_code(400);
                echo json_encode(['error' => 'El telefono debe tener 10 digitos y comenzar con 0']);
                exit;
            }
            if ($correo && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(['error' => 'El correo electronico no es valido']);
                exit;
            }

            $horario_programa = trim($input['horario_programa'] ?? '');
            if ($horario_programa !== '' && !preg_match('/^([01]\d|2[0-3]):[0-5]\d\s*-\s*([01]\d|2[0-3]):[0-5]\d$/', $horario_programa)) {
                http_response_code(400);
                echo json_encode(['error' => 'El horario debe estar en formato 24h (HH:MM - HH:MM), ej: 20:00 - 23:00']);
                exit;
            }
            if ($horario_programa !== '') {
                $partes = array_map('trim', explode('-', $horario_programa));
                $inicio = explode(':', $partes[0]);
                $fin = explode(':', $partes[1]);
                if ((int)$fin[0] < (int)$inicio[0] || ((int)$fin[0] === (int)$inicio[0] && (int)$fin[1] <= (int)$inicio[1])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'La hora de fin debe ser posterior a la hora de inicio']);
                    exit;
                }
            }

            if ($method === 'POST') {
                $sql = "INSERT INTO discjockey (nombre_artistico, nombre_real, cedula, telefono, correo, genero_favorito, horario_programa, nombre_programa, estado, fecha_ingreso) 
                        VALUES (:nombre_artistico, :nombre_real, :cedula, :telefono, :correo, :genero_favorito, :horario_programa, :nombre_programa, :estado, :fecha_ingreso)";
            } else {
                $sql = "UPDATE discjockey SET nombre_artistico = :nombre_artistico, nombre_real = :nombre_real, cedula = :cedula, 
                        telefono = :telefono, correo = :correo, genero_favorito = :genero_favorito, 
                        horario_programa = :horario_programa, nombre_programa = :nombre_programa, 
                        estado = :estado, fecha_ingreso = :fecha_ingreso WHERE id = :id";
            }
            $stmt = $pdo->prepare($sql);
            $params = [
                ':nombre_artistico' => $nombre_artistico,
                ':nombre_real' => $nombre_real ?: null,
                ':cedula' => $cedula,
                ':telefono' => $telefono ?: null,
                ':correo' => $correo ?: null,
                ':genero_favorito' => $input['genero_favorito'] ?: null,
                ':horario_programa' => $horario_programa ?: null,
                ':nombre_programa' => trim($input['nombre_programa'] ?? '') ?: null,
                ':estado' => $input['estado'] ?? 1,
                ':fecha_ingreso' => $input['fecha_ingreso'] ?: null,
            ];
            if ($method === 'PUT') $params[':id'] = $input['id'];
            $stmt->execute($params);
            echo json_encode(['message' => $method === 'POST' ? 'Discjockey creado con exito' : 'Discjockey actualizado con exito']);
            break;

        case 'DELETE':
            $sql = "DELETE FROM discjockey WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $input['id']]);
            echo json_encode(['message' => 'Discjockey eliminado con exito']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Metodo no permitido']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
