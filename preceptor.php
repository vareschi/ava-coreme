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

verificarAcessoRecurso('usuarios');

$pdo = getPDO();

$usuario_id = $_GET['usuario_id'] ?? null;
$preceptor = [];
$campos_estagio = $pdo->query("SELECT id, nome FROM campos_estagio WHERE data_exclusao IS NULL ORDER BY nome")->fetchAll();
// Buscar especialidades disponíveis
$especialidades = $pdo->query("SELECT id, nome FROM especialidades ORDER BY nome")->fetchAll();

$dados_usuario = [];

if ($usuario_id) {

    $stmtDados = $pdo->prepare("SELECT crm FROM usuarios_dados WHERE usuario_id = ?");
    $stmtDados->execute([$usuario_id]);
    $dados_usuario = $stmtDados->fetch(PDO::FETCH_ASSOC) ?: [];

    $stmt = $pdo->prepare("SELECT * FROM preceptores WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);
    $preceptor = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmtCampos = $pdo->prepare("SELECT campo_estagio_id FROM preceptor_campoestagio WHERE preceptor_id = ?");
    $stmtCampos->execute([$preceptor['id'] ?? 0]);
    $camposSelecionados = $stmtCampos->fetchAll(PDO::FETCH_COLUMN);
} else {
    $camposSelecionados = [];
}
?>

<div class="container mt-4">
  <h2>Dados do Preceptor</h2>
  <form action="actions/salvar_preceptor.php" method="POST">
    <div class="card mb-3">
      <div class="card-header">Preceptoria</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label">Data de Início na Preceptoria</label>
              <input type="date" name="data_inicio" class="form-control" value="<?= htmlspecialchars($preceptor['data_inicio'] ?? '') ?>">
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label">Especialidade</label>
              <select name="especialidade_id" class="form-control">
                <option value="">Selecione</option>
                <?php foreach ($especialidades as $esp): ?>
                  <option value="<?= $esp['id'] ?>" <?= ($preceptor['especialidade_id'] ?? '') == $esp['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($esp['nome']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label">Coordenador</label>
              <select name="coordenador" class="form-control">
                <option value="">Selecione</option>
                <option value="Sim" <?= ($preceptor['coordenador'] ?? '') === 'Sim' ? 'selected' : '' ?>>Sim</option>
                <option value="Não" <?= ($preceptor['coordenador'] ?? '') === 'Não' ? 'selected' : '' ?>>Não</option>
              </select>
            </div>
          </div>
        </div>


        <div class="form-group">
          <label class="form-label">Campos de Estágio</label>
          <div class="row">
            <?php foreach ($campos_estagio as $campo): ?>
              <div class="col-md-6">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="campos_estagio[]" value="<?= $campo['id'] ?>" <?= in_array($campo['id'], $camposSelecionados) ? 'checked' : '' ?>>
                  <label class="form-check-label"><?= htmlspecialchars($campo['nome']) ?></label>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-header">Informações do Usuário</div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <label class="form-label">CRM</label>
            <input type="text" name="crm" class="form-control" value="<?= htmlspecialchars($dados_usuario['crm'] ?? '') ?>">
          </div>
        </div>
      </div>
    </div>


    <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario_id) ?>">

    <div class="d-grid">
      <button type="submit" class="btn btn-success">Salvar Dados</button>
    </div>
  </form>
</div>


<?php include 'includes/footer.php'; ?>
