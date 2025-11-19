<?php
require_once __DIR__ . '/../config/database.php';

class CommentModel {

    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    // Obtener comentarios de un apunte
    public function getComments($apunteId) {
        $sql = "SELECT c.*, u.nombre, u.apellido_paterno 
                FROM comentarios c
                INNER JOIN usuarios u ON u.idUsuario = c.idUsuario
                WHERE c.idApunte = ?
                ORDER BY c.fecha_comentario DESC";

        $query = $this->db->prepare($sql);
        $query->execute([$apunteId]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear comentario
    public function addComment($user, $apunte, $texto) {
        $sql = "INSERT INTO comentarios (contenido, fecha_comentario, idUsuario, idApunte)
                VALUES (?, NOW(), ?, ?)";

        $query = $this->db->prepare($sql);
        return $query->execute([$texto, $user, $apunte]);
    }
}
