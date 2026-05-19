<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('professor');
$pageTitle = 'Professor — Ranking Geral';
$ranking = getRanking(null);
include __DIR__ . '/../includes/header.php';
?>

<h4 class="fw-bold mb-4"><i class="bi bi-trophy-fill text-warning me-2"></i>Ranking Geral dos Alunos</h4>
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
