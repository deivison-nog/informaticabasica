<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('professor');

$aulaId = (int)($_POST['aula_id'] ?? 0);
$slot   = $_POST['slot'] ?? '';

// Validate aula
if ($aulaId < 1 || $aulaId > 4) {
    header('Location: aulas.php');
    exit;
}

// Validate slot name: must be foto01..foto05 + .png
if (!preg_match('/^foto0[1-5]\.png$/', $slot)) {
    $_SESSION['upload_erro'] = 'Slot inválido.';
    header('Location: ver_aula.php?id=' . $aulaId);
    exit;
}

if (empty($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['upload_erro'] = 'Nenhum arquivo enviado ou erro no upload.';
    header('Location: ver_aula.php?id=' . $aulaId);
    exit;
}

$file = $_FILES['imagem'];

// Validate MIME type and real image structure (PNG only, matching slide slot format).
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($file['tmp_name']);
if ($mime !== 'image/png' || @getimagesize($file['tmp_name']) === false) {
    $_SESSION['upload_erro'] = 'Tipo de arquivo não permitido. Envie uma imagem PNG válida.';
    header('Location: ver_aula.php?id=' . $aulaId);
    exit;
}

// Max 5 MB
if ($file['size'] > 5 * 1024 * 1024) {
    $_SESSION['upload_erro'] = 'Arquivo muito grande (máximo 5 MB).';
    header('Location: ver_aula.php?id=' . $aulaId);
    exit;
}

$destDir  = __DIR__ . '/../images/aula' . $aulaId . '/';
$destFile = $destDir . $slot;

if (!is_dir($destDir)) {
    mkdir($destDir, 0755, true);
}

if (!move_uploaded_file($file['tmp_name'], $destFile)) {
    $_SESSION['upload_erro'] = 'Falha ao salvar o arquivo. Verifique as permissões do servidor.';
    header('Location: ver_aula.php?id=' . $aulaId);
    exit;
}

$_SESSION['upload_ok'] = 'Imagem <strong>' . htmlspecialchars($slot) . '</strong> enviada com sucesso!';
header('Location: ver_aula.php?id=' . $aulaId);
exit;
