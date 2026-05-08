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

    $userCpfCheck = $_SESSION['user']['cpf'] ?? '';
    $allScoresNow = loadScores();
    $jaRespondeu  = ($userCpfCheck !== '') && isset($allScoresNow[$userCpfCheck][$id]);

    // Show read-only view (no correct/wrong indicators) when already answered
    if ($jaRespondeu && $_SERVER['REQUEST_METHOD'] === 'GET') {
        include __DIR__ . '/../includes/header.php';
        $best      = $allScoresNow[$userCpfCheck][$id];
        $respostas = $best['respostas'] ?? [];
        $pct       = $best['total'] > 0 ? round($best['acertos'] / $best['total'] * 100) : 0;
        $cls       = $best['acertos'] === $best['total'] ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
        ?>
        <a href="questionario.php" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
        <h4 class="fw-bold mb-4"><?= htmlspecialchars($quiz['titulo']) ?></h4>
        <div class="alert alert-<?= $cls ?> fw-semibold mb-4">
          <i class="bi bi-bar-chart-fill me-1"></i>
          Sua nota: <strong><?= $best['acertos'] ?>/<?= $best['total'] ?></strong> (<?= $pct ?>%)
        </div>
        <?php foreach ($quiz['perguntas'] as $qi => $pergunta):
            $escolha = $respostas[(string)$qi] ?? null;
        ?>
        <div class="card mb-3">
          <div class="card-body">
            <p class="fw-semibold mb-2"><?= ($qi+1) ?>. <?= htmlspecialchars($pergunta['texto']) ?></p>
            <?php foreach ($pergunta['opcoes'] as $letra => $texto):
                $isSelected = ($escolha === $letra);
            ?>
            <div class="form-check py-1 px-3 rounded <?= $isSelected ? 'bg-primary bg-opacity-10' : '' ?>">
              <span class="fw-bold"><?= strtoupper($letra) ?></span> — <?= htmlspecialchars($texto) ?>
              <?php if ($isSelected): ?><span class="text-primary ms-1"><i class="bi bi-check2"></i></span><?php endif; ?>
            </div>
            <?php endforeach; ?>
            <?php if ($escolha === null): ?>
            <p class="text-muted mt-1 mb-0 small"><i class="bi bi-dash-circle me-1"></i>Não respondida.</p>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
        <?php
        include __DIR__ . '/../includes/footer.php';
        exit;
    }

    // Server-side correction on POST submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quiz_id'])
        && (int)$_POST['quiz_id'] === $id) {
        $resultados = [];
        $acertos = 0;
        $respostasEnviadas = [];
        foreach ($quiz['perguntas'] as $qi => $pergunta) {
            $resposta = $_POST['q' . $qi] ?? null;
            $gabarito = $pergunta['gabarito'];
            $correto  = ($resposta === $gabarito);
            if ($correto) $acertos++;
            $respostasEnviadas[$qi] = $resposta;
            $resultados[$qi] = [
                'resposta' => $resposta,
                'opcoes'   => $pergunta['opcoes'],
            ];
        }
        $total = count($quiz['perguntas']);
        $resultados['_score'] = ['acertos' => $acertos, 'total' => $total];

        // Persist best score and individual answers for professor review
        $userCpf = $_SESSION['user']['cpf'] ?? '';
        if ($userCpf !== '') {
            saveBestScore($userCpf, $id, $acertos, $total, $respostasEnviadas);
        }
    }

    include __DIR__ . '/../includes/header.php';
    ?>
    <a href="questionario.php" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
    <h4 class="fw-bold mb-4"><?= htmlspecialchars($quiz['titulo']) ?></h4>

    <?php if ($resultados !== null):
        $score   = $resultados['_score'];
        $pct     = $score['total'] > 0 ? round($score['acertos'] / $score['total'] * 100) : 0;
        $cls     = $score['acertos'] === $score['total'] ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
    ?>
    <div class="alert alert-<?= $cls ?> fw-semibold mb-4">
      <i class="bi bi-bar-chart-fill me-1"></i>
      Sua nota: <strong><?= $score['acertos'] ?> de <?= $score['total'] ?> questões</strong> (<?= $pct ?>%)
    </div>
    <?php foreach ($quiz['perguntas'] as $qi => $pergunta):
        $r = $resultados[$qi];
    ?>
    <div class="card mb-3">
      <div class="card-body">
        <p class="fw-semibold mb-2"><?= ($qi+1) ?>. <?= htmlspecialchars($pergunta['texto']) ?></p>
        <?php foreach ($r['opcoes'] as $letra => $texto):
            $isSelected = ($r['resposta'] === $letra);
        ?>
        <div class="form-check py-1 px-3 rounded <?= $isSelected ? 'bg-primary bg-opacity-10' : '' ?>">
          <span class="fw-bold"><?= strtoupper($letra) ?></span> — <?= htmlspecialchars($texto) ?>
          <?php if ($isSelected): ?><span class="text-primary ms-1"><i class="bi bi-check2"></i></span><?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php if ($r['resposta'] === null): ?>
        <p class="text-muted mt-1 mb-0 small"><i class="bi bi-dash-circle me-1"></i>Não respondida.</p>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
    <a href="questionario.php" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>Voltar aos Questionários
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
$userCpf   = $_SESSION['user']['cpf'] ?? '';
$allScores = loadScores();
?>
<h4 class="fw-bold mb-4"><i class="bi bi-patch-question me-2"></i>Questionários</h4>
<div class="row g-3">
  <?php foreach ($aulas as $id => $a):
    $disponivel = isQuestionarioPublicado($id);
    $best       = $allScores[$userCpf][$id] ?? null;
  ?>
  <div class="col-md-6">
    <div class="card h-100 <?= !$disponivel ? 'opacity-50' : '' ?>">
      <div class="card-header bg-<?= $a['cor'] ?> text-white fw-semibold"><?= $a['titulo'] ?></div>
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <?php if ($disponivel): ?>
            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Disponível</span>
            <?php if (!$best): ?>
              <a href="questionario.php?id=<?= $id ?>" class="btn btn-<?= $a['cor'] ?> btn-sm">
                <i class="bi bi-pencil-square me-1"></i>Fazer Questionário
              </a>
            <?php else: ?>
              <a href="questionario.php?id=<?= $id ?>" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-eye me-1"></i>Ver Respostas
              </a>
            <?php endif; ?>
          <?php else: ?>
            <span class="badge bg-secondary"><i class="bi bi-lock me-1"></i>Não liberado</span>
            <span class="text-muted small">Aguarde o professor</span>
          <?php endif; ?>
        </div>
        <?php if ($best): ?>
        <div class="small text-muted">
          <i class="bi bi-trophy-fill text-warning me-1"></i>
          Melhor nota: <strong><?= $best['acertos'] ?>/<?= $best['total'] ?></strong>
          (<?= round($best['acertos'] / $best['total'] * 100) ?>%)
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>

