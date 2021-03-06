app.factory('searchService', ['$http','$log','$q', function($http, $log, $q){
	// Use x-www-form-urlencoded Content-Type
    $http.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
	return {
		search: function(ttId, catId, txtSearch){		
			var params = {
				'cityId': ttId,
				'catId': catId,
				'txtSearch': txtSearch
			};			
			var defer = $q.defer();
			var paramater = $.param(params);
			$http.post('/api/search', paramater).success(function(data){
					defer.resolve(data);
				});
			return defer.promise;	
		},
		getNews: function(isVip, iOffset, iLimit){
			var params = {
				'isVip': isVip,
				'iOffset': iOffset,
				'iLimit': iLimit
			};
			var defer = $q.defer();
			$http.post('/api/search/get-news', $.param(params))
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