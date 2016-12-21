app.factory('searchService', function($http, $log, $q){
	// Use x-www-form-urlencoded Content-Type
    $http.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
	return {
		search : function(ttId, catId, txtSearch){			
			var params = {
				'cityId': ttId,
				'catId': catId,
				'txtSearch': txtSearch
			};			
			var defer = $q.defer();
			$http.post('/api/search', $.param(params))
				.success(function (data){
					defer.resolve(data);
				})
				.error(function (msg, code){
					defer.reject(msg);
					$log.error(msg, code);
				});
			return defer.promise;			
		},
		checkUserExisted : function(email){
			var params = {
				sEmail: email
			};			
			var defer = $q.defer();
			$http.post('/api/user/check-user-existed', $.param(params))
				.success(function (data){
					defer.resolve(data);
				})
				.error(function (msg, colde){
					defer.reject(msg);
					$log.error(msg, code);
				});
			return defer.promise;
		}
	}
});