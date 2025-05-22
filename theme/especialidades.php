<?php
session_start();
require_once 'includes/config.php';

$pdo = getPDO();
$especialidades = $pdo->query("SELECT * FROM especialidades ORDER BY nome ASC")->fetchAll();

$editar_id = isset($_GET['editar']) ? (int)$_GET['editar'] : null;
$editar_especialidade = null;

if ($editar_id) {
    $stmt = $pdo->prepare("SELECT * FROM especialidades WHERE id = ?");
    $stmt->execute([$editar_id]);
    $editar_especialidade = $stmt->fetch();
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/topbar.php'; ?>



<div class="content">
  <div class="card">
    <div class="card-body">
      <h4 class="card-title d-flex justify-content-between align-items-center">
        Especialidades
        <form class="form-inline" method="POST" action="actions/salvar_especialidade.php">
            <input type="hidden" name="id" value="<?= $editar_especialidade['id'] ?? '' ?>">
            <input type="text" name="nome" class="form-control mr-2"
                    placeholder="Digite uma especialidade..."
                    value="<?= htmlspecialchars($editar_especialidade['nome'] ?? '') ?>" required>
            <input type="number" name="duracao" class="form-control mr-2"
                    placeholder="Duração (anos)"
                    value="<?= htmlspecialchars($editar_especialidade['duracao_anos'] ?? '') ?>" min="1" required>
            <button type="submit" class="btn btn-<?= $editar_especialidade ? 'warning' : 'primary' ?>">
                <?= $editar_especialidade ? 'Atualizar' : 'Cadastrar' ?>
            </button>
        </form>
      </h4>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <table class="table">
        <thead>
          <tr>
            <th>Especialidade</th>
            <th>Duração (anos)</th>
            <th class="text-right">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($especialidades as $esp): ?>
            <tr>
              <td><?= htmlspecialchars($esp['nome']) ?></td>
              <td><?= htmlspecialchars($esp['duracao_anos']) ?></td>
              <td class="text-right">
                <a href="especialidades.php?editar=<?= $esp['id'] ?>" class="mr-2 text-primary"><i class="mdi mdi-pencil"></i></a>
                <a href="actions/excluir_especialidade.php?id=<?= $esp['id'] ?>" onclick="return confirm('Deseja excluir esta especialidade?')" class="text-danger"><i class="mdi mdi-delete"></i></a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
