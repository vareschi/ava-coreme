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

$imagem = $usuario['imagem_perfil'] 
    ? 'assets/img/user/' . $usuario['imagem_perfil'] 
    : 'assets/img/user/u-xl-1.jpg';

?>

<div class="row">
  <?php foreach ($usuarios as $usuario): ?>
    <div class="col-lg-6 col-xl-4">
        <div class="card card-default p-4">
            <img src="<?= $imagem ?>" class="mr-3 img-fluid rounded" style="width:64px; height:64px;" alt="Avatar">


            <div class="media-body">
                <h5 class="mt-0 mb-2 text-dark"><?= htmlspecialchars($usuario['nome']) ?></h5>

                <ul class="list-unstyled">
                <li class="d-flex mb-1">
                <i class="mdi mr-1"></i>
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
      <form method="POST" action="actions/salvar_usuario.php" enctype="multipart/form-data">
        <div class="modal-header px-4">
          <h5 class="modal-title">Criar Novo Usuário</h5>
        </div>

        <div class="modal-body px-4">
          <div class="form-group row mb-3">
            <label class="col-sm-3 col-form-label">Imagem de Perfil</label>
            <div class="col-sm-9">
              <div class="custom-file">
                <input type="file" name="imagem_perfil" class="custom-file-input" id="imagem_perfil">
                <label class="custom-file-label" for="imagem_perfil">Escolher imagem...</label>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-6">
              <label>Nome completo</label>
              <input type="text" name="nome" class="form-control" required>
            </div>

            <div class="col-lg-6">
              <label>E-mail</label>
              <input type="email" name="email" class="form-control" required>
            </div>

            <div class="col-lg-6 mt-3">
              <label>Senha</label>
              <input type="password" name="senha" class="form-control" required>
            </div>

            <div class="col-lg-12 mt-3">
                <label>Perfis</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="perfis[]" value="admin" id="perfilAdmin">
                    <label class="form-check-label" for="perfilAdmin">Admin</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="perfis[]" value="secretaria" id="perfilSecretaria">
                    <label class="form-check-label" for="perfilSecretaria">Secretária</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="perfis[]" value="residente" id="perfilResidente">
                    <label class="form-check-label" for="perfilResidente">Residente</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="perfis[]" value="preceptor" id="perfilPreceptor">
                    <label class="form-check-label" for="perfilPreceptor">Preceptor</label>
                </div>
            </div>


            <div class="col-lg-6 mt-3">
              <label>Data de nascimento</label>
              <input type="date" name="data_nascimento" class="form-control">
            </div>

            <div class="col-lg-6 mt-3">
              <label>Telefone</label>
              <input type="text" name="telefone" class="form-control">
            </div>

            <div class="col-lg-6 mt-3">
              <label>CPF</label>
              <input type="text" name="cpf" class="form-control">
            </div>

            <div class="col-lg-6 mt-3">
              <label>Sexo</label>
              <select name="sexo" class="form-control">
                <option value="">Selecione</option>
                <option value="M">Masculino</option>
                <option value="F">Feminino</option>
                <option value="Outro">Outro</option>
              </select>
            </div>

            <div class="col-lg-6 mt-3">
              <label>CEP</label>
              <input type="text" name="cep" class="form-control">
            </div>

            <div class="col-lg-6 mt-3">
              <label>Cidade</label>
              <input type="text" name="cidade" class="form-control">
            </div>

            <div class="col-lg-6 mt-3">
              <label>Estado (UF)</label>
              <input type="text" name="estado" maxlength="2" class="form-control">
            </div>

            <div class="col-lg-12 mt-3">
              <label>Endereço</label>
              <textarea name="endereco" class="form-control" rows="2"></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer px-4">
          <button type="button" class="btn btn-secondary btn-pill" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary btn-pill">Salvar</button>
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

