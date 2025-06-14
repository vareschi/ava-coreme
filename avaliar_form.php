<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'includes/config.php';
require_once 'includes/funcoes.php';

verificarAcessoRecurso('avaliar');

if (!isset($_GET['id'])) {
    die('Avaliação não especificada.');
}

$avaliacao_gerada_id = $_GET['id'];
$pdo = getPDO();

$perfil_id = $_SESSION['perfil_id'] ?? null;
$usuario_id = $_SESSION['usuario_id'];

$sql = "SELECT ag.*, u.nome AS residente_nome, p.nome AS preceptor_nome
        FROM avaliacoes_geradas ag
        JOIN usuarios u ON ag.residente_id = u.id
        JOIN usuarios p ON ag.preceptor_id = p.id
        WHERE ag.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$avaliacao_gerada_id]);
$avaliacao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$avaliacao) {
    die('Avaliação não encontrada.');
}

if ($perfil_id == 4 && $avaliacao['preceptor_id'] != $usuario_id) {
    die('Você não tem permissão para avaliar esta avaliação.');
}

$desabilitarCampos = ($avaliacao['status'] == 3); // Desativa se finalizada

// Carrega perguntas e critérios
$stmt = $pdo->prepare("SELECT id, titulo FROM avaliacoes_perguntas WHERE avaliacao_id = ? AND status = 1");
$stmt->execute([$avaliacao['modelo_id']]);
$perguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pergunta_ids = array_column($perguntas, 'id');
$criterios = [];
if ($pergunta_ids) {
    $in = implode(',', array_fill(0, count($pergunta_ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM avaliacoes_criterios WHERE pergunta_id IN ($in) and status=1");
    $stmt->execute($pergunta_ids);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $c) {
        $criterios[$c['pergunta_id']][] = $c;
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/topbar.php';
?>

<div class="container mt-4">
  <h2>Avaliar Residente: <?= htmlspecialchars($avaliacao['residente_nome']) ?></h2>
  <form action="actions/salvar_nota_criterio.php" method="POST">
    <input type="hidden" name="avaliacao_gerada_id" value="<?= $avaliacao_gerada_id ?>">

    <?php foreach ($perguntas as $pergunta): ?>
      <div class="card mb-3">
        <div class="card-header">
          <strong><?= htmlspecialchars($pergunta['titulo']) ?></strong>
        </div>
        <div class="card-body">
          <?php if (!empty($criterios[$pergunta['id']])): ?>
            <?php foreach ($criterios[$pergunta['id']] as $criterio): ?>
              <div class="form-group mb-2">
                <label><?= htmlspecialchars($criterio['descricao']) ?></label>
                <select class="form-control" name="criterios[<?= $criterio['id'] ?>]" <?= $desabilitarCampos ? 'disabled' : '' ?> required>
                  <option value="">Selecione...</option>
                  <?php
                    $notas = [
                      0 => '0 - Insuficiente', 1 => '1 - Insuficiente', 2 => '2 - Insuficiente', 3 => '3 - Insuficiente',
                      4 => '4 - Insatisfatório', 5 => '5 - Insatisfatório', 6 => '6 - Insatisfatório',
                      7 => '7 - Satisfatório',
                      8 => '8 - Plenamente Satisfatório', 9 => '9 - Plenamente Satisfatório',
                      10 => '10 - Acima das Expectativas'
                    ];
                    foreach ($notas as $valor => $desc) {
                        echo "<option value=\"$valor\">$desc</option>";
                    }
                  ?>
                </select>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p><em>Sem critérios associados.</em></p>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>

    <div class="form-group">
      <label>Observações do Preceptor (opcional)</label>
      <textarea name="observacoes" class="form-control" rows="4" <?= $desabilitarCampos ? 'disabled' : '' ?>>
        <?= htmlspecialchars($avaliacao['observacoes_preceptor'] ?? '') ?>
      </textarea>
    </div>

    <?php if (!$desabilitarCampos): ?>
      <div class="d-grid">
        <button type="submit" class="btn btn-success">Salvar Avaliação</button>
      </div>
    <?php endif; ?>
  </form>
</div>

<?php include 'includes/footer.php'; ?>
