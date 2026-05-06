<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('professor');
$pageTitle = 'Professor — Dashboard';
$users = loadUsers();
$alunos = array_filter($users, fn($u) => $u['role'] === 'aluno');
$pub = loadPublicacoes();
$aulasPublicadas = count(array_filter($pub['aulas'] ?? []));
$quizPublicados  = count(array_filter($pub['questionarios'] ?? []));
include __DIR__ . '/../includes/header.php';
?>
<h4 class="fw-bold mb-4"><i class="bi bi-speedometer2 me-2"></i>Dashboard do Professor</h4>
<div class="row g-3 mb-4">
  <div class="col-sm-4">
    <div class="card text-center p-3">
      <div class="display-5 text-success"><i class="bi bi-mortarboard-fill"></i></div>
      <h2 class="fw-bold"><?= count($alunos) ?></h2>
      <p class="text-muted mb-0">Alunos Cadastrados</p>
    </div>
  </div>
  <div class="col-sm-4">
    <div class="card text-center p-3">
      <div class="display-5 text-primary"><i class="bi bi-journal-check"></i></div>
      <h2 class="fw-bold"><?= $aulasPublicadas ?>/4</h2>
      <p class="text-muted mb-0">Aulas Publicadas</p>
    </div>
  </div>
  <div class="col-sm-4">
    <div class="card text-center p-3">
      <div class="display-5 text-warning"><i class="bi bi-patch-check-fill"></i></div>
      <h2 class="fw-bold"><?= $quizPublicados ?>/4</h2>
      <p class="text-muted mb-0">Questionários Liberados</p>
    </div>
  </div>
</div>
<div class="d-flex gap-2 flex-wrap">
  <a href="aulas.php" class="btn btn-primary"><i class="bi bi-book me-1"></i>Planos de Aula</a>
  <a href="publicar.php" class="btn btn-success"><i class="bi bi-send me-1"></i>Publicar Conteúdo</a>
  <a href="add_aluno.php" class="btn btn-outline-secondary"><i class="bi bi-person-plus me-1"></i>Adicionar Aluno</a>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
