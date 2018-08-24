
var finanzasPersonales = angular.module("appFinanzas",['ngAnimate','ngRoute','ngResource','LocalStorageModule'])
.config(['$routeProvider',function($routeProvider) {
	$routeProvider
		.when("/",{
			templateUrl: '../../templates/home.php',
			controller: 'MainController'
		})
		.when("/repo/:name",{
			templateUrl: '../../templates/repo.php',
			controller: 'RepoController'
		})
		.otherwise({
			redirect: "/",
		})
}])

