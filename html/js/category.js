/*var DanhMuc = {
	batdongsan:
		{
			id:"1",
			name:"Bất động sản",
			icon:"",
			vitri:"1"
		},
	vieclamtuyensinh: {
		id:"2",
		name:"Việc làm, Tuyển sinh",
		icon:"",
		vitri:"2"
	},
	oto:{
		id:"3",
		name:"Ôtô",
		icon:"",
		vitri:"3"
	},
	xemay:{
		id:"4",
		name:"Xe máy",
		icon:"",
		vitri:"4"
	},
	dichvu:{
		id:"5",
		name:"Dịch vụ",
		icon:"",
		vitri:"5"
	},
	dodientu:{
		id:"6",
		name:"Đồ điện tử",
		icon:"",
		vitri:"6"
	},
	dienmaygiadung:{
		id:"7",
		name:"Điện máy, Đồ gia dụng",
		icon:"",
		vitri:"7"
	},
	sothichmathangkhac:{
		id:"8",
		name:"Sở thích, Mặt hàng khác",
		icon:"",
		vitri:"8"
	},
	thoitrangmypham:{
		id:"9",
		name:"Thời trang, Mỹ phẩm",
		icon:"",
		vitri:"9"
	},
	doitaccongdong:{
		id:"10",
		name:"Đối tác, Cộng đồng",
		icon:"",
		vitri:"10"
	}
};*/

var DanhMuc = [
		{
			id:"1",
			name:"Bất động sản",
			icon:"",
			vitri:"1"
		},
		{
		id:"2",
		name:"Việc làm, Tuyển sinh",
		icon:"",
		vitri:"2"
	},
	{
		id:"3",
		name:"Ôtô",
		icon:"",
		vitri:"3"
	},
	{
		id:"4",
		name:"Xe máy",
		icon:"",
		vitri:"4"
	},
	{
		id:"5",
		name:"Dịch vụ",
		icon:"",
		vitri:"5"
	},
	{
		id:"6",
		name:"Đồ điện tử",
		icon:"",
		vitri:"6"
	},
	{
		id:"7",
		name:"Điện máy, Đồ gia dụng",
		icon:"",
		vitri:"7"
	},
	{
		id:"8",
		name:"Sở thích, Mặt hàng khác",
		icon:"",
		vitri:"8"
	},
	{
		id:"9",
		name:"Thời trang, Mỹ phẩm",
		icon:"",
		vitri:"9"
	},
	{
		id:"10",
		name:"Đối tác, Cộng đồng",
		icon:"",
		vitri:"10"
	}
];


var TinhThanh = [
	{id:"0",name:"Toàn quốc",icon:"",vitri:"1"},
	{id:"1",name:"TP.HCM",icon:"",vitri:"2"},
	{id:"2",name:"Hà Nội",icon:"",vitri:"3"},
	{id:"3",name:"Đà Nẵng",icon:"",vitri:"4"},
	{id:"4",name:"Hải Phòng",icon:"",vitri:"5"},
	{id:"5",name:"Cần Thơ",icon:"",vitri:"6"},
	{id:"6",name:"Bình Dương",icon:"",vitri:"7"},
	{id:"7",name:"An Giang",icon:"",vitri:"8"},
	{id:"8",name:"Bà Rịa - Vũng Tàu",icon:"",vitri:"9"},
	{id:"9",name:"Bắc Giang",icon:"",vitri:"10"},
	{id:"10",name:"Bắc Kạn",icon:"",vitri:"11"},
	{id:"11",name:"Bạc Liêu",icon:"",vitri:"12"},
	{id:"12",name:"Bắc Ninh",icon:"",vitri:"13"},
	{id:"13",name:"Bến Tre",icon:"",vitri:"14"},
	{id:"14",name:"Bình Định",icon:"",vitri:"15"},
	{id:"15",name:"Bình Phước",icon:"",vitri:"16"},
	{id:"16",name:"Bình Thuận",icon:"",vitri:"17"},
	{id:"17",name:"Cà Mau",icon:"",vitri:"18"},
	{id:"18",name:"Cao Bằng",icon:"",vitri:"19"},
	{id:"19",name:"Đắk Lắk",icon:"",vitri:"20"},
	{id:"20",name:"Đắk Nông",icon:"",vitri:"21"},
	{id:"21",name:"Điện Biên",icon:"",vitri:"22"},
	{id:"22",name:"Đồng Nai",icon:"",vitri:"23"},
	{id:"23",name:"Đồng Tháp",icon:"",vitri:"24"},
	{id:"24",name:"Gia Lai",icon:"",vitri:"25"},
	{id:"25",name:"Hà Giang",icon:"",vitri:"26"},
	{id:"26",name:"Hà Nam",icon:"",vitri:"27"},
	{id:"27",name:"Hà Tĩnh",icon:"",vitri:"28"},
	{id:"28",name:"Hải Dương",icon:"",vitri:"29"},
	{id:"29",name:"Hậu Giang",icon:"",vitri:"30"},
	{id:"30",name:"Hòa Bình",icon:"",vitri:"31"},
	{id:"31",name:"Hưng Yên",icon:"",vitri:"32"},
	{id:"32",name:"Khánh Hòa",icon:"",vitri:"33"},
	{id:"33",name:"Kiên Giang",icon:"",vitri:"34"},
	{id:"34",name:"Kon Tum",icon:"",vitri:"35"},
	{id:"35",name:"Lai Châu",icon:"",vitri:"36"},
	{id:"36",name:"Lâm Đồng",icon:"",vitri:"37"},
	{id:"37",name:"Lạng Sơn",icon:"",vitri:"38"},
	{id:"38",name:"Lào Cai",icon:"",vitri:"39"},
	{id:"39",name:"Long An",icon:"",vitri:"40"},
	{id:"40",name:"Nam Định",icon:"",vitri:"41"},
	{id:"41",name:"Nghệ An",icon:"",vitri:"42"},
	{id:"42",name:"Ninh Bình",icon:"",vitri:"43"},
	{id:"43",name:"Ninh Thuận",icon:"",vitri:"44"},
	{id:"44",name:"Phú Thọ",icon:"",vitri:"45"},
	{id:"45",name:"Phú Yên",icon:"",vitri:"46"},
	{id:"46",name:"Quảng Bình",icon:"",vitri:"47"},
	{id:"47",name:"Quảng Nam",icon:"",vitri:"48"},
	{id:"48",name:"Quảng Ngãi",icon:"",vitri:"49"},
	{id:"49",name:"Quảng Ninh",icon:"",vitri:"50"},
	{id:"50",name:"Quảng Trị",icon:"",vitri:"51"},
	{id:"51",name:"Sóc Trăng",icon:"",vitri:"52"},
	{id:"52",name:"Sơn La",icon:"",vitri:"53"},
	{id:"53",name:"Tây Ninh",icon:"",vitri:"54"},
	{id:"54",name:"Thái Bình",icon:"",vitri:"55"},
	{id:"55",name:"Thái Nguyên",icon:"",vitri:"56"},
	{id:"56",name:"Thanh Hóa",icon:"",vitri:"57"},
	{id:"57",name:"Thừa Thiên Huế",icon:"",vitri:"58"},
	{id:"58",name:"Tiền Giang",icon:"",vitri:"59"},
	{id:"59",name:"Trà Vinh",icon:"",vitri:"60"},
	{id:"60",name:"Tuyên Quang",icon:"",vitri:"61"},
	{id:"61",name:"Vĩnh Long",icon:"",vitri:"62"},
	{id:"62",name:"Vĩnh Phúc",icon:"",vitri:"63"},
	{id:"63",name:"Yên Bái",icon:"",vitri:"64"},
	{id:"64",name:"Nước Ngoài",icon:"",vitri:"65"},
];

/*DanhMuc.batdongsan.danhmuccon = {
	bandat: {id:"1.1", name:"Bán đất", icon:"", vitri:"1"},
	bannhacanho: {id:"1.2", name:"Bán nhà, Căn hộ", icon:"", vitri:"2"},
	sangnhuong: {id:"1.3", name:"Sang nhượng cửa hàng, Mặt bằng", icon:"", vitri:"3"},
	chothuenhadat: {id:"1.4", name:"Cho thuê nhà đất", icon:"", vitri:"4"},
	canmuanhadat: {id:"1.5", name:"Cần mua nhà đất", icon:"", vitri:"5"},
	dichvunhadat: {id:"1.6", name:"Dịch vụ nhà đất", icon:"", vitri:"6"},	
};

DanhMuc.vieclamtuyensinh.danhmuccon = {
	viectimnguoi: {id:"2.1", name:"Việc tìm người", icon:"", vitri:"1"},
	nguoitimviec: {id:"2.2", name:"Người tìm việc", icon:"", vitri:"2"},
	Dichvulaodong: {id:"2.3", name:"Dịch vụ lao động", icon:"", vitri:"3"},
	tuyensinhduhocdaotao: {id:"2.4", name:"Tuyển sinh, Du học, Đào tạo", icon:"", vitri:"4"},
};*/

// var app = angular.module('TTApp', ['ngRoute']).config(mctRouter);
//var appNewsDat = angular.module('TTApp',[]);
/*appNewsDat.controller('NewsDetailCtrl', function($scope) {
	console.log(TinhThanh[1].name);
	$scope.tinhthanh = TinhThanh;
	
});*/

var app = angular.module('TTApp',[]);
app.controller('TTCtrl', function($scope) {
	$scope.danhmuc = DanhMuc;
	$scope.tinhthanh = TinhThanh;
	$scope.bannertop = BannerTop;	
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
});

/*var appNewsDat = angular.module('TTApp',[]);
app.controller('NewsDetailCtrl', function($scope) {
	$scope.tinhthanh = TinhThanh;
	console.log(TinhThanh[1].name)
});*/

/*batdongsan = "batdongsan";
console.log(DanhMuc[batdongsan].danhmuccon.bandat);
for(item in DanhMuc)
{
	// alert(DanhMuc[item].name);
	console.log(DanhMuc[item]);
}*/