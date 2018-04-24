finanzasPersonales.controller('MainController',['$scope','finanzas', function ($scope,finanzas) {
	

	/*Get Data*/
		var ob = [
			'income',
			'spendings',
			'creditors',
			'debts',
			'nextDebts',
			'debt_payment',
			'fixed_charges'
		];
		ob.forEach(function (item) {
			getData(item);
		})

		function getData(e) {
			finanzas.getAll(e).then(
				function (data, status) {
					$scope[e] = data;
			});
		}
	/*#Get Data*/

	/*Income*/
		$scope.newEntry = {};

		$scope.addEntry = function () {
			finanzas.add('entry',$scope.newEntry)
			.then(function (data, status) {
				$scope.income.unshift(data);
			});
			$scope.newEntry = {};
		}
	/*#Income*/


	/*spendings*/
		$scope.newSpending = {};
		$scope.addSpending = function () {
			finanzas.add('spending',$scope.newSpending)
			.then(function (data, status) {
				$scope.spendings.unshift(data);
			});
			$scope.newSpending = {};
		}
	/*#spendings*/

	
	
	/*debts*/
		$scope.tipos_deuda = [
			{ id: 1,  tipo_deuda: 'Prestamo por cuotas' },
			{ id: 2,  tipo_deuda: 'Cargo fijo mensual' },
			{ id: 3,  tipo_deuda: 'Deuda única' },
		];

		$scope.selectTypeDebt = function () {
			/*dxp (Deuda por cuotas) cfm (cargo fijo mensual) du (deuda unica)*/
			$scope.dxc = $scope.cfm = $scope.du = false;
			if ($scope.typeDebt == 1 ) {
				$scope.dxc = true;
				$scope.cfm = $scope.du = false;
			} else if($scope.typeDebt == 2) {
				$scope.cfm = true;
				$scope.dxc = $scope.du = false;
			} else if($scope.typeDebt == 3) {
				$scope.du = true;
				$scope.cfm = $scope.dxc = false;
			}
		}

		$scope.formCreditor = function () {
			$scope.creditorForm = false;
			if ($scope.newDebt.id_acreedor == 'new') {
				$scope.creditorForm = true;
			}
		}

		/*creditors*/
			$scope.newCreditor = {};
			$scope.addCreditor = function () {
				finanzas.add('creditor',$scope.newCreditor)
				.then(function (data, status) {
					$scope.creditors.unshift(data);
				});
				$scope.newCreditor = {};
			}
		/*#creditors*/


		$scope.newDebt =  {};
		$scope.conceptosDeuda = [
			{ id:1, concepto: 'Personal' },
			{ id:2, concepto: 'Tarjeta de Crédito' },
		]; 
		$scope.addDebt = function () {
			finanzas.add('debt',$scope.newDebt)
			.then(function (data, status) {
				console.log(data);
				$scope.debts.unshift(data);
				$scope.newDebt = {};
				getData('nextDebts');
			})
		}

		/*fixed_charges*/
			$scope.newFixedCharge = {};
			$scope.addFixedCharges = function () {
				finanzas.add('fixed_charges',$scope.newFixedCharge)
				.then(function (data, status) {
					console.log($scope.fixed_charges);
					$scope.fixed_charges.push(data);
					$scope.newFixedCharge = {};
					getData('nextDebts');
				})
			}
		/*#fixed_charges*/

	/*#debts*/

	

	/*#Debt Payment*/
		$scope.newDebtPayment = {};

		$scope.set_ndp = function () {
			if ($scope.selectedDebt) {
				$scope.newDebtPayment.id_deuda = $scope.selectedDebt.id_deuda;
				$scope.newDebtPayment.id_cargo_fijo = $scope.selectedDebt.id_cargo_fijo;
				$scope.newDebtPayment.pago_total = Math.ceil($scope.selectedDebt.valor_pagar);
			}
		}

		$scope.addDebtPayment = function () {
			finanzas.add('debt_payment',$scope.newDebtPayment)
			.then(function (data, status) {
				$scope.newDebtPayment = {};
				getData('nextDebts');
			})
		}
	/*#Debt Payment*/
	

}]);