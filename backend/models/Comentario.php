<?php
require_once __DIR__ . '/../config/database.php';

class Comentario {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function obtenerPorApunte($idApunte) {
        $sql = "SELECT c.contenido, c.fecha_comentario,
                u.nombre, u.apellido_paterno, u.apellido_materno
                FROM comentarios c
                INNER JOIN usuarios u ON c.idUsuario = u.idUsuario
                WHERE c.idApunte = :idApunte
                ORDER BY c.idcomentarios DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":idApunte", $idApunte);
        $stmt->execute();

        return $stmt->fetchAll();
    }


    public function insertar($contenido, $idUsuario, $idApunte) {
        $fecha = date("Y-m-d H:i:s");

        $sql = "INSERT INTO comentarios (contenido, fecha_comentario, idUsuario, idApunte)
                VALUES (:contenido, :fecha, :idUsuario, :idApunte)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":contenido", $contenido);
        $stmt->bindParam(":fecha", $fecha);
        $stmt->bindParam(":idUsuario", $idUsuario);
        $stmt->bindParam(":idApunte", $idApunte);
        $stmt->execute();

        // retornar datos del usuario
        $sql2 = "SELECT nombre, apellido_paterno, apellido_materno 
                 FROM usuarios WHERE idUsuario = :idUsuario";

        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->bindParam(":idUsuario", $idUsuario);
        $stmt2->execute();
        $user = $stmt2->fetch();

        return [
            "contenido" => $contenido,
            "fecha" => $fecha,
            "nombre" => $user["nombre"] . " " . $user["apellido_paterno"] . " " . $user["apellido_materno"]
        ];
    }
}
