app.controller('searchCtrl', function($scope, searchService, $http, $q) {
	$scope.danhmuc = DanhMuc;
	$scope.tinhthanh = TinhThanh;
	$scope.loading = false;
	$scope.redirectURL = $("#returnUrl").val();
	$scope.cbDanhMuc = "";
	$scope.cbTinhThanh = "";
	$scope.txtSearch = "";
	$scope.changeColor = function(e){
		$(e.currentTarget).toggleClass("highlighted");
	}
	$scope.chosenDM = function(item) {
		$(".cat-name-dm").text(item.name);		
		$scope.cbDanhMuc = item.id;
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
		$scope.loading = true;
		searchService.search($scope.cbTinhThanh, $scope.cbDanhMuc.id, $scope.txtSearch).then(function successCallback(data){
			console.log(data);
			if(data){
				console.log(data.error);
				$scope.loading = false;
				if (data.error){
					$scope.message = data.message;
					console.log(data.message);
					$(".field-validation-error").removeClass("hide");
				}
				else{								
					if(parseInt(data.body.totals) > 0){
						console.log("error_log OK");						
					}
					else{
						console.log("error_log 1 OK");							
					}
				}	
			}
		}, function errorCallback(data){console.log("error_new" + data);});	
	}	

});