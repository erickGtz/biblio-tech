<?php
// backend/models/Apunte.php
class Apunte {
    private $conn;
    private $table = 'apuntes'; // usa minúsculas si tu tabla está en minúsculas

    public function __construct($db) { $this->conn = $db; }

    /** 
     * Inserta un apunte con todos los campos extra del formulario.
     * $data = [
     *   titulo, ruta_archivo, idUsuario, fecha_subida,
     *   descripcion, semestre, universidad, carrera, etiquetas, materia
     * ]
     */
    public function crear(array $data) : bool {
        $sql = "INSERT INTO {$this->table}
                (titulo, ruta_archivo, idUsuario, fecha_subida, 
                 descripcion, semestre, universidad, carrera, etiquetas)
                VALUES
                (:titulo, :ruta, :idUsuario, :fecha,
                 :descripcion, :semestre, :universidad, :carrera, :etiquetas)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':titulo' => $data['titulo'],
            ':ruta' => $data['ruta_archivo'],
            ':idUsuario' => $data['idUsuario'],
            ':fecha' => $data['fecha_subida'],
            ':descripcion' => $data['descripcion'],
            ':semestre' => $data['semestre'],
            ':universidad' => $data['universidad'],
            ':carrera' => $data['carrera'],
            ':etiquetas' => $data['etiquetas'],
        ]);
    }
}
