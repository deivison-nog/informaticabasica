<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('professor');
$pageTitle = 'Professor — Planos de Aula';
include __DIR__ . '/../includes/header.php';

$aulas = [
    1 => ['titulo' => 'Aula 1 — Conceitos Básicos de Informática',     'icon' => 'cpu-fill',        'cor' => 'primary'],
    2 => ['titulo' => 'Aula 2 — Internet, E-mail e Arquivos',          'icon' => 'globe2',          'cor' => 'info'],
    3 => ['titulo' => 'Aula 3 — Pacote Office, Nuvem e Redes',         'icon' => 'cloud-fill',      'cor' => 'success'],
    4 => ['titulo' => 'Aula 4 — Segurança Digital e Tendências 2026',  'icon' => 'shield-lock-fill','cor' => 'warning'],
];
?>
<h4 class="fw-bold mb-4"><i class="bi bi-book me-2"></i>Planos de Aula</h4>
<p class="text-muted">Visualize o conteúdo completo de cada aula. Para publicar ou liberar questionários, acesse <a href="publicar.php">Publicar Conteúdo</a>.</p>
<div class="row g-3">
  <?php foreach ($aulas as $id => $a): ?>
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-header bg-<?= $a['cor'] ?> text-white d-flex align-items-center gap-2">
        <i class="bi bi-<?= $a['icon'] ?> fs-5"></i>
        <span class="fw-semibold"><?= $a['titulo'] ?></span>
      </div>
      <div class="card-body d-flex flex-column gap-2">
        <a href="../content/aula<?= $id ?>.php" class="btn btn-outline-<?= $a['cor'] ?> btn-sm" target="_blank">
          <i class="bi bi-eye me-1"></i>Ver Plano de Aula
        </a>
        <a href="publicar.php?aula=<?= $id ?>" class="btn btn-<?= $a['cor'] ?> btn-sm">
          <i class="bi bi-send me-1"></i>Gerenciar Publicação
        </a>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
