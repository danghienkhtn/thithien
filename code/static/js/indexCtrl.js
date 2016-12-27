require("/static/js/searchService.js");
require("/static/js/bannerService.js");
require("/static/js/searchCtrl.js");
require("/static/js/bannerCtrl.js");
app.controller('homeCtrl', function($scope, searchService, $http, $q) {
	$scope.email= $("Email").val();	
	$scope.loading = false;
	$scope.redirectURL = $("#returnUrl").val();

	$scope.loading = true;		
	searchService.getNews(0, 0, 30).then(function successCallback(data){
		if(data){
			// console.log(data.error);
			$scope.loading = false;
			if (data.error){
				$scope.message = data.message;
				console.log(data.message);
				// $(".field-validation-error").removeClass("hide");
			}
			else{								
				if(parseInt(data.body.totals) > 0){
					console.log(data.body.data);
					$arrNews = data.body.data;
					var cnt = 0;
					var arrNewsLeft = [];
					var arrNewsRight = [];
					angular.forEach($arrNews, function(val, key){
						var arrTmp = {};
						arrTmp.tittle = val.news_tittle;
						arrTmp.detail = val.news_detail;
						arrTmp.detailURL = val.news_detailURL;
						if(cnt % 2 == 0){//col left
							arrNewsLeft.push(arrTmp);
						}
						else{//col right
							console.log();
							arrNewsRight.push(arrTmp);
						}
						cnt++;
					});
					$scope.leftItem = arrNewsLeft;
					$scope.rightItem = arrNewsRight;
				}
			}	
		}
	}, function errorCallback(data){console.log("error_new" + data);});

});