angular.module('app', ['ionic'])

.controller('Page1Ctrl', function($scope) {
  $scope.myTitle = 'Hal 1';
})

.controller('Page2Ctrl', function($scope) {

})

.controller('Page3Ctrl', function($scope) {

})

.config(function($stateProvider, $urlRouterProvider) {
  $stateProvider
    .state('page1', {
      url: "/page1",
      templateUrl: "templates/page1.html",
      controller: 'Page1Ctrl'
    })
    .state('page2', {
      url: "/page2",
      templateUrl: "templates/page2.html",
      controller: 'Page2Ctrl'
    })
    .state('page3', {
      url: "/page3",
      templateUrl: "templates/page3.html",
      controller: 'Page3Ctrl'
    });

  // If none of the above states are matched, use this as the fallback:
  $urlRouterProvider.otherwise('page1');
});

