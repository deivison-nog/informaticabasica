<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('aluno');

$allQuizzes = require __DIR__ . '/../content/questionarios_data.php';

$aulas = [
    1 => ['titulo' => 'Aula 1 — Conceitos Básicos de Informática',    'cor' => 'primary'],
    2 => ['titulo' => 'Aula 2 — Internet, E-mail e Arquivos',         'cor' => 'info'],
    3 => ['titulo' => 'Aula 3 — Pacote Office, Nuvem e Redes',        'cor' => 'success'],
    4 => ['titulo' => 'Aula 4 — Segurança Digital e Tendências 2026', 'cor' => 'warning'],
];

$id = (int)($_GET['id'] ?? 0);

if ($id && isQuestionarioPublicado($id) && isset($allQuizzes[$id])) {
    $quiz = $allQuizzes[$id];
    $pageTitle = $quiz['titulo'];
    $resultados = null;

    // Server-side correction on POST submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quiz_id'])
        && (int)$_POST['quiz_id'] === $id) {
        $resultados = [];
        $acertos = 0;
        foreach ($quiz['perguntas'] as $qi => $pergunta) {
            $resposta = $_POST['q' . $qi] ?? null;
            $gabarito = $pergunta['gabarito'];
            $correto  = ($resposta === $gabarito);
            if ($correto) $acertos++;
            $resultados[$qi] = [
                'resposta' => $resposta,
                'gabarito' => $gabarito,
                'correto'  => $correto,
                'opcoes'   => $pergunta['opcoes'],
            ];
        }
        $resultados['_score'] = ['acertos' => $acertos, 'total' => count($quiz['perguntas'])];
    }

    include __DIR__ . '/../includes/header.php';
    ?>
    <a href="questionario.php" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
    <h4 class="fw-bold mb-4"><?= htmlspecialchars($quiz['titulo']) ?></h4>

    <?php if ($resultados !== null):
        $score = $resultados['_score'];
        $pct   = $score['total'] > 0 ? round($score['acertos'] / $score['total'] * 100) : 0;
        $cls   = $score['acertos'] === $score['total'] ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
    ?>
    <div class="alert alert-<?= $cls ?> fw-semibold mb-4">
      <i class="bi bi-bar-chart-fill me-1"></i>
      Você acertou <?= $score['acertos'] ?> de <?= $score['total'] ?> questões (<?= $pct ?>%).
    </div>
    <?php foreach ($quiz['perguntas'] as $qi => $pergunta):
        $r = $resultados[$qi];
    ?>
    <div class="card mb-3 border-<?= $r['correto'] ? 'success' : 'danger' ?>">
      <div class="card-body">
        <p class="fw-semibold mb-2"><?= ($qi+1) ?>. <?= htmlspecialchars($pergunta['texto']) ?></p>
        <?php foreach ($r['opcoes'] as $letra => $texto):
            $isSelected = ($r['resposta'] === $letra);
            $isCorrect  = ($r['gabarito'] === $letra);
            $bg = '';
            if ($isSelected && $isCorrect)  $bg = 'bg-success bg-opacity-25';
            elseif ($isSelected && !$isCorrect) $bg = 'bg-danger bg-opacity-25';
            elseif (!$isSelected && $isCorrect) $bg = 'bg-success bg-opacity-10';
        ?>
        <div class="form-check py-1 px-3 rounded <?= $bg ?>">
          <span class="fw-bold"><?= strtoupper($letra) ?></span> — <?= htmlspecialchars($texto) ?>
          <?php if ($isSelected && $isCorrect):  ?><span class="text-success ms-1">✅</span>
          <?php elseif ($isSelected && !$isCorrect): ?><span class="text-danger ms-1">❌</span>
          <?php elseif (!$isSelected && $isCorrect): ?><span class="text-success ms-1">← correta</span>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php if ($r['resposta'] === null): ?>
        <p class="text-warning mt-1 mb-0 small"><i class="bi bi-exclamation-triangle me-1"></i>Não respondida.</p>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
    <a href="questionario.php?id=<?= $id ?>" class="btn btn-outline-primary">
      <i class="bi bi-arrow-repeat me-1"></i>Refazer Questionário
    </a>

    <?php else: ?>
    <form method="post" action="questionario.php?id=<?= $id ?>">
      <input type="hidden" name="quiz_id" value="<?= $id ?>">
      <?php foreach ($quiz['perguntas'] as $qi => $pergunta): ?>
      <div class="card mb-3">
        <div class="card-body">
          <p class="fw-semibold mb-3"><?= ($qi+1) ?>. <?= htmlspecialchars($pergunta['texto']) ?></p>
          <?php foreach ($pergunta['opcoes'] as $letra => $texto): ?>
          <div class="form-check">
            <input class="form-check-input" type="radio"
                   name="q<?= $qi ?>" id="q<?= $qi ?>_<?= $letra ?>"
                   value="<?= htmlspecialchars($letra) ?>">
            <label class="form-check-label" for="q<?= $qi ?>_<?= $letra ?>">
              <strong><?= strtoupper($letra) ?></strong> — <?= htmlspecialchars($texto) ?>
            </label>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>
      <button type="submit" class="btn btn-success mb-4">
        <i class="bi bi-check-all me-1"></i>Corrigir Questionário
      </button>
    </form>
    <?php endif; ?>
    <?php
    include __DIR__ . '/../includes/footer.php';
    exit;
}

$pageTitle = 'Aluno — Questionários';
include __DIR__ . '/../includes/header.php';
?>
<h4 class="fw-bold mb-4"><i class="bi bi-patch-question me-2"></i>Questionários</h4>
<div class="row g-3">
  <?php foreach ($aulas as $id => $a):
    $disponivel = isQuestionarioPublicado($id);
  ?>
  <div class="col-md-6">
    <div class="card h-100 <?= !$disponivel ? 'opacity-50' : '' ?>">
      <div class="card-header bg-<?= $a['cor'] ?> text-white fw-semibold"><?= $a['titulo'] ?></div>
      <div class="card-body d-flex align-items-center justify-content-between">
        <?php if ($disponivel): ?>
          <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Disponível</span>
          <a href="questionario.php?id=<?= $id ?>" class="btn btn-<?= $a['cor'] ?> btn-sm">
            <i class="bi bi-pencil-square me-1"></i>Fazer Questionário
          </a>
        <?php else: ?>
          <span class="badge bg-secondary"><i class="bi bi-lock me-1"></i>Não liberado</span>
          <span class="text-muted small">Aguarde o professor</span>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>

