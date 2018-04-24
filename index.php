<?php 

	require 'config/config.php'; 
  
	$title = 'Log In';
	require DIRECTORIO_ROOT.'inc/header.php';

  if (isset($_GET['ac']) && $_GET['ac'] === 'l') {
      var_dump($_POST);
      $user = new Login;
  }

	if ($session) {
		header('cuenta/');
		exit();
	}

?>
  <body class="height-class">

    <form class="form-signin" method="post" action="?ac=l">
      	<div class="text-center mb-4">
       		<!-- <img class="mb-4" src="https://getbootstrap.com/assets/brand/bootstrap-solid.svg" alt="" width="72" height="72"> -->
        	<h1 class="h3 mb-3 font-weight-normal">Iniciar Sesión</h1>
     	 </div>

      	<div class="form-label-group">
        	<input type="email" id="inputEmail" class="form-control" placeholder="Email address" name="mail" required autofocus>
        	<label for="inputEmail">Correo</label>
     	</div>

      	<div class="form-label-group">
        	<input type="password" id="inputPassword" class="form-control" placeholder="Password" name="password" required>
        	<label for="inputPassword">Clave</label>
      	</div>
        <input type="hidden" name="empt_val">
      	<button class="btn btn-lg btn-primary btn-block" type="submit">Entrar</button>
      	<p class="mt-5 mb-3 text-muted text-center">&copy; 2018</p>
    </form>

  </body>


<?php require DIRECTORIO_ROOT.'inc/footer.php'; ?>
	