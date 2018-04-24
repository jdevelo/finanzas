<?php


	/*=================================================*/
	// Autoload
	spl_autoload_register( function ($nombre_clase) {
		include DIRECTORIO_ROOT.'class/class.'.$nombre_clase.".php";
	});

	// Config Conexion DB
	Database::config_conection_db(nombre_servidor,usuario,clave,base_datos);

	

	/*=================================================*/

	/*=================================================*/
	// Autologin
		// session_start();
		$log_in = Cookie::readCookie('lg_us_rem');

		if ($log_in AND isset($log_in['close']) AND !$log_in['close']) {
			$where = 'correo = ? AND clave = ? AND tm_delete IS NULL';
			$params = ['ss',$log_in['correo'],Secure::montar_clave_verificacion($log_in['clave'])];
			$user_info = CRUD::all('usuarios','*',$where,$params);
			if (count($user_info) > 0) {
				$_SESSION['user'] = Sqlconsult::escape($user_info[0]['nombre']);
				$_SESSION['id_usuario'] = $user_info[0]['id_usuario'];
			  	$_SESSION['csrf_token'] = parent::crear_csrf_token();
			 	$_SESSION['csrf_token_time'] = time();
			}
		}	

		$session = false;
		if (isset($_SESSION['id_usuario'])) {
			$session = true;
		}

	/*=================================================*/

	/*=============================================*/
	// maintenance
		// Secure::maintenance();
		// echo Secure::getUserIP();
	/*=============================================*/

	/*=============================================*/
	// config datetime
		date_default_timezone_set('America/Bogota');
	/*=============================================*/
