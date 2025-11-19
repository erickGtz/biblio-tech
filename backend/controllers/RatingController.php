<?php
require_once "../config/database.php";
require_once "../models/Rating.php";

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$database = new Database();
$db = $database->getConnection();
$ratingModel = new Rating($db);

$method = $_SERVER["REQUEST_METHOD"];

try {
    if ($method === "POST") {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("JSON inválido");
        }

    


        // CORREGIDO: usar id_usuario en lugar de user_id
        if (!isset($data["user_id"]) || !isset($data["apunte_id"]) || !isset($data["rating"])) {
            throw new Exception("Faltan campos requeridos: id_usuario, id_apunte, rating");
        }

        $id_usuario = (int)$data["user_id"];
        $id_apunte = (int)$data["apunte_id"];
        $ratingValue = (int)$data["rating"];

        if ($id_usuario <= 0) throw new Exception("ID de usuario inválido");
        if ($id_apunte <= 0) throw new Exception("ID de apunte inválido");
        if ($ratingValue < 1 || $ratingValue > 5) throw new Exception("Rating debe ser entre 1 y 5");

        $success = $ratingModel->setRating($id_usuario, $id_apunte, $ratingValue);
        
        if (!$success) {
            throw new Exception("Error al guardar la calificación");
        }

        $averageData = $ratingModel->getAverage($id_apunte);
        $userRating = $ratingModel->getUserRating($id_usuario, $id_apunte);

        echo json_encode([
            "success" => true,
            "message" => "Voto registrado correctamente",
            "average" => round((float)$averageData['average'], 2),
            "vote_count" => (int)$averageData['total'],
            "user_rating" => $userRating
        ]);

    } else if ($method === "GET") {
        // CORREGIDO: usar id_usuario en lugar de user_id
        $id_apunte = isset($_GET["apunte_id"]) ? (int)$_GET["apunte_id"] : 0;
        $id_usuario = isset($_GET["user_id"]) ? (int)$_GET["user_id"] : 0;


        if ($id_apunte <= 0) {
            throw new Exception("Se requiere un id_apunte válido");
        }

        $averageData = $ratingModel->getAverage($id_apunte);
        $userRating = $id_usuario > 0 ? $ratingModel->getUserRating($id_usuario, $id_apunte) : 0;

        echo json_encode([
            "success" => true,
            "average" => round((float)$averageData['average'], 2),
            "vote_count" => (int)$averageData['total'],
            "user_rating" => $userRating
        ]);

    } else {
        throw new Exception("Método no permitido: $method");
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>