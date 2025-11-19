<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Comentario.php'; // ajústalo según tu nombre de archivo

try {
    $db = (new Database())->getConnection();
    echo "Conexión exitosa a la BD<br>";
} catch (Exception $e) {
    die("Error de conexión: " . $e->getMessage());
}

$model = new CommentModel();

// DATOS FORZADOS (sin frontend)
$userId = 1;
$apunteId = 1;
$texto = "COMENTARIO FORZADO DE PRUEBA " . date("Y-m-d H:i:s");

echo "Insertando comentario...<br>";

$result = $model->addComment($userId, $apunteId, $texto);

if ($result) {
    echo "✔ Comentario insertado correctamente<br>";
} else {
    echo "❌ Error al insertar comentario<br>";
}
