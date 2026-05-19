<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
$pageTitle = 'Admin — Dashboard';
$users = loadUsers();
$alunos    = array_filter($users, fn($u) => $u['role'] === 'aluno');
$professores = array_filter($users, fn($u) => $u['role'] === 'professor');
include __DIR__ . '/../includes/header.php';
?>
<h4 class="fw-bold mb-4"><i class="bi bi-speedometer2 me-2"></i>Dashboard do Administrador</h4>
<div class="row g-3 mb-4">
  <div class="col-sm-4">
    <div class="card text-center p-3">
      <div class="display-5 text-primary"><i class="bi bi-people-fill"></i></div>
      <h2 class="fw-bold"><?= count($users) ?></h2>
      <p class="text-muted mb-0">Total de Usuários</p>
    </div>
  </div>
  <div class="col-sm-4">
    <div class="card text-center p-3">
      <div class="display-5 text-success"><i class="bi bi-mortarboard-fill"></i></div>
      <h2 class="fw-bold"><?= count($alunos) ?></h2>
      <p class="text-muted mb-0">Alunos</p>
    </div>
  </div>
  <div class="col-sm-4">
    <div class="card text-center p-3">
      <div class="display-5 text-warning"><i class="bi bi-person-badge-fill"></i></div>
      <h2 class="fw-bold"><?= count($professores) ?></h2>
      <p class="text-muted mb-0">Professores</p>
    </div>
  </div>
</div>
<div class="d-flex gap-2">
  <a href="add_user.php" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Adicionar Usuário</a>
  <a href="usuarios.php" class="btn btn-outline-secondary"><i class="bi bi-list-ul me-1"></i>Listar Usuários</a>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
