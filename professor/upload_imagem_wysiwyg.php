<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('professor');

header('Content-Type: application/json; charset=utf-8');

function respondJson(int $status, array $payload): void {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondJson(405, ['ok' => false, 'message' => 'Método não permitido.']);
}

$aulaId = (int)($_POST['aula'] ?? 0);
if ($aulaId < 1 || $aulaId > 4) {
    respondJson(400, ['ok' => false, 'message' => 'Aula inválida.']);
}

if (empty($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
    respondJson(400, ['ok' => false, 'message' => 'Nenhuma imagem enviada ou upload inválido.']);
}

$file = $_FILES['imagem'];
if (($file['size'] ?? 0) > 8 * 1024 * 1024) {
    respondJson(400, ['ok' => false, 'message' => 'A imagem excede 8 MB.']);
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = (string)$finfo->file($file['tmp_name']);
$allowed = [
    'image/png' => 'png',
    'image/jpeg' => 'jpg',
    'image/webp' => 'webp',
    'image/gif' => 'gif',
];
if (!isset($allowed[$mime]) || getimagesize($file['tmp_name']) === false) {
    respondJson(400, ['ok' => false, 'message' => 'Formato inválido. Use PNG, JPG, WEBP ou GIF.']);
}

$destDir = __DIR__ . '/../images/slides/aula' . $aulaId;
if (!is_dir($destDir) && !mkdir($destDir, 0755, true)) {
    respondJson(500, ['ok' => false, 'message' => 'Não foi possível preparar a pasta de imagens.']);
}

$filename = 'slide_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
$destFile = $destDir . '/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $destFile)) {
    respondJson(500, ['ok' => false, 'message' => 'Falha ao salvar a imagem.']);
}

$url = '../images/slides/aula' . $aulaId . '/' . rawurlencode($filename) . '?v=' . filemtime($destFile);
respondJson(200, ['ok' => true, 'url' => $url]);
