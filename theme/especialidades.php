<?php
session_start();
require_once 'includes/config.php';

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

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/topbar.php'; ?>

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
      <table id="hoverable-data-table" class="table table-hover nowrap" style="width:100%">
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
                        <button type="button" class="btn btn-sm btn-link text-primary" onclick="habilitarEdicao(<?= $esp['id'] ?>)">
                            <i class="mdi mdi-pencil" id="icon-<?= $esp['id'] ?>"></i>
                        </button>
                        <button type="submit" class="btn btn-sm btn-link text-success d-none" id="btn-salvar-<?= $esp['id'] ?>">
                            <i class="mdi mdi-content-save"></i>
                        </button>
                        <a href="actions/excluir_especialidade.php?id=<?= $esp['id'] ?>" onclick="return confirm('Excluir?')" class="text-danger">
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
  const icon = document.getElementById("icon-" + id);
  const btnSalvar = document.getElementById("btn-salvar-" + id);
  const btnCancelar = document.getElementById("btn-cancelar-" + id);

  nome.readOnly = false;
  duracao.readOnly = false;

  nome.classList.remove('form-control-plaintext');
  nome.classList.add('form-control');
  duracao.classList.remove('form-control-plaintext');
  duracao.classList.add('form-control');

  btnSalvar.classList.remove("d-none");
  btnCancelar.classList.remove("d-none");

  // Muda ícone lápis para "X"
  icon.classList.remove("mdi-pencil");
  icon.classList.add("mdi-close");

  // Troca onclick do botão lápis para cancelar
  icon.parentNode.setAttribute("onclick", `cancelarEdicao(${id})`);
}


function cancelarEdicao(id) {
  const nome = document.getElementById("nome-" + id);
  const duracao = document.getElementById("duracao-" + id);
  const icon = document.getElementById("icon-" + id);
  const btnSalvar = document.getElementById("btn-salvar-" + id);
  const btnCancelar = document.getElementById("btn-cancelar-" + id);

  nome.value = nome.getAttribute("data-original");
  duracao.value = duracao.getAttribute("data-original");

  nome.readOnly = true;
  duracao.readOnly = true;

  nome.classList.remove("form-control");
  nome.classList.add("form-control-plaintext");
  duracao.classList.remove("form-control");
  duracao.classList.add("form-control-plaintext");

  btnSalvar.classList.add("d-none");
  btnCancelar.classList.add("d-none");

  icon.classList.remove("mdi-close");
  icon.classList.add("mdi-pencil");

  icon.parentNode.setAttribute("onclick", `habilitarEdicao(${id})`);
}

</script>

<?php include 'includes/footer.php'; ?>
