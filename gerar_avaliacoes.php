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
$modelos = $pdo->query("SELECT id, titulo FROM avaliacoes_modelos ORDER BY titulo")->fetchAll();
$campos_estagio = $pdo->query("SELECT id, nome FROM campos_estagio WHERE data_exclusao IS NULL ORDER BY nome")->fetchAll();
?>

<div class="container mt-4">
  <h2>Gerar Avaliações</h2>
  <form action="actions/gerar_avaliacoes.php" method="POST">
    <div class="card mb-3">
      <div class="card-header">Parâmetros</div>
      <div class="card-body">
        <div class="form-group mb-3">
          <label for="tipo_destino">Destino</label>
          <select name="tipo_destino" id="tipo_destino" class="form-control" onchange="toggleDestino()" required>
            <option value="">Selecione</option>
            <option value="turma">Turma</option>
            <option value="residente">Residente</option>
          </select>
        </div>

        <div class="form-group mb-3 d-none" id="grupo_turma">
          <label for="turma_id">Turma</label>
          <select name="turma_id" id="turma_id" class="form-control">
            <option value="">Selecione</option>
            <?php foreach ($turmas as $t): ?>
              <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group mb-3 d-none" id="grupo_residente">
          <label for="residente_id">Residente</label>
          <select name="residente_id" id="residente_id" class="form-control">
            <option value="">Selecione</option>
            <?php foreach ($residentes as $r): ?>
              <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group mb-3">
          <label for="modelo_id">Modelo de Avaliação</label>
          <select name="modelo_id" id="modelo_id" class="form-control" required>
            <option value="">Selecione</option>
            <?php foreach ($modelos as $m): ?>
              <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['titulo']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group mb-3">
          <label for="campo_estagio_id">Campo de Estágio</label>
          <select name="campo_estagio_id" id="campo_estagio_id" class="form-control" required>
            <option value="">Selecione</option>
            <?php foreach ($campos_estagio as $c): ?>
              <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>

    <div class="d-grid">
      <button type="submit" class="btn btn-success">Gerar Avaliações</button>
    </div>
  </form>
</div>

<script>
function toggleDestino() {
  const tipo = document.getElementById('tipo_destino').value;
  document.getElementById('grupo_turma').classList.toggle('d-none', tipo !== 'turma');
  document.getElementById('grupo_residente').classList.toggle('d-none', tipo !== 'residente');
}
</script>

<?php include 'includes/footer.php'; ?>
