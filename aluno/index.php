<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('aluno');
$pageTitle = 'Aluno — Dashboard';
$pub       = loadPublicacoes();
$aulasDisp = count(array_filter($pub['aulas'] ?? []));
$quizDisp  = count(array_filter($pub['questionarios'] ?? []));
$ranking   = getRanking();
$userCpf   = $_SESSION['user']['cpf'] ?? '';
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
<div class="d-flex gap-2 flex-wrap mb-4">
  <a href="aula.php" class="btn btn-primary"><i class="bi bi-journal-text me-1"></i>Ver Aulas</a>
  <a href="questionario.php" class="btn btn-warning text-white"><i class="bi bi-patch-question me-1"></i>Fazer Questionários</a>
</div>

<!-- Ranking -->
<h5 class="fw-bold mb-3"><i class="bi bi-trophy-fill text-warning me-2"></i>Ranking dos Alunos</h5>
<?php if (empty($ranking)): ?>
  <p class="text-muted">Nenhuma nota registrada ainda. Faça os questionários para aparecer no ranking!</p>
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
        <?php foreach ($ranking as $pos => $r):
          $isMe = ($r['cpf'] === $userCpf);
        ?>
        <tr class="<?= $isMe ? 'table-warning fw-semibold' : '' ?>">
          <td>
            <?php if ($pos === 0): ?><i class="bi bi-trophy-fill text-warning"></i>
            <?php elseif ($pos === 1): ?><i class="bi bi-trophy-fill text-secondary"></i>
            <?php elseif ($pos === 2): ?><i class="bi bi-trophy-fill text-danger" style="color:#cd7f32!important"></i>
            <?php else: ?><?= $pos + 1 ?>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($r['nome']) ?><?= $isMe ? ' <span class="badge bg-warning text-dark ms-1">você</span>' : '' ?></td>
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
<p class="text-muted small mt-2">Ranking baseado na soma das melhores notas em cada questionário. Máximo: 28 pontos (4 questionários × 7 questões).</p>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>

