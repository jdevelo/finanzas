finanzasPersonales.service('finanzas',function ($http,localStorageService) {

	var writeUrl = '/finanzas/db/write.php';
	var readUrl = '/finanzas/db/read.php';
	var deleteUrl = '/finanzas/db/delete.php';

	this.key = 'finanzasAPP';

	/*GLOBAL FUNCTIONS*/
	this.getAll = function (table) {
		return $http.get(readUrl+"?read="+table)
			.then( function (response) {
				console.log(response.data);
				return response.data;
			}, function (response) {
				console.log(response.data);
			}
		);
	}

	this.add = function (data,obj) {
		return $http.post(writeUrl+"?add="+data,obj)
		.then(function (response) {
			return response.data
		}, function (response) {
			console.log(response.data);
		})
	}

	this.delete = function (obj) {
		$http.post(deleteUrl+"?delete=",obj)
		.then(function (response) {
			return response.data
		}, function (response) {
			console.log(response.data);
		})
	}

})