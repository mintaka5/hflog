var stationsApp = angular.module('stationsApp', [
    'anguFixedHeaderTable',
    'ui.bootstrap',
    'ui.mask',
    'ui.validate'
], function ($httpProvider) {
    // Use x-www-form-urlencoded Content-Type
    $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';

    /**
     * The workhorse; converts an object to x-www-form-urlencoded serialization.
     * @param {Object} obj
     * @return {String}
     */
    var param = function (obj) {
        var query = '', name, value, fullSubName, subName, subValue, innerObj, i;

        for (name in obj) {
            value = obj[name];

            if (value instanceof Array) {
                for (i = 0; i < value.length; ++i) {
                    subValue = value[i];
                    fullSubName = name + '[' + i + ']';
                    innerObj = {};
                    innerObj[fullSubName] = subValue;
                    query += param(innerObj) + '&';
                }
            }
            else if (value instanceof Object) {
                for (subName in value) {
                    subValue = value[subName];
                    fullSubName = name + '[' + subName + ']';
                    innerObj = {};
                    innerObj[fullSubName] = subValue;
                    query += param(innerObj) + '&';
                }
            }
            else if (value !== undefined && value !== null)
                query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
        }

        return query.length ? query.substr(0, query.length - 1) : query;
    };

    // Override $http service's default transformRequest
    $httpProvider.defaults.transformRequest = [function (data) {
        return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
    }];
});

stationsApp.run(['$rootScope', function ($rootScope) {
    $rootScope.stationsUrl = globals.ajaxpath + 'admin_station.php';
    $rootScope.locationsUrl = globals.ajaxpath + 'admin_location.php';
    $rootScope.languagesUrl = globals.ajaxpath + 'admin_languages.php';
}]);

stationsApp.service('LanguagesService', function ($http, $rootScope) {
    this.getAll = function () {
        var promise = $http.post($rootScope.languagesUrl, {
            _mode: 'get',
            _task: 'all'
        }).then(function (response) {
            return response.data;
        });

        return promise;
    };
});

stationsApp.service('StationsService', function ($http, $rootScope) {
    this.getAll = function () {
        var promise = $http.post($rootScope.stationsUrl, {
            _mode: 'get',
            _task: 'all'
        }).then(function (response) {
            return response.data;
        });

        return promise;
    };

    this.deactivate = function (stationId) {
        var promise = $http.post($rootScope.stationsUrl, {
            _mode: 'active',
            _task: 'no',
            id: stationId
        }).then(function (response) {
            return response.data;
        });

        return promise;
    };

    this.activate = function (stationId) {
        var promise = $http.post($rootScope.stationsUrl, {
            _mode: 'active',
            _task: 'yes',
            id: stationId
        }).then(function (response) {
            return response.data;
        });

        return promise;
    };

    this.getInactive = function () {
        var promise = $http.post($rootScope.stationsUrl, {
            _mode: 'get',
            _task: 'inactive'
        }).then(function (response) {
            return response.data;
        });

        return promise;
    };

    this.delete = function (stationId) {
        var promise = $http.post($rootScope.stationsUrl, {
            _mode: 'delete',
            id: stationId
        }).then(function (response) {
            return response.data;
        });

        return promise;
    };

    this.new = function (stationTitle) {
        var promise = $http.post($rootScope.stationsUrl, {
            _mode: 'add',
            name: stationTitle
        }).then(function (response) {
            return response.data;
        });

        return promise;
    };

    this.update = function (stnName, stnTitle) {
        var promise = $http.post($rootScope.stationsUrl, {
            _mode: 'update',
            name: stnName,
            title: stnTitle
        }).then(function (response) {
            return response.data;
        });

        return promise;
    };

    this.getOne = function(id) {
        var promise = $http.post($rootScope.stationsUrl, {
            _mode: 'get',
            _task: 'one',
            'id': id
        }).then(function (response) {
            return response.data;
        });

        return promise;
    };
});

stationsApp.service('LocationsService', function ($http, $rootScope) {
    this.getAll = function () {
        var promise = $http.post($rootScope.locationsUrl, {
            _mode: 'get',
            _task: 'all'
        }).then(function (response) {
            return response.data;
        });

        return promise;
    };

    this.getAllByStation = function (stationId) {
        var promise = $http.post($rootScope.locationsUrl, {
            _mode: 'get',
            _task: 'bystation',
            id: stationId
        }).then(function (response) {
            return response.data;
        });

        return promise;
    };

    this.getInactive = function () {
        var promise = $http.post($rootScope.locationsUrl, {
            _mode: 'get',
            _task: 'inactive'
        }).then(function (response) {
            return response.data;
        });

        return promise;
    };

    this.delete = function (locationId) {
        var promise = $http.post($rootScope.locationsUrl, {
            _mode: 'delete',
            id: locationId
        }).then(function (response) {
            return response.data;
        });

        return promise;
    };

    this.activate = function (locationId) {
        var promise = $http.post($rootScope.locationsUrl, {
            _mode: 'active',
            _task: 'yes',
            id: locationId
        }).then(function (response) {
            return response.data;
        });

        return promise;
    };

    this.deactivate = function (locationId) {
        var promise = $http.post($rootScope.locationsUrl, {
            _mode: 'active',
            _task: 'no',
            id: locationId
        }).then(function (response) {
            return response.data;
        });

        return promise;
    };

    this.new = function(location) {
        var promise = $http.post($rootScope.locationsUrl, {
            _mode: 'add',
            _task: 'admin',
            data: location
        }).then(function (response) {
            return response.data;
        });

        return promise;
    };

    this.update = function(location) {
        var promise = $http.post($rootScope.locationsUrl, {
            _mode: 'update',
            data: location
        }).then(function (response) {
            return response.data;
        });

        return promise;
    };
});

stationsApp.controller('MainController', ['$scope', '$http', 'StationsService', 'LocationsService', 'LanguagesService', '$uibModal', function ($scope, $http, StationsService, LocationsService, LanguagesService, $uibModal) {
    $scope.init = function () {
        StationsService.getAll().then(function (stations) {
            $scope.stations = stations;
        });

        LocationsService.getAll().then(function (locations) {
            $scope.locations = locations;
        });

        LanguagesService.getAll().then(function (languages) {
            $scope.languages = languages;
        });
    };

    $scope.showAllLocations = function () {
        LocationsService.getAll().then(function (locations) {
            $scope.locations = locations;
        });
    };

    $scope.showInactiveLocations = function () {
        LocationsService.getInactive().then(function (locations) {
            $scope.locations = locations;
        });
    };

    $scope.showAllStations = function () {
        StationsService.getAll().then(function (stations) {
            $scope.stations = stations;
            $scope.stationQuery = '';
        });
    };

    $scope.showInactiveStations = function () {
        StationsService.getInactive().then(function (stations) {
            $scope.stations = stations;
            $scope.stationQuery = '';
        });
    };

    $scope.selectStation = function (stationId) {
        LocationsService.getAllByStation(stationId).then(function (locations) {
            $scope.locations = locations;
        });
    };

    $scope.setStationActive = function () {
        var stationId = this.station.id;
        StationsService.activate(stationId).then(function (data) {
            StationsService.getAll().then(function (stations) {
                $scope.stations = stations;
            });
        });
    }

    $scope.setStationInactive = function () {
        var stationId = this.station.id;
        StationsService.deactivate(stationId).then(function (data) {
            StationsService.getAll().then(function (stations) {
                $scope.stations = stations;
            });
        });
    };

    $scope.deleteStation = function () {
        var stationId = this.station.id;
        StationsService.delete(stationId).then(function (data) {
            StationsService.getAll().then(function (stations) {
                $scope.stations = stations;
            });
        });
    };

    $scope.deleteLocation = function () {
        var locationId = this.location.id;
        LocationsService.delete(locationId).then(function (data) {
            LocationsService.getAll().then(function (locations) {
                $scope.locations = locations;
            });
        });
    };

    $scope.setLocationActive = function () {
        var locationId = this.location.id;
        LocationsService.activate(locationId).then(function (data) {
            LocationsService.getAll().then(function (locations) {
                $scope.locations = locations;
            });
        });
    };

    $scope.setLocationInactive = function () {
        var locationId = this.location.id;
        LocationsService.deactivate(locationId).then(function (data) {
            LocationsService.getAll().then(function (locations) {
                $scope.locations = locations;
            });
        });
    };

    $scope.openStationForm = function () {
        var modalInstance = $uibModal.open({
            templateUrl: 'station_form.html',
            backdrop: 'static',
            controller: 'StationFormController',
            size: 'sm',
            resolve: {
                save: function () {
                    return null;
                }
            }
        });

        modalInstance.result.then(function (item) {
            StationsService.getAll().then(function (stations) {
                $scope.stations = stations;
                $scope.stationQuery = '';
            });
        }, function () {
        });
    };

    $scope.openStationEdit = function () {
        var station = this.station;

        var modalInstance = $uibModal.open({
            templateUrl: 'station_form.html',
            backdrop: 'static',
            controller: 'StationFormController',
            scope: $scope,
            size: 'sm',
            resolve: {
                save: function () {
                    return {
                        station: {
                            title: station.title,
                            station_name: station.station_name
                        }
                    };
                }
            }
        });

        modalInstance.result.then(function (item) {
            StationsService.getAll().then(function (stations) {
                $scope.stations = stations;
            });
        }, function () {
        });
    };

    $scope.openLocationForm = function () {
        var modalInstance = $uibModal.open({
            templateUrl: 'location_form.html',
            backdrop: 'static',
            controller: 'LocationFormController',
            scope: $scope,
            size: 'lg',
            resolve: {
                save: function () {
                    return null;
                }
            }
        });

        modalInstance.result.then(function (item) {
            LocationsService.getAll().then(function (locations) {
                $scope.locations = locations;
            });
        }, function () {});
    };
    
    $scope.openEditLocationForm = function () {
        var location = this.location;

        StationsService.getOne(location.station_id).then(function (station) {
            var stn = station;
            var loc = location;
            var modalInstance = $uibModal.open({
                templateUrl: 'location_form.html',
                backdrop: 'static',
                controller: 'LocationFormController',
                scope: $scope,
                size: 'lg',
                resolve: {
                    save: function() {
                        var l = loc;
                        l.station = stn;

                        return {location: l};
                    }
                }
            });

            modalInstance.result.then(function (item) {
                LocationsService.getAll().then(function (locations) {
                    $scope.locations = locations;
                });
            }, function () {});
        });
    }
}]);

stationsApp.controller('LocationFormController', function ($scope, $uibModalInstance, LocationsService, StationsService, save) {
    $scope.save = save;

    StationsService.getAll().then(function(stations) {
        $scope.stations = stations;
    });

    $scope.cancelLocation = function () {
        $uibModalInstance.close();
    };

    $scope.saveLocations = function () {
        if(!$scope.save.location.id) {
            // new
            LocationsService.new($scope.save.location).then(function (data) {});
        } else {
            console.log($scope.save.location);
            LocationsService.update($scope.save.location).then(function (data) {});
        }

        $uibModalInstance.close($scope.save);
    };
});

stationsApp.controller('StationFormController', function ($scope, $uibModalInstance, StationsService, save) {
    $scope.save = save;

    $scope.cancelStation = function () {
        $uibModalInstance.close();
    };

    $scope.saveStation = function () {
        if (!$scope.save.station.station_name) {
            // new
            StationsService.new($scope.save.station.title).then(function (data) {
            });
        } else {
            // update
            var stnName = $scope.save.station.station_name;
            var stnTitle = $scope.save.station.title;
            StationsService.update(stnName, stnTitle).then(function (data) {
            });
        }

        $uibModalInstance.close($scope.save);
    };
});