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

verificarAcessoRecurso('campos_estagio');

$pdo = getPDO();
$campos = $pdo->query("SELECT * FROM campos_estagio WHERE status = 1 ORDER BY nome")->fetchAll();
?>

<div class="content">
  <div class="card">
    <div class="card-body">
      <h4 class="card-title d-flex justify-content-between align-items-center">
        Campos de Estágio
        <button class="btn btn-primary" onclick="abrirModalNovoCampo()">+ Novo Campo</button>
      </h4>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <table class="table table-hover nowrap" style="width:100%">
        <thead>
          <tr>
            <th>ID</th>
            <th>Campo de Estágio</th>
            <th>Cor</th>
            <th class="text-right">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($campos as $c): ?>
            <tr>
              <td><?= $c['id'] ?></td>
              <td><?= htmlspecialchars($c['nome']) ?></td>
              <td>
                <div style="width: 20px; height: 20px; border-radius: 50%; background-color: <?= htmlspecialchars($c['cor']) ?>; border: 1px solid #ccc;"></div>
              </td>
              <td class="text-right">
                <a href="#" class="btn btn-sm btn-link text-info" onclick="abrirModalEditarCampo(<?= $c['id'] ?>, '<?= htmlspecialchars($c['nome'], ENT_QUOTES) ?>', '<?= $c['cor'] ?>')">
                  <i class="mdi mdi-pencil"></i>
                </a>
                <a href="actions/excluir_campo_estagio.php?id=<?= $c['id'] ?>" onclick="return confirm('Excluir este campo?')" class="btn btn-sm btn-link text-danger">
                  <i class="mdi mdi-delete"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>

      </table>
    </div>
  </div>
</div>

<!-- Modal Novo/Editar Campo -->
<div class="modal fade" id="modalNovoCampo" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form class="modal-content" method="POST" action="actions/salvar_campo_estagio.php">
      <input type="hidden" name="id" id="campo_id">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCampoTitulo">Novo Campo de Estágio</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="nome">Nome do Campo</label>
          <input type="text" name="nome" id="nome" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="cor">Cor</label>
          <input type="color" name="cor" id="cor" class="form-control" value="#2196f3">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Salvar</button>
      </div>
    </form>
  </div>
</div>


<script>
function abrirModalNovoCampo() {
  document.getElementById('campo_id').value = '';
  document.getElementById('nome').value = '';
  document.getElementById('cor').value = '#2196f3';
  document.getElementById('modalCampoTitulo').innerText = 'Novo Campo de Estágio';
  $('#modalNovoCampo').modal('show');
}

function abrirModalEditarCampo(id, nome, cor) {
  document.getElementById('campo_id').value = id;
  document.getElementById('nome').value = nome;
  document.getElementById('cor').value = cor || '#2196f3';
  document.getElementById('modalCampoTitulo').innerText = 'Editar Campo de Estágio';
  $('#modalNovoCampo').modal('show');
}
</script>


<?php include 'includes/footer.php'; ?>
