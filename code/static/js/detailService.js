app.factory('detailService', ['$http','$log','$q', function($http, $log, $q){
	// Use x-www-form-urlencoded Content-Type
    $http.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
	return {
		getOther: function(nid, catId){		
			var params = {
				'nid': nid,
				'catId': catId
			};			
			var defer = $q.defer();
			var paramater = $.param(params);
			$http.post('/api/search/getOther', paramater).success(function(data){
					defer.resolve(data);
				});
			return defer.promise;	
		},
		getDetail: function(nid){
			var params = {
				'nid': nid
			};
			var defer = $q.defer();
			$http.post('/api/search/get-detail-news', $.param(params))
				.success(function (data){
					defer.resolve(data);
				})
				.error(function (msg, code){
					defer.reject(msg);
					$log.error(msg, code);
				});
			return defer.promise;
		}
	};
}]);