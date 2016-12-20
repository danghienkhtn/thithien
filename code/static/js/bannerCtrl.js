app.controller('bannerCtrl', function($scope, bannerService, $http, $q) {
	$scope.email= $("Email").val();	
	$scope.loading = false;
	$scope.redirectURL = $("#returnUrl").val();
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
					if(parseInt(data.body.data.account_id) > 0){
						$(".col-right-login").toggleClass("hide");
						$(".col-left-login").toggleClass("hide");
						$(".col-right-tbao").toggleClass("hide");						
						if($scope.redirectURL !== ""){
							setTimeout(function(){
								window.location.href = $scope.redirectURL;
							}, 2500); 							
						}						
					}
				}	
			}
		}, function errorCallback(data){console.log("error_new" + data);});	
	};	

});