angular.module('app', ['ionic'])

.controller('AppCtrl', function($scope, $ionicActionSheet) {
  $scope.showActionSheet = function() {
    // Show the action sheet
    $ionicActionSheet.show({
      buttons: [{
        text: '<b>Share</b> This'
      }, {
        text: 'Move'
      }],
      destructiveText: 'Delete',
      cancelText: 'Cancel',
      titleText: 'Modify your album',
      cancel: function() {
        alert("Clicked Cancel");
      },
      destructiveButtonClicked: function() {
        alert("Clicked Delete");
        return true;
      },
      buttonClicked: function(index, buttonObj) {
        switch (index) {
          case 0:
            alert("Clicked Share");
            return false;
          case 1:
            alert("Clicked Move");
            return false;
        }
      }
    });
  };
});

