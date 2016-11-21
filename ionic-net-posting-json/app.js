angular.module('app', ['ionic', 'ngResource'])

.controller('AppCtrl', function($scope, Post) {
  // Our form data for creating a new post with ng-model
  $scope.postData = {};
  $scope.newPost = function() {
    var post = new Post($scope.postData);
    post.$save(function(postObject) {
      alert(JSON.stringify(postObject));
    });
  };
})

.factory('Post', function($resource) {
  return $resource('http://jsonplaceholder.typicode.com/posts');
});
