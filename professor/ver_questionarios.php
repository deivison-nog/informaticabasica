<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('professor');

$allQuizzes = require __DIR__ . '/../content/questionarios_data.php';

$aulas = [
    1 => ['titulo' => 'Aula 1 — Conceitos Básicos de Informática',    'cor' => 'primary'],
    2 => ['titulo' => 'Aula 2 — Internet, E-mail e Arquivos',         'cor' => 'info'],
    3 => ['titulo' => 'Aula 3 — Pacote Office, Nuvem e Redes',        'cor' => 'success'],
    4 => ['titulo' => 'Aula 4 — Segurança Digital e Tendências 2026', 'cor' => 'warning'],
];

$users   = loadUsers();
$alunos  = array_values(array_filter($users, fn($u) => $u['role'] === 'aluno'));
$scores  = loadScores();

$cpf  = $_GET['cpf']  ?? null;
$quiz = (int)($_GET['quiz'] ?? 0);

// Find selected aluno
$alunoSel = null;
if ($cpf !== null) {
    foreach ($alunos as $a) {
        if ($a['cpf'] === $cpf) { $alunoSel = $a; break; }
    }
}

$pageTitle = 'Professor — Respostas dos Alunos';
include __DIR__ . '/../includes/header.php';
?>
<h4 class="fw-bold mb-4"><i class="bi bi-clipboard2-data me-2"></i>Respostas dos Alunos</h4>

<!-- Filter form -->
<form method="get" action="ver_questionarios.php" class="row g-2 mb-4 align-items-end">
  <div class="col-sm-5">
    <label class="form-label fw-semibold">Aluno</label>
    <select name="cpf" class="form-select">
      <option value="">— Selecione um aluno —</option>
      <?php foreach ($alunos as $a): ?>
      <option value="<?= htmlspecialchars($a['cpf']) ?>"
        <?= ($cpf === $a['cpf']) ? 'selected' : '' ?>>
        <?= htmlspecialchars($a['nome']) ?> (<?= htmlspecialchars($a['cpf']) ?>)
      </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-sm-4">
    <label class="form-label fw-semibold">Questionário</label>
    <select name="quiz" class="form-select">
      <option value="0">— Selecione um questionário —</option>
      <?php foreach ($aulas as $aid => $a): ?>
      <option value="<?= $aid ?>" <?= ($quiz === $aid) ? 'selected' : '' ?>>
        <?= htmlspecialchars($a['titulo']) ?>
      </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-sm-3">
    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Ver Respostas</button>
  </div>
</form>

<?php if ($alunoSel && $quiz && isset($allQuizzes[$quiz])):
    $quizData  = $allQuizzes[$quiz];
    $scoreData = $scores[$cpf][$quiz] ?? null;

    if (!$scoreData): ?>
    <div class="alert alert-warning">
      <i class="bi bi-exclamation-triangle me-1"></i>
      <strong><?= htmlspecialchars($alunoSel['nome']) ?></strong> ainda não respondeu este questionário.
    </div>
<?php else:
    $respostas = $scoreData['respostas'] ?? [];
    $pct = $scoreData['total'] > 0 ? round($scoreData['acertos'] / $scoreData['total'] * 100) : 0;
    $cls = $scoreData['acertos'] === $scoreData['total'] ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
    ?>
    <div class="alert alert-<?= $cls ?> fw-semibold mb-2">
      <i class="bi bi-person-check me-1"></i>
      <strong><?= htmlspecialchars($alunoSel['nome']) ?></strong> —
      <?= htmlspecialchars($quizData['titulo']) ?>:
      <?= $scoreData['acertos'] ?>/<?= $scoreData['total'] ?> questões (<?= $pct ?>%)
    </div>

    <?php foreach ($quizData['perguntas'] as $qi => $pergunta):
        $escolha  = $respostas[(string)$qi] ?? null;
        $gabarito = $pergunta['gabarito'];
        $correto  = ($escolha === $gabarito);
    ?>
    <div class="card mb-3 border-<?= $correto ? 'success' : 'danger' ?>">
      <div class="card-body">
        <p class="fw-semibold mb-2"><?= ($qi+1) ?>. <?= htmlspecialchars($pergunta['texto']) ?></p>
        <?php foreach ($pergunta['opcoes'] as $letra => $texto):
            $isSelected = ($escolha === $letra);
            $isCorrect  = ($gabarito === $letra);
            $bg = '';
            if ($isSelected && $isCorrect)       $bg = 'bg-success bg-opacity-25';
            elseif ($isSelected && !$isCorrect)  $bg = 'bg-danger  bg-opacity-25';
            elseif (!$isSelected && $isCorrect)  $bg = 'bg-success bg-opacity-10';
        ?>
        <div class="form-check py-1 px-3 rounded <?= $bg ?>">
          <span class="fw-bold"><?= strtoupper($letra) ?></span> — <?= htmlspecialchars($texto) ?>
          <?php if ($isSelected && $isCorrect):  ?><span class="text-success ms-1">✅</span>
          <?php elseif ($isSelected && !$isCorrect): ?><span class="text-danger ms-1">❌</span>
          <?php elseif (!$isSelected && $isCorrect): ?><span class="text-success ms-1 small">← correta</span>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php if ($escolha === null): ?>
        <p class="text-warning mt-1 mb-0 small"><i class="bi bi-exclamation-triangle me-1"></i>Não respondida.</p>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>

<?php endif;
elseif ($cpf !== null || $quiz): ?>
<div class="alert alert-secondary"><i class="bi bi-info-circle me-1"></i>Selecione um aluno e um questionário para visualizar as respostas.</div>
<?php endif; ?>

<!-- Summary table for all students (if quiz selected without specific student) -->
<?php if (!$cpf && $quiz && isset($allQuizzes[$quiz])): ?>
<h5 class="fw-bold mt-2 mb-3">Visão geral — <?= htmlspecialchars($aulas[$quiz]['titulo']) ?></h5>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-dark">
        <tr>
          <th>Aluno</th>
          <th class="text-center">Acertos</th>
          <th class="text-center">%</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($alunos as $a):
            $s = $scores[$a['cpf']][$quiz] ?? null;
        ?>
        <tr>
          <td><?= htmlspecialchars($a['nome']) ?></td>
          <td class="text-center"><?= $s ? $s['acertos'].'/'.$s['total'] : '—' ?></td>
          <td class="text-center">
            <?php if ($s):
                $p = round($s['acertos']/$s['total']*100);
                $c = $s['acertos']===$s['total'] ? 'success' : ($p>=50?'warning':'danger');
            ?>
            <span class="badge bg-<?= $c ?>"><?= $p ?>%</span>
            <?php else: ?><span class="text-muted small">Não respondeu</span><?php endif; ?>
          </td>
          <td class="text-end">
            <?php if ($s): ?>
            <a href="ver_questionarios.php?cpf=<?= urlencode($a['cpf']) ?>&quiz=<?= $quiz ?>" class="btn btn-sm btn-outline-primary">
              <i class="bi bi-eye me-1"></i>Ver
            </a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
