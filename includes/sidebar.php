<?php
session_start();
$perfis = $_SESSION['perfis'] ?? [];

require_once 'funcoes.php';
?>

<aside class="left-sidebar bg-sidebar">
  <div id="sidebar" class="sidebar sidebar-with-footer">
    
    <!-- Marca da aplicação -->
    <div class="app-brand">
      <a href="index.php" title="COREME">
        <img 
          src="assets/img/logo-coremehub.png" 
          alt="Logo COREME" 
          class="brand-logo" 
          style="max-height:40px; width:auto;"
        />
      </a>
    </div>

    <div class="" data-simplebar style="height: 100%;">
      <ul class="nav sidebar-inner" id="sidebar-menu">
        
        <!-- DASHBOARD -->
        <li class="has-sub active expand">
          <a class="sidenav-item-link" href="index.php">
            <i class="mdi mdi-view-dashboard-outline"></i>
            <span class="nav-text">Dashboard</span>
          </a>
        </li>

        <!-- CADASTROS (somente Admin - perfil 1) -->
        <?php if (temPerfil(1)): ?>
        <li class="has-sub">
          <a class="sidenav-item-link" href="javascript:void(0)" data-toggle="collapse" data-target="#cadastros" aria-expanded="false" aria-controls="cadastros">
            <i class="mdi mdi-pencil-box-multiple"></i>
            <span class="nav-text">Cadastros</span> <b class="caret"></b>
          </a>
          <ul class="collapse" id="cadastros" data-parent="#sidebar-menu">
            <div class="sub-menu">
              <li><a class="sidenav-item-link" href="users.php"><span class="nav-text">Usuários</span></a></li>
              <li><a class="sidenav-item-link" href="especialidades.php"><span class="nav-text">Especialidades</span></a></li>
              <li><a class="sidenav-item-link" href="editais.php"><span class="nav-text">Editais</span></a></li>
              <li><a class="sidenav-item-link" href="turmas.php"><span class="nav-text">Turmas</span></a></li>
              <li><a class="sidenav-item-link" href="matriculas.php"><span class="nav-text">Matrículas</span></a></li>
              <li><a class="sidenav-item-link" href="campos_estagio.php"><span class="nav-text">Campos de Estágio</span></a></li>
            </div>
          </ul>
        </li>
        <?php endif; ?>

        <!-- AVALIAÇÕES (várias permissões) -->
        <?php if (temPerfil(1) || temPerfil(2) || temPerfil(4)): ?>
        <li class="has-sub">
          <a class="sidenav-item-link" href="javascript:void(0)" data-toggle="collapse" data-target="#avaliacoes" aria-expanded="false" aria-controls="avaliacoes">
            <i class="mdi mdi-pencil-box-multiple"></i>
            <span class="nav-text">Avaliações</span> <b class="caret"></b>
          </a>
          <ul class="collapse" id="avaliacoes" data-parent="#sidebar-menu">
            <div class="sub-menu">
              <?php if (temPerfil(1) || temPerfil(2)): ?>
                <li><a class="sidenav-item-link" href="avaliacoes.php"><span class="nav-text">Modelos</span></a></li>
                <li><a class="sidenav-item-link" href="gerar_avaliacoes.php"><span class="nav-text">Gerar Avaliações</span></a></li>
              <?php endif; ?>

              <?php if (temPerfil(4)): ?>
                <li><a class="sidenav-item-link" href="avaliar.php"><span class="nav-text">Avaliar</span></a></li>
              <?php endif; ?>
            </div>
          </ul>
        </li>
        <?php endif; ?>

      </ul>
    </div>

    <!-- Rodapé do menu lateral 
    <div class="sidebar-footer">
      <hr class="separator mb-0" />
      <div class="sidebar-footer-content">
        <h6 class="text-uppercase">Cpu Uses <span class="float-right">40%</span></h6>
        <div class="progress progress-xs">
          <div class="progress-bar active" style="width: 40%;" role="progressbar"></div>
        </div>
        <h6 class="text-uppercase">Memory Uses <span class="float-right">65%</span></h6>
        <div class="progress progress-xs">
          <div class="progress-bar progress-bar-warning" style="width: 65%;" role="progressbar"></div>
        </div>
      </div>
    </div>-->

  </div>
</aside>
