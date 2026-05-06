<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('professor');
$pageTitle = 'Professor — Publicar Conteúdo';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $aulaId = (int)($_POST['aula_id'] ?? 0);
    if ($aulaId >= 1 && $aulaId <= 4) {
        if ($action === 'toggle_aula') toggleAula($aulaId);
        elseif ($action === 'toggle_quiz')  toggleQuestionario($aulaId);
    }
}

$pub = loadPublicacoes();

$aulas = [
    1 => ['titulo' => 'Aula 1 — Conceitos Básicos de Informática',    'cor' => 'primary'],
    2 => ['titulo' => 'Aula 2 — Internet, E-mail e Arquivos',         'cor' => 'info'],
    3 => ['titulo' => 'Aula 3 — Pacote Office, Nuvem e Redes',        'cor' => 'success'],
    4 => ['titulo' => 'Aula 4 — Segurança Digital e Tendências 2026', 'cor' => 'warning'],
];

$focusAula = (int)($_GET['aula'] ?? 0);
include __DIR__ . '/../includes/header.php';
?>
<h4 class="fw-bold mb-4"><i class="bi bi-send me-2"></i>Publicar Conteúdo</h4>
<p class="text-muted mb-4">Ative ou desative a visibilidade de cada aula e questionário para os alunos.</p>

<div class="row g-3">
  <?php foreach ($aulas as $id => $a):
    $aulaOn = (bool)($pub['aulas'][$id] ?? false);
    $quizOn = (bool)($pub['questionarios'][$id] ?? false);
    $highlight = ($focusAula === $id) ? 'border border-2 border-dark' : '';
  ?>
  <div class="col-md-6">
    <div class="card h-100 <?= $highlight ?>">
      <div class="card-header bg-<?= $a['cor'] ?> text-white fw-semibold">
        <?= $a['titulo'] ?>
      </div>
      <div class="card-body">
        <!-- Toggle Aula -->
        <form method="post" class="mb-2 d-flex align-items-center justify-content-between">
          <input type="hidden" name="aula_id" value="<?= $id ?>">
          <input type="hidden" name="action" value="toggle_aula">
          <span><i class="bi bi-journal-text me-1"></i>Conteúdo da Aula</span>
          <button type="submit"
                  class="btn btn-sm <?= $aulaOn ? 'btn-success' : 'btn-outline-secondary' ?>"
                  title="<?= $aulaOn ? 'Clique para despublicar' : 'Clique para publicar' ?>">
            <i class="bi bi-<?= $aulaOn ? 'toggle-on' : 'toggle-off' ?> me-1"></i>
            <?= $aulaOn ? 'Publicada' : 'Não publicada' ?>
          </button>
        </form>
        <!-- Toggle Questionário -->
        <form method="post" class="d-flex align-items-center justify-content-between">
          <input type="hidden" name="aula_id" value="<?= $id ?>">
          <input type="hidden" name="action" value="toggle_quiz">
          <span><i class="bi bi-patch-question me-1"></i>Questionário</span>
          <button type="submit"
                  class="btn btn-sm <?= $quizOn ? 'btn-warning' : 'btn-outline-secondary' ?>"
                  title="<?= $quizOn ? 'Clique para desativar' : 'Clique para liberar' ?>">
            <i class="bi bi-<?= $quizOn ? 'toggle-on' : 'toggle-off' ?> me-1"></i>
            <?= $quizOn ? 'Liberado' : 'Não liberado' ?>
          </button>
        </form>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
