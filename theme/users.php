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

<div class="row">
  <div class="col-lg-6 col-xl-4">
    <div class="card card-default p-4">
      <a href="javascript:0" class="media text-secondary" data-toggle="modal" data-target="#modal-contact">
        <img src="assets/img/user/u-xl-1.jpg" class="mr-3 img-fluid rounded" alt="Avatar Image">

        <div class="media-body">
          <h5 class="mt-0 mb-2 text-dark">Derick</h5>

          <ul class="list-unstyled">
            <li class="d-flex mb-1">
              <i class="mdi mdi-map mr-1"></i>
              <span>Nulla vel metus 15/178</span>
            </li>

            <li class="d-flex mb-1">
              <i class="mdi mdi-email mr-1"></i>
              <span>exmaple@email.com</span>
            </li>

            <li class="d-flex mb-1">
              <i class="mdi mdi-phone mr-1"></i>
              <span>(123) 888 777 632</span>
            </li>
          </ul>
        </div>
      </a>
    </div>
  </div>
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
                <input type="text" class="form-control" id="firstName" value="Albrecht">
              </div>
            </div>

            <div class="col-lg-6">
              <div class="form-group">
                <label for="lastName">Sobrenome</label>
                <input type="text" class="form-control" id="lastName" value="Straub">
              </div>
            </div>

            <div class="col-lg-6">
              <div class="form-group mb-4">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" value="albrecht.straub@gmail.com">
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
          <button type="button" class="btn btn-secondary btn-pill" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary btn-pill">Save Contact</button>
        </div>
      </form>
    </div>
  </div>
</div>









<?php include __DIR__.'/includes/footer.php'; ?>