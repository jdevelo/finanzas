<?php 
	
	require '../config/config.php';


	switch ($_GET['read']) {
		case 'income':
			echo Read::income();
			break;
		
		case 'debts':
			echo Read::debts();
			break;
		
		case 'nextDebts':
			echo Read::nextDebts();
			break;

		case 'creditors':
			echo Read::creditors();
			break;

		case 'fixed_charges':
			echo Read::fixed_charges();
			break;

		case 'spendings':
			echo Read::spendings();
			break;
	}






