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

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM avaliacoes WHERE id = ?");
    $stmt->execute([$id]);
    $avaliacao = $stmt->fetch(PDO::FETCH_ASSOC);
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

        <div class="d-grid">
          <button type="submit" class="btn btn-success">Salvar Avaliação</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
