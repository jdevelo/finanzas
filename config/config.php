<?php 

	/*******************-Conexión a Base de Datos-********************/

	define("nombre_servidor","localhost");
	define("usuario","root");
	define("clave","");
	define("base_datos","finanzas");
	/*******************-Conexión a Base de Datos-********************/



	define("URL_BASE", "/finanzas/");
	define("DIRECTORIO_ROOT",$_SERVER["DOCUMENT_ROOT"] . "/finanzas/");
	define('URL_PAGE', '/finanzas/');
	define('ASSETS', URL_BASE.'assets/');
	


	//Admin	
	define('ADMIN',URL_BASE."admin/");	


	// Tokens
	define("SALTREG", "BB67*6765v/8c545");
	define('SALTPSW', '$%imund9489//8=mo');

	require DIRECTORIO_ROOT.'config/autoload.php';
	require DIRECTORIO_ROOT.'functions.php';
	
	
	