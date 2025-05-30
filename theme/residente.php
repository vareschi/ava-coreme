<?php
require_once 'includes/header.php';
require_once 'includes/config.php';

include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/topbar.php';

$pdo = getPDO();

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
            <input type="text" name="nome_pai" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Nome da Mãe</label>
            <input type="text" name="nome_mae" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Estado Civil</label>
            <select name="estado_civil" class="form-select">
              <option value="">Selecione</option>
              <option>Solteiro</option>
              <option>Casado</option>
              <option>Divorciado</option>
              <option>Viúvo</option>
              <option>União Estável</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Nome do Cônjuge</label>
            <input type="text" name="nome_conjuge" class="form-control">
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
            <input type="text" name="rg" class="form-control" data-mask="00.000.000-0">
          </div>
          <div class="col-md-4">
            <label class="form-label">Órgão Expedidor</label>
            <input type="text" name="orgao_rg" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Data Expedição RG</label>
            <input type="date" name="data_expedicao_rg" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">PIS/PASEP/NIT</label>
            <input type="text" name="pispasep" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Título de Eleitor</label>
            <input type="text" name="titulo_eleitor" class="form-control">
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-header">Residência Médica</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Especialidade</label>
            <select name="especialidade_id" class="form-select">
              <option value="">Selecione</option>
              <?php foreach ($especialidades as $e): ?>
                <option value="<?= $e['id'] ?>"><?= $e['nome'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Preceptor</label>
            <select name="preceptor_id" class="form-select">
              <option value="">Selecione</option>
              <?php foreach ($preceptores as $p): ?>
                <option value="<?= $p['id'] ?>"><?= $p['nome'] ?></option>
              <?php endforeach; ?>
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
            <input type="text" name="curso" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Faculdade</label>
            <input type="text" name="faculdade" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Sigla Faculdade</label>
            <input type="text" name="sigla_faculdade" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Data Início</label>
            <input type="date" name="data_inicio" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Data Término</label>
            <input type="date" name="data_termino" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">Peso (kg)</label>
            <input type="text" name="peso" class="form-control" data-mask="##0.0" data-mask-reverse="true">
          </div>
          <div class="col-md-3">
            <label class="form-label">Altura (m)</label>
            <input type="text" name="altura" class="form-control" data-mask="#0.00" data-mask-reverse="true">
          </div>
        </div>
      </div>
    </div>

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
