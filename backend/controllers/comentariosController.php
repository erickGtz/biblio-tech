<?php
require_once "../models/Comentario.php";

$comentario = new Comentario();

header("Content-Type: application/json");

switch($_SERVER["REQUEST_METHOD"]) {

    case "GET":
        $idApunte = $_GET["idApunte"] ?? null;
        echo json_encode($comentario->obtenerPorApunte($idApunte));
        break;


    case "POST":
        $contenido = $_POST["comentario"];
        $idUsuario = $_POST["idUsuario"];
        $idApunte  = $_POST["idApunte"];

        echo json_encode($comentario->insertar($contenido, $idUsuario, $idApunte));
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
}
