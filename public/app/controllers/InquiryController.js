//app.controller('AdminController', function($scope,$http){
// 
//  $scope.pools = [];
//   
//});
var app = angular.module('autoparts', [], function($interpolateProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
}).constant('API_URL','http://localhost/clients/prinquiry/public');

app.controller('appCtrl', function($scope) {
    $scope.test = { text: 'Hello' };
});

app.controller('InquiryController', function($scope, $http, API_URL) {
    // retrieve Supplier listing from API
    $http.get(API_URL + "/inquiry/suppliers")
    .success(function(response){
        $scope.suppliers = response;
        //show more functionality

        var pagesShown = 1;

        var pageSize = 5;

        $scope.paginationLimit = function(data) {
         return pageSize * pagesShown;
        };

        $scope.hasMoreItemsToShow = function() {
            return pagesShown < ($scope.suppliers.length / pageSize);
        };
        
        $scope.filterFunction = function(element) {
            console.log(element);
            return element.name.match(/^Ma/) ? true : false;
        };
        
        $scope.showMoreItems = function() {
            pagesShown = pagesShown + 1;       
        }; 
    });
});
