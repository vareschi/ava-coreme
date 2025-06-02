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
$editais = $pdo->query("SELECT * FROM editais ORDER BY data_abertura DESC")->fetchAll();
?>

<div class="content">
  <div class="card">
    <div class="card-body">
      <h4 class="card-title d-flex justify-content-between align-items-center">
        Editais
        <a href="editar_edital.php" class="btn btn-primary">+ Novo Edital</a>
      </h4>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <table class="table table-hover nowrap" style="width:100%">
        <thead>
          <tr>
            <th>Número</th>
            <th>Nome</th>
            <th>Tipo</th>
            <th>Abertura</th>
            <th>Fechamento</th>
            <th class="text-right">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($editais as $edital): ?>
            <tr>
              <td><?= htmlspecialchars($edital['numero']) ?></td>
              <td><?= htmlspecialchars($edital['nome']) ?></td>
              <td><?= htmlspecialchars($edital['tipo']) ?></td>
              <td><?= date('d/m/Y', strtotime($edital['data_abertura'])) ?></td>
              <td><?= date('d/m/Y', strtotime($edital['data_fechamento'])) ?></td>
              <td class="text-right">
                <a href="editar_edital.php?id=<?= $edital['id'] ?>" class="btn btn-sm btn-link text-primary">
                  <i class="mdi mdi-pencil"></i>
                </a>
                <a href="actions/excluir_edital.php?id=<?= $edital['id'] ?>" onclick="return confirm('Excluir este edital?')" class="btn btn-sm btn-link text-danger">
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

<?php include 'includes/footer.php'; ?>