angular.module('app', ['ionic'])

.controller('AppCtrl', function($scope) {
  $scope.items = [];
  for (var i = 0; i<5000; i++) {
    $scope.items.push({
      id: i,
      nextId: i,
      anotherId: i
    });
  }
});

