<link href="assets/options/optionswitch.css" rel="stylesheet">
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

verificarAcessoRecurso('usuarios');

$pdo = getPDO();

$usuario_id = $_GET['usuario_id'] ?? null;
$residente = [];

function formatDateInput($data)
{
  if (!$data || $data == '0000-00-00')
    return '';
  return date('Y-m-d', strtotime($data)); // formato para campo input[type="date"]
}

if ($usuario_id) {
  $stmt = $pdo->prepare("SELECT * FROM residentes WHERE usuario_id = ?");
  $stmt->execute([$usuario_id]);
  $residente = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Buscar tipos de documento
$tipos = $pdo->query("SELECT * FROM documentos_tipo WHERE perfil_id = 0")->fetchAll(PDO::FETCH_ASSOC);

// Buscar documentos já enviados
$documentos = $pdo->prepare("
          SELECT d.id, dt.nome AS tipo, d.caminho_arquivo, d.data_inclusao 
          FROM documentos d
          JOIN documentos_tipo dt ON dt.id = d.tipo_documento_id
          WHERE d.residente_id = ? AND d.data_exclusao IS NULL
      ");
$documentos->execute([$usuario_id]);
$documentos = $documentos->fetchAll(PDO::FETCH_ASSOC);
$documentos_enviados_ids = array_column($documentos, 'tipo_documento_id');


?>

<div class="container mt-4">
  <h2>Cadastro de Residente</h2>
  <form action="actions/salvar_residente.php" method="POST" enctype="multipart/form-data">
    <div class="card mb-3">
      <div class="card-header">Status e Identificação</div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">Situação</label><br>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status" value="Em análise" checked>
            <label class="form-check-label">Em análise</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status" value="Documentação pendente">
            <label class="form-check-label">Documentação pendente</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status" value="Ativo">
            <label class="form-check-label">Ativo</label>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nome do Pai</label>
            <input type="text" name="nome_pai" class="form-control"
              value="<?= htmlspecialchars($residente['nome_pai'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Nome da Mãe</label>
            <input type="text" name="nome_mae" class="form-control"
              value="<?= htmlspecialchars($residente['nome_mae'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Estado Civil</label>
            <select name="estado_civil" class="form-select">
              <option value="">Selecione</option>
              <option <?= ($residente['estado_civil'] ?? '') === 'Solteiro' ? 'selected' : '' ?>>Solteiro</option>
              <option <?= ($residente['estado_civil'] ?? '') === 'Casado' ? 'selected' : '' ?>>Casado</option>
              <option <?= ($residente['estado_civil'] ?? '') === 'Divorciado' ? 'selected' : '' ?>>Divorciado</option>
              <option <?= ($residente['estado_civil'] ?? '') === 'Viúvo' ? 'selected' : '' ?>>Viúvo</option>
              <option <?= ($residente['estado_civil'] ?? '') === 'União Estável' ? 'selected' : '' ?>>União Estável
              </option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Nome do Cônjuge</label>
            <input type="text" name="nome_conjuge" class="form-control"
              value="<?= htmlspecialchars($residente['nome_conjuge'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Nacionalidade</label>
            <input type="text" name="nacionalidade" class="form-control"
              value="<?= htmlspecialchars($residente['nacionalidade'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Cor/Origem Étnica</label>
            <input type="text" name="cor_etnica" class="form-control"
              value="<?= htmlspecialchars($residente['cor_etnica'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Naturalidade</label>
            <input type="text" name="naturalidade" class="form-control"
              value="<?= htmlspecialchars($residente['naturalidade'] ?? '') ?>">
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-header">Documentação</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">RG</label>
            <input type="text" name="rg" class="form-control" data-mask="00.000.000-0"
              value="<?= htmlspecialchars($residente['rg'] ?? '') ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Órgão Expedidor</label>
            <input type="text" name="orgao_expedidor" class="form-control"
              value="<?= htmlspecialchars($residente['orgao_expedidor'] ?? '') ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Data Expedição RG</label>
            <input type="date" name="data_expedicao_rg" class="form-control"
              value="<?= formatDateInput($residente['data_expedicao'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">PIS/PASEP/NIT</label>
            <input type="text" name="pispasep" class="form-control"
              value="<?= htmlspecialchars($residente['pispasep'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Título de Eleitor</label>
            <input type="text" name="titulo_eleitor" class="form-control"
              value="<?= htmlspecialchars($residente['titulo_eleitor'] ?? '') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Zona</label>
            <input type="text" name="zona" class="form-control"
              value="<?= htmlspecialchars($residente['zona'] ?? '') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Seção</label>
            <input type="text" name="secao" class="form-control"
              value="<?= htmlspecialchars($residente['secao'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Cidade Título Eleitor</label>
            <input type="text" name="cidade_eleitor" class="form-control"
              value="<?= htmlspecialchars($residente['cidade_eleitor'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Reservista</label>
            <input type="text" name="reservista" class="form-control"
              value="<?= htmlspecialchars($residente['reservista'] ?? '') ?>">
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-header">Residência Médica</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">CRM</label>
            <input type="text" name="crm" class="form-control" value="<?= htmlspecialchars($residente['crm'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Grupo Sanguíneo</label>
            <select name="sistema_abo" class="form-select">
              <option value="">Selecione</option>
              <option value="A" <?= ($residente['sistema_abo'] ?? '') === 'A' ? 'selected' : '' ?>>A</option>
              <option value="B" <?= ($residente['sistema_abo'] ?? '') === 'B' ? 'selected' : '' ?>>B</option>
              <option value="AB" <?= ($residente['sistema_abo'] ?? '') === 'AB' ? 'selected' : '' ?>>AB</option>
              <option value="O" <?= ($residente['sistema_abo'] ?? '') === 'O' ? 'selected' : '' ?>>O</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Fator RH</label>
            <select name="fator_rh" class="form-select">
              <option value="">Selecione</option>
              <option value="RH+" <?= ($residente['fator_rh'] ?? '') === 'RH+' ? 'selected' : '' ?>>RH+</option>
              <option value="RH-" <?= ($residente['fator_rh'] ?? '') === 'RH-' ? 'selected' : '' ?>>RH-</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-header">Informações Complementares</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Curso</label>
            <input type="text" name="curso" class="form-control"
              value="<?= htmlspecialchars($residente['curso'] ?? '') ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Faculdade</label>
            <input type="text" name="faculdade" class="form-control"
              value="<?= htmlspecialchars($residente['nome_faculdade'] ?? '') ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Sigla Faculdade</label>
            <input type="text" name="sigla_faculdade" class="form-control"
              value="<?= htmlspecialchars($residente['sigla_faculdade'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Data Início</label>
            <input type="date" name="data_inicio" class="form-control"
              value="<?= formatDateInput($residente['data_inicio'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Data Término</label>
            <input type="date" name="data_termino" class="form-control"
              value="<?= formatDateInput($residente['data_termino'] ?? '') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Peso (kg)</label>
            <input type="text" name="peso" class="form-control" data-mask="##0.0" data-mask-reverse="true"
              value="<?= htmlspecialchars($residente['peso'] ?? '') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Altura (m)</label>
            <input type="text" name="altura" class="form-control" data-mask="#0.00" data-mask-reverse="true"
              value="<?= htmlspecialchars($residente['altura'] ?? '') ?>">
          </div>
        </div>
      </div>
    </div>




    <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario_id) ?>">


    <div class="d-grid">
      <button type="submit" class="btn btn-success">Salvar Residente</button>
    </div>
  </form>

  <div class="card mb-3">
    <div class="card-header">Documentos Enviados</div>
    <div class="card-body">

      <!-- Upload de novo documento -->
      <form action="actions/upload_documento.php" method="POST" enctype="multipart/form-data" class="row g-3">
        <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario_id) ?>">
        <input type="hidden" name="origem" value="residente">

        <div class="col-md-6">
          <label class="form-label">Tipo de Documento</label>
          <select name="id_tipo_documento" class="form-select" required>
            <option value="">Selecione</option>
            <?php foreach ($tipos as $tipo): ?>
              <option value="<?= $tipo['id'] ?>"><?= htmlspecialchars($tipo['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Arquivo</label>
          <input type="file" name="arquivo" class="form-control" required>
        </div>

        <div class="col-12 d-grid">
          <button type="submit" class="btn btn-primary">Enviar Documento</button>
        </div>
      </form>

      <!-- Lista de arquivos já enviados -->
      <hr>
      <h6 class="mt-4">Arquivos Enviados</h6>
      <ul class="list-group">
        <?php foreach ($documentos as $doc): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span>
              <?= htmlspecialchars($doc['tipo']) ?> -
              <a href="<?= htmlspecialchars($doc['caminho_arquivo']) ?>" target="_blank">Ver documento</a>
            </span>
            <form method="POST" action="actions/excluir_documento.php" style="margin: 0;">
              <input type="hidden" name="documento_id" value="<?= $doc['id'] ?>">
              <input type="hidden" name="usuario_id" value="<?= $usuario_id ?>">
              <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
            </form>
          </li>
        <?php endforeach; ?>
      </ul>
      <?php
        $tiposFaltando = array_filter($tipos, function($tipo) use ($documentos_enviados_ids) {
            return !in_array($tipo['id'], $documentos_enviados_ids);
        });
        ?>

        <?php if (!empty($tiposFaltando)): ?>
          <hr>
          <h6 class="mt-4 text-warning">Documentos Obrigatórios Não Enviados</h6>
          <ul class="list-group">
            <?php foreach ($tiposFaltando as $faltando): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center list-group-item-warning">
                <?= htmlspecialchars($faltando['nome']) ?>
                <span class="badge bg-warning text-dark">Pendente</span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>

    </div>
  </div>

</div>

<!-- Javascript -->
<script src="assets/plugins/jquery/jquery.min.js"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/plugins/simplebar/simplebar.min.js"></script>
<script src="assets/js/sleek.js"></script>
<script src="assets/options/optionswitcher.js"></script>
<script src="assets/js/jquery.mask.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>

</script>

<?php require_once 'includes/footer.php'; ?>