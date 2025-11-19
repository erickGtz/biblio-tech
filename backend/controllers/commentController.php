<?php
require_once __DIR__ . '/../models/Comentario.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

$model = new CommentModel();

switch ($method) {

    // ==========================
    // GET → Obtener comentarios
    // ==========================
    case 'GET':
        if (!isset($_GET['apunte_id'])) {
            echo json_encode(["success" => false, "message" => "Falta apunte_id"]);
            exit;
        }

        $apunteId = intval($_GET['apunte_id']);
        $result = $model->getComments($apunteId);

        echo json_encode([
            "success" => true,
            "data" => $result
        ]);
        break;

    // ==========================
    // POST → Guardar comentario
    // ==========================
    case 'POST':
        $json = json_decode(file_get_contents("php://input"), true);

        if (!isset($json["user_id"], $json["apunte_id"], $json["contenido"])) {
            echo json_encode(["success" => false, "message" => "Datos incompletos"]);
            exit;
        }

        $ok = $model->addComment(
            intval($json["user_id"]),
            intval($json["apunte_id"]),
            trim($json["contenido"])
        );

        echo json_encode([
            "success" => $ok,
            "message" => $ok ? "Comentario guardado" : "Error al guardar comentario"
        ]);
        break;

    default:
        echo json_encode(["success" => false, "message" => "Método no permitido"]);
        break;
}
