app.factory('registService', function($http, $log, $q){
	// Use x-www-form-urlencoded Content-Type
    $http.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
	return {
		regist : function(email, password, confirmpassword, fullname){			
			var params = {
				'sEmail': email,
				'sPassword': password,
				'sConfirmpassword': confirmpassword,
				'sFullname': fullname || email,
				'sRecaptcha': $("#g-recaptcha-response").val()
			};			
			var defer = $q.defer();
			$http.post('/api/user/regist', $.param(params))
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
				.error(function (msg, code){
					defer.reject(msg);
					$log.error(msg, code);
				});
			return defer.promise;
		}
	}
});