<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

verificarAcessoRecurso('matriculas');

$pdo = getPDO();

$turmas = $pdo->query("SELECT id, nome FROM turmas WHERE status = 1 ORDER BY nome")->fetchAll();

$editais = $pdo->query("SELECT id, numero, nome FROM editais WHERE CURDATE() BETWEEN data_abertura AND data_fechamento ORDER BY data_abertura DESC")->fetchAll();

$turma_id = $_GET['turma_id'] ?? '';

$matriculas = [];
$where = "WHERE m.status = 1";
$params = [];

if (!empty($turma_id)) {
    $where .= " AND m.turma_id = ?";
    $params[] = $turma_id;
}

$sql = "SELECT 
            m.id,
            m.usuario_id,
            m.turma_id,
            m.edital_origem_id,
            u.nome as nome_residente,
            t.nome as nome_turma,
            e.numero as numero_edital
        FROM matriculas m
        JOIN usuarios u ON m.usuario_id = u.id
        JOIN turmas t ON m.turma_id = t.id
        LEFT JOIN editais e ON m.edital_origem_id = e.id
        $where
        ORDER BY u.nome";


$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$matriculas = $stmt->fetchAll();


?>

<div class="content">
  <div class="card">
    <div class="card-body">
      <h4 class="card-title d-flex justify-content-between align-items-center">
        Matrículas
        <button class="btn btn-primary" onclick="abrirModalNovaMatricula()">+ Nova Matrícula</button>
      </h4>

      <form method="GET" class="form-inline mb-3">
        <label for="turma_id" class="mr-2">Turma:</label>
        <select name="turma_id" id="turma_id" class="form-control mr-2" onchange="this.form.submit()">
          <option value="">Selecione</option>
          <?php foreach ($turmas as $t): ?>
            <option value="<?= $t['id'] ?>" <?= $turma_id == $t['id'] ? 'selected' : '' ?>><?= htmlspecialchars($t['nome']) ?></option>
          <?php endforeach; ?>
        </select>
      </form>

      <table class="table table-hover">
        <thead>
          <tr>
            <th>Residente</th>
            <th>Turma</th>
            <th>Edital de Origem</th>
            <th class="text-right">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($matriculas): ?>
            <?php foreach ($matriculas as $m): ?>
              <tr>
                <td><?= htmlspecialchars($m['nome_residente']) ?></td>
                <td><?= htmlspecialchars($m['nome_turma']) ?></td>
                <td><?= htmlspecialchars($m['numero_edital'] ?? 'Não informado') ?></td>
                <td class="text-right">
                  <a href="actions/excluir_matricula.php?id=<?= $m['id'] ?>" onclick="return confirm('Remover esta matrícula?')" class="btn btn-sm btn-link text-danger">
                    <i class="mdi mdi-delete"></i>
                  </a>
                  <a href="javascript:void(0)" 
                    class="btn btn-sm btn-link text-primary" 
                    onclick="editarMatricula(<?= htmlspecialchars(json_encode($m)) ?>)">
                    <i class="mdi mdi-pencil"></i>
                  </a>

                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="3">Nenhuma matrícula encontrada para esta turma.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Nova Matrícula -->
<div class="modal fade" id="modalNovaMatricula" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form class="modal-content" method="POST" action="actions/salvar_matricula.php">
      <input type="hidden" name="matricula_id" id="matricula_id">
      <div class="modal-header">
        <h5 class="modal-title">Nova Matrícula</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Turma</label>
          <select name="turma_id" class="form-control" required>
            <option value="">Selecione</option>
            <?php foreach ($turmas as $t): ?>
              <option value="<?= $t['id'] ?>" <?= $turma_id == $t['id'] ? 'selected' : '' ?>><?= htmlspecialchars($t['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label>Residente</label>
          <select name="usuario_id" class="form-control" required>
            <option value="">Selecione</option>
            <?php
              $resQuery = $pdo->prepare("SELECT u.id, u.nome FROM usuarios u 
                JOIN usuario_perfis p ON p.usuario_id = u.id
                WHERE p.perfil_id = 3 AND (
                  u.id NOT IN (
                    SELECT usuario_id FROM matriculas WHERE turma_id = ? AND status = 1
                  ) OR u.id = ?
                )
                ORDER BY u.nome");

              $resQuery->execute([$turma_id ?: 0, $usuario_id_atual ?? 0]);

              foreach ($resQuery->fetchAll() as $res):
            ?>
              <option value="<?= $res['id'] ?>"><?= htmlspecialchars($res['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
            <label for="edital_origem_id">Edital de Origem</label>
            <select name="edital_origem_id" id="edital_origem_id" class="form-control">
                <option value="">Selecione (opcional)</option>
                <?php foreach ($editais as $e): ?>
                <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['numero'] . ' - ' . $e['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Salvar Matrícula</button>
      </div>
    </form>
  </div>
</div>

<script>
    function abrirModalNovaMatricula() {
      document.querySelector('#matricula_id').value = '';
      document.querySelector('#modalNovaMatricula form').reset();

      document.querySelector('#modalNovaMatricula .modal-title').textContent = 'Nova Matrícula';
      document.querySelector('#modalNovaMatricula button[type="submit"]').textContent = 'Salvar Matrícula';

      $('#modalNovaMatricula').modal('show');
    }


    function editarMatricula(matricula) {
      $('#modalNovaMatricula').modal('show');

      document.querySelector('#matricula_id').value = matricula.id;
      document.querySelector('select[name="turma_id"]').value = matricula.turma_id || '';
      document.querySelector('select[name="usuario_id"]').value = matricula.usuario_id || '';
      document.querySelector('select[name="edital_origem_id"]').value = matricula.edital_origem_id || '';

      document.querySelector('#modalNovaMatricula .modal-title').textContent = 'Editar Matrícula';
      document.querySelector('#modalNovaMatricula button[type="submit"]').textContent = 'Atualizar Matrícula';
    }


</script>

<?php include 'includes/footer.php'; ?>

