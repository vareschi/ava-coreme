<?php
require_once 'includes/header.php';
require_once 'includes/config.php';

include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/topbar.php';

$pdo = getPDO();

$usuario_id = $_GET['usuario_id'] ?? null;
$residente = [];

if ($usuario_id) {
    $stmt = $pdo->prepare("SELECT * FROM residentes WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);
    $residente = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Carrega especialidades e preceptores para os selects
$especialidades = $pdo->query("SELECT id, nome FROM especialidades ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$preceptores = $pdo->query("SELECT id, nome FROM usuarios WHERE id IN (SELECT usuario_id FROM usuario_perfis WHERE perfil_id = (SELECT id FROM perfis WHERE nome = 'Preceptor')) ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
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
            <input type="text" name="nome_pai" class="form-control" value="<?= htmlspecialchars($residente['nome_pai'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Nome da Mãe</label>
            <input type="text" name="nome_mae" class="form-control" value="<?= htmlspecialchars($residente['nome_mae'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Estado Civil</label>
            <select name="estado_civil" class="form-select">
              <option value="">Selecione</option>
              <option <?= ($residente['estado_civil'] ?? '') === 'Solteiro' ? 'selected' : '' ?>>Solteiro</option>
              <option <?= ($residente['estado_civil'] ?? '') === 'Casado' ? 'selected' : '' ?>>Casado</option>
              <option <?= ($residente['estado_civil'] ?? '') === 'Divorciado' ? 'selected' : '' ?>>Divorciado</option>
              <option <?= ($residente['estado_civil'] ?? '') === 'Viúvo' ? 'selected' : '' ?>>Viúvo</option>
              <option <?= ($residente['estado_civil'] ?? '') === 'União Estável' ? 'selected' : '' ?>>União Estável</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Nome do Cônjuge</label>
            <input type="text" name="nome_conjuge" class="form-control" value="<?= htmlspecialchars($residente['nome_conjuge'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Nacionalidade</label>
            <input type="text" name="nacionalidade" class="form-control" value="<?= htmlspecialchars($residente['nacionalidade'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Cor/Origem Étnica</label>
            <input type="text" name="cor_etnica" class="form-control" value="<?= htmlspecialchars($residente['cor_etnica'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Naturalidade</label>
            <input type="text" name="naturalidade" class="form-control" value="<?= htmlspecialchars($residente['naturalidade'] ?? '') ?>">
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
            <input type="text" name="rg" class="form-control" data-mask="00.000.000-0"  value="<?= htmlspecialchars($residente['rg'] ?? '') ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Órgão Expedidor</label>
            <input type="text" name="orgao_rg" class="form-control" value="<?= htmlspecialchars($residente['orgao_expedidor'] ?? '') ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Data Expedição RG</label>
            <input type="date" name="data_expedicao_rg" class="form-control" value="<?= htmlspecialchars($residente['data_expedicao'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">PIS/PASEP/NIT</label>
            <input type="text" name="pispasep" class="form-control" value="<?= htmlspecialchars($residente['pispasep'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Título de Eleitor</label>
            <input type="text" name="titulo_eleitor" class="form-control" value="<?= htmlspecialchars($residente['titulo_eleitor'] ?? '') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Zona</label>
            <input type="text" name="zona" class="form-control" value="<?= htmlspecialchars($residente['zona'] ?? '') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Seção</label>
            <input type="text" name="secao" class="form-control" value="<?= htmlspecialchars($residente['secao'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Cidade Título Eleitor</label>
            <input type="text" name="cidade_eleitor" class="form-control" value="<?= htmlspecialchars($residente['cidade_eleitor'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Reservista</label>
            <input type="text" name="reservista" class="form-control" value="<?= htmlspecialchars($residente['reservista'] ?? '') ?>">
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
            <select name="grupo_sanguineo" class="form-select">
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
            <input type="text" name="curso" class="form-control" value="<?= htmlspecialchars($residente['curso'] ?? '') ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Faculdade</label>
            <input type="text" name="faculdade" class="form-control" value="<?= htmlspecialchars($residente['nome_faculdade'] ?? '') ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Sigla Faculdade</label>
            <input type="text" name="sigla_faculdade" class="form-control" value="<?= htmlspecialchars($residente['sigla_faculdade'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Data Início</label>
            <input type="date" name="data_inicio" class="form-control" value="<?= htmlspecialchars($residente['data_inicio'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Data Término</label>
            <input type="date" name="data_termino" class="form-control" value="<?= htmlspecialchars($residente['data_termino'] ?? '') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Peso (kg)</label>
            <input type="text" name="peso" class="form-control" data-mask="##0.0" data-mask-reverse="true" value="<?= htmlspecialchars($residente['peso'] ?? '') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Altura (m)</label>
            <input type="text" name="altura" class="form-control" data-mask="#0.00" data-mask-reverse="true" value="<?= htmlspecialchars($residente['altura'] ?? '') ?>">
          </div>
        </div>
      </div>
    </div>

    <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario_id) ?>">


    <div class="d-grid">
      <button type="submit" class="btn btn-success">Salvar Residente</button>
    </div>
  </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
  $(document).ready(function() {
    $('[data-mask]').each(function() {
      $(this).mask($(this).attr('data-mask'), {
        reverse: $(this).data('mask-reverse') === true
      });
    });
  });
</script>

<?php require_once 'includes/footer.php'; ?>
