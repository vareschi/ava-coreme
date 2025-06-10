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

verificarAcessoRecurso('turmas');

$pdo = getPDO();
$turmas = $pdo->query("SELECT t.*, e.nome AS nome_especialidade, ed.numero AS numero_edital FROM turmas t
                       LEFT JOIN especialidades e ON t.especialidade_id = e.id
                       LEFT JOIN editais ed ON t.edital_id = ed.id
                       WHERE t.status = 1
                       ORDER BY t.data_abertura DESC")->fetchAll();
$especialidades = $pdo->query("SELECT id, nome FROM especialidades ORDER BY nome")->fetchAll();
$preceptores = $pdo->query("SELECT u.id, u.nome from usuarios u, usuario_perfis p WHERE p.usuario_id = u.id AND p.perfil_id = 4 ORDER BY u.nome")->fetchAll();
?>

<div class="content">
  <div class="card">
    <div class="card-body">
      <h4 class="card-title d-flex justify-content-between align-items-center">
        Turmas
        <button class="btn btn-primary" onclick="abrirModalNovaTurma()">+ Nova Turma</button>
      </h4>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <table class="table table-hover nowrap" style="width:100%">
        <thead>
          <tr>
            <th>Nome</th>
            <th>Especialidade</th>
            <th>Abertura</th>
            <th>Fechamento</th>
            <th class="text-right">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($turmas as $turma): ?>
            <tr>
              <td><?= htmlspecialchars($turma['nome']) ?></td>
              <td><?= htmlspecialchars($turma['nome_especialidade']) ?></td>
              <td><?= date('d/m/Y', strtotime($turma['data_abertura'])) ?></td>
              <td><?= date('d/m/Y', strtotime($turma['data_fechamento'])) ?></td>
              <td class="text-right">
                <button class="btn btn-sm btn-link text-primary" onclick="editarTurma(<?= $turma['id'] ?>)">
                  <i class="mdi mdi-pencil"></i>
                </button>
                <a href="actions/excluir_turma.php?id=<?= $turma['id'] ?>" onclick="return confirm('Excluir esta turma?')" class="btn btn-sm btn-link text-danger">
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

<!-- Modal Nova Turma -->
<div class="modal fade" id="modalNovaTurma" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <!-- Dentro da tag <form> -->
    <form class="modal-content" id="form-turma" method="POST" action="actions/salvar_turma.php">
    <div class="modal-header">
        <h5 class="modal-title">Nova Turma</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
    </div>
    <div class="modal-body">
        <!-- Campo oculto para ID da turma -->
        <input type="hidden" name="id" id="turma-id">

        <div class="form-group">
        <label>Nome da Turma</label>
        <input type="text" name="nome" id="nome" class="form-control" required>
        </div>

        <div class="form-row">
        <div class="form-group col-md-6">
            <label>Especialidade</label>
            <select name="especialidade_id" id="especialidade_id" class="form-control" required>
            <option value="">Selecione</option>
            <?php foreach ($especialidades as $esp): ?>
                <option value="<?= $esp['id'] ?>"><?= htmlspecialchars($esp['nome']) ?></option>
            <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group col-md-6">
            <label>Preceptor</label>
            <select name="preceptor_id" id="preceptor_id" class="form-control">
            <option value="">Opcional</option>
            <?php foreach ($preceptores as $p): ?>
                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nome']) ?></option>
            <?php endforeach; ?>
            </select>
        </div>
        </div>

        <div class="form-row">
        <div class="form-group col-md-6">
            <label>Carga Horária</label>
            <input type="number" name="carga_horaria" id="carga_horaria" class="form-control" required>
        </div>
        </div>

        <div class="form-row">
        <div class="form-group col-md-6">
            <label>Data de Abertura</label>
            <input type="date" name="data_abertura" id="data_abertura" class="form-control" required>
        </div>
        <div class="form-group col-md-6">
            <label>Data de Finalização</label>
            <input type="date" name="data_fechamento" id="data_fechamento" class="form-control" required>
        </div>
        </div>

        <div class="form-group">
        <label>Vagas</label>
        <input type="number" name="vagas" id="vagas" class="form-control" required>
        </div>

        <div class="form-group">
        <label>Descrição</label>
        <textarea name="descricao" id="descricao" class="form-control" rows="5"></textarea>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Salvar Turma</button>
    </div>
    </form>

  </div>
</div>

<script>
function abrirModalNovaTurma() {
    document.getElementById('form-turma').reset();
    document.getElementById('turma-id').value = '';
    document.getElementById('especialidade_id').value = '';
    document.getElementById('preceptor_id').value = '';
    document.getElementById('descricao').value = '';
    $('#modalNovaTurma').modal('show');
}


function editarTurma(id) {
    fetch('actions/get_turma.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data && data.id) {
                document.getElementById('turma-id').value = data.id;
                document.getElementById('nome').value = data.nome;
                document.getElementById('especialidade_id').value = data.especialidade_id;
                document.getElementById('preceptor_id').value = data.preceptor_id ?? '';
                document.getElementById('data_abertura').value = data.data_abertura;
                document.getElementById('data_fechamento').value = data.data_fechamento;
                document.getElementById('descricao').value = data.descricao;
                document.getElementById('carga_horaria').value = data.carga_horaria;
                document.getElementById('vagas').value = data.vagas;

                $('#modalNovaTurma').modal('show');
            } else {
                alert('Erro ao carregar dados da turma.');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao carregar turma.');
        });
}

</script>


<?php include 'includes/footer.php'; ?>

