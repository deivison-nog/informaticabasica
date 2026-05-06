<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('professor');
$pageTitle       = 'Professor — Dashboard';
$users           = loadUsers();
$alunos          = array_filter($users, fn($u) => $u['role'] === 'aluno');
$pub             = loadPublicacoes();
$aulasPublicadas = count(array_filter($pub['aulas'] ?? []));
$quizPublicados  = count(array_filter($pub['questionarios'] ?? []));
$ranking         = getRanking();
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
<div class="d-flex gap-2 flex-wrap mb-4">
  <a href="aulas.php" class="btn btn-primary"><i class="bi bi-book me-1"></i>Planos de Aula</a>
  <a href="publicar.php" class="btn btn-success"><i class="bi bi-send me-1"></i>Publicar Conteúdo</a>
  <a href="add_aluno.php" class="btn btn-outline-secondary"><i class="bi bi-person-plus me-1"></i>Adicionar Aluno</a>
</div>

<!-- Ranking -->
<h5 class="fw-bold mb-3"><i class="bi bi-trophy-fill text-warning me-2"></i>Ranking dos Alunos</h5>
<?php if (empty($ranking)): ?>
  <p class="text-muted">Nenhuma nota registrada ainda.</p>
<?php else: ?>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Aluno</th>
          <th class="text-center">Aula 1</th>
          <th class="text-center">Aula 2</th>
          <th class="text-center">Aula 3</th>
          <th class="text-center">Aula 4</th>
          <th class="text-center">Total</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($ranking as $pos => $r): ?>
        <tr>
          <td>
            <?php if ($pos === 0): ?><i class="bi bi-trophy-fill text-warning"></i>
            <?php elseif ($pos === 1): ?><i class="bi bi-trophy-fill text-secondary"></i>
            <?php elseif ($pos === 2): ?><i class="bi bi-trophy-fill text-danger" style="color:#cd7f32!important"></i>
            <?php else: ?><?= $pos + 1 ?>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($r['nome']) ?></td>
          <?php for ($a = 1; $a <= 4; $a++):
            $d = $r['detalhe'][$a] ?? null;
          ?>
          <td class="text-center small">
            <?php if ($d): ?>
              <?= $d['acertos'] ?>/<?= $d['total'] ?>
            <?php else: ?>
              <span class="text-muted">—</span>
            <?php endif; ?>
          </td>
          <?php endfor; ?>
          <td class="text-center fw-bold"><?= $r['pontos'] ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<p class="text-muted small mt-2">Melhores notas por aluno em cada questionário. Máximo: 28 pontos.</p>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>

