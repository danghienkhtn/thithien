app.controller('searchCtrl', function($scope, searchService, $http, $q) {
	$scope.danhmuc = DanhMuc;
	$scope.tinhthanh = TinhThanh;
	$scope.loading = false;
	$scope.redirectURL = $("#returnUrl").val();
	$scope.changeColor = function(e){
		$(e.currentTarget).toggleClass("highlighted");
	}
	$scope.chosenDM = function(item) {
		$(".cat-name-dm").text(item.name);		
		$scope.cbDanhMuc = item;
	}
	$scope.chosenTT = function(item) {
		$(".cat-name-tt").text(item.name);		
		$scope.cbTinhThanh = item.id;
	}
	$scope.choseCatItem = function(e, item) {
		$scope.CatItemSelected = item;
		$(".cat-list .active").removeClass("active");
		$(e.currentTarget).toggleClass("active");
		$(".category").css('display','block');
	}

	$scope.search = function(){
		console.log('search');
		$scope.loading = true;		
		searchService.search($scope.cbTinhThanh, $scope.cbDanhMuc.id, $scope.txtSearch).then(function successCallback(data){
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