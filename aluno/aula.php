<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('aluno');

$aulas = [
    1 => ['titulo' => 'Aula 1 — Conceitos Básicos de Informática',    'cor' => 'primary'],
    2 => ['titulo' => 'Aula 2 — Internet, E-mail e Arquivos',         'cor' => 'info'],
    3 => ['titulo' => 'Aula 3 — Pacote Office, Nuvem e Redes',        'cor' => 'success'],
    4 => ['titulo' => 'Aula 4 — Segurança Digital e Tendências 2026', 'cor' => 'warning'],
];

$id = (int)($_GET['id'] ?? 0);

if ($id && isAulaPublicada($id)) {
    // Show specific lesson content
    $pageTitle = $aulas[$id]['titulo'] ?? 'Aula';
    include __DIR__ . '/../includes/header.php';
    echo '<a href="aula.php" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left me-1"></i>Voltar</a>';
    include __DIR__ . '/../content/aula' . $id . '.php';
    include __DIR__ . '/../includes/footer.php';
    exit;
}

$pageTitle = 'Aluno — Minhas Aulas';
include __DIR__ . '/../includes/header.php';
?>
<h4 class="fw-bold mb-4"><i class="bi bi-journal-text me-2"></i>Minhas Aulas</h4>
<div class="row g-3">
  <?php foreach ($aulas as $id => $a):
    $disponivel = isAulaPublicada($id);
  ?>
  <div class="col-md-6">
    <div class="card h-100 <?= !$disponivel ? 'opacity-50' : '' ?>">
      <div class="card-header bg-<?= $a['cor'] ?> text-white fw-semibold"><?= $a['titulo'] ?></div>
      <div class="card-body d-flex align-items-center justify-content-between">
        <?php if ($disponivel): ?>
          <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Disponível</span>
          <a href="aula.php?id=<?= $id ?>" class="btn btn-<?= $a['cor'] ?> btn-sm">
            <i class="bi bi-eye me-1"></i>Acessar
          </a>
        <?php else: ?>
          <span class="badge bg-secondary"><i class="bi bi-lock me-1"></i>Não liberada</span>
          <span class="text-muted small">Aguarde o professor</span>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
