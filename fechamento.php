<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/funcoes.php';

session_start();
verificarAcessoRecurso('avaliacoes');

$usuario_id = $_GET['usuario_id'] ?? null;
if (!$usuario_id || !is_numeric($usuario_id)) {
    die("ID de residente inválido.");
}

$pdo = getPDO();

$stmt = $pdo->prepare("SELECT 
    ag.id AS avaliacao_id,
    DATE_FORMAT(ag.inicio_avaliacao, '%M') AS mes,
    u.nome AS avaliador,
    ce.nome AS campo_estagio,
    a.titulo,
    ROUND(AVG(ar.nota_atribuida), 2) AS nota_media
FROM avaliacoes_geradas ag
JOIN usuarios u ON u.id = ag.preceptor_id
JOIN campos_estagio ce ON ce.id = ag.campo_estagio_id
JOIN avaliacoes_respostas ar ON ar.avaliacao_gerada_id = ag.id
JOIN avaliacoes_criterios ac ON ac.id = ar.criterio_id
JOIN avaliacoes_perguntas ap ON ap.id = ac.pergunta_id
JOIN avaliacoes a ON a.id = ag.modelo_id
WHERE ag.residente_id = ? 
GROUP BY mes, avaliador, campo_estagio, a.titulo
ORDER BY ag.inicio_avaliacao;");

$stmt->execute([$usuario_id]);
$avaliacoes = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>
<div class="container mt-4">
  <h2>Fechamento de Avaliações</h2>
  <p><strong>Residente ID:</strong> <?= htmlspecialchars($usuario_id) ?></p>

  <table class="table table-bordered table-hover">
    <thead class="table-light">
      <tr>
        <th>Mês</th>
        <th>Avaliador</th>
        <th>Campo de Estágio</th>
        <th>Tipo de Avaliação</th>
        <th>Média</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($avaliacoes as $av): ?>
        <tr>
          <td><?= ucfirst(htmlspecialchars($av['mes'])) ?></td>
          <td><?= htmlspecialchars($av['avaliador']) ?></td>
          <td><?= htmlspecialchars($av['campo_estagio']) ?></td>
          <td><?= htmlspecialchars($av['titulo']) ?></td>
          <td><strong><?= number_format($av['nota_media'], 2, ',', '.') ?></strong></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <a href="residentes.php" class="btn btn-secondary mt-3">Voltar</a>
</div>
<?php include 'includes/footer.php'; ?>
