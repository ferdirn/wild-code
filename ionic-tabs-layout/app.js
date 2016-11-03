angular.module('app', ['ionic'])

.controller('DashCtrl', function($scope) {

})  

.controller('ChatsCtrl', function($scope) {

})  

.controller('AccountCtrl', function($scope) {

})

.controller('ChatDetailCtrl', function($scope) {

})  

.config(function($stateProvider, $urlRouterProvider) {
  $stateProvider
  // Set up an abstract state for the tabs directive.
  .state('tab', {
    url: "/tab",
    abstract: true,
    templateUrl: "templates/tabs.html"
  })
  
  // Each tab has its own nav history stack
  .state('tab.dash', {
    url: "/dash",
    views: {
      'tab-dash': {
        templateUrl: 'templates/tab-dash.html',
        controller: 'DashCtrl'
      }
    }
  })
  
  .state('tab.chats', {
    url: '/chats',
    views: {
      'tab-chats': {
        templateUrl: 'templates/tab-chats.html',
        controller: 'ChatsCtrl'
      }
    }
  })
  
  .state('tab.chatDetail', {
    url: '/chatDetail',
    views: {
      'tab-chats': {
        templateUrl: 'templates/tab-chat-details.html',
        controller: 'ChatDetailCtrl'
      }
    }
  })
  
  .state('tab.account', {
    url: '/account',
    views: {
      'tab-account': {
        templateUrl: 'templates/tab-account.html',
        controller: 'AccountCtrl'
      }
    }
  })
  
  // If none of the above states are matched, use this as the fallback:
  $urlRouterProvider.otherwise('/tab/dash');
})
