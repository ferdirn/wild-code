angular.module('app', ['ionic'])

.controller('AppCtrl', function($scope) {

  $scope.items = [
    { id: 0 },
    { id: 1 },
    { id: 2 },
    { id: 3 },
    { id: 4 },
    { id: 5 },
    { id: 6 },
    { id: 7 },
    { id: 8 },
    { id: 9 },
    { id: 10 }
  ];
  
  $scope.doRefresh = function() {
    var newId = $scope.items[0].id - 1;
    $scope.items.unshift({ id: newId });
    $scope.$broadcast('scroll.refreshComplete');
  };
  
});
