<?php 



/**
* 
*/
class Write
{
	// Almacenar en DB ingreso de dinero
	static public function addEntry($data)
	{
		CRUD::insert('ingresos',$data);
		$args = array(
			'where' => 'tm_delete IS NULL',
			'order' => 'fecha DESC',
			'limit' => 10 
		);
		$income = CRUD::all('ingresos',$args);
		self::balanceSheet( [ 'ingresos' => $data['valor'] ] );
		return json_encode($income[0]);
	}

	static public function addSpending($data)
	{	
		$date=date_create($data['fecha']);
		$data['fecha'] = date_format($date,"Y-m-d");
		CRUD::insert('gastos',$data);
		self::balanceSheet( [ 'egresos' => $data['valor'] ] );
		return Read::spendings(1);
	}

	// Almacenar nueva deuda en DB
	static public function addDebt($data)
	{	
		$date=date_create($data['primer_pago']);
		$data['primer_pago'] = date_format($date,"Y-m-d");
		CRUD::insert('deudas',$data);

		$debts = json_decode(Read::debts());

		/*Ajustar la tabla balance de deudas*/
		$set = [
			'id_deuda' => $debts[0]->id_deuda,
			'saldo' => $data['valor'],
		];
		$unique = [
			'conditional' => 'id_deuda = ?',
			'where_values' => [$debts[0]->id_deuda]
		];
		CRUD::insert('deudas_balance',$set,$unique);

		return json_encode($debts[0]);
	}

	// Almacenar nuevo acreedor en DB
	static public function addCreditor($data)
	{
		$newCreditor = CRUD::insert('acreedores',$data);
		$args = array(
			'where' => 'id_acreedor = ?',
			'where_values' => [$newCreditor[0]->insert_id] 
		);
		$creditor = CRUD::all('acreedores',$args);
		return json_encode($creditor[0]);	
	}

	// Agregar cargos fijos
	static public function addFixedCharges($data)
	{			
		$month = date('m');		
		if ($month > 1) {
			$last_month = $month - 1;
			$last_month_p = date('Y-').$last_month;
		}elseif ($month == 1) {
			$last_year = date('Y') - 1;
			$last_month_p = $last_year.'-12';
		}
		$data['ultimo_mes_pagado'] = $last_month_p;
		$newFixedCharge = CRUD::insert('cargos_fijos',$data);
		$args = array(
			'where' => 'id_cargo_fijo = ?',
			'where_values' => [$newFixedCharge[0]->insert_id] 
		);
		$fixedCharges = CRUD::all('cargos_fijos',$args);

		return json_encode($fixedCharges[0]);		
	}

	// Realizar pago de deuda o responsabilidad fija
	static public function addDebtPayment($data=[])
	{
		if ( isset($data['id_deuda']) ) {
			self::loanPayment($data);	
			$wh = 'id_deuda = ?';
			$whv = [$data['id_deuda']];
			CRUD::update('deudas_balance',['ultimo_mes_pagado' => date('Y-m')],$wh,$whv);		
		}elseif ( isset($data['id_cargo_fijo']) ) {
			$data['abono_capital'] = $data['pago_total'];
			CRUD::insert('pagos',$data);
			$wh = 'id_cargo_fijo = ?';
			$whv = [$data['id_cargo_fijo']];
			CRUD::update('cargos_fijos',['ultimo_mes_pagado' => date('Y-m')],$wh,$whv);
			self::balanceSheet(['egresos' => $data['pago_total']]);
		}else{
			return false;
		}
		return Read::nextDebts();
	}


	/*Insert savings (Ahorros)*/
	static public function newSaving($data = [])
	{
		return CRUD::insert('ahorro',$data);
	}

	/*Insert new debt Payment*/
	static public function loanPayment($data=[])
	{	
		$data['pago_intereses'] = Read::interesDebt($data['id_deuda']);
		$data['abono_capital'] = $data['pago_total'] - $data['pago_intereses'];
		CRUD::insert('pagos',$data);
		
		self::balanceSheet( [ 
				'egresos' => $data['pago_total'],
		 ] );

		$args = array(
			'where' => 'id_deuda = ?', 
			'where_values' => [$data['id_deuda']]
		);
		$deudasB = CRUD::all('deudas_balance',$args);

		$update['abonos_capital'] = $deudasB[0]->abonos_capital + $data['abono_capital'];
		$update['interes_pagado'] = $deudasB[0]->interes_pagado + $data['pago_intereses'];
		$update['cuotas_pagas'] = $deudasB[0]->cuotas_pagas + 1; 
		$update['saldo'] = $deudasB[0]->saldo - $data['abono_capital']; 

		if ( $data['pago_total'] < Read::actualPayment($data['id_deuda']) ) {
			$update['mora'] =  Read::actualPayment($data['id_deuda']) - $data['pago_total'];
		}
		/*ERROR: Se debe modificar el metodo para calcular las cuotas transcurridas*/

		CRUD::update('deudas_balance',$update,'id_deuda = ?',[$data['id_deuda']]);

		
	}

	// Ingresar Gastos diarios
	static public function outgoings($data = [])
	{
		CRUD::insert('gastos',$data);
		self::balanceSheet( [ 'egresos' => $data['valor'] ] );
		return true;
	}


	// Balance Gral
	static public function balanceSheet($data)
	{	
		/*
			
			$data = [
				'ingresos' => $ingresos,
				'egresos' => $egresos,
				'dinero_actual' => (No se debe definir como tal, la calcula la  función automáticamente)
			];
		
		*/
		$mes = date("Y-m").'-1';

		$args = array(
			'where' => 'mes = ?', 
			'where_values' => [$mes], 
		);
		$buscar = CRUD::all('balance_general',$args);
		if (count($buscar) === 0) {
			self::setBalanceSheet($data);
		}elseif (count($buscar) === 1) {
			$data['dinero_actual'] = 0;
			if (isset($data['ingresos'])) {
				$data['dinero_actual'] = $data['dinero_actual'] + $data['ingresos'];
			}
			if (isset($data['egresos'])) {				
				$data['dinero_actual'] = $data['dinero_actual'] - $data['egresos'];
			}
			self::updateBalanceSheet($data);
		}		
	}

	static public function setBalanceSheet($data = [])
	{	
		$ingresos = $egresos = $dinero_actual = 0;
		foreach ($data as $key => $value) {
			${$key} = $value;
		}

		$mes_anterior;
		if (date("m") > 1) {
			$mes_anterior = date("m") - 1;
			$ano = date("Y");
		}elseif (date("m") == 1) {
			$mes_anterior = 12;
			$ano = date("Y")-1;
		}
		$last_month = $ano.'-'.$mes_anterior.'-1';

		$args = array(
			'columns' => 'dinero_actual', 
			'where' => 'mes = ?', 
			'where_values' => [$last_month]
		);
		$ultimoCapital = CRUD::all('balance_general',$args);

		if (count($ultimoCapital) > 0) {
			if ($ultimoCapital[0]->dinero_actual != 0) {
				$ingresos = $ingresos + $ultimoCapital[0]->dinero_actual;
			}
		}

		$dinero_actual = $ingresos - $egresos; /**/

		$data = [
			'mes' => date("Y-m").'-1',
			'ingresos' => $ingresos,
			'egresos' => $egresos,
			'dinero_actual' => $dinero_actual,
		];

		CRUD::insert('balance_general',$data);
	}

	static public function updateBalanceSheet($set)
	{	
		$args = array(
			'where' => 'mes = ?', 
			'where_values' => [date("Y-m").'-1']
		);
		$buscar = CRUD::all('balance_general',$args);
		$datos = $buscar[0];
		$update = [];
		foreach ($set as $key => $value) {
			$update[$key] = $value + $datos->{$key};
		}

		CRUD::update('balance_general',$update,'mes = ?',[date("Y-m").'-1']);
	}
}