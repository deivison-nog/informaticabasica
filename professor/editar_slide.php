<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('professor');

function contentWithoutPhpBlocks(string $path): string {
    if (!file_exists($path)) return '';
    return sanitizeSlideOverrideHtml((string)file_get_contents($path));
}

$aulas = [
    1 => 'Aula 1 — Conceitos Básicos de Informática',
    2 => 'Aula 2 — Internet, E-mail e Arquivos',
    3 => 'Aula 3 — Pacote Office, Nuvem e Redes',
    4 => 'Aula 4 — Segurança Digital e Tendências 2026',
];

$aula = (int)($_GET['aula'] ?? $_POST['aula'] ?? 1);
if (!isset($aulas[$aula])) $aula = 1;

$overrideDir  = __DIR__ . '/../data/slides';
$overrideFile = $overrideDir . '/aula' . $aula . '.html';
$baseFile     = __DIR__ . '/../content/aula' . $aula . '.php';

$msg = '';
$msgType = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reset_slide'])) {
        if (file_exists($overrideFile)) unlink($overrideFile);
        $msg = 'Texto dos slides restaurado para o conteúdo original.';
        $msgType = 'warning';
    } else {
        $conteudo = sanitizeSlideOverrideHtml((string)($_POST['conteudo'] ?? ''));
        if ($conteudo === '') {
            $msg = 'Conteúdo inválido. Revise o texto dos slides e tente novamente.';
            $msgType = 'danger';
        } else {
            if (!is_dir($overrideDir)) {
                mkdir($overrideDir, 0755, true);
            }
            file_put_contents($overrideFile, $conteudo . "\n");
            $msg = 'Texto dos slides salvo com sucesso.';
            $msgType = 'success';
        }
    }
}

$conteudoAtual = file_exists($overrideFile)
    ? file_get_contents($overrideFile)
    : contentWithoutPhpBlocks($baseFile);

$pageTitle = 'Professor — Editar Texto dos Slides';
include __DIR__ . '/../includes/header.php';
?>
<h4 class="fw-bold mb-3"><i class="bi bi-pencil-square me-2"></i>Editar Texto dos Slides</h4>
<?php if ($msg): ?>
  <div class="alert alert-<?= $msgType ?> py-2"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="card p-4">
  <form method="get" class="row g-2 mb-3">
    <div class="col-sm-8 col-md-6">
      <label class="form-label fw-semibold">Aula</label>
      <select name="aula" class="form-select">
        <?php foreach ($aulas as $id => $titulo): ?>
          <option value="<?= $id ?>" <?= $id === $aula ? 'selected' : '' ?>><?= htmlspecialchars($titulo) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-sm-4 col-md-3 d-flex align-items-end">
      <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-arrow-repeat me-1"></i>Carregar</button>
    </div>
  </form>

  <form method="post" id="slideForm">
    <input type="hidden" name="aula" value="<?= $aula ?>">
    <label class="form-label fw-semibold">Conteúdo HTML dos slides da <?= htmlspecialchars($aulas[$aula]) ?></label>
    <textarea name="conteudo" id="conteudoEditor" class="form-control font-monospace" rows="20" required><?= htmlspecialchars($conteudoAtual) ?></textarea>
    <p class="text-muted small mt-2 mb-0">Dica: mantenha os blocos com <code>&lt;div class="slide-section"&gt;...&lt;/div&gt;</code> para não quebrar a navegação.</p>
    <div class="d-flex gap-2 mt-3 flex-wrap">
      <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Salvar Alterações</button>
      <button type="submit" name="reset_slide" value="1" class="btn btn-outline-danger" onclick="return confirm('Restaurar texto original desta aula?')">
        <i class="bi bi-arrow-counterclockwise me-1"></i>Restaurar Original
      </button>
      <a href="ver_aula.php?id=<?= $aula ?>" class="btn btn-outline-secondary">Visualizar Aula</a>
    </div>
  </form>
</div>
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  tinymce.init({
    selector: '#conteudoEditor',
    height: 540,
    menubar: false,
    branding: false,
    plugins: 'lists link table code preview fullscreen',
    toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | table link | code preview fullscreen',
    content_style: 'body { font-family: Arial, sans-serif; font-size: 15px; }'
  });

  document.getElementById('slideForm').addEventListener('submit', function () {
    if (window.tinymce) tinymce.triggerSave();
  });
});
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
