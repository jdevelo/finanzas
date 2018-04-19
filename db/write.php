<?php 
	
	require '../config/config.php';

	$option_read = $_GET['add'];
	unset($_GET['add']);

	$postdata = file_get_contents("php://input");
	$data = Secure::recibirAngularObject(json_decode($postdata));



	switch ($option_read) {
		case 'entry':
			echo Write::addEntry($data);
			break;
		case 'debt':
			echo Write::addDebt($data);
			break;
		case 'creditor':
			echo Write::addCreditor($data);
			break;		
		case 'fixed_charges':
			echo Write::addFixedCharges($data);
			break;		
		case 'debt_payment':
			echo Write::addDebtPayment($data);
			break;
		case 'saving':
			echo Write::newSaving($data);
			break;
		case 'spending':
			echo Write::addSpending($data);
			break;
	}
