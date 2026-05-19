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
<style>
  .editor-stage {
    background:#1e2a38;
    border-radius:14px;
    padding:1.25rem;
  }
  .editor-stage .slide-section {
    display:block !important;
    background:#fff;
    border-radius:10px;
    padding:2rem 2.5rem;
    min-height:440px;
    box-shadow:0 8px 24px rgba(0,0,0,.25);
    margin-bottom:1rem;
  }
  #conteudoEditorVisual {
    min-height:540px;
    background:transparent;
    outline:none;
  }
  #conteudoEditorVisual img,
  .slide-editor-image {
    width:100%;
    max-width:100%;
    max-height:320px;
    object-fit:contain;
    display:block;
    margin:.75rem auto;
    border-radius:8px;
    box-shadow:0 2px 12px rgba(0,0,0,.14);
  }
  .slide-editor-image-selected {
    outline:3px solid #0d6efd;
    outline-offset:4px;
  }
</style>
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
    <div class="border rounded overflow-hidden">
      <div class="bg-light border-bottom p-2 d-flex flex-wrap gap-1" id="wysiwygToolbar">
        <button type="button" class="btn btn-sm btn-outline-secondary" data-action="bold"><i class="bi bi-type-bold"></i></button>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-action="italic"><i class="bi bi-type-italic"></i></button>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-action="underline"><i class="bi bi-type-underline"></i></button>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-action="h2">H2</button>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-action="h3">H3</button>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-action="ul"><i class="bi bi-list-ul"></i></button>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-action="ol"><i class="bi bi-list-ol"></i></button>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnLink"><i class="bi bi-link-45deg"></i></button>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnImage"><i class="bi bi-image"></i></button>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-action="unlink"><i class="bi bi-link"></i><i class="bi bi-slash-lg"></i></button>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-action="clear">Limpar</button>
      </div>
      <div class="editor-stage">
        <div id="conteudoEditorVisual" class="p-3" contenteditable="true"></div>
      </div>
    </div>
    <input type="file" id="editorImageInput" accept="image/png,image/jpeg,image/webp,image/gif" class="d-none">
    <textarea name="conteudo" id="conteudoEditor" class="d-none" rows="20" required><?= htmlspecialchars($conteudoAtual) ?></textarea>
    <p class="small mt-2 mb-0 text-muted">Dica: mantenha os blocos com <code>&lt;div class="slide-section"&gt;...&lt;/div&gt;</code> para não quebrar a navegação e use o botão <i class="bi bi-image"></i> para inserir fotos.</p>
    <p id="editorUploadMsg" class="small mt-1 mb-0" aria-live="polite" aria-atomic="true"></p>
    <div class="d-flex gap-2 mt-3 flex-wrap">
      <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Salvar Alterações</button>
      <button type="submit" name="reset_slide" value="1" class="btn btn-outline-danger" onclick="return confirm('Restaurar texto original desta aula?')">
        <i class="bi bi-arrow-counterclockwise me-1"></i>Restaurar Original
      </button>
      <a href="ver_aula.php?id=<?= $aula ?>" class="btn btn-outline-secondary">Visualizar Aula</a>
    </div>
  </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('slideForm');
  const visualEditor = document.getElementById('conteudoEditorVisual');
  const hiddenTextarea = document.getElementById('conteudoEditor');
  const toolbar = document.getElementById('wysiwygToolbar');
  const linkButton = document.getElementById('btnLink');
  const imageButton = document.getElementById('btnImage');
  const imageInput = document.getElementById('editorImageInput');
  const uploadMsg = document.getElementById('editorUploadMsg');
  const aulaId = <?= json_encode($aula, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  let selectedImage = null;

  function sanitizeClientHtml(rawHtml) {
    const template = document.createElement('template');
    template.innerHTML = rawHtml;

    template.content.querySelectorAll('script, iframe, object, embed').forEach(function (node) {
      node.remove();
    });

    template.content.querySelectorAll('*').forEach(function (element) {
      Array.from(element.attributes).forEach(function (attribute) {
        const name = attribute.name.toLowerCase();
        const value = attribute.value.trim().toLowerCase();
        if (name.startsWith('on')) element.removeAttribute(attribute.name);
        if ((name === 'href' || name === 'src') && value.startsWith('javascript:')) {
          element.removeAttribute(attribute.name);
        }
      });
    });

    return template.innerHTML;
  }

  function clearSelectedImage() {
    if (!selectedImage) return;
    selectedImage.classList.remove('slide-editor-image-selected');
    selectedImage = null;
  }

  function selectImage(img) {
    if (!(img instanceof HTMLImageElement) || !visualEditor.contains(img)) return;
    clearSelectedImage();
    selectedImage = img;
    selectedImage.classList.add('slide-editor-image-selected');
    visualEditor.focus();
  }

  function prepareEditorImage(img) {
    img.classList.add('slide-editor-image');
    img.removeAttribute('width');
    img.removeAttribute('height');
    img.setAttribute('draggable', 'false');
  }

  function normalizeSlideMedia() {
    visualEditor.querySelectorAll('img').forEach(function (img) {
      prepareEditorImage(img);
    });

    if (selectedImage && !visualEditor.contains(selectedImage)) {
      clearSelectedImage();
    }
  }

  visualEditor.innerHTML = sanitizeClientHtml(hiddenTextarea.value);
  normalizeSlideMedia();

  function syncEditorToTextarea() {
    normalizeSlideMedia();
    hiddenTextarea.value = sanitizeClientHtml(visualEditor.innerHTML.trim());
  }

  function getSelectionRange() {
    const selection = window.getSelection();
    if (!selection || selection.rangeCount === 0) return null;
    const range = selection.getRangeAt(0);
    if (!visualEditor.contains(range.commonAncestorContainer)) return null;
    return range;
  }

  function wrapSelectionWith(tagName, attributes) {
    const range = getSelectionRange();
    if (!range || range.collapsed) return;
    const wrapper = document.createElement(tagName);
    if (attributes) {
      Object.keys(attributes).forEach(function (key) {
        wrapper.setAttribute(key, attributes[key]);
      });
    }
    wrapper.appendChild(range.extractContents());
    range.insertNode(wrapper);
    range.selectNodeContents(wrapper);
    const selection = window.getSelection();
    selection.removeAllRanges();
    selection.addRange(range);
    syncEditorToTextarea();
  }

  function insertList(type) {
    const range = getSelectionRange();
    if (!range) return;
    const selectedText = range.toString().trim();
    if (!selectedText) return;
    const list = document.createElement(type === 'ol' ? 'ol' : 'ul');
    selectedText.split(/\n+/).map(function (line) { return line.trim(); }).filter(Boolean).forEach(function (line) {
      const item = document.createElement('li');
      item.textContent = line;
      list.appendChild(item);
    });
    if (!list.children.length) return;
    range.deleteContents();
    range.insertNode(list);
    syncEditorToTextarea();
  }

  function removeLinkFromSelection() {
    const selection = window.getSelection();
    if (!selection || selection.rangeCount === 0) return;
    let node = selection.anchorNode;
    while (node && node !== visualEditor) {
      if (node.nodeType === Node.ELEMENT_NODE && node.tagName === 'A') {
        const fragment = document.createDocumentFragment();
        while (node.firstChild) fragment.appendChild(node.firstChild);
        node.replaceWith(fragment);
        syncEditorToTextarea();
        return;
      }
      node = node.parentNode;
    }
  }

  function insertNodeAtSelection(node) {
    const range = getSelectionRange();
    if (!range) {
      visualEditor.appendChild(node);
      syncEditorToTextarea();
      return;
    }
    range.deleteContents();
    range.insertNode(node);
    range.setStartAfter(node);
    range.setEndAfter(node);
    const selection = window.getSelection();
    selection.removeAllRanges();
    selection.addRange(range);
    syncEditorToTextarea();
  }

  toolbar.querySelectorAll('[data-action]').forEach(function (button) {
    button.addEventListener('mousedown', function (event) {
      event.preventDefault();
    });

    button.addEventListener('click', function () {
      visualEditor.focus();
      const action = button.getAttribute('data-action');
      if (action === 'bold') wrapSelectionWith('strong');
      if (action === 'italic') wrapSelectionWith('em');
      if (action === 'underline') wrapSelectionWith('u');
      if (action === 'h2') wrapSelectionWith('h2');
      if (action === 'h3') wrapSelectionWith('h3');
      if (action === 'ul' || action === 'ol') insertList(action);
      if (action === 'unlink') removeLinkFromSelection();
      if (action === 'clear') {
        const range = getSelectionRange();
        if (range) {
          const plain = document.createTextNode(range.toString());
          range.deleteContents();
          range.insertNode(plain);
          syncEditorToTextarea();
        }
      }
    });
  });

  linkButton.addEventListener('mousedown', function (event) {
    event.preventDefault();
  });

  linkButton.addEventListener('click', function () {
    const range = getSelectionRange();
    if (!range || range.collapsed) return;
    const url = window.prompt('Digite a URL do link:', 'https://');
    if (url && url.trim() !== '') {
      wrapSelectionWith('a', { href: url.trim(), target: '_blank', rel: 'noopener noreferrer' });
    }
  });

  imageButton.addEventListener('mousedown', function (event) {
    event.preventDefault();
  });

  imageButton.addEventListener('click', function () {
    imageInput.click();
  });

  imageInput.addEventListener('change', async function () {
    const file = imageInput.files && imageInput.files[0];
    if (!file) return;

    uploadMsg.className = 'small mt-2 mb-0 text-muted';
    uploadMsg.textContent = 'Enviando imagem...';

    const formData = new FormData();
    formData.append('aula', String(aulaId));
    formData.append('imagem', file);

    try {
      const response = await fetch('upload_imagem_wysiwyg.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      });
      const payload = await response.json();
      if (!response.ok || !payload.ok || !payload.url) {
        throw new Error(payload.message || 'Falha no envio da imagem.');
      }

      const img = document.createElement('img');
      img.src = payload.url;
      img.alt = file.name || 'Imagem do slide';
      prepareEditorImage(img);
      insertNodeAtSelection(img);
      selectImage(img);
      uploadMsg.className = 'small mt-2 mb-0 text-success';
      uploadMsg.textContent = 'Imagem inserida com sucesso.';
    } catch (error) {
      uploadMsg.className = 'small mt-2 mb-0 text-danger';
      uploadMsg.textContent = error.message || 'Não foi possível enviar a imagem.';
    } finally {
      imageInput.value = '';
    }
  });

  visualEditor.addEventListener('click', function (event) {
    const clickedImage = event.target instanceof Element ? event.target.closest('img') : null;
    if (clickedImage && visualEditor.contains(clickedImage)) {
      selectImage(clickedImage);
      return;
    }

    clearSelectedImage();
  });

  visualEditor.addEventListener('keydown', function (event) {
    if (!selectedImage) return;
    if (event.key !== 'Delete' && event.key !== 'Backspace') return;

    event.preventDefault();
    const imageToRemove = selectedImage;
    clearSelectedImage();
    imageToRemove.remove();
    syncEditorToTextarea();
    uploadMsg.className = 'small mt-2 mb-0 text-muted';
    uploadMsg.textContent = 'Imagem removida do slide.';
  });

  visualEditor.addEventListener('input', syncEditorToTextarea);
  form.addEventListener('submit', syncEditorToTextarea);
});
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
