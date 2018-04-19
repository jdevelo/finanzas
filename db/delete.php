<?php 
	
	require '../config/config.php';

	$option_delete = $_GET['delete'];
	unset($_GET['delete']);

	$postdata = file_get_contents("php://input");
	$data = Secure::recibirAngularObject(json_decode($postdata));



	switch ($option_delete) {
		case 'entry':
			$table = 'ingresos';
			$id = $data['id_ingresos']
			
			break;
	}


	echo Write::delete($table,$id);
