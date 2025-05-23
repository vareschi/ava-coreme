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
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-add-contact"> Add Contact
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













<?php include __DIR__.'/includes/footer.php'; ?>