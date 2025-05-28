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

verificarAcessoRecurso('editais');

$pdo = getPDO();

$id = $_GET['id'] ?? null;

$edital = [
    'id' => '',
    'nome' => '',
    'numero' => '',
    'descricao' => '',
    'data_abertura' => '',
    'data_fechamento' => '',
    'tipo' => ''
];
$especialidadesMarcadas = [];
$tipos = ['Normal', 'Vagas Remanescentes'];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM editais WHERE id = ?");
    $stmt->execute([$id]);
    $edital = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmtEsp = $pdo->prepare("SELECT especialidade_id FROM edital_especialidades WHERE edital_id = ?");
    $stmtEsp->execute([$id]);
    $especialidadesMarcadas = $stmtEsp->fetchAll(PDO::FETCH_COLUMN);
}

$especialidades = $pdo->query("SELECT * FROM especialidades ORDER BY nome")->fetchAll();

$anexos = [];
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM edital_arquivos WHERE edital_id = ?");
    $stmt->execute([$editar_id]);
    $anexos = $stmt->fetchAll();
}
?>

<div class="content">
  <form method="POST" action="actions/salvar_edital.php" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $edital['id'] ?>">

    <div class="card">
      <div class="card-body">
        <h4 class="card-title"><?= $id ? 'Editar' : 'Novo' ?> Edital</h4>

        <div class="form-group">
          <label>Selecione as Especialidades para este Edital</label><br>
          <?php foreach ($especialidades as $esp): ?>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" name="especialidades[]" value="<?= $esp['id'] ?>"
                <?= in_array($esp['id'], $especialidadesMarcadas) ? 'checked' : '' ?>>
              <label class="form-check-label"><?= htmlspecialchars($esp['nome']) ?></label>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="row">
          <div class="col-md-6 form-group">
            <label>Nome do edital *</label>
            <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($edital['nome']) ?>" required>
          </div>
          <div class="col-md-6 form-group">
            <label>Número do edital *</label>
            <input type="text" name="numero" class="form-control" value="<?= htmlspecialchars($edital['numero']) ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label>Descrição do Edital</label>
          <textarea name="descricao" class="form-control" rows="5"><?= htmlspecialchars($edital['descricao']) ?></textarea>
        </div>

        <div class="row">
          <div class="col-md-6 form-group">
            <label>Data de abertura *</label>
            <input type="date" name="data_abertura" class="form-control" value="<?= $edital['data_abertura'] ?>" required>
          </div>
          <div class="col-md-6 form-group">
            <label>Data de fechamento *</label>
            <input type="date" name="data_fechamento" class="form-control" value="<?= $edital['data_fechamento'] ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label>Tipo *</label>
          <select name="tipo" class="form-control" required>
            <option value="">Selecione...</option>
            <?php foreach ($tipos as $tipo): ?>
              <option value="<?= $tipo ?>" <?= $edital['tipo'] === $tipo ? 'selected' : '' ?>><?= $tipo ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label>Arquivos Anexos:</label>
          <input type="file" name="anexos[]" multiple class="form-control-file">
        </div>

        <?php if (!empty($anexos)): ?>
            <div class="mt-2">
                <label><strong>Arquivos anexados:</strong></label>
                <ul class="list-unstyled">
                    <?php foreach ($anexos as $arquivo): ?>
                        <li>
                            <a href="<?= htmlspecialchars($arquivo['caminho_arquivo']) ?>" target="_blank">
                                <?= htmlspecialchars($arquivo['nome_arquivo']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>


        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="editais.php" class="btn btn-secondary">Cancelar</a>
      </div>
    </div>
  </form>
</div>

<?php include 'includes/footer.php'; ?>
