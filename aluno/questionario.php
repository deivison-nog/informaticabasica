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
    include __DIR__ . '/../includes/header.php';
    ?>
    <a href="questionario.php" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
    <h4 class="fw-bold mb-4"><?= htmlspecialchars($quiz['titulo']) ?></h4>
    <form id="quizForm">
    <?php foreach ($quiz['perguntas'] as $qi => $pergunta): ?>
    <div class="card mb-3">
      <div class="card-body">
        <p class="fw-semibold mb-3"><?= ($qi+1) ?>. <?= htmlspecialchars($pergunta['texto']) ?></p>
        <?php foreach ($pergunta['opcoes'] as $letra => $texto): ?>
        <div class="form-check">
          <input class="form-check-input" type="radio"
                 name="q<?= $qi ?>" id="q<?= $qi ?>_<?= $letra ?>"
                 value="<?= $letra ?>"
                 data-correct="<?= $letra === $pergunta['gabarito'] ? '1' : '0' ?>">
          <label class="form-check-label" for="q<?= $qi ?>_<?= $letra ?>">
            <strong><?= strtoupper($letra) ?></strong> — <?= htmlspecialchars($texto) ?>
          </label>
        </div>
        <?php endforeach; ?>
        <div class="mt-2 result-msg d-none"></div>
      </div>
    </div>
    <?php endforeach; ?>
    <button type="button" class="btn btn-success mb-4" onclick="corrigirQuiz()">
      <i class="bi bi-check-all me-1"></i>Corrigir Questionário
    </button>
    <div id="quizScore" class="d-none alert alert-info fw-semibold"></div>
    </form>
    <script>
    function corrigirQuiz() {
      const form = document.getElementById('quizForm');
      let acertos = 0, total = <?= count($quiz['perguntas']) ?>;
      for (let i = 0; i < total; i++) {
        const selected = form.querySelector(`input[name="q${i}"]:checked`);
        const msgEl = form.querySelectorAll('.result-msg')[i];
        msgEl.classList.remove('d-none', 'text-success', 'text-danger');
        if (!selected) {
          msgEl.textContent = '⚠️ Não respondida.';
          msgEl.classList.add('text-danger');
          continue;
        }
        const isCorrect = selected.dataset.correct === '1';
        if (isCorrect) {
          acertos++;
          msgEl.textContent = '✅ Correto!';
          msgEl.classList.add('text-success');
        } else {
          const correctInput = form.querySelector(`input[name="q${i}"][data-correct="1"]`);
          const correctLabel = correctInput
            ? correctInput.nextElementSibling.textContent.trim()
            : '';
          msgEl.textContent = `❌ Incorreto. Resposta certa: ${correctLabel}`;
          msgEl.classList.add('text-danger');
        }
      }
      const score = document.getElementById('quizScore');
      score.classList.remove('d-none');
      score.textContent = `Você acertou ${acertos} de ${total} questões (${Math.round(acertos/total*100)}%).`;
      score.className = `alert ${acertos === total ? 'alert-success' : acertos >= total/2 ? 'alert-warning' : 'alert-danger'} fw-semibold`;
    }
    </script>
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
