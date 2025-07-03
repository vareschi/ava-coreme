<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once 'includes/config.php';
require_once 'includes/funcoes.php';

session_start();

if (!isset($_SESSION['usuario_id'])) {
  header("Location: sign-in.php");
  exit;
}

$pdo = getPDO();

// Busca dados do usuário logado
$usuario_id = $_SESSION['usuario_id'];

$residenteStmt = $pdo->prepare("SELECT * FROM residentes WHERE usuario_id = ?");
$residenteStmt->execute([$usuario_id]);
$residente = $residenteStmt->fetch(PDO::FETCH_ASSOC) ?: [];

$stmt = $pdo->prepare("
    SELECT u.nome, u.email, d.telefone, d.data_nascimento, d.imagem_perfil 
    FROM usuarios u 
    LEFT JOIN usuarios_dados d ON d.usuario_id = u.id 
    WHERE u.id = ?
");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch();

// Caminho da imagem
$imagem = !empty($usuario['imagem_perfil'])
  ? "assets/img/user/" . $usuario['imagem_perfil']
  : "assets/img/user/u-default.jpg";

// Buscar tipos de documento
$tipos = $pdo->query("SELECT * FROM documentos_tipo WHERE perfil_id = 0")->fetchAll(PDO::FETCH_ASSOC);

// Buscar documentos já enviados
$documentos = $pdo->prepare("
          SELECT d.id, d.tipo_documento_id, dt.nome AS tipo, d.caminho_arquivo, d.data_inclusao
          FROM documentos d
          JOIN documentos_tipo dt ON dt.id = d.tipo_documento_id
          WHERE d.residente_id = ? AND d.data_exclusao IS NULL
      ");
$documentos->execute([$usuario_id]);
$documentos = $documentos->fetchAll(PDO::FETCH_ASSOC);
$documentos_enviados_ids = array_column($documentos, 'tipo_documento_id');

include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/topbar.php';

function formatDateInput($data)
{
  if (!$data || $data == '0000-00-00')
    return '';
  return date('Y-m-d', strtotime($data)); // formato para campo input[type="date"]
}

?>

<!-- ====================================
          ——— CONTENT WRAPPER
          ===================================== -->
<div class="content-wrapper">
  <div class="content">





    <div class="bg-white border rounded">
      <div class="row no-gutters">
        <div class="col-lg-4 col-xl-3">
          <div class="profile-content-left profile-left-spacing pt-5 pb-3 px-3 px-xl-5">
            <div class="card text-center widget-profile px-0 border-0">
              <div class="card-img mx-auto rounded-circle">
                <img src="<?= $imagem ?>" alt="user image" style="width: 150px; height: 150px; object-fit: cover;">
              </div>

              <div class="card-body">
                <h5 class="py-2 text-dark"><?= htmlspecialchars($usuario['nome']) ?></h5>

              </div>
            </div>

            <hr class="w-100">

            <div class="contact-info pt-4">
              <h5 class="text-dark mb-1">Informação de Contato</h5>
              <p class="text-dark font-weight-medium pt-4 mb-2">Email</p>
              <p><?= htmlspecialchars($usuario['email'] ?? '') ?></p>
              <p class="text-dark font-weight-medium pt-4 mb-2">Telefone</p>
              <p><?= htmlspecialchars($usuario['telefone'] ?? '') ?></p>
              <p class="text-dark font-weight-medium pt-4 mb-2">Nascimento</p>
              <p><?= htmlspecialchars($usuario['data_nascimento'] ?? '') ?></p>
            </div>
          </div>
        </div>

        <div class="col-lg-8 col-xl-9">
          <div class="profile-content-right profile-right-spacing py-5">
            <ul class="nav nav-tabs px-3 px-xl-5 nav-style-border" id="myTab" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="settings-tab" data-toggle="tab" href="#settings" role="tab"
                  aria-controls="settings" aria-selected="false">Usuário</a>
              </li>

              <?php if (temPerfil(3)): ?>
                <li class="nav-item">
                  <a class="nav-link" id="complemento-tab" data-toggle="tab" href="#complemento" role="tab"
                    aria-controls="complemento" aria-selected="false">Dados</a>
                </li>
              <?php endif; ?>


              <li class="nav-item">
                <a class="nav-link" id="arquivos-tab" data-toggle="tab" href="#arquivos" role="tab"
                  aria-controls="arquivos" aria-selected="false">Arquivos</a>
              </li>
            </ul>

            <div class="tab-content px-3 px-xl-5" id="myTabContent">
              <div class="tab-pane fade show active" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                <div class="tab-pane-content mt-5">
                  <form action="actions/atualizar_perfil.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="usuario_id" value="<?= $usuario_id ?>">

                    <div class="form-group row mb-6">
                      <label for="coverImage" class="col-sm-4 col-lg-2 col-form-label">Imagem Perfil</label>
                      <div class="col-sm-8 col-lg-10">
                        <div class="custom-file mb-1">
                          <input type="file" class="custom-file-input" name="imagem_perfil" id="coverImage">
                          <label class="custom-file-label" for="coverImage">Carregar Imagem...</label>
                          <div class="invalid-feedback">Imagem invalida</div>
                        </div>
                      </div>
                    </div>

                    <div class="form-group mb-4">
                      <label for="firstName">nome Completo</label>
                      <input type="text" class="form-control" name="nome"
                        value="<?= htmlspecialchars($usuario['nome'] ?? '') ?>">
                    </div>
                    <div class="form-group mb-4">
                      <label for="email">Email</label>
                      <input type="email" class="form-control" name="email"
                        value="<?= htmlspecialchars($usuario['email'] ?? '') ?>">
                    </div>

                    <div class="row mb-2">
                      <div class="col-lg-6">
                        <div class="form-group mb-4">
                          <label for="oldPassword">Senha Antiga</label>
                          <input type="password" class="form-control" name="senha_atual">
                        </div>

                        <div class="form-group mb-4">
                          <label for="newPassword">Nova Senha</label>
                          <input type="password" class="form-control" name="nova_senha">
                        </div>

                        <div class="form-group mb-4">
                          <label for="conPassword">Confirme a Nova Senha</label>
                          <input type="password" class="form-control" name="confirmar_senha">
                        </div>
                      </div>
                    </div>





                    <div class="d-flex justify-content-end mt-5">
                      <button type="submit" class="btn btn-primary mb-2 btn-pill">Atualizar</button>
                    </div>
                  </form>
                </div>
              </div>

              <!-- Arquivos -->
              <div class="tab-pane fade show active" id="arquivos" role="tabpanel" aria-labelledby="arquivos-tab">
                <div class="tab-pane-content mt-5">
                  <div class="card mb-3">
                    <div class="card-header">Documentos Enviados</div>
                    <div class="card-body">

                      <!-- Upload de novo documento -->
                      <form action="actions/upload_documento.php" method="POST" enctype="multipart/form-data"
                        class="row g-3">
                        <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario_id) ?>">
                        <input type="hidden" name="origem" value="perfil"> <!-- <-- campo para identificar a origem -->

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
                      $tiposFaltando = array_filter($tipos, function ($tipo) use ($documentos_enviados_ids) {
                        return !in_array($tipo['id'], $documentos_enviados_ids);
                      });
                      ?>

                      <?php if (!empty($tiposFaltando)): ?>
                        <hr>
                        <h6 class="mt-4 text-warning">Documentos Obrigatórios Não Enviados</h6>
                        <ul class="list-group">
                          <?php foreach ($tiposFaltando as $faltando): ?>
                            <li
                              class="list-group-item d-flex justify-content-between align-items-center list-group-item-warning">
                              <?= htmlspecialchars($faltando['nome']) ?>
                              <span class="badge bg-warning text-dark">Pendente</span>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Dados Complementares -->
            <?php if (temPerfil(3)): ?>
              <div class="tab-pane fade show active" id="complemento" role="tabpanel" aria-labelledby="complemento-tab">
                <div class="tab-pane-content mt-5">
                  <div class="card mb-3">
                    <div class="card-header">Dados Complementares</div>
                      <div class="card-body">
                        <form action="actions/salvar_residente.php" method="POST" enctype="multipart/form-data">
                          <input type="hidden" name="origem" value="perfil">
                          <div class="card mb-3">
                            <div class="card-header">Status e Identificação</div>
                            <div class="card-body">

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
                            <button type="submit" class="btn btn-success">Salvar</button>
                          </div>
                        </form>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>





    <!-- Javascript -->
    <script src="assets/plugins/jquery/jquery.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/plugins/simplebar/simplebar.min.js"></script>
    <script src='assets/plugins/daterangepicker/moment.min.js'></script>
    <script src='assets/plugins/daterangepicker/daterangepicker.js'></script>
    <script src='assets/js/date-range.js'></script>





















    <script src="assets/js/sleek.js"></script>
    <link href="assets/options/optionswitch.css" rel="stylesheet">
    <script src="assets/options/optionswitcher.js"></script>


    <?php include 'includes/footer.php'; ?>