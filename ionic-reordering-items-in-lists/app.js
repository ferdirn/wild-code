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
  
  $scope.moveItem = function(item, fromIndex, toIndex)   {
    $scope.items.splice(fromIndex, 1);
    $scope.items.splice(toIndex, 0, item);
  };
});

