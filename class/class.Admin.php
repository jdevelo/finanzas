<?php 

/**
* Admin Data
*/
class Admin extends Secure
{	
	public $adminData;

	function __construct()
	{
		$permalink= $_SERVER["REQUEST_URI"];
		if (!isset($_SESSION['id_usuario'])) {
	        echo '<script type="text/javascript">
	        window.location.assign("'.URL_BASE.'page/usuarios/iniciar-sesion/");
	        </script>';
	        exit();
    	}	
    	$this->personalInfo();
	}

	private function personalInfo()
	{	
		$params = ['i',$_SESSION['id_usuario']];
		$data = CRUD::all('usuarios','*','id_usuario = ?',$params);
		unset($data[0]['clave']);
		$this->adminData = $data;
	}

}

?>