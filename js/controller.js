var app = angular.module('winesApp', ["ngRoute", "ngResource"]);

app.config(function($routeProvider, $locationProvider) {
    $routeProvider.when('/wines', {
        templateUrl: 'templates/wine-list.html',
        controller: "WinesController"
    }).when('/wines/:wineId/details', {
        templateUrl: 'templates/wine-details.html',
        controller: "WinesDetailsController"
    }).when('/wines/add/', {
        templateUrl: 'templates/wine-add.html',
        controller: "WinesController"
    }).when('/wines/:wineId/edit', {
        templateUrl: 'templates/wine-add.html',
        controller: "WinesDetailsController"
    }).otherwise({
        redirectTo: '/wines'
    });

    // $locationProvider.html5Mode(true);
});

app.service('wineService', function($resource) {
    return $resource('api/public/wines/:wineId', {}, {
        update: { method: 'PUT' }
    })
})

app.run(function($rootScope) {
    $rootScope.addWine = function() {
        window.location = '#!/wines/add';
    }
});

app.controller('WinesController', function($scope, wineService, $location, $route) {
    $scope.wines = wineService.query();

    $scope.submitForm = function(isValid) {
        if (isValid) {
            wineService.save({
                "name": $scope.wine.name,
                "grapes": $scope.wine.grapes,
                "country": $scope.wine.country,
                "region": $scope.wine.region,
                "year": $scope.wine.year,
                "description": $scope.wine.description,
                "picture": $scope.wine.picture
            });
            $location.path('/wines');
            $route.reload();
        }
    }
});

app.controller('WinesDetailsController', function($scope, $routeParams, wineService, $location, $route) {
    $scope.wine = wineService.get({
        wineId: $routeParams.wineId
    });

    $scope.submitForm = function(isValid) {
        if (isValid) {
            $scope.saveWine();
            $location.path('/wines/'+$scope.wine.id);
            $route.reload();
        }
    }

    $scope.saveWine = function() {
        if ($scope.wine.id > 0) {
            $scope.wine.$update({
                wineId: $scope.wine.id
            });
        } else {
            $scope.wine.$save();
        }
    }

    $scope.deleteWine = function() {
        $scope.wine.$delete({
            wineId: $scope.wine.id
        }, function() {
            $location.path('/wines/');
            $route.reload();
        });
    }
});

app.directive('stringToNumber', function() {
    return {
      require: 'ngModel',
      link: function(scope, element, attrs, ngModel) {
        ngModel.$parsers.push(function(value) {
          return '' + value;
        });
        ngModel.$formatters.push(function(value) {
          return parseFloat(value);
        });
      }
    };
  })