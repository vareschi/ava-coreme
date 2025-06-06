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

    $stmt = $pdo->prepare("SELECT * FROM avaliacoes_perguntas WHERE avaliacao_id = ? and status = 1");
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
                        <?php
                            $perguntaId = (int) $p['id'];
                            $avaliacaoId = (int) $id;
                        ?>
                        <button type="button"
                                class="btn btn-sm btn-secondary"
                                onclick="abrirModalNovoCriterio(<?= $perguntaId ?>, <?= $avaliacaoId ?>)">
                        Novo Critério
                        </button>


                        <button type="button"
                                class="btn btn-sm btn-primary"
                                onclick="abrirModalEditarPergunta(
                                <?= $p['id'] ?>,
                                '<?= str_replace("'", "\\'", htmlspecialchars($p['titulo'], ENT_QUOTES)) ?>',
                                <?= $p['avaliacao_id'] ?>
                                )">
                        Editar Pergunta
                        </button>

                    <a href="actions/excluir_pergunta.php?id=<?= $p['id'] ?>&avaliacao_id=<?= $id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta pergunta?')">Excluir</a>
                    </td>
                </tr>

                <!-- Carregar critérios da pergunta -->
                <?php
                    $stmtCrit = $pdo->prepare("SELECT * FROM avaliacoes_criterios WHERE pergunta_id = ? AND status = 1");
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

                                <a href="actions/excluir_criterio.php?id=<?= $c['id'] ?>&avaliacao_id=<?= $id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja excluir este critério?')">Excluir</a>
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
    <form class="modal-content" method="POST" action="actions/salvar_edicao_pergunta.php">
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
        <button type="button" class="btn btn-secondary" onclick="fecharModalEditarPergunta()">Cancelar</button>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Criar/Editar Critério -->
<div class="modal fade" id="modalNovoCriterio" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" id="form-criterio">
      <div class="modal-header">
        <h5 class="modal-title">Critério</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" name="id" id="criterio-id">
        <input type="hidden" name="pergunta_id" id="criterio-pergunta-id">
        <input type="hidden" name="avaliacao_id" id="criterio-avaliacao-id">
        <div class="mb-3">
          <label class="form-label">Descrição</label>
          <input type="text" name="descricao" id="criterio-edit-descricao" class="form-control" required>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Salvar</button>
      </div>
    </form>
  </div>
</div>


 <!-- Javascript -->
 <script src="assets/plugins/jquery/jquery.min.js"></script>
 <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
 <script src="assets/plugins/simplebar/simplebar.min.js"></script>
 <script src="assets/js/sleek.js"></script>
 <script src="assets/options/optionswitcher.js"></script>

<script>

    function abrirModalNovoCriterio(perguntaId, avaliacaoId) {
        const modal = new bootstrap.Modal(document.getElementById('modalNovoCriterio'));
        document.getElementById('criterio-id').value = '';
        document.getElementById('criterio-edit-descricao').value = '';
        document.getElementById('criterio-pergunta-id').value = perguntaId;
        document.getElementById('criterio-avaliacao-id').value = avaliacaoId;
        document.getElementById('form-criterio').action = 'actions/salvar_criterio.php';
        new bootstrap.Modal(document.getElementById('modalNovoCriterio')).show();
    }

    function abrirModalEditarCriterio(id, descricao, perguntaId, avaliacaoId) {
        const modal = new bootstrap.Modal(document.getElementById('modalNovoCriterio'));
        document.getElementById('criterio-id').value = id;
        document.getElementById('criterio-edit-descricao').value = descricao;
        document.getElementById('criterio-pergunta-id').value = perguntaId;
        document.getElementById('criterio-avaliacao-id').value = avaliacaoId;
        document.getElementById('form-criterio').action = 'actions/salvar_edicao_criterio.php';
        modal.show();
    }

    function abrirModalEditarPergunta(id, titulo, avaliacaoId) {
        const modal = new bootstrap.Modal(document.getElementById('modalEditarPergunta'));
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-avaliacao-id').value = avaliacaoId;
        document.getElementById('edit-titulo').value = titulo;
        modal.show();
    }

    function fecharModalEditarPergunta() {
        const modalElement = document.getElementById('modalEditarPergunta');
        const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
        modal.hide();
    }



  // Modal Novo Critério
  const modalNovoCriterio = document.getElementById('modalNovoCriterio');
  modalNovoCriterio.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('criterio-pergunta-id').value = button.getAttribute('data-pergunta-id');
  });

</script>


<?php include 'includes/footer.php'; ?>
