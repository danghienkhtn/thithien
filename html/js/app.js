var app = angular.module('TTApp',[]);
app.controller('TTCtrl', function($scope) {
	$scope.danhmuc = DanhMuc;
	$scope.tinhthanh = TinhThanh;
	$scope.bannertop = BannerTop;
	$scope.quanhuyen = {};	
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
	$scope.chosenSubCatItem = function(item) {
		// console.log(item);
		$scope.SubCatItemSelected = item;
		$(".se-category").hide();
		$(".form-post").show();
		showHangSanXuat($scope, item.id);
		showRelateBlock($scope, item.id);
	}
	$scope.changeCat = function(){
		$(".se-category").show();
		$(".form-post").hide();
		$(".dang-tin-bds").css("display","none");
		$(".dang-tin-xe").css("display","none");
		$(".dang-tin-viec-lam").css("display","none");
	}	
	/*$scope.cityClick = function(e){
		$(e.target.parentNode).toggleClass("chosen-with-drop");
	}*/
	$scope.chosenCity = function(e, item){
		$scope.cbTinhThanh = item;
		$(".chosen-citys .cat-name-tt").text(item.name);
		$(".chosen-citys .chosen-container").toggleClass("chosen-with-drop");
		$scope.quanhuyen = item.quanhuyen;
	}
	/*$scope.hinhthuclvClick = function(e){
		$scope.hinhthuclv = HinhThucLV;
		$(e.target.parentNode).toggleClass("chosen-with-drop");
	}*/
	$scope.chosenHinhThucLV = function(item){		
		$scope.cbHinhThucLV = item;
		$(".chosen-hinhthuclv .cat-name-tt").text(item.name);
		$(".chosen-hinhthuclv .chosen-container").toggleClass("chosen-with-drop");		
	}
	/*$scope.nganhngheClick = function(e){
		$scope.nganhnghe = NganhNghe;
		$(e.target.parentNode).toggleClass("chosen-with-drop");
	}*/
	$scope.chosenNganhNghe = function(item){		
		$scope.cbNganhNghe = item;
		$(".chosen-nganhnghe .cat-name-tt").text(item.name);
		$(".chosen-nganhnghe .chosen-container").toggleClass("chosen-with-drop");		
	}	
	/*$scope.gioitinhClick = function(e){
		$scope.gioitinh = GioiTinh;
		$(e.target.parentNode).toggleClass("chosen-with-drop");
	}*/
	$scope.chosenGioiTinh = function(item){		
		$scope.cbGioiTinh = item;
		$(".chosen-gioitinh .cat-name-tt").text(item.name);
		$(".chosen-gioitinh .chosen-container").toggleClass("chosen-with-drop");		
	}
	/*$scope.namsinhClick = function(e){
		$scope.namsinh = NamSinh;
		$(e.target.parentNode).toggleClass("chosen-with-drop");
	}*/
	$scope.chosenNamSinh = function(item){		
		$scope.cbNamSinh = item;
		$(".chosen-namsinh .cat-name-tt").text(item.name);
		$(".chosen-namsinh .chosen-container").toggleClass("chosen-with-drop");		
	}
	/*$scope.tuoituClick = function(e){
		$scope.dotuoi = DoTuoi;
		$(e.target.parentNode).toggleClass("chosen-with-drop");
	}*/
	$scope.chosenTuoiTu = function(item){		
		$scope.cbTuoiTu = item;
		$(".from-age .cat-name-tt").text(item.name);
		$(".from-age .chosen-container").toggleClass("chosen-with-drop");
		$scope.dendotuoi = DenDoTuoi(item.id);		
		if(typeof($scope.cbTuoiDen) !== 'undefined'){				
			$scope.cbTuoiDen = ($scope.cbTuoiDen.id < $scope.cbTuoiTu.id) ? $scope.cbTuoiTu : $scope.cbTuoiDen;
			$(".to-age .cat-name-tt").text($scope.cbTuoiDen.id);
		}	
	}
	/*$scope.tuoidenClick = function(e){		
		$(e.target.parentNode).toggleClass("chosen-with-drop");
	}*/
	$scope.chosenTuoiDen = function(item){		
		$scope.cbTuoiDen = item;
		$(".to-age .cat-name-tt").text(item.name);
		$(".to-age .chosen-container").toggleClass("chosen-with-drop");		
	}
	/*$scope.districtClick = function(e){
		$(e.target.parentNode).toggleClass("chosen-with-drop");
	}*/
	$scope.chosenDistrict = function(e, item){
		$scope.cbQuanHuyen = item;
		$(".chosen-districts .cat-name-tt").text(item.name);
		$(".chosen-districts .chosen-container").toggleClass("chosen-with-drop");
		$scope.phuongxa = PhuongXa;
	}
	/*$scope.phuongxaClick = function(e){		
		$scope.phuongxa = PhuongXa;
		$(e.target.parentNode).toggleClass("chosen-with-drop");
	}*/
	$scope.chosenPhuongXa = function(item){			
		$(".chosen-phuongxa .cat-name-tt").text(item.name);
		$(".chosen-phuongxa .chosen-container").toggleClass("chosen-with-drop");
		$scope.cbPhuongXa = item;		
	}
	/*$scope.loaibdsClick = function(e){		
		$(e.target.parentNode).toggleClass("chosen-with-drop");
	}*/
	$scope.chosenLoaiBDS = function(item){				
		$(".chosen-loaibds .cat-name-tt").text(item.name);
		$(".chosen-loaibds .chosen-container").toggleClass("chosen-with-drop");
		$scope.cbLoaiBDS = item;
		showChiTietBDS($scope, item.id);
	}
	$scope.chosenHangXe = function(item){				
		$(".chosen-hangxe .cat-name-tt").text(item.name);
		$(".chosen-hangxe .chosen-container").toggleClass("chosen-with-drop");		
		$scope.cbHangXe = item;
	}
	$scope.chosenDongXe = function(item){				
		$(".chosen-dongxe .cat-name-tt").text(item.name);
		$(".chosen-dongxe .chosen-container").toggleClass("chosen-with-drop");		
		$scope.cbDongXe = item;
	}
	$scope.chosenNamSX = function(item){		
		$scope.cbNamSX = item;
		$(".chosen-namsx .cat-name-tt").text(item.name);
		$(".chosen-namsx .chosen-container").toggleClass("chosen-with-drop");		
	}
	$scope.chosenXuatXu = function(item){		
		$scope.cbXuatXu = item;
		$(".chosen-xuatxu .cat-name-tt").text(item.name);
		$(".chosen-xuatxu .chosen-container").toggleClass("chosen-with-drop");		
	}
	$scope.chosenHopSo = function(item){		
		$scope.cbHopSo = item;
		$(".chosen-hopso .cat-name-tt").text(item.name);
		$(".chosen-hopso .chosen-container").toggleClass("chosen-with-drop");		
	}
	$scope.chosenKieuDD = function(item){		
		$scope.cbKieuDD = item;
		$(".chosen-kieudandong .cat-name-tt").text(item.name);
		$(".chosen-kieudandong .chosen-container").toggleClass("chosen-with-drop");		
	}
	$scope.chosenNhienLieu = function(item){		
		$scope.cbNhienLieu = item;
		$(".chosen-nhienlieu .cat-name-tt").text(item.name);
		$(".chosen-nhienlieu .chosen-container").toggleClass("chosen-with-drop");		
	}
	$scope.chosenMauXe = function(item){		
		$scope.cbMauXe = item;
		$(".chosen-mauxe .cat-name-tt").text(item.name);
		$(".chosen-mauxe .chosen-container").toggleClass("chosen-with-drop");		
	}
	/*$scope.ct3Click = function(e){
		$scope.huong = Huong;		
		$(e.target.parentNode).toggleClass("chosen-with-drop");
	}*/
	$scope.chosenCT3 = function(item){				
		$(".chosen-ct3 .cat-name-tt").text(item.name);
		$(".chosen-ct3 .chosen-container").toggleClass("chosen-with-drop");		
		$scope.CT3 = item;
	}
	$scope.checkLength = function(e, maxLengthAllow){
		var strLength = $(e.target).val().length;
		if(strLength >= maxLengthAllow){			
			$(e.target.nextElementSibling.firstElementChild).text("Đã vượt quá giới hạn số ký tự cho phép!");
			return false;
		}
		else{
			$(e.target.offsetParent.children[2].firstElementChild).text(strLength+1);
			// $scope.
		}
	}
	$scope.thongtinmorongClick = function(){
		$(".thong-tin-mo-rong").toggle();
	}
	$scope.thongtinmorongbdsClick = function(loaibds){
		$(".thong-tin-mo-rong-bds").toggle();		
	}
	$scope.chosensingleClick = function(parentClass){		
		console.log($("."+parentClass +" .chosen-container-active"));
		//$(e.target.parentNode).toggleClass("chosen-with-drop");
		$($("."+parentClass +" .chosen-container-active")).toggleClass("chosen-with-drop")
	} 
	$scope.tbatChange = function(tbat_id){		
		// console.log(e);		
		if(typeof $scope.ThietBiAnToanChecked[tbat_id] !== "undefined"){
			delete $scope.ThietBiAnToanChecked[tbat_id];			
		}
		else {
			var Otmp = {};
			Otmp.id = tbat_id;
			Otmp.name = ThietBiAnToan[tbat_id].name;
			$scope.ThietBiAnToanChecked[tbat_id] = Otmp; 
		}			
		// console.log($scope["ThietBiAnToanChecked"]);	
	}
	$scope.tnChange = function(tn_id){
		// console.log(e);		
		if(typeof $scope.TienNghiChecked[tn_id] !== "undefined"){
			delete $scope.TienNghiChecked[tn_id];			
		}
		else {
			var Otmp = {};
			Otmp.id = tn_id;
			Otmp.name = TienNghi[tn_id].name;
			$scope.TienNghiChecked[tn_id] = Otmp; 
		}
		// console.log($scope.TienNghiChecked);	
	}
	$scope.GotoStep2 = function() {
		$(".post-step-1").hide();
		$(".post-step-2").show();
		showReviewBlock($scope, $scope.SubCatItemSelected.id);
	}
	$scope.GotoStep1 = function() {
		$(".post-step-1").show();
		$(".post-step-2").hide();
	}
	/*$scope.catnamettClick = function(e){
		console.log("cat name tt");
		console.log(e);
		$(e.target.offsetParent.offsetParent).toggleClass("chosen-with-drop");
	}*/
});

var showHangSanXuat = function($scope, hangSXId){
	
	if(typeof(HangSXDienTu[hangSXId]) !== 'undefined'){
		$scope.hangsanxuat = HangSXDienTu[hangSXId];
		$(".hang-san-xuat").show();
	}
	else {
		$scope.hangsanxuat = {};
		$(".hang-san-xuat").hide();	
	}
}

var showRelateBlock = function(scope, subCatId) {
	if(typeof(blockDisplay[subCatId]) !== 'undefined'){
		$(".dang-tin-bds").css("display",blockDisplay[subCatId]["dang-tin-bds"]);
		if(blockDisplay[subCatId]["dang-tin-xe"] == "block"){
			scope.hangxe = HangXe;
			scope.dongxe = DongXe;
			scope.namsanxuat = NamSanXuat;
			scope.xuatxu = XuatXu;
			scope.hopso = HopSo;
			scope.kieudandong = KieuDanDong;
			scope.nhienlieu = NhienLieu;
			scope.mauxe = MauXe;
			scope.thietbiantoan = ThietBiAnToan;
			scope.tiennghi = TienNghi;
			scope.hangxe = HangXe[subCatId];
			scope.ThietBiAnToanChecked = {};			
			scope.TienNghiChecked = {};
			$(".hang-xe").css("display",blockDisplay[subCatId]["hang-xe"]);
			$(".dang-tin-xe").css("display",blockDisplay[subCatId]["dang-tin-xe"]);
		}
		//chi dung cho dang tin ban xe máy, oto
		if((subCatId === "3.1" || subCatId === "4.1") && blockDisplay[subCatId]["hang-xe"] == "block"){
			scope.hangxe = HangXe[subCatId];
			$(".hang-xe").css("display",blockDisplay[subCatId]["hang-xe"]);
		}
		else $(".hang-xe").css("display",blockDisplay[subCatId]["hang-xe"]);
		
		if(blockDisplay[subCatId]["dang-tin-viec-lam"] == "block"){
			scope.hinhthuclv = HinhThucLV;
			scope.nganhnghe = NganhNghe;
			scope.gioitinh = GioiTinh;
			scope.namsinh = NamSinh;
			scope.dotuoi = DoTuoi;
			$(".dang-tin-viec-lam").css("display",blockDisplay[subCatId]["dang-tin-viec-lam"]);
		}	
		// console.log(blockDisplay[subCatId]["dang-tin-viec-lam"]);
		if(typeof(blockDisplay[subCatId]["gia"]) !== 'undefined'){
			$(".gia").css("display",blockDisplay[subCatId]["gia"]);
		}
		//chi danh cho viec tim nguoi
		if(typeof(blockDisplay[subCatId]["dotuoi"]) !== 'undefined'){
			$(".dotuoi").css("display",blockDisplay[subCatId]["dotuoi"]);
		}
		//chi danh cho bds
		if(typeof(blockDisplay[subCatId]["loaibds"]) !== 'undefined'){
			$(".loaibds").css("display",blockDisplay[subCatId]["loaibds"]);
		}	
	}
	// console.log(LoaiBDS);
	//hien thi loai bds
	if(typeof(LoaiBDS[subCatId]) !== 'undefined'){
		scope.loaibds = LoaiBDS[subCatId];			
	}			
}

var showChiTietBDS = function(scope, loaibds){
	if(typeof(ChiTietBDS[loaibds]) !== 'undefined'){
		// console.log("loaibds id:" + loaibds);
		for(i=1;i<=7;i++)
		{			
			ct = "ct"+i;
			if(typeof(ChiTietBDS[loaibds][ct]) !== 'undefined'){
				if(i===3){
					scope.huong = Huong;
				}				
				$("input[name="+ct+"]").attr("placeholder", ChiTietBDS[loaibds][ct].label);
				$("."+ct+"-name").text(ChiTietBDS[loaibds][ct].name);
				$("."+ct).show();				
			}	
		}
		scope.phaply = PhapLy;
		// console.log(ChiTietBDS[loaibds]['ttmr']);
		$(".ttmr-text").css("display", ChiTietBDS[loaibds]['ttmr'].ttmr_text);	
		$(".thong-tin-mo-rong-bds").css("display", ChiTietBDS[loaibds]['ttmr'].thong_tin_mo_rong_bds);
	}
}		

var showReviewBlock = function(scope, subCatId) {
	if(typeof(reviewDisplay[subCatId]) !== 'undefined'){
		$(".rv-dang-tin-bds").css("display",reviewDisplay[subCatId]["rv-dang-tin-bds"]);
		if(reviewDisplay[subCatId]["rv-dang-tin-xe"] == "block"){			
			// $(".rv-hang-xe").css("display",reviewDisplay[subCatId]["rv-hang-xe"]);
			$(".rv-dang-tin-xe").css("display",reviewDisplay[subCatId]["rv-dang-tin-xe"]);
			//chi dung cho dang tin ban xe máy, oto
			if((subCatId === "3.1" || subCatId === "4.1") && reviewDisplay[subCatId]["rv-hang-xe"] == "block"){			
				$(".rv-hang-xe").css("display",reviewDisplay[subCatId]["rv-hang-xe"]);
			}
			else $(".rv-hang-xe").css("display",reviewDisplay[subCatId]["rv-hang-xe"]);
		}		
		
		if(reviewDisplay[subCatId]["rv-dang-tin-viec-lam"] == "block"){			
			$(".rv-dang-tin-viec-lam").css("display",reviewDisplay[subCatId]["rv-dang-tin-viec-lam"]);
		}	
		// console.log(reviewDisplay[subCatId]["dang-tin-viec-lam"]);
		if(typeof(reviewDisplay[subCatId]["rv-gia"]) !== 'undefined'){
			$(".rv-gia").css("display",reviewDisplay[subCatId]["rv-gia"]);
		}
		//chi danh cho viec tim nguoi
		if(typeof(reviewDisplay[subCatId]["rv-dotuoi"]) !== 'undefined'){
			$(".rv-dotuoi").css("display",reviewDisplay[subCatId]["rv-dotuoi"]);
		}
		//chi danh cho bds
		if(typeof(reviewDisplay[subCatId]["rv-loaibds"]) !== 'undefined'){
			$(".rv-loaibds").css("display",reviewDisplay[subCatId]["rv-loaibds"]);
		}	
	}				
}