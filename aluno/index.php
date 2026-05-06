<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('aluno');
$pageTitle = 'Aluno — Dashboard';
$pub = loadPublicacoes();
$aulasDisp = count(array_filter($pub['aulas'] ?? []));
$quizDisp  = count(array_filter($pub['questionarios'] ?? []));
include __DIR__ . '/../includes/header.php';
?>
<h4 class="fw-bold mb-4"><i class="bi bi-speedometer2 me-2"></i>Meu Painel</h4>
<div class="row g-3 mb-4">
  <div class="col-sm-6">
    <div class="card text-center p-3">
      <div class="display-5 text-primary"><i class="bi bi-journal-text"></i></div>
      <h2 class="fw-bold"><?= $aulasDisp ?></h2>
      <p class="text-muted mb-0">Aulas Disponíveis</p>
    </div>
  </div>
  <div class="col-sm-6">
    <div class="card text-center p-3">
      <div class="display-5 text-warning"><i class="bi bi-patch-question-fill"></i></div>
      <h2 class="fw-bold"><?= $quizDisp ?></h2>
      <p class="text-muted mb-0">Questionários Disponíveis</p>
    </div>
  </div>
</div>
<div class="d-flex gap-2 flex-wrap">
  <a href="aula.php" class="btn btn-primary"><i class="bi bi-journal-text me-1"></i>Ver Aulas</a>
  <a href="questionario.php" class="btn btn-warning text-white"><i class="bi bi-patch-question me-1"></i>Fazer Questionários</a>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
