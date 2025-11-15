<?php
// backend/models/Apunte.php
class Apunte {
    private $conn;
    private $table = 'apuntes'; // usa minúsculas si tu tabla está en minúsculas

    public function __construct($db) { $this->conn = $db; }

    public function crear(array $data) : bool {
        $sql = "INSERT INTO {$this->table}
                (titulo, ruta_archivo, idUsuario, fecha_subida, materia,
                 descripcion, semestre, universidad, carrera, etiquetas)
                VALUES
                (:titulo, :ruta, :idUsuario, :fecha,
                 :materia, :descripcion, :semestre, :universidad, :carrera, :etiquetas)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':titulo' => $data['titulo'],
            ':ruta' => $data['ruta_archivo'],
            ':idUsuario' => $data['idUsuario'],
            ':fecha' => $data['fecha_subida'],
            ':materia' => $data['materia'],
            ':descripcion' => $data['descripcion'],
            ':semestre' => $data['semestre'],
            ':universidad' => $data['universidad'],
            ':carrera' => $data['carrera'],
            ':etiquetas' => $data['etiquetas'],
        ]);
    }

    // public function obtenerTodos() {
    //     $sql = "SELECT * FROM " . $this->table . " ORDER BY fecha_subida DESC";
    //     $stmt = $this->conn->prepare($sql);
    //     $stmt->execute();
    //     return $stmt;
    // }
    public function obtenerTodos() {
        $sql = "SELECT 
                    a.*, 
                    CONCAT_WS(' ', u.nombre, u.apellido_paterno) AS nombre_usuario
                FROM 
                    " . $this->table . " a
                INNER JOIN 
                    usuarios u ON a.idUsuario = u.idUsuario
                ORDER BY 
                    a.fecha_subida DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

}
