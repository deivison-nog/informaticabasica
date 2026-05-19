<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$user = $_SESSION['user'] ?? null;
$roleName = ['admin' => 'Administrador', 'professor' => 'Professor', 'aluno' => 'Aluno'];
$roleColor = ['admin' => 'danger', 'professor' => 'primary', 'aluno' => 'success'];
$role = $user['role'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle ?? 'Informática Básica') ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body { background: #f8f9fa; }
    .navbar-brand { font-weight: 700; letter-spacing: .5px; }
    .sidebar { min-height: calc(100vh - 56px); background: #212529; }
    .sidebar .nav-link { color: rgba(255,255,255,.75); border-radius: 6px; margin-bottom: 2px; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,.1); }
    .sidebar .nav-link i { width: 20px; }
    .card { border: none; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
    .badge-role { font-size: .75rem; }
  </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark px-3">
  <span class="navbar-brand"><i class="bi bi-pc-display-horizontal me-2"></i>Informática Básica</span>
  <div class="d-flex align-items-center gap-2">
    <span class="badge bg-<?= $roleColor[$role] ?? 'secondary' ?> badge-role"><?= $roleName[$role] ?? $role ?></span>
    <span class="text-white small"><?= htmlspecialchars($user['nome'] ?? $user['cpf'] ?? '') ?></span>
    <a href="<?= rootPath() ?>logout.php"
       class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right"></i> Sair</a>
  </div>
</nav>
<div class="container-fluid">
  <div class="row">
    <nav class="col-md-2 col-lg-2 d-none d-md-block sidebar py-3 px-2">
      <ul class="nav flex-column">
<?php
$base = rootPath();
if ($role === 'admin'): ?>
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>admin/index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>admin/usuarios.php"><i class="bi bi-people"></i> Usuários</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>admin/add_user.php"><i class="bi bi-person-plus"></i> Adicionar Usuário</a></li>
<?php elseif ($role === 'professor'): ?>
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>professor/index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>professor/aulas.php"><i class="bi bi-book"></i> Planos de Aula</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>professor/editar_slide.php"><i class="bi bi-pencil-square"></i> Editar Slides</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>professor/publicar.php"><i class="bi bi-send"></i> Publicar Conteúdo</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>professor/add_aluno.php"><i class="bi bi-person-plus"></i> Adicionar Aluno</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>professor/editar_alunos.php"><i class="bi bi-person-gear"></i> Editar Alunos</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>professor/ranking_geral.php"><i class="bi bi-trophy"></i> Ranking Geral</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>professor/ver_questionarios.php"><i class="bi bi-clipboard2-data"></i> Respostas dos Alunos</a></li>
<?php elseif ($role === 'aluno'): ?>
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>aluno/index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>aluno/aula.php"><i class="bi bi-journal-text"></i> Minhas Aulas</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= $base ?>aluno/questionario.php"><i class="bi bi-patch-question"></i> Questionários</a></li>
<?php endif; ?>
      </ul>
    </nav>
    <main class="col-md-10 col-lg-10 ms-sm-auto px-4 py-4">
