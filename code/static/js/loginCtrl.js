app.controller('loginCtrl', function($scope, loginService, $http, $q) {
	$scope.email= $("Email").val();	
	$scope.loading = false;

	$scope.Login = function(){
		$scope.loading = true;		
		loginService.login($scope.email, $scope.password).then(function successCallback(data){
			if(data){
				// console.log(data.error);
				$scope.loading = false;
				if (data.error){
					$scope.message = data.message;
					console.log(data.message);
					$(".field-validation-error").removeClass("hide");
				}
				else{
					console.log("OM");
					if(data.body.userId){
						$(".col-right-login").toggleClass("hide");
						$(".col-left-login").toggleClass("hide");
						$(".col-right-tbao").toggleClass("hide");
					}
				}	
			}
		}, function errorCallback(data){console.log("error_new" + data);});	
	};	

});