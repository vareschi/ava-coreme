<?php
session_start();
ini_set('display_errors', 1);
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
$usuario_id = $_SESSION['usuario_id'];
$perfil_id = $_SESSION['perfil_id'] ?? null; // Ex: 1 = admin, 2 = secretaria, 3 = residente, 4 = preceptor

// Filtros
$campo_estagio_id = $_GET['campo_estagio_id'] ?? '';
$especialidade_id = $_GET['especialidade_id'] ?? '';
$status = $_GET['status'] ?? '';
$busca = $_GET['busca'] ?? '';

// Construir query base
$sql = "SELECT ag.*, 
        u.nome AS residente_nome,
        p.nome AS preceptor_nome,
        e.nome AS especialidade_nome,
        ce.nome AS campo_estagio_nome
        FROM avaliacoes_geradas ag
        JOIN usuarios u ON ag.residente_id = u.id
        JOIN usuarios p ON ag.preceptor_id = p.id
        JOIN especialidades e ON ag.especialidade_id = e.id
        JOIN campos_estagio ce ON ag.campo_estagio_id = ce.id
        WHERE ag.inicio_avaliacao <= CURDATE() AND ag.fim_avaliacao >= CURDATE()";

// Aplicar permissões por perfil
if ($perfil_id == 3) {
    $sql .= " AND ag.residente_id = $usuario_id";
} elseif ($perfil_id == 4) {
    $sql .= " AND ag.preceptor_id = $usuario_id";
}

// Aplicar filtros
if ($campo_estagio_id) $sql .= " AND ag.campo_estagio_id = " . (int)$campo_estagio_id;
if ($especialidade_id) $sql .= " AND ag.especialidade_id = " . (int)$especialidade_id;
if ($status !== '') $sql .= " AND ag.status = " . (int)$status;
if ($busca) $sql .= " AND (u.nome LIKE '%$busca%' OR p.nome LIKE '%$busca%')";

$sql .= " ORDER BY ag.inicio_avaliacao DESC";

// Paginação
$limite = 10;
$pagina = $_GET['pagina'] ?? 1;
$offset = ($pagina - 1) * $limite;

$sqlPaginada = $sql . " LIMIT $limite OFFSET $offset";
$avaliacoes = $pdo->query($sqlPaginada)->fetchAll(PDO::FETCH_ASSOC);

// Total para paginação
$total = $pdo->query(str_replace("SELECT ag.*,", "SELECT COUNT(*) as total,", $sql))->fetch()['total'];
$totalPaginas = ceil($total / $limite);

// Filtros auxiliares
$campos_estagio = $pdo->query("SELECT id, nome FROM campos_estagio WHERE data_exclusao IS NULL ORDER BY nome")->fetchAll();
$especialidades = $pdo->query("SELECT id, nome FROM especialidades ORDER BY nome")->fetchAll();
?>

<div class="container mt-4">
  <h2>Avaliações</h2>

  <form class="row g-3 mb-3">
    <div class="col-md-3">
      <label>Campo de Estágio</label>
      <select name="campo_estagio_id" class="form-control">
        <option value="">Todos</option>
        <?php foreach ($campos_estagio as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $campo_estagio_id == $c['id'] ? 'selected' : '' ?>><?= $c['nome'] ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-3">
      <label>Especialidade</label>
      <select name="especialidade_id" class="form-control">
        <option value="">Todas</option>
        <?php foreach ($especialidades as $e): ?>
          <option value="<?= $e['id'] ?>" <?= $especialidade_id == $e['id'] ? 'selected' : '' ?>><?= $e['nome'] ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-2">
      <label>Status</label>
      <select name="status" class="form-control">
        <option value="">Todos</option>
        <option value="1" <?= $status === '1' ? 'selected' : '' ?>>Avaliar</option>
        <option value="2" <?= $status === '2' ? 'selected' : '' ?>>Iniciado</option>
        <option value="3" <?= $status === '3' ? 'selected' : '' ?>>Finalizado</option>
      </select>
    </div>

    <div class="col-md-2">
      <label>Busca</label>
      <input type="text" name="busca" value="<?= htmlspecialchars($busca) ?>" class="form-control" placeholder="Nome">
    </div>

    <div class="col-md-2 align-self-end">
      <button class="btn btn-primary w-100">Filtrar</button>
    </div>
  </form>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Preceptor</th>
        <th>Residente</th>
        <th>Especialidade</th>
        <th>Status</th>
        <th>Prazo</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($avaliacoes as $av): ?>
        <tr>
          <td><?= htmlspecialchars($av['preceptor_nome']) ?></td>
          <td><?= htmlspecialchars($av['residente_nome']) ?></td>
          <td><?= htmlspecialchars($av['especialidade_nome']) ?></td>
          <td>
            <?php
              echo match($av['status']) {
                1 => 'Avaliar',
                2 => 'Iniciado',
                3 => 'Finalizado',
                default => 'Desconhecido'
              };
            ?>
          </td>
          <td>
            <?php
              $hoje = new DateTime();
              $fim = new DateTime($av['fim_avaliacao']);
              $inicio = new DateTime($av['inicio_avaliacao']);
              if ($hoje < $inicio || $hoje > $fim) {
                  echo "Fora do Prazo";
              } else {
                  echo $hoje->diff($fim)->format('%a dias');
              }
            ?>
          </td>
          <td>
            <?php if ($perfil_id == 4 && $av['status'] == 1): ?>
              <a href="avaliar_form.php?id=<?= $av['id'] ?>" class="btn btn-sm btn-primary">Avaliar</a>
            <?php else: ?>
              -
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <nav>
    <ul class="pagination">
      <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
        <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
          <a class="page-link" href="?pagina=<?= $i ?>&<?= http_build_query($_GET) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
</div>

<?php include 'includes/footer.php'; ?>
