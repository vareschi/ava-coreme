<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Sleek Dashboard - Free Bootstrap 4 Admin Dashboard Template and UI Kit. It is very powerful bootstrap admin dashboard, which allows you to build products like admin panels, content management systems and CRMs etc.">

    <title>Login - AVA - Coreme</title>

    <!-- GOOGLE FONTS -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500|Poppins:400,500,600,700|Roboto:400,500" rel="stylesheet" />
    <link href="https://cdn.materialdesignicons.com/4.4.95/css/materialdesignicons.min.css" rel="stylesheet" />

    <!-- SLEEK CSS -->
    <link id="sleek-css" rel="stylesheet" href="/assets/css/sleek.css" />

    <!-- FAVICON -->
    <link href="/assets/img/favicon.png" rel="shortcut icon" />

    <!--
      HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries
    -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="/assets/plugins/nprogress/nprogress.js"></script>
  </head>

  <body class="" id="body">
    <div class="container d-flex align-items-center justify-content-center vh-100">
      <div class="row justify-content-center">
        <div class="col-lg-6 col-md-10">
          <div class="card">
            <div class="card-header bg-primary">
              <div class="app-brand">
                <a href="#">
                  <img src="/assets/img/logo-coreme.png" alt="Logo" style="max-height:40px;" />
                  <span class="brand-name text-white">AVA Coreme</span>
                </a>
              </div>
            </div>

            <div class="card-body p-5">
              <h4 class="text-dark mb-5">Login</h4>

              <?php if (isset($_GET['erro'])): ?>
                <div class="alert alert-danger">E-mail ou senha inválidos.</div>
              <?php endif; ?>
              
              <form method="POST" action="auth.php">
                <div class="form-group mb-4">
                  <input type="email" name="email" class="form-control input-lg" placeholder="E-mail" required>
                </div>
                <div class="form-group mb-4">
                  <input type="password" name="senha" class="form-control input-lg" placeholder="Senha" required>
                </div>
                <div class="form-check mb-4">
                  <input type="checkbox" class="form-check-input" id="rememberMe">
                  <label class="form-check-label" for="rememberMe">Lembrar-me</label>
                </div>
                <button type="submit" class="btn btn-primary btn-lg btn-block">Entrar</button>
              </form>
              <p class="mt-4">Esqueceu sua senha? <a class="text-blue" href="#">Recuperar</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>


    <!-- <script type="module">
      import 'https://cdn.jsdelivr.net/npm/@pwabuilder/pwaupdate';

      const el = document.createElement('pwa-update');
      document.body.appendChild(el);
    </script> -->

    <!-- Javascript -->
    <script src="/assets/plugins/jquery/jquery.min.js"></script>
    <script src="/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/sleek.js"></script>
  <link href="/assets/options/optionswitch.css" rel="stylesheet">
<script src="/assets/options/optionswitcher.js"></script>
</body>
</html>