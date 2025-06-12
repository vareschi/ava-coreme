<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: sign-in.php");
    exit;
}

require_once 'includes/config.php';
require_once 'includes/funcoes.php';

include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/topbar.php';

verificarAcessoRecurso('especialidades');

$pdo = getPDO();
$especialidades = $pdo->query("SELECT * FROM especialidades ORDER BY nome ASC")->fetchAll();

$editar_id = isset($_GET['editar']) ? (int)$_GET['editar'] : null;
$editar_especialidade = null;

if ($editar_id) {
    $stmt = $pdo->prepare("SELECT * FROM especialidades WHERE id = ?");
    $stmt->execute([$editar_id]);
    $editar_especialidade = $stmt->fetch();
}
?>

<script src="assets/plugins/data-tables/jquery.datatables.min.js"></script>
<script src="assets/plugins/data-tables/datatables.bootstrap4.min.js"></script>

<div class="content">
  <div class="card">
    <div class="card-body">
      <h4 class="card-title d-flex justify-content-between align-items-center">
        Especialidades
        <form class="form-inline" method="POST" action="actions/salvar_especialidade.php">
            <input type="hidden" name="id" value="<?= $editar_especialidade['id'] ?? '' ?>">
            <input type="text" name="nome" class="form-control mr-2"
                    placeholder="Digite uma especialidade..."
                    value="<?= htmlspecialchars($editar_especialidade['nome'] ?? '') ?>" required>
            <input type="number" name="duracao" class="form-control mr-2"
                    placeholder="Duração (anos)"
                    value="<?= htmlspecialchars($editar_especialidade['duracao_anos'] ?? '') ?>" min="1" required>
            <button type="submit" class="btn btn-<?= $editar_especialidade ? 'warning' : 'primary' ?>">
                <?= $editar_especialidade ? 'Atualizar' : 'Cadastrar' ?>
            </button>
        </form>
      </h4>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <table class="table table-hover nowrap" style="width:100%">
        <thead>
          <tr>
            <th>Especialidade</th>
            <th>Duração (anos)</th>
            <th class="text-right">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($especialidades as $esp): ?>
            <tr id="linha-<?= $esp['id'] ?>">
                <form method="POST" action="actions/salvar_especialidade.php" class="form-especialidade">
                    <input type="hidden" name="id" value="<?= $esp['id'] ?>">

                    <td>
                    <input type="text" name="nome" class="form-control-plaintext" 
                        value="<?= htmlspecialchars($esp['nome']) ?>"
                        readonly id="nome-<?= $esp['id'] ?>" 
                        data-original="<?= htmlspecialchars($esp['nome']) ?>">
                    </td>

                    <td>
                    <input type="number" name="duracao" class="form-control-plaintext"
                        value="<?= $esp['duracao_anos'] ?>"
                        readonly id="duracao-<?= $esp['id'] ?>" 
                        data-original="<?= $esp['duracao_anos'] ?>">
                    </td>

                    <td class="text-right">
                         <!-- botão de editar -->
                        <button type="button" class="btn btn-sm btn-link text-primary" id="btn-editar-<?= $esp['id'] ?>"
                                onclick="habilitarEdicao(<?= $esp['id'] ?>)">
                            <i class="mdi mdi-pencil"></i>
                        </button>

                        <!-- botão de salvar -->
                        <button type="submit" class="btn btn-sm btn-link text-success d-none" id="btn-salvar-<?= $esp['id'] ?>">
                            <i class="mdi mdi-content-save"></i>
                        </button>

                        <!-- botão de cancelar -->
                        <button type="button" class="btn btn-sm btn-link text-secondary d-none" id="btn-cancelar-<?= $esp['id'] ?>"
                                onclick="cancelarEdicao(<?= $esp['id'] ?>)">
                            <i class="mdi mdi-close"></i>
                        </button>

                        <!-- botão de excluir -->
                        <a href="actions/excluir_especialidade.php?id=<?= $esp['id'] ?>" class="text-danger" onclick="return confirm('Excluir?')">
                            <i class="mdi mdi-delete"></i>
                        </a>
                    </td>
                </form>
                </tr>

          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
function habilitarEdicao(id) {
  const nome = document.getElementById("nome-" + id);
  const duracao = document.getElementById("duracao-" + id);

  nome.readOnly = false;
  duracao.readOnly = false;

  nome.classList.remove("form-control-plaintext");
  nome.classList.add("form-control");
  duracao.classList.remove("form-control-plaintext");
  duracao.classList.add("form-control");

  // mostrar botões salvar e cancelar
  document.getElementById("btn-salvar-" + id).classList.remove("d-none");
  document.getElementById("btn-cancelar-" + id).classList.remove("d-none");

  // esconder botão de editar
  document.getElementById("btn-editar-" + id).classList.add("d-none");
}

function cancelarEdicao(id) {
  const nome = document.getElementById("nome-" + id);
  const duracao = document.getElementById("duracao-" + id);

  nome.value = nome.getAttribute("data-original");
  duracao.value = duracao.getAttribute("data-original");

  nome.readOnly = true;
  duracao.readOnly = true;

  nome.classList.remove("form-control");
  nome.classList.add("form-control-plaintext");
  duracao.classList.remove("form-control");
  duracao.classList.add("form-control-plaintext");

  // esconder salvar e cancelar
  document.getElementById("btn-salvar-" + id).classList.add("d-none");
  document.getElementById("btn-cancelar-" + id).classList.add("d-none");

  // mostrar botão de editar de novo
  document.getElementById("btn-editar-" + id).classList.remove("d-none");
}

</script>

<?php include 'includes/footer.php'; ?>
