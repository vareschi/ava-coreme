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

?>

<div class="breadcrumb-wrapper breadcrumb-contacts">
  <div>
    <h1>Usuários</h1>

    
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb p-0">
            <li class="breadcrumb-item">
              <a href="index.html">
                <span class="mdi mdi-home"></span>                
              </a>
            </li>
            <li class="breadcrumb-item">
              configurações
            </li>
            <li class="breadcrumb-item" aria-current="page">usuários</li>
          </ol>
        </nav>

  </div>

  <div>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-add-contact"> Novo Usuário
    </button>
  </div>
</div>

<form method="GET" class="form-inline mb-4">
  <input type="text" name="nome" class="form-control mr-2" placeholder="Nome" value="<?= $_GET['nome'] ?? '' ?>">
  <input type="email" name="email" class="form-control mr-2" placeholder="E-mail" value="<?= $_GET['email'] ?? '' ?>">
  <select name="perfil" class="form-control mr-2">
    <option value="">Todos os Perfis</option>
    <option value="admin">Admin</option>
    <option value="secretaria">Secretária</option>
    <option value="residente">Residente</option>
    <option value="preceptor">Preceptor</option>
  </select>
  <button type="submit" class="btn btn-outline-primary">Filtrar</button>
</form>

<?php
$pdo = getPDO();

$stmt = $pdo->query("
  SELECT u.*, d.telefone, d.cidade, d.estado, d.imagem_perfil
  FROM usuarios u
  LEFT JOIN usuarios_dados d ON d.usuario_id = u.id
  ORDER BY u.nome ASC
");
$usuarios = $stmt->fetchAll();

// Busca os perfis de todos os usuários
$perfisStmt = $pdo->query("
  SELECT up.usuario_id, p.nome 
  FROM usuario_perfis up 
  JOIN perfis p ON p.id = up.perfil_id
");
$mapaPerfis = [];
foreach ($perfisStmt as $linha) {
  $mapaPerfis[$linha['usuario_id']][] = ucfirst($linha['nome']);
}

?>

<div class="row">
  <?php foreach ($usuarios as $usuario): ?>
    <div class="col-lg-6 col-xl-4">
        <div class="card card-default p-4">
            <img src="<?= $usuario['imagem_perfil'] ?: 'assets/img/user/u-xl-1.jpg' ?>" class="mr-3 img-fluid rounded" style="width:64px; height:64px;" alt="Avatar">

            <div class="media-body">
                <h5 class="mt-0 mb-2 text-dark"><?= htmlspecialchars($usuario['nome']) ?></h5>

                <ul class="list-unstyled">
                <li class="d-flex mb-1">
                <i class="mdi mdi-account mr-1"></i>
                <span>
                    <?php foreach ($mapaPerfis[$usuario['id']] ?? [] as $perfil): ?>
                    <a href="<?= strtolower($perfil) ?>.php?usuario_id=<?= $usuario['id'] ?>" class="badge badge-info mr-1">
                        <?= $perfil ?>
                    </a>
                    <?php endforeach; ?>
                    <?php if (empty($mapaPerfis[$usuario['id']])): ?>
                    <span class="badge badge-secondary">Sem perfil</span>
                    <?php endif; ?>
                </span>
                </li>


                <li class="d-flex mb-1">
                    <i class="mdi mdi-email mr-1"></i>
                    <span><?= htmlspecialchars($usuario['email']) ?></span>
                </li>

                <li class="d-flex mb-1">
                    <i class="mdi mdi-phone mr-1"></i>
                    <span><?= $usuario['telefone'] ?: '(não informado)' ?></span>
                </li>

                <li class="d-flex mb-1">
                    <i class="mdi mdi-map-marker mr-1"></i>
                    <span><?= $usuario['cidade'] && $usuario['estado'] ? "{$usuario['cidade']}/{$usuario['estado']}" : '(local não informado)' ?></span>
                </li>
                </ul>
            </div>
        </div>
    </div>

  <?php endforeach; ?>
</div>




<!-- Add Usuários Botão  -->
<div class="modal fade" id="modal-add-contact" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <form >
        <div class="modal-header px-4">
          <h5 class="modal-title" id="exampleModalCenterTitle">Criar Novo Usuário</h5>
        </div>

        <div class="modal-body px-4">
          <div class="form-group row mb-6">
            <label for="coverImage" class="col-sm-4 col-lg-2 col-form-label">Usuário Imagem</label>

            <div class="col-sm-8 col-lg-10">
              <div class="custom-file mb-1">
                <input type="file" class="custom-file-input" id="coverImage" required>
                <label class="custom-file-label" for="coverImage">Carregar Arquivo...</label>
                <div class="invalid-feedback">Example invalid custom file feedback</div>
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-lg-6">
              <div class="form-group">
                <label for="firstName">Primeiro nome</label>
                <input type="text" class="form-control" id="firstName" value="Emanoel">
              </div>
            </div>

            <div class="col-lg-6">
              <div class="form-group">
                <label for="lastName">Sobrenome</label>
                <input type="text" class="form-control" id="lastName" value="Gomes">
              </div>
            </div>

            <div class="col-lg-6">
              <div class="form-group mb-4">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" value="emanoel@gmail.com">
              </div>
            </div>

            <div class="col-lg-6">
              <div class="form-group mb-4">
                <label for="Birthday">Nascimento</label>
                <input type="text" class="form-control" id="Birthday" value="01-10-1993">
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer px-4">
          <button type="button" class="btn btn-secondary btn-pill" data-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary btn-pill">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>



 <!-- Javascript -->
 <script src="assets/plugins/jquery/jquery.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/plugins/simplebar/simplebar.min.js"></script>
 
    
    

    
    
    

    <script src="assets/js/sleek.js"></script>
  <link href="assets/options/optionswitch.css" rel="stylesheet">
<script src="assets/options/optionswitcher.js"></script>





<?php include __DIR__.'/includes/footer.php'; ?>

