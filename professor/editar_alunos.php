<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('professor');

$pageTitle = 'Professor — Editar Alunos';
$msg = '';
$msgType = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aluno_id'])) {
    $alunoId = (int)$_POST['aluno_id'];
    $cpf     = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
    $nome    = trim($_POST['nome'] ?? '');

    if (strlen($cpf) !== 11 || $nome === '') {
        $msg = 'Nome e CPF válido são obrigatórios.';
        $msgType = 'danger';
    } else {
        $users = loadUsers();
        $idx = null;
        foreach ($users as $i => $u) {
            if ((int)($u['id'] ?? 0) === $alunoId && ($u['role'] ?? '') === 'aluno') {
                $idx = $i;
                break;
            }
        }

        if ($idx === null) {
            $msg = 'Aluno não encontrado.';
            $msgType = 'warning';
        } else {
            $cpfDuplicado = false;
            foreach ($users as $u) {
                if ((int)($u['id'] ?? 0) !== $alunoId && ($u['cpf'] ?? '') === $cpf) {
                    $cpfDuplicado = true;
                    break;
                }
            }

            if ($cpfDuplicado) {
                $msg = 'CPF já cadastrado para outro usuário.';
                $msgType = 'warning';
            } else {
                $users[$idx]['nome'] = $nome;
                $users[$idx]['cpf'] = $cpf;
                saveUsers($users);
                $msg = 'Dados do aluno atualizados com sucesso.';
                $msgType = 'success';
            }
        }
    }
}

$alunos = array_values(array_filter(loadUsers(), fn($u) => ($u['role'] ?? '') === 'aluno'));
include __DIR__ . '/../includes/header.php';
?>
<h4 class="fw-bold mb-4"><i class="bi bi-person-gear me-2"></i>Editar Dados dos Alunos</h4>
<?php if ($msg): ?>
  <div class="alert alert-<?= $msgType ?> py-2"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if (empty($alunos)): ?>
  <div class="alert alert-info">Nenhum aluno cadastrado.</div>
<?php else: ?>
<div class="card">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="table-dark">
        <tr>
          <th style="min-width:220px;">Nome</th>
          <th style="min-width:180px;">CPF</th>
          <th style="width:140px;">Ação</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($alunos as $a): ?>
        <tr>
          <td>
            <form method="post" class="m-0">
              <input type="hidden" name="aluno_id" value="<?= (int)$a['id'] ?>">
              <input type="text" name="nome" class="form-control form-control-sm" value="<?= htmlspecialchars($a['nome']) ?>" required>
          </td>
          <td>
              <input type="text" name="cpf" class="form-control form-control-sm cpf-input" maxlength="14"
                     value="<?= htmlspecialchars(preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $a['cpf'])) ?>" required>
          </td>
          <td>
              <button class="btn btn-primary btn-sm w-100"><i class="bi bi-save me-1"></i>Salvar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<script>
document.querySelectorAll('.cpf-input').forEach(function (input) {
  input.addEventListener('input', function() {
    let v = this.value.replace(/\D/g, '').substring(0, 11);
    v = v.replace(/(\d{3})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    this.value = v;
  });
});
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
