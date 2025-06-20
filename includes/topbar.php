<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';

$pdo = getPDO();

$usuario_id = $_SESSION['usuario_id'] ?? null;
$usuario = null;

if ($usuario_id) {
    $stmt = $pdo->prepare("SELECT nome, email, imagem_perfil FROM usuarios 
        LEFT JOIN usuarios_dados ON usuarios.id = usuarios_dados.usuario_id 
        WHERE usuarios.id = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

        <div class="page-wrapper">
          
          <!-- Header -->
          <header class="main-header " id="header">
            <nav class="navbar navbar-static-top navbar-expand-lg">
              <!-- Sidebar toggle button -->
              <button id="sidebar-toggler" class="sidebar-toggle">
                <span class="sr-only">Toggle navigation</span>
              </button>
              <!-- search form -->
              <div class="search-form d-none d-lg-inline-block">
                <!--<div class="input-group">
                  <button type="button" name="search" id="search-btn" class="btn btn-flat">
                    <i class="mdi mdi-magnify"></i>
                  </button>
                  <input type="text" name="query" id="search-input" class="form-control" placeholder="'button', 'chart' etc."
                    autofocus autocomplete="off" />
                </div>
                <div id="search-results-container">
                  <ul id="search-results"></ul>
                </div>-->
              </div> 

              <div class="navbar-right ">
                <ul class="nav navbar-nav">
                  
                  <li class="right-sidebar-in right-sidebar-2-menu">
                    <i class="mdi mdi-settings mdi-spin"></i>
                  </li>
                  <!-- User Account -->
                  <li class="dropdown user-menu">
                    <button href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                      <img src="assets/img/user/<?= $usuario['imagem_perfil'] ?? 'user.png' ?>" class="user-image" alt="User Image" />
                      <span class="d-none d-lg-inline-block"><?= htmlspecialchars($usuario['nome'] ?? 'Usuário') ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                      <!-- User image 
                      <li class="dropdown-header">
                        <img src="assets/img/user/<?= $usuario['imagem_perfil'] ?? 'user.png' ?>" class="img-circle" alt="User Image" />
                        <div class="d-inline-block">
                          <?= htmlspecialchars($usuario['nome'] ?? 'Usuário') ?>
                          <small class="pt-1"><?= htmlspecialchars($usuario['email'] ?? '') ?></small>
                        </div>
                      </li>-->


                      <li>
                        <a href="perfil.php">
                          <i class="mdi mdi-account"></i> Perfil
                        </a>
                      </li>
                      <li>
                        <a href="#">
                          <i class="mdi mdi-email"></i> Mensagens
                        </a>
                      </li>
                      <!-- <li class="right-sidebar-in">
                        <a href="javascript:0"> <i class="mdi mdi-settings"></i> Configuração </a>
                      </li> -->

                      <li class="dropdown-footer">
                        <a href="logout.php"> <i class="mdi mdi-logout"></i> Log Out </a>
                      </li>
                    </ul>
                  </li>
                </ul>
              </div>
            </nav>
          </header>

          
          <!-- ====================================
          ——— CONTENT WRAPPER
          ===================================== -->
          <div class="content-wrapper">
            <div class="content">

