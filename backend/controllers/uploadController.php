<?php
// backend/controllers/uploadController.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit(); }

session_start();

require_once '../config/database.php';
require_once '../models/Apunte.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(["success"=>false, "message"=>"Método no permitido"]); exit;
    }

    // Validar archivo
    if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(["success"=>false, "message"=>"Archivo no recibido"]); exit;
    }
    $file = $_FILES['archivo'];
    $max = 25 * 1024 * 1024; // 25MB
    if ($file['size'] > $max) {
        http_response_code(400);
        echo json_encode(["success"=>false, "message"=>"Archivo mayor a 25 MB"]); exit;
    }
    $allowed = ['pdf','doc','docx','ppt','pptx'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        http_response_code(400);
        echo json_encode(["success"=>false, "message"=>"Formato no permitido"]); exit;
    }

    // Campos del form
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $materia = trim($_POST['materia'] ?? '');
    $semestre = trim($_POST['semestre'] ?? '');
    $universidad = trim($_POST['universidad'] ?? '');
    $carrera = trim($_POST['carrera'] ?? '');
    $etiquetas = trim($_POST['etiquetas'] ?? '');

    if ($titulo === '' || $descripcion === '' || $materia === '' || $semestre === '' || $universidad === '' || $carrera === '') {
        http_response_code(400);
        echo json_encode(["success"=>false, "message"=>"Completa todos los campos obligatorios"]); exit;
    }

    // Usuario (desde sesión si existe)
    $idUsuario = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : 1; // fallback

    // Mover archivo a /uploads
    $root = dirname(__DIR__, 2); // carpeta del proyecto
    $uploadsDir = $root . DIRECTORY_SEPARATOR . 'uploads';
    if (!is_dir($uploadsDir)) { mkdir($uploadsDir, 0777, true); }

    $safeName = uniqid('apunte_', true) . '.' . $ext;
    $destPath = $uploadsDir . DIRECTORY_SEPARATOR . $safeName;
    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        http_response_code(500);
        echo json_encode(["success"=>false, "message"=>"No se pudo guardar el archivo"]); exit;
    }

    // Ruta a guardar en DB (relativa)
    $ruta_archivo = 'uploads/' . $safeName;

    // Guardar en DB
    $db = (new Database())->getConnection();
    $apunte = new Apunte($db);
    $ok = $apunte->crear([
        'titulo' => $titulo,
        'ruta_archivo' => $ruta_archivo,
        'idUsuario' => $idUsuario,
        'fecha_subida' => date('Y-m-d'),
        'descripcion' => $descripcion,
        'semestre' => $semestre,
        'universidad' => $universidad,
        'carrera' => $carrera,
        'etiquetas' => $etiquetas,
        'materia' => $materia, // si luego desean normalizarla a otra tabla, aquí mismo la procesan
    ]);

    if ($ok) {
        echo json_encode(["success"=>true, "message"=>"Apunte registrado", "ruta"=>$ruta_archivo]);
    } else {
        http_response_code(500);
        echo json_encode(["success"=>false, "message"=>"No se pudo registrar en la base de datos"]);
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(["success"=>false, "message"=>"Error: ".$e->getMessage()]);
}
