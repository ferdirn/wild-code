angular.module('app', ['ionic'])

.controller('AppCtrl', function($scope, $ionicPopup) {
  $scope.showAlert = function() {
    var alertPopup = $ionicPopup.alert({
      title: "Don't eat that!",
      template: "It might taste good",
      okText: "I won't",
      okType: "button-balanced"
    });
    
    alertPopup.then(function(res) {
      console.log('Thank you for not eating my delicious ice cream cone');
    });
  };
  
  $scope.showConfirm = function() {
    var confirmPopup = $ionicPopup.confirm({
      title: "Consume Ice Cream",
      template: "Are you sure you want to eat this ice cream?"
    });
    
    confirmPopup.then(function(res) {
      if (res) {
        console.log('You click OK');
      } else {
        console.log('You click Cancel');
      }
    });
  };
  
  $scope.showPrompt = function() {
    var promptPopup = $ionicPopup.prompt({
      title: "Password Check",
      template: "Enter your secret password",
      inputType: "password",
      inputPlaceholder: "Your password"
    });
    
    promptPopup.then(function(res) {
      // if user clicks cancel, it will print undefined
      console.log('Your password is', res);
    });
  };
  
  $scope.showCustom = function() {
    $scope.data = {};
    
    var customPopup = $ionicPopup.show({
      title: "Enter WiFi Password",
      template: '<input type="password" ng-model="data.wifi">',
      subTitle: "Please use normal things",
      scope: $scope,
      buttons: [{
        text: 'Cancel'
      }, {
        text: 'Save',
        type: 'button-positive',
        onTap: function(e) {
          if (!$scope.data.wifi) {
            // Don't allow the user to close unless they enter a WiFi password
            e.preventDefault();
          } else {
            return $scope.data.wifi;
          }
        }
      }]
    });
    
    customPopup.then(function(res) {
      console.log('Tapped!', res);
    });
  };
  
});

