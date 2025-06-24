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
          SELECT d.id, dt.nome AS tipo, d.caminho_arquivo, d.data_inclusao 
          FROM documentos d
          JOIN documentos_tipo dt ON dt.id = d.tipo_documento_id
          WHERE d.residente_id = ? AND d.data_exclusao IS NULL
      ");
      $documentos->execute([$usuario_id]);
      $documentos = $documentos->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/topbar.php';

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
            <a class="nav-link active" id="settings-tab" data-toggle="tab" href="#settings" role="tab" aria-controls="settings" aria-selected="false">Usuário</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" id="arquivos-tab" data-toggle="tab" href="#arquivos" role="tab" aria-controls="arquivos" aria-selected="false">Arquivos</a>
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
                      <input type="text" class="form-control" name="nome" value="<?= htmlspecialchars($usuario['nome'] ?? '') ?>">
                    </div>
                    <div class="form-group mb-4">
                      <label for="email">Email</label>
                      <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>">
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
                    <form action="actions/upload_documento.php" method="POST" enctype="multipart/form-data" class="row g-3">
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
                  </div>
              </div>
            </div>
          </div>
        </div>
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

