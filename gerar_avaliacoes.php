<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

$turmas = $pdo->query("SELECT id, nome FROM turmas WHERE status = 1 ORDER BY nome")->fetchAll();
$residentes = $pdo->query("SELECT u.id, u.nome FROM usuarios u JOIN usuario_perfis up ON u.id = up.usuario_id WHERE up.perfil_id = 3 ORDER BY u.nome")->fetchAll();
$modelos = $pdo->query("SELECT id, titulo FROM avaliacoes WHERE status=1 ORDER BY titulo")->fetchAll();
$campos_estagio = $pdo->query("SELECT id, nome FROM campos_estagio WHERE data_exclusao IS NULL ORDER BY nome")->fetchAll();
?>

<div class="container mt-4">
    <div class="card">
  <div class="card-body">
    <h4 class="card-title">Gerar Avaliações</h4>
    <form action="actions/processar_geracao_avaliacoes.php" method="POST">

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="tipo_geracao">Gerar por:</label>
          <select name="tipo_geracao" id="tipo_geracao" class="form-control" required>
            <option value="">Selecione...</option>
            <option value="turma">Turma</option>
            <option value="residente">Residente</option>
          </select>
        </div>

        <div class="col-md-6 mb-3" id="campo_turma" style="display:none;">
          <label for="turma_id">Turma</label>
          <select name="turma_id" id="turma_id" class="form-control">
            <option value="">Selecione</option>
            <?php foreach ($turmas as $turma): ?>
              <option value="<?= $turma['id'] ?>"><?= htmlspecialchars($turma['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6 mb-3" id="campo_residente" style="display:none;">
          <label for="residente_id">Residente</label>
          <select name="residente_id" id="residente_id" class="form-control">
            <option value="">Selecione</option>
            <?php foreach ($residentes as $residente): ?>
              <option value="<?= $residente['id'] ?>"><?= htmlspecialchars($residente['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6 mb-3">
          <label for="modelo_avaliacao_id">Modelo de Avaliação</label>
          <select name="modelo_avaliacao_id" id="modelo_avaliacao_id" class="form-control" required>
            <option value="">Selecione</option>
            <?php foreach ($modelos  as $a): ?>
              <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['titulo']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6 mb-3">
          <label for="campo_estagio_id">Campo de Estágio</label>
          <select name="campo_estagio_id" id="campo_estagio_id" class="form-control" required>
            <option value="">Selecione</option>
            <?php foreach ($campos_estagio as $c): ?>
              <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6 mb-3">
          <label for="preceptor_id">Preceptor (opcional)</label>
          <select name="preceptores[]" id="preceptor_id" class="form-control js-example-basic-multiple" multiple="multiple"></select>
        </div>

        <div class="col-md-3 mb-3">
          <label for="inicio_avaliacao">Início Avaliação</label>
          <input type="date" name="inicio_avaliacao" id="inicio_avaliacao" class="form-control" required>
        </div>

        <div class="col-md-3 mb-3">
          <label for="fim_avaliacao">Fim Avaliação</label>
          <input type="date" name="fim_avaliacao" id="fim_avaliacao" class="form-control" required>
        </div>

        <div class="col-md-3 mb-3">
          <label for="nivel_especialidade">Nível da Especialidade</label>
          <select name="nivel_especialidade" id="nivel_especialidade" class="form-control" required>
            <option value="">Selecione...</option>
            <option value="R1">R1</option>
            <option value="R2">R2</option>
            <option value="R3">R3</option>
            <option value="R4">R4</option>
            <option value="R5">R5</option>
          </select>
        </div>

        <div class="col-md-3 mb-3">
          <label for="ano_letivo">Ano Letivo</label>
          <input type="number" name="ano_letivo" id="ano_letivo" class="form-control" value="<?= date('Y') ?>" required>
        </div>

        <div class="col-md-3 mb-3">
          <label for="mes_referencia">Mês de Referência</label>
          <select name="mes_referencia" id="mes_referencia" class="form-control" required>
            <option value="">Selecione...</option>
            <?php
            $meses = [
                '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
                '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
                '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
              ];
              foreach ($meses as $num => $nome): ?>
                <option value="<?= $num ?>"><?= $nome ?></option>
            <?php endforeach; ?>

          </select>
        </div>
      </div>

      <div class="d-grid mt-3">
        <button type="submit" class="btn btn-success">Gerar Avaliações</button>
      </div>
       </form>
  </div>
</div>

  
</div>

<script>

document.getElementById('tipo_geracao').addEventListener('change', function () {
  const tipo = this.value;
  document.getElementById('campo_turma').style.display = tipo === 'turma' ? 'block' : 'none';
  document.getElementById('campo_residente').style.display = tipo === 'residente' ? 'block' : 'none';
});

document.getElementById('campo_estagio_id').addEventListener('change', function () {
  const campoId = this.value;
  const preceptorSelect = document.getElementById('preceptor_id');

  preceptorSelect.innerHTML = '<option value="">Todos os preceptores do campo</option>';

  if (campoId) {
    fetch(`api/preceptores_por_campo.php?campo_id=${campoId}`)
      .then(res => res.json())
      .then(data => {
        data.forEach(preceptor => {
          const option = document.createElement('option');
          option.value = preceptor.id;
          option.textContent = preceptor.nome;
          preceptorSelect.appendChild(option);
        });
      });
  }
});

</script>

<?php include 'includes/footer.php'; ?>
