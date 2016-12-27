require("/static/js/detailService.js");
app.controller('detailCtrl', function($scope, detailService, $http, $q) {
	// $scope.danhmuc = DanhMuc;
	// $scope.tinhthanh = TinhThanh;
	$scope.loading = false;
	$scope.redirectURL = $("#returnUrl").val();
	$scope.nid = $("#newsId").val();
	$scope.newsDetail = {};
	// $scope.cbDanhMuc = "";
	// $scope.cbTinhThanh = "";
	// $scope.txtSearch = "";
	/*$scope.changeColor = function(e){
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
	}*/

	$scope.loading = true;
	detailService.getDetail($scope.nid).then(function successCallback(data){
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
				if(typeof(data.body.detail) !== 'undefined'){
					console.log("error_log OK");
					$scope.newsDetail = data.body.detail;
					console.log($scope.newsDetail);
					switch($scope.newsDetail.news_type) {
						case "1":
							$scope.ntype = "properties";
							$scope.newsDetail= getPropeDetail($scope.newsDetail);
							break;
						case "2":						
							$scope.ntype = "job";
							console.log($scope.newsDetail);
							$scope.newsDetail = getJobDetail($scope.newsDetail);
							break;
						case "3":
							$scope.ntype = "car";
							break;
						case "4":
							$scope.ntype = "bike";
							break;
						case "0":
							$scope.ntype = "normal";
							break;			
					}
					// getTitleURL(newsDetail.news_cat_id);				
					
				}
				else{
					window.location.href = $scope.redirectURL;					
				}
			}	
		}
	}, function errorCallback(data){console.log("error_new" + data);});	
});

function getJobDetail(newsDetail){	
	newsDetail.jobTypeName = (newsDetail.job_type_id !== null)? HinhThucLV[newsDetail.job_type_id].name:"-";
	newsDetail.jobGenderName = (newsDetail.job_gender_id !== null)? GioiTinh[newsDetail.job_gender_id].name:"-";
	newsDetail.jobCatName = (newsDetail.job_cat_id !== null)? NganhNghe[newsDetail.job_cat_id].name:"-";
	if(newsDetail.job_birth_year_from !== null && newsDetail.job_birth_year_to !== null){
		newsDetail.jobAge = newsDetail.job_birth_year_from + " - " + newsDetail.job_birth_year_to;
	}
	else if(newsDetail.job_birth_year_from !== null){
		newsDetail.jobAge = ">= " + newsDetail.job_birth_year_from;
	}
	else if(newsDetail.job_birth_year_to !== null){
		newsDetail.jobAge = "<= " + newsDetail.job_birth_year_to;
	}	
	else newsDetail.jobAge = "-";

	if(newsDetail.job_salary_from !== null && newsDetail.job_salary_to !== null){
		newsDetail.jobSalary = newsDetail.job_salary_from + " - " + newsDetail.job_salary_to;
	}
	else if(newsDetail.job_salary_from !== null){
		newsDetail.jobSalary = ">= " + newsDetail.job_salary_from;
	}
	else if(newsDetail.job_salary_to !== null){
		newsDetail.jobSalary = "<= " + newsDetail.job_salary_to;
	}
	else newsDetail.jobSalary = "-";
	newsDetail.jobExperience = (newsDetail.job_experience !== null)? newsDetail.job_experience:"-";
	return newsDetail;
}
function getPropeDetail(newsDetail){	
	newsDetail.propeProject = (newsDetail.proper_project !== null)? newsDetail.proper_project:"-";
	newsDetail.propeAddress = (newsDetail.proper_address !== null)? newsDetail.proper_address:"-";
	newsDetail.propeCatName = (newsDetail.proper_type_id !== null)? LoaiBDS[newsDetail.news_sub_cat_id][newsDetail.proper_type_id].name:"-";
	if(newsDetail.proper_CT1 !== null){
		newsDetail.CT1 = newsDetail.proper_CT1;
	}
	else newsDetail.CT1 = "-";
	if(newsDetail.proper_CT2 !== null){
		newsDetail.CT2 = newsDetail.proper_CT2;
	}
	else newsDetail.CT2 = "-";
	if(newsDetail.proper_CT3_id !== null){
		newsDetail.CT3Name = Huong[newsDetail.proper_CT3_id].name;
	}
	else newsDetail.CT3Name = "-";
	if(newsDetail.proper_CT4_id !== null){
		newsDetail.CT4Name = PhapLy[newsDetail.proper_CT4_id].name;
	}
	else newsDetail.CT4Name = "-";
	if(newsDetail.proper_CT5 !== null){
		newsDetail.CT5 = newsDetail.proper_CT5;
	}
	else newsDetail.CT5 = "-";
	if(newsDetail.proper_CT6 !== null){
		newsDetail.CT6 = newsDetail.proper_CT6;
	}
	else newsDetail.CT6 = "-";
	if(newsDetail.proper_CT7 !== null){
		newsDetail.CT7 = newsDetail.proper_CT7;
	}
	else newsDetail.CT7 = "-";

	if(newsDetail.job_salary_from !== null && newsDetail.job_salary_to !== null){
		newsDetail.jobSalary = newsDetail.job_salary_from + " - " + newsDetail.job_salary_to;
	}
	else if(newsDetail.job_salary_from !== null){
		newsDetail.jobSalary = ">= " + newsDetail.job_salary_from;
	}
	else if(newsDetail.job_salary_to !== null){
		newsDetail.jobSalary = "<= " + newsDetail.job_salary_to;
	}
	else newsDetail.jobSalary = "-";
	newsDetail.jobExperience = (newsDetail.job_experience !== null)? newsDetail.job_experience:"-";
	return newsDetail;
}
app.directive("detailJob", function() {	
    return {
    	restrict: 'E',
    	scope: {
    		newsDetail: '=info'
    	},
    	templateUrl : '/api/news/get-template?ntype=2'
    };
});
app.directive("detailProperties", function() {	
    return {
    	restrict: 'E',
    	scope: {
    		newsDetail: '=info'
    	},
    	templateUrl : '/api/news/get-template?ntype=1'
    };
});
app.directive("detailCar", function() {	
    return {
    	restrict: 'E',
    	scope: {
    		newsDetail: '=info'
    	},
    	templateUrl : '/api/news/get-template?ntype=3'
    };
});
app.directive("detailBike", function() {	
    return {
    	restrict: 'E',
    	scope: {
    		newsDetail: '=info'
    	},
    	templateUrl : '/api/news/get-template?ntype=4'
    };
});