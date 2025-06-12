<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Sleek Dashboard - Free Bootstrap 4 Admin Dashboard Template and UI Kit. It is very powerful bootstrap admin dashboard, which allows you to build products like admin panels, content management systems and CRMs etc.">

    <title>AVA - Coreme</title>

    <!-- GOOGLE FONTS -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500|Poppins:400,500,600,700|Roboto:400,500" rel="stylesheet" />
    <link href="https://cdn.materialdesignicons.com/4.4.95/css/materialdesignicons.min.css" rel="stylesheet" />

    <!-- SLEEK CSS -->
    <link id="sleek-css" rel="stylesheet" href="assets/css/sleek.css" />

    <!-- FAVICON -->
    <link href="assets/img/favicon.png" rel="shortcut icon" />

    <!--
      HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries
    -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="assets/plugins/nprogress/nprogress.js"></script>
  </head>

  <body class="" id="body">
    <div class="container d-flex align-items-center justify-content-center vh-100">
      <div class="row justify-content-center">
        <div class="col-lg-6 col-md-10">
          <div class="card">
            <div class="card-header bg-primary">
              <div class="app-brand">
                <a href="#">
                  <img src="assets/img/logo-coremehub.png" alt="Logo" style="max-height:40px;" />
                </a>
              </div>
            </div>

            <div class="card-body p-5">
              <h4 class="text-dark mb-5">Registrar-se</h4>

              <?php if (isset($_GET['erro'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['erro']) ?></div>
              <?php elseif (isset($_GET['ok'])): ?>
                <div class="alert alert-success">Usuário registrado com sucesso! <a href="sign-in.php">Login</a></div>
              <?php endif; ?>

              <form action="register.php" method="POST">
                <div class="row">
                  <div class="form-group col-md-12 mb-4">
                    <input type="text" name="nome" class="form-control input-lg" id="name" aria-describedby="nameHelp" placeholder="Name">
                  </div>

                  <div class="form-group col-md-12 mb-4">
                    <input type="email" name="email" class="form-control input-lg" id="email" aria-describedby="emailHelp" placeholder="Email">
                  </div>

                  <div class="form-group col-md-12 ">
                    <input type="password" name="senha" class="form-control input-lg" id="password" placeholder="Password">
                  </div>

                  <div class="form-group col-md-12 ">
                    <input type="password" name="confirmar" class="form-control input-lg" id="cpassword" placeholder="Confirm Password">
                  </div>

                  <div class="col-md-12">
                    <div class="d-inline-block mr-3">
                      <label class="control control-checkbox">
                        <input type="checkbox" id="aceite" required/>
                        <div class="control-indicator"></div>
                        Eu aceito os termos e condições.
                      </label>
                    </div>

                    <button type="submit" class="btn btn-lg btn-primary btn-block mb-4">Cadastrar</button>

                    <p>Já tem uma conta?
                      <a class="text-blue" href="sign-in.php">Entrar</a>
                    </p>
                  </div>
                </div>
              </form>
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
    <script src="assets/plugins/jquery/jquery.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/sleek.js"></script>
  <link href="assets/options/optionswitch.css" rel="stylesheet">
<script src="assets/options/optionswitcher.js"></script>
</body>
</html>
