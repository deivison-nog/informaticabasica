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

if ($id && isAulaPublicada($id) && isset($aulas[$id])) {
    $pageTitle = $aulas[$id]['titulo'];
    $cor = $aulas[$id]['cor'];
    include __DIR__ . '/../includes/header.php';
    ?>
    <style>
      .slide-viewer-bg { background:#1e2a38; border-radius:14px; padding:1.25rem 1.25rem .75rem; margin-bottom:1rem; }
      .slide-section   { display:none; background:#fff; border-radius:10px; padding:2rem 2.5rem; min-height:440px;
                         box-shadow:0 8px 32px rgba(0,0,0,.35); animation:fadeSlide .22s ease; overflow:auto; }
      .slide-section.active { display:block; }
      @keyframes fadeSlide { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:none} }
      .slide-nav { display:flex; align-items:center; justify-content:space-between; padding:.6rem 0 0; gap:.5rem; flex-wrap:wrap; }
      .slide-counter { color:#b0bec5; font-size:.9rem; white-space:nowrap; }
      .slide-dots { display:flex; gap:5px; flex-wrap:wrap; justify-content:center; }
      .slide-dot { width:9px; height:9px; border-radius:50%; background:rgba(255,255,255,.25); cursor:pointer; transition:background .2s; border:none; padding:0; }
      .slide-dot.active { background:#fff; }
      .slide-progress { height:3px; background:rgba(255,255,255,.15); border-radius:3px; margin-bottom:.75rem; }
      .slide-progress-bar { height:3px; border-radius:3px; transition:width .3s; }
      .lesson-image { max-width:100%; border-radius:8px; box-shadow:0 2px 12px rgba(0,0,0,.14); }
      .image-placeholder { background:linear-gradient(135deg,#f8f9fa,#e9ecef); border:2px dashed #ced4da; border-radius:8px; }
      .slide-viewer-header { display:flex; align-items:center; justify-content:flex-end; margin-bottom:.4rem; }
      /* Fullscreen styles */
      .slide-viewer-bg:fullscreen,
      .slide-viewer-bg:-webkit-full-screen {
        border-radius:0; padding:1.5rem 2rem; overflow:auto;
        display:flex; flex-direction:column;
      }
      .slide-viewer-bg:fullscreen .slide-section,
      .slide-viewer-bg:-webkit-full-screen .slide-section {
        min-height:calc(100vh - 180px); flex:1;
      }
    </style>

    <a href="aula.php" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
    <h4 class="fw-bold mb-3"><?= htmlspecialchars($aulas[$id]['titulo']) ?></h4>
    <p class="text-muted small mb-3"><i class="bi bi-keyboard me-1"></i>Use as setas ← → do teclado ou os botões para navegar entre os slides.</p>

    <div class="slide-viewer-bg" id="slideViewer">
      <!-- Fullscreen button -->
      <div class="slide-viewer-header">
        <button class="btn btn-outline-light btn-sm" id="fsBtn" title="Tela cheia (F)">
          <i class="bi bi-fullscreen"></i>
        </button>
      </div>
      <!-- Progress strip -->
      <div class="slide-progress">
        <div class="slide-progress-bar bg-<?= $cor ?>" id="progressBar" style="width:0%"></div>
      </div>

      <!-- Slide content (injected by content/aulaX.php) -->
      <?php include __DIR__ . '/../content/aula' . $id . '.php'; ?>

      <!-- Navigation -->
      <div class="slide-nav mt-2">
        <button class="btn btn-outline-light btn-sm" id="prevBtn" disabled>
          <i class="bi bi-chevron-left me-1"></i>Anterior
        </button>
        <div class="d-flex flex-column align-items-center gap-1">
          <div class="slide-dots" id="slideDots"></div>
          <span class="slide-counter" id="slideCounter"></span>
        </div>
        <button class="btn btn-<?= $cor ?> btn-sm" id="nextBtn">
          Próximo<i class="bi bi-chevron-right ms-1"></i>
        </button>
      </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
      const viewer   = document.getElementById('slideViewer');
      const sections = viewer.querySelectorAll('.slide-section');
      const counter  = document.getElementById('slideCounter');
      const bar      = document.getElementById('progressBar');
      const prevBtn  = document.getElementById('prevBtn');
      const nextBtn  = document.getElementById('nextBtn');
      const dotsWrap = document.getElementById('slideDots');
      const fsBtn    = document.getElementById('fsBtn');
      let cur = 0;

      // Build dots
      sections.forEach(function (_, i) {
        const d = document.createElement('button');
        d.className = 'slide-dot' + (i === 0 ? ' active' : '');
        d.setAttribute('aria-label', 'Slide ' + (i + 1));
        d.addEventListener('click', function () { go(i); });
        dotsWrap.appendChild(d);
      });

      function go(n) {
        if (n < 0 || n >= sections.length) return;
        sections[cur].classList.remove('active');
        dotsWrap.children[cur].classList.remove('active');
        cur = n;
        sections[cur].classList.add('active');
        dotsWrap.children[cur].classList.add('active');
        update();
        window.scrollTo({ top: viewer.getBoundingClientRect().top + window.scrollY - 16, behavior: 'smooth' });
      }

      function update() {
        counter.textContent = (cur + 1) + ' / ' + sections.length;
        bar.style.width = ((cur + 1) / sections.length * 100) + '%';
        prevBtn.disabled = cur === 0;
        nextBtn.disabled = cur === sections.length - 1;
      }

      prevBtn.addEventListener('click', function () { go(cur - 1); });
      nextBtn.addEventListener('click', function () { go(cur + 1); });

      function toggleFullscreen() {
        if (!document.fullscreenElement) {
          viewer.requestFullscreen().catch(function () {});
        } else {
          document.exitFullscreen();
        }
      }

      fsBtn.addEventListener('click', toggleFullscreen);

      document.addEventListener('fullscreenchange', function () {
        const icon = fsBtn.querySelector('i');
        if (document.fullscreenElement) {
          icon.className = 'bi bi-fullscreen-exit';
          fsBtn.title = 'Sair da tela cheia (Esc)';
        } else {
          icon.className = 'bi bi-fullscreen';
          fsBtn.title = 'Tela cheia (F)';
        }
      });

      document.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowRight' || e.key === 'ArrowDown') go(cur + 1);
        if (e.key === 'ArrowLeft'  || e.key === 'ArrowUp')   go(cur - 1);
        if (e.key === 'f' || e.key === 'F') toggleFullscreen();
      });

      sections[0].classList.add('active');
      update();
    });
    </script>
    <?php
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
            <i class="bi bi-play-fill me-1"></i>Iniciar Aula
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
