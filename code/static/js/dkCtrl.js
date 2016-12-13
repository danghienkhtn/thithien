app.controller('dkCtrl', function($scope) {
	$scope.email="<?= $this->email; ?>";
	$scope.fullname="<?= $this->fullname; ?>";
	$scope.inserted="<?= $this->registOK; ?>";
}