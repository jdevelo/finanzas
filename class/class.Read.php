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
			'where_values' => [$mes_actual]  
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

	static public function fixed_charges($columns="*",$order=null)
	{		
		$this_month = date('Y-m');
		$args = array(
			'columns' => $columns, 
			'where' => 'tm_delete IS NULL && ( ultimo_mes_pagado IS NULL || ultimo_mes_pagado < ? || mora > ? )',
			'where_values' => [$this_month,0],
			'order' => $order
		);
		$fixed = CRUD::all('cargos_fijos',$args);
		return json_encode($fixed);
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
			'columns' => 'deudas.id_deuda, deudas.concepto, deudas.primer_pago, deudas_balance.mora',
			'where' => 'deudas_balance.saldo > ? && (deudas_balance.ultimo_mes_pagado <> ? || deudas_balance.ultimo_mes_pagado IS NULL || deudas_balance.mora > ?)', 
			'where_values' => [0,$this_month,0],
			'join' => $join
		);
		$actual_debts = CRUD::all('deudas',$args);

		foreach ($actual_debts as $kDebt => $debt) {
			$fecha = substr($debt->primer_pago,0,7);
			if ($fecha > date('Y-m')) {
				unset($actual_debts[$kDebt]);
			}
		}

		// Cargos fijos
		$fixed = json_decode(self::fixed_charges('id_cargo_fijo, primer_pago, concepto, mora'),true);

		foreach ($fixed as $key => $charge) {		
			$fixed[$key]['valor_pagar'] = self::fixed_payment($charge['id_cargo_fijo']);
		}

		$result = array_merge($actual_debts,$fixed);

		foreach ($result as $rsKey => $rsVal) {
			$result[$rsKey] = (object) $rsVal;
		}

		foreach ($result as $rsKey => $rsVal) {
			if (isset($rsVal->id_deuda)) {
				$result[$rsKey]->valor_pagar = self::actualPayment($rsVal->id_deuda);
				$id['id_deuda'] = $rsVal->id_deuda;
			}elseif (isset($rsVal->id_cargo_fijo)) {

				$result[$rsKey]->valor_pagar = Read::fixed_payment($rsVal->id_cargo_fijo);
				$id['id_cargo_fijo'] = $rsVal->id_cargo_fijo;
			}
			$result[$rsKey]->fecha_pago = self::fechaPago($rsVal->primer_pago);
		}

		return json_encode($result);

	}

	/*Rerturn the payment-date*/
	static public function fechaPago($primer_pago)
	{	
		if (is_null($primer_pago)) {
			$primer_pago = date('Y-m-t');
		}
		$this_month = date('Y-m');
		$dia_pago = substr($primer_pago,8,2);

		$fecha_pago = date("Y-m-").$dia_pago;

		$valores = explode('-', $fecha_pago);

		if (!checkdate($valores[1], $valores[2], $valores[0])) {
			do {
				$valores[2]--;
				$valores[2] = (string) $valores[2];
			} while ( !checkdate( $valores[1], $valores[2], $valores[0] ) );
		}

		return implode("-", $valores);
			
	}

	/*Return all the info of a specific Debt*/
	static public function rDebt($id_deuda)
	{	
		$args = array(
			'where' => 'deudas.id_deuda = ?', 
			'where_values' => [$id_deuda], 
			'join' => [ 
					['INNER', 'deudas_balance','deudas_balance.id_deuda = deudas.id_deuda']
				]
		);
		$s_debt = CRUD::all('deudas',$args);
		return $s_debt[0];
	}

	static public function paymentsDebt($id_deuda) /*Pagos hechos a la deuda*/
	{	
		$debt = self::rDebt($id_deuda);
		$total_debt = $debt->valor; /*Total de la deuda adquirida*/

		$args = array(
			'where' => 'id_deuda = ?',
			'where_values' => [$id_deuda]
		);

		$payments = CRUD::all('pagos',$args);

		$payments_today = 0;	
		if ( count($payments) > 0 ) {
			foreach ( $payments as $payment ) {
				$payments_today = $payments_today + $payment->abono_capital;
			}
		}

		return $payments_today;
	}

	// Retorna la actual cuota (Abono a capital)
	static public function actualDebt($id_deuda)
	{	
		$args = array(
			'columns' => 'deudas.primer_pago, deudas.cuotas, deudas.valor, deudas_balance.ultimo_mes_pagado',
			'where' => 'deudas.id_deuda = ?',
			'where_values' => [$id_deuda],
			'join' => [['INNER', 'deudas_balance','deudas_balance.id_deuda = deudas.id_deuda']]
		);
		$d = CRUD::all('deudas',$args);
		$deuda = $d[0];

		if ($deuda->ultimo_mes_pagado >= date('Y-m')) {
			return 0;
		}

		$primerPago = new DateTime($deuda->primer_pago);
		$this_month = new DateTime( date("Y-m") );
		$diferencia = $primerPago->diff($this_month);
		$n_cuota = ( $diferencia->y * 12 ) + $diferencia->m;

		$c_faltantes = $deuda->cuotas - $n_cuota;

		$s_payments = self::paymentsDebt($id_deuda);
		
		$s_pend = $deuda->valor - $s_payments;

		return $s_pend / $c_faltantes;
	}

	/*Return the actual interest of the debt*/
	static public function interesDebt($id_deuda)
	{	
		if (self::actualDebt($id_deuda) == 0) {
			return 0;
		}
		$debt = self::rDebt($id_deuda);
		$actual_debt = self::paymentsDebt($id_deuda) - $debt->valor;
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

		$deuda = CRUD::all('deudas_balance',$args);

		if ( !is_null($deuda[0]->ultimo_mes_pagado) && $deuda[0]->ultimo_mes_pagado >= date('Y-m') ) 
		{	
			return $deuda[0]->mora;
		}else{
			return self::actualDebt($id_deuda) + self::interesDebt($id_deuda) + $deuda[0]->mora;
		}
	}

	// return the fixed payment to this month
	static public function fixed_payment($id_cargo_fijo)
	{	
		$args = array(
			'columns' => 'valor, ultimo_mes_pagado, mora', 
			'where' => 'id_cargo_fijo = ?', 
			'where_values' => [$id_cargo_fijo], 
		);
		$cargo_fijo = CRUD::all('cargos_fijos',$args);

		$mora = 0;
		if ($cargo_fijo[0]->mora > 0) {
			$mora = $cargo_fijo[0]->mora;
		}

		$months_tp = 1;
		if ( !is_null($cargo_fijo[0]->ultimo_mes_pagado )) {
			$lastPayM = new DateTime($cargo_fijo[0]->ultimo_mes_pagado);
			$this_month = new DateTime( date("Y-m") );
			$diferencia = $lastPayM->diff($this_month);
		    $months_tp = ( $diferencia->y * 12 ) + $diferencia->m;
		}

		$valorPagar = ($cargo_fijo[0]->valor * $months_tp) + $mora;
		return $valorPagar;
	}
	
}