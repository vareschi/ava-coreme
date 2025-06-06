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

verificarAcessoRecurso('avaliacoes');

$pdo = getPDO();

// Edição
$id = $_GET['id'] ?? null;
$avaliacao = null;
$perguntas = [];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM avaliacoes WHERE id = ?");
    $stmt->execute([$id]);
    $avaliacao = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM avaliacoes_perguntas WHERE avaliacao_id = ?");
    $stmt->execute([$id]);
    $perguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$especialidades = $pdo->query("SELECT id, nome FROM especialidades ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content">
  <div class="card">
    <div class="card-body">
      <h4 class="card-title">
        <?= $avaliacao ? 'Editar Avaliação' : 'Nova Avaliação' ?>
      </h4>

      <form action="actions/salvar_avaliacao.php" method="POST">
        <?php if ($avaliacao): ?>
          <input type="hidden" name="id" value="<?= $avaliacao['id'] ?>">
        <?php endif; ?>

        <div class="mb-3">
          <label class="form-label">Título</label>
          <input type="text" name="titulo" class="form-control" required value="<?= htmlspecialchars($avaliacao['titulo'] ?? '') ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Descrição</label>
          <textarea name="descricao" class="form-control"><?= htmlspecialchars($avaliacao['descricao'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Especialidade</label>
          <select name="especialidade_id" class="form-select">
            <option value="">Geral</option>
            <?php foreach ($especialidades as $e): ?>
              <option value="<?= $e['id'] ?>" <?= ($avaliacao['especialidade_id'] ?? '') == $e['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($e['nome']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="d-grid mb-4">
          <button type="submit" class="btn btn-success">Salvar Avaliação</button>
        </div>
      </form>

      <?php if ($avaliacao): ?>
        <hr>
        <h5 class="mt-4">Perguntas Cadastradas</h5>

        <?php if ($perguntas): ?>
          <table class="table table-bordered">
            <thead>
              <thead>
                <tr>
                    <th>Pergunta</th>
                    <th>Ações</th>
                </tr>
            </thead>

            </thead>
                <tbody>
              <?php foreach ($perguntas as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['titulo']) ?></td>
                    <td>
                    <button type="button"
                            class="btn btn-sm btn-secondary"
                            data-bs-toggle="modal"
                            data-bs-target="#modalNovoCriterio"
                            data-pergunta-id="<?= $p['id'] ?>"
                        Novo Critério
                    </button>
                    <button type="button"
                        class="btn btn-sm btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#modalEditarPergunta"
                        data-id="<?= $p['id'] ?>"
                        data-titulo="<?= htmlspecialchars($p['titulo'], ENT_QUOTES) ?>"
                        data-avaliacao="<?= $p['avaliacao_id'] ?>">
                        Editar
                    </button>

                    <a href="actions/excluir_pergunta.php?id=<?= $p['id'] ?>&avaliacao_id=<?= $id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta pergunta?')">Excluir</a>
                    </td>
                </tr>

                <!-- Carregar critérios da pergunta -->
                <?php
                    $stmtCrit = $pdo->prepare("SELECT * FROM avaliacoes_criterios WHERE pergunta_id = ?");
                    $stmtCrit->execute([$p['id']]);
                    $criterios = $stmtCrit->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <?php if ($criterios): ?>
                    <tr>
                    <td colspan="4">
                        <strong>Critérios:</strong>
                        <table class="table table-sm mt-2 table-bordered">
                        <thead>
                            <tr>
                            <th>Descrição</th>
                            <th style="width: 150px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($criterios as $c): ?>
                            <tr>
                                <td><?= htmlspecialchars($c['descricao']) ?></td>
                                <td>
                                <button type="button"
                                        class="btn btn-sm btn-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditarCriterio"
                                        data-id="<?= $c['id'] ?>"
                                        data-descricao="<?= htmlspecialchars($c['descricao'], ENT_QUOTES) ?>"
                                        data-pergunta-id="<?= $p['id'] ?>"
                                        data-avaliacao-id="<?= $id ?>">
                                Editar
                                </button>

                                <a href="excluir_criterio.php?id=<?= $c['id'] ?>&avaliacao_id=<?= $id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja excluir este critério?')">Excluir</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        </table>
                    </td>
                    </tr>
                <?php endif; ?>
                <?php endforeach; ?>

            </tbody>
          </table>
        <?php else: ?>
          <p>Nenhuma pergunta cadastrada ainda.</p>
        <?php endif; ?>

        <hr>
        <h5 class="mt-4">Adicionar Nova Pergunta</h5>
        <form action="actions/salvar_pergunta.php" method="POST">
          <input type="hidden" name="avaliacao_id" value="<?= $id ?>">

          <div class="mb-3">
            <label class="form-label">Pergunta</label>
            <input type="text" name="titulo" class="form-control" required>
          </div>


          <div class="d-grid">
            <button type="submit" class="btn btn-primary">Salvar Pergunta</button>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Modal Edição Pergunta -->
<div class="modal fade" id="modalEditarPergunta" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="salvar_edicao_pergunta.php">
      <div class="modal-header">
        <h5 class="modal-title">Editar Pergunta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" name="id" id="edit-id">
        <input type="hidden" name="avaliacao_id" id="edit-avaliacao-id">

        <div class="mb-3">
          <input type="text" name="titulo" id="edit-titulo" class="form-control" required>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Novo Critério -->
<div class="modal fade" id="modalNovoCriterio" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="salvar_criterio.php">
      <div class="modal-header">
        <h5 class="modal-title">Novo Critério</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" name="pergunta_id" id="criterio-pergunta-id">
        <div class="mb-3">
          <label class="form-label">Critério</label>
          <input type="text" name="descricao" class="form-control" required>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">Salvar Critério</button>
      </div>
    </form>
  </div>
</div>

<!-- MOdal Editar Criterio -->
<div class="modal fade" id="modalEditarCriterio" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="actions/salvar_edicao_criterio.php">
      <div class="modal-header">
        <h5 class="modal-title">Editar Critério</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" name="id" id="criterio-edit-id">
        <input type="hidden" name="avaliacao_id" id="criterio-edit-avaliacao-id">
        <input type="hidden" name="pergunta_id" id="criterio-edit-pergunta-id">
        <div class="mb-3">
          <label class="form-label">Descrição</label>
          <input type="text" name="descricao" id="criterio-edit-descricao" class="form-control" required>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
      </div>
    </form>
  </div>
</div>



<script>
  const modalEditar = document.getElementById('modalEditarPergunta');
  modalEditar.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;

    document.getElementById('edit-id').value = button.getAttribute('data-id');
    document.getElementById('edit-avaliacao-id').value = button.getAttribute('data-avaliacao');
    document.getElementById('edit-titulo').value = button.getAttribute('data-titulo');
    
  });

   const modalNovoCriterio = document.getElementById('modalNovoCriterio');
    modalNovoCriterio.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('criterio-pergunta-id').value = button.getAttribute('data-pergunta-id');
    });

    const modalEditarCriterio = document.getElementById('modalEditarCriterio');
    modalEditarCriterio.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('criterio-edit-id').value = button.getAttribute('data-id');
    document.getElementById('criterio-edit-avaliacao-id').value = button.getAttribute('data-avaliacao-id');
    document.getElementById('criterio-edit-pergunta-id').value = button.getAttribute('data-pergunta-id');
    document.getElementById('criterio-edit-descricao').value = button.getAttribute('data-descricao');
    });


</script>

<?php include 'includes/footer.php'; ?>
