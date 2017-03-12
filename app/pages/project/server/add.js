"use strict";

angular.module('AppProjectServerAdd', [
    'ngRoute',
]).config([
    '$routeProvider',
    function ($routeProvider) {
        $routeProvider.when('/view/:id/:name/server/add', {
            templateUrl: 'app/pages/project/server/add.html',
            controller: 'createServerController',
        }).when('/view/:id/:name/server/:server_id', {
            templateUrl: 'app/pages/project/server/add.html',
            controller: 'createServerController',
        });
    }
]).controller('createServerController', [
    '$scope',
    '$rootScope',
    '$routeParams',
    'Utils',
    'Api',
    '$window',
    '$q',
    function ($scope, $rootScope, $routeParams, Utils, Api, $window, $q) {
        $scope.project_id = $routeParams.id;
        $scope.server_id = $routeParams.server_id;

        if ($scope.server_id) {
            $scope.page = 'view-server';
            Utils.setTitle('View server');
        }
        else {
            $scope.page = 'new-server';
            Utils.setTitle('Add new server');
        }

        $scope.server = {
            type: 1,
            port: 21,
            secure: true,
        };

        $scope.typeChange = function () {
            if (angular.isDefined($scope.form.port)) {
                if ($scope.form.port.$pristine) {
                    if ($scope.server.type == 1)
                        $scope.server.port = 21;
                    else if ($scope.server.type == 2)
                        $scope.server.port = 22;
                }
            }
        };

        $scope.saving = false;
        $scope.newServer = function () {
            $scope.saving = true;
            $scope.testConnection().then(function () {
                // ok tested.
                var s = angular.copy($scope.server);
                var server = {};
                server['name'] = s.name;
                server['branch'] = s.branch;
                server['auto_deploy'] = s.auto_deploy;
                server['type'] = s.type;
                server['id'] = s.id;

                if (s.type == 1 || s.type == 2) {
                    server['host'] = s.host;
                    server['port'] = s.port;
                    server['username'] = s.username;
                    server['password'] = s.password;
                    server['edit_password'] = s.edit_password;
                    server['path'] = s.path;
                    if (s.type == 1) {
                        server['secure'] = s.secure;
                    }
                }

                Api.createServer($scope.project_id, server).then(function () {
                    $scope.saving = false;
                }, function () {
                    $scope.saving = false;
                });
            }, function () {
                $scope.saving = false;
            });
        };

        $scope.testingConnection = false;
        $scope.testingErrorMessage = false;
        $scope.testingMessage = false;
        $scope.testConnection = function () {
            var defer = $q.defer();
            var server = {};
            var s = angular.copy($scope.server);
            server['type'] = s.type;
            server['id'] = s.id;
            if (s.type == 1 || s.type == 2) {
                server['host'] = s.host;
                server['port'] = s.port;
                server['username'] = s.username;
                server['password'] = s.password;
                server['edit_password'] = s.edit_password;
                server['path'] = s.path;
                if (s.type == 1) {
                    server['secure'] = s.secure;
                }
            }

            $scope.testingMessage = '';
            $scope.testingErrorMessage = '';

            $scope.testingConnection = true;
            Api.testServerConnectionByData(server).then(function (data) {
                $scope.testingConnection = false;
                $scope.testingMessage = 'Successfully connected. ';
                if (!data.empty) {
                    $scope.testingMessage += ' NOTE: The deploy path is not empty.'
                }
                $scope.isEmpty = data;
                defer.resolve();
            }, function (reason) {
                $scope.testingErrorMessage = reason;
                // Utils.error(reason, 'red', $scope.testConnection);
                $scope.testingConnection = false;
                defer.reject();
            });

            return defer.promise;
        };

        $scope.loadingBranches = false;
        $scope.branches = [];
        $scope.loadBranches = function () {
            $scope.loadingBranches = true;
            $scope.branches = [];
            Api.getAvailableBranchesByProject($scope.project_id).then(function (branches) {
                $scope.branches = branches;
                $scope.loadingBranches = false;
            }, function (reason) {
                Utils.error(reason, 'red', $scope.loadBranches);
                $scope.loadingBranches = false;
            });
        };
        $scope.loadBranches();


        $scope.loading = false;
        $scope.server_name = false;
        $scope.server.edit_password = true;

        $scope.load = function () {
            $scope.loading = true;
            Api.getServer($scope.server_id).then(function (data) {
                $scope.server = data;
                $scope.server_name = $scope.server.name;
                $scope.server.auto_deploy = data.auto_deploy == '1';
                $scope.server.secure = data.secure == '1';
                $scope.loading = false;
                $scope.server.edit_password = false;
            }, function (reason) {
                $scope.loading = false;
                Utils.error(reason, 'red', $scope.load);
            });
        };
        if ($scope.server_id) {
            $scope.load();
        }

    }
]);