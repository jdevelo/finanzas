<?php 



/**
* 
*/
class Read
{
	
	static public function currentMoney()
	{
		$mes_actual = date("Y-m").'-1';
		$args =  array(
			'where' => 'mes = ?',
			'where_values' => [$mes_actual] 
		);
		$cMoney = CRUD::all('balance_general',$args);

		if ( count($cMoney) === 1 ) {
			return 	$cMoney[0]->dinero_actual;
		}elseif ( count($cMoney) === 0 ) {
			Write::setBalanceSheet( [ 'ingresos' => 0 ] );
		}
		return self::getCurrentMoney();
	}


	static public function getCurrentMoney()
	{
		$mes_actual = date("Y-m").'-1';
		$args =  array(
			'where' => 'mes = ?',
			'where_values' => $mes_actual 
		);

		$cMoney = CRUD::all('balance_general',$args);
		return $cMoney[0]->dinero_actual;
	}

	/*Ingresos*/
	static public function income()
	{	
		$args =  array(
			'where' => 'tm_delete IS NULL',
			'order' => 'fecha DESC',
			'limit' => 10
		);
		$income = CRUD::all('ingresos',$args);
		return json_encode($income);
	}


	/*Retrurn all spendings*/
	static public function spendings($limit = 10)
	{	
		$args = array(
			'where' => 'tm_delete IS NULL', 
			'limit' => $limit,
			'order' => 'fecha DESC'
		);
		$spendings = CRUD::all('gastos',$args);
		return json_encode($spendings);
	}


	/*Retrurn all acreedores*/
	static public function creditors()
	{
		$creditors = CRUD::all('acreedores');
		return json_encode($creditors);
	}

	/*Return all debts*/
	static public function debts()
	{	
		$join = [
			['INNER','acreedores','acreedores.id_acreedor = deudas.id_acreedor'],
		];
		$args = array(
			'where' => 'deudas.tm_delete IS NULL', 
			'join' => $join,
			'order' => 'deudas.fecha DESC',
			'limit' => 10,
		);
		$debts = CRUD::all('deudas',$args);
		return json_encode($debts);
	}

	static public function fixed_charges($columns="*",$order=null)
	{	
		$args = array(
			'columns' => $columns, 
			'where' => 'tm_delete IS NULL',
			'order' => $order
		);
		$fixed = CRUD::all('cargos_fijos',$args);
		return json_encode($fixed);
	}

	static public function debtsResume()
	{		
		$join = [
			['INNER','acreedores','acreedores.id_acreedor = deudas.id_acreedor']
		];
		$args = array(
			'where' => 'deudas.tm_delete IS NULL', 
			'join' => $join, 
			'order' => 'deudas.fecha DESC'
		);
		$debts = CRUD::all('deudas','*','deudas.tm_delete IS NULL',[],$join,'deudas.fecha DESC');

		foreach ($debts as $key) {
			$pagos = CRUD::all('pagos','*','id_deuda = ?',['i',$key['id_deuda']]);
			$cuotas = count($pagos);
			$abono = 0;
			foreach ($pagos as $pago) {
				$abono = $abono + $pago['abono_capital'];
			}
		}

	}

	static public function nextDebts()
	{	
		$this_month = date('Y-m');
		$first_day = date('Y-m-01');
		$last_day = date('Y-m-t');

		$join = [
			['INNER','deudas_balance','deudas_balance.id_deuda = deudas.id_deuda']
		];
		$args = array(
			// 'columns' => 'deudas.id_deuda, deudas.concepto, deudas.primer_pago',
			'where' => 'deudas_balance.saldo > ? && (deudas_balance.ultimo_mes_pagado <> ? || deudas_balance.ultimo_mes_pagado IS NULL || deudas_balance.mora > ?)', 
			'where_values' => [0,$this_month,0],
			'join' => $join
		);
		$actual_debts = CRUD::all('deudas',$args);

		foreach ($actual_debts as $value) {
			$value->mes_primer_pago = substr($value->primer_pago, 0, 7);
			if ($value->mora > 0) {
				if ($value->ultimo_mes_pagado === $this_month) {
					
				}
			}
		}

		var_dump($actual_debts);

		// Cargos fijos
		$fixed = (object) json_decode(self::fixed_charges('id_cargo_fijo, primer_pago, concepto'),true);

		var_dump($fixed);

		$pending_fc = [];
		foreach ($fixed as $charge) {		
			$charge['valor_pagar'] = self::fixed_payment($charge['id_cargo_fijo']);

			if ($charge['valor_pagar'] > 0) {
				$pending_fc[] = $charge;
			}
		}

		$result = array_merge($actual_debts,$pending_fc);

		foreach ($result as $rsKey => $rsVal) {
			$result[$rsKey] = (object) $rsVal;
		}

		foreach ($result as $rsKey => $rsVal) {
			if (isset($rsVal->id_deuda)) {
				$result[$rsKey]->valor_pagar = self::actualPayment($rsVal->id_deuda);
			}elseif (isset($rsVal->id_cargo_fijo)) {
				$result[$rsKey]->valor_pagar = Read::fixed_payment($rsVal->id_cargo_fijo);
			}
			$result[$rsKey]->fecha_pago = $this_month.'-'.$rsVal->dia_pago;
		}

		return json_encode($result);

	}


	/*Return all the info of a specific Debt*/
	static public function rDebt($id_deuda)
	{	
		$args = array(
			'where' => 'id_deuda = ?', 
			'where_values' => [$id_deuda], 
		);
		$s_debt = CRUD::all('deudas',$args);
		return $s_debt[0];
	}

	static public function paymentsDebt($id_deuda) /*Pagos hechos a la deuda*/
	{	
		$args = array(
			'where' => 'id_deuda = ?',
			'where_values' => [$id_deuda]
		);
		return CRUD::all('pagos',$args);
	}

	// Retorna la actual cuota (Abono a capital)
	static public function actualDebt($id_deuda)
	{	
		$debt = self::rDebt($id_deuda);
		$total_debt = $debt->valor; /*Total de la deuda adquirida*/

		$s_payments = self::paymentsDebt($id_deuda);
		$payments_today = 0;	
		if ( count($s_payments) > 0 ) {
			foreach ( $s_payments as $payment ) {
				$payments_today = $payments_today + $payment->abono_capital;
			}
		}

		return $total_debt - $payments_today;
	}

	/*Return the actual interest of the debt*/
	static public function interesDebt($id_deuda)
	{
		$debt = self::rDebt($id_deuda);
		$actual_debt = self::actualDebt($id_deuda);
		return ($actual_debt * $debt->tasa) / 100;
	}

	/*Actual Payment Debt*/
	static public function actualPayment($id_deuda)
	{
		$debt = self::rDebt($id_deuda);/*Deuda Actual*/
		$interes = self::interesDebt($id_deuda);/*Interes Actual*/

		$args = array(
			'where' => 'id_deuda = ?',
			'where_values' => [$id_deuda] 
		);

		foreach (CRUD::all('deudas_balance',$args) as $key) {

			$mora = 0;
			if ($key->mora > 0) {
				$mora = $key->mora;
			}

			if (is_null($key->ultimo_mes_pagado)) {
				$months_tp = 1;
			}else {
				$lastPayM = new DateTime($key->ultimo_mes_pagado);
		        $this_month = new DateTime( date("Y-m") );
		        $diferencia = $lastPayM->diff($this_month);
		        $months_tp = ( $diferencia->y * 12 ) + $diferencia->m;
			}

		}

		$debt_date = new DateTime($debt->fecha);
		$today = new DateTime( date("Y-m-d H:i:s") );

		$diferencia = $debt_date->diff($today);
		// El método diff nos devuelve un objeto del tipo DateInterval,
		// que almacena la información sobre la diferencia de tiempo 
		// entre fechas (años, meses, días, etc.).

		$meses = ( $diferencia->y * 12 ) + $diferencia->m; /*Meses en los que he pagado*/

		$months_term = $debt->cuotas - $meses; /*Meses restantes para pago*/

		$base_pay = self::actualDebt($id_deuda) / $months_term; /*Abono a capital del presente mes*/

		return ($base_pay + $interes) * $months_tp + $mora;
	}


	static public function fixed_payment($id_cargo_fijo)
	{	
		$args = array(
			'columns' => 'valor, ultimo_mes_pagado', 
			'where' => 'id_cargo_fijo = ?', 
			'where_values' => [$id_cargo_fijo], 
		);
		$cargo_fijo = CRUD::all('cargos_fijos',$args);

		$months_tp = 1;
		if (!is_null($cargo_fijo[0]->ultimo_mes_pagado)) {
			$lastPayM = new DateTime($cargo_fijo[0]->ultimo_mes_pagado);
			$this_month = new DateTime( date("Y-m") );
			$diferencia = $lastPayM->diff($this_month);
		    $months_tp = ( $diferencia->y * 12 ) + $diferencia->m;
		}

		return $cargo_fijo[0]->valor * $months_tp;
	}
	
}