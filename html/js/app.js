var app = angular.module('TTApp',[]);
app.controller('TTCtrl', function($scope) {
	// a="1";
	// b="danhmuccon";
	$scope.danhmuc = DanhMuc;
	$scope.tinhthanh = TinhThanh;
	$scope.bannertop = BannerTop;
	$scope.quanhuyen = {};	
	$scope.changeColor = function(e){
		// console.log(e);
		// console.log(el);
		$(e.currentTarget).toggleClass("highlighted");
	}
	$scope.chosenDM = function(item) {
		// console.log(e.target.parentNode.parentNode.parentNode.parentNode);
		// console.log(e);
		$(".cat-name-dm").text(item.name);		
		$scope.cbDanhMuc = item.id;
	}
	$scope.chosenTT = function(item) {
		$(".cat-name-tt").text(item.name);		
		$scope.cbTinhThanh = item.id;
	}
	$scope.choseCatItem = function(e, item) {
		$scope.CatItemSelected = item;
		// console.log(item);
		// console.log(e);
		$(".cat-list .active").removeClass("active");
		$(e.currentTarget).toggleClass("active");
		$(".category").css('display','block');
	}
	$scope.chosenSubCatItem = function(item) {
		$scope.SubCatItemSelected = item;
		// console.log(item);
		$(".se-category").hide();
		$(".form-post").show();
		showHangSanXuat($scope, item.id);
		showRelateBlock(item.id);
	}
	$scope.changeCat = function(){
		$(".se-category").show();
		$(".form-post").hide();
	}	
	$scope.cityClick = function(e){
		// console.log(e);
		// console.log(el);
		$(e.target.parentNode).toggleClass("chosen-with-drop");
	}
	$scope.chosenCity = function(e, item){
		$scope.cbTinhThanh = item;
		console.log(e);
		$(".chosen-citys .cat-name-tt").text(item.name);
		$(".chosen-citys .chosen-container").toggleClass("chosen-with-drop");
		$scope.quanhuyen = item.quanhuyen;
		console.log(item.quanhuyen);
	}
	$scope.hinhthuclvClick = function(e){
		$scope.hinhthuclv = HinhThucLV;
		$(e.target.parentNode).toggleClass("chosen-with-drop");
	}
	$scope.chosenHinhThucLV = function(item){		
		$scope.cbHinhThucLV = item;
		$(".chosen-hinhthuclv .cat-name-tt").text(item.name);
		$(".chosen-hinhthuclv .chosen-container").toggleClass("chosen-with-drop");		
	}
	$scope.nganhngheClick = function(e){
		$scope.nganhnghe = NganhNghe;
		$(e.target.parentNode).toggleClass("chosen-with-drop");
	}
	$scope.chosenNganhNghe = function(item){		
		$scope.cbNganhNghe = item;
		$(".chosen-nganhnghe .cat-name-tt").text(item.name);
		$(".chosen-nganhnghe .chosen-container").toggleClass("chosen-with-drop");		
	}
	$scope.gioitinhClick = function(e){
		$scope.gioitinh = GioiTinh;
		$(e.target.parentNode).toggleClass("chosen-with-drop");
	}
	$scope.chosenGioiTinh = function(item){		
		$scope.cbGioiTinh = item;
		$(".chosen-gioitinh .cat-name-tt").text(item.name);
		$(".chosen-gioitinh .chosen-container").toggleClass("chosen-with-drop");		
	}
	$scope.namsinhClick = function(e){
		$scope.namsinh = NamSinh;
		$(e.target.parentNode).toggleClass("chosen-with-drop");
	}
	$scope.chosenNamSinh = function(item){		
		$scope.cbNamSinh = item;
		$(".chosen-namsinh .cat-name-tt").text(item.name);
		$(".chosen-namsinh .chosen-container").toggleClass("chosen-with-drop");		
	}
	$scope.tuoituClick = function(e){
		$scope.dotuoi = DoTuoi;
		$(e.target.parentNode).toggleClass("chosen-with-drop");
	}
	$scope.chosenTuoiTu = function(item){		
		$scope.cbTuoiTu = item;
		$(".from-age .cat-name-tt").text(item.name);
		$(".from-age .chosen-container").toggleClass("chosen-with-drop");
		$scope.dendotuoi = DenDoTuoi(item.id);		
	}
	$scope.tuoidenClick = function(e){		
		$(e.target.parentNode).toggleClass("chosen-with-drop");
	}
	$scope.chosenTuoiDen = function(item){		
		$scope.cbTuoiDen = item;
		$(".to-age .cat-name-tt").text(item.name);
		$(".to-age .chosen-container").toggleClass("chosen-with-drop");		
	}
	$scope.districtClick = function(e){
		// console.log(e);
		// console.log(el);
		$(e.target.parentNode).toggleClass("chosen-with-drop");
	}
	$scope.chosenDistrict = function(e, item){
		$scope.cbQuanHuyen = item;
		console.log(e);
		$(".chosen-districts .cat-name-tt").text(item.name);
		$(".chosen-districts .chosen-container").toggleClass("chosen-with-drop");
		// $scope.quanhuyen = item.quanhuyen;
		// console.log(item.quanhuyen);
	}
	$scope.checkLength = function(e, maxLengthAllow){
		var strLength = $(e.target).val().length;
		//console.log(strLength);
		// console.log(e);
		if(strLength >= maxLengthAllow){			
			$(e.target.nextElementSibling.firstElementChild).text("Đã vượt quá giới hạn số ký tự cho phép!");
			return false;
		}
		else{
			// console.log($(e.target.offsetParent.children[2].firstChild));
			$(e.target.offsetParent.children[2].firstElementChild).text(strLength+1);
			// $scope.
		}
	}
	$scope.thongtinmorongClick = function(){
		$(".thong-tin-mo-rong").toggle();
	}
});

var showHangSanXuat = function($scope, hangSXId){
	
	if(typeof(HangSXDienTu[hangSXId]) !== 'undefined'){
		console.log(HangSXDienTu[hangSXId]);
		$scope.hangsanxuat = HangSXDienTu[hangSXId];
		$(".hang-san-xuat").show();
	}
	else {
		$scope.hangsanxuat = {};
		$(".hang-san-xuat").hide();	
	}
}
var showRelateBlock = function(subCatId) {
	if(typeof(blockDisplay[subCatId]) !== 'undefined'){
		$(".dang-tin-bds").css("display",blockDisplay[subCatId]["dang-tin-bds"]);
		$(".dang-tin-xe").css("display",blockDisplay[subCatId]["dang-tin-xe"]);
		$(".dang-tin-viec-lam").css("display",blockDisplay[subCatId]["dang-tin-viec-lam"]);
		console.log(blockDisplay[subCatId]["dang-tin-viec-lam"]);
		if(typeof(blockDisplay[subCatId]["gia"]) !== 'undefined'){
			$(".gia").css("display",blockDisplay[subCatId]["gia"]);
		}
		//chi danh cho viec tim nguoi
		if(typeof(blockDisplay[subCatId]["dotuoi"]) !== 'undefined'){
			$(".dotuoi").css("display",blockDisplay[subCatId]["dotuoi"]);
		}	
	}	
}
