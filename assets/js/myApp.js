
/*
	=====================================================================
	#	FINANZAS
	=====================================================================

*/

var finanzasPersonales = angular.module("appFinanzas",['ngAnimate'])
.factory('finanzas',function ($http) {
	var finanzas = [];

	var writeUrl = '/finanzas/db/write.php';
	var readUrl = '/finanzas/db/read.php';
	var deleteUrl = '/finanzas/db/delete.php';

	finanzas.key = 'finanzasAPP';

	/*GLOBAL FUNCTIONS*/
	finanzas.getAll = function (table) {
		return $http.get(readUrl+"?read="+table)
			.then( function (response) {
				return response.data;
			}, function (response) {
				console.log(response.data);
			}
		);
	}

	finanzas.add = function (data,obj) {
		return $http.post(writeUrl+"?add="+data,obj)
		.then(function (response) {
			console.log(response.data);
			return response.data
		}, function (response) {
			console.log(response.data);
		})
	}

	finanzas.delete = function (obj) {
		$http.post(deleteUrl+"?delete=",obj)
		.then(function (response) {
			return response.data
		}, function (response) {
			console.log(response.data);
		})
	}


	return finanzas;
});


/*
	=====================================================================
	#	FINANZAS END
	=====================================================================

*/
