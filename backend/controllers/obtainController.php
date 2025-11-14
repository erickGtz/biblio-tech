<?php
// backend/controllers/obtenerApuntes.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

session_start();

require_once '../config/database.php';
require_once '../models/Apunte.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Método no permitido"]);
        exit;
    }

    // Conexión a la base de datos
    $db = (new Database())->getConnection();
    $apunte = new Apunte($db);

    // Obtener apuntes
    $result = $apunte->obtenerTodos();

    if (!$result || $result->rowCount() === 0) {
        echo json_encode(["success" => true, "data" => []]);
        exit;
    }

    $apuntes = [];
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $apuntes[] = [
            "idApuntes" => intval($row['idApuntes']),
            "titulo" => $row['titulo'],
            "descripcion" => $row['descripcion'],
            "materia" => $row['materia'],
            "semestre" => $row['semestre'],
            "universidad" => $row['universidad'],
            "carrera" => $row['carrera'],
            "etiquetas" => $row['etiquetas'],
            "ruta_archivo" => $row['ruta_archivo'],
            "fecha_subida" => $row['fecha_subida'],
            "idUsuario" => $row['idUsuario'],
            "nombre_usuario" => $row['nombre_usuario']
        ];
    }

    echo json_encode([
        "success" => true,
        "data" => $apuntes
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error interno: " . $e->getMessage()
    ]);
}
