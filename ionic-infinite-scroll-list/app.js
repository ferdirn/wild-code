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
  
  $scope.loadMore = function() {
    var itemsLength = $scope.items.length;
    for (var i = itemsLength; i < itemsLength + 6; i++) {
      $scope.items.push({
        id: $scope.items.length
      });
    }
    
    $scope.$broadcast('scroll.infiniteScrollComplete');
  };
  
  $scope.moreDataCanBeLoaded = function() {
    return $scope.items.length <= 100;
  };
});

