<?php
session_start();
require_once 'includes/config.php';

$pdo = getPDO();
$especialidades = $pdo->query("SELECT * FROM especialidades ORDER BY nome ASC")->fetchAll();
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/topbar.php'; ?>

<div class="content">
  <div class="card">
    <div class="card-body">
      <h4 class="card-title d-flex justify-content-between align-items-center">
        Especialidades
        <form class="form-inline" method="POST" action="salvar_especialidade.php">
          <input type="text" name="nome" class="form-control mr-2" placeholder="Digite uma especialidade..." required>
          <input type="number" name="duracao" class="form-control mr-2" placeholder="Duração (anos)" min="1" required>
          <button type="submit" class="btn btn-primary">Cadastrar</button>
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
                <a href="actions/especialidades.php?editar=<?= $esp['id'] ?>" class="mr-2 text-primary"><i class="mdi mdi-pencil"></i></a>
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
