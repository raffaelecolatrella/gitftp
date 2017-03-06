"use strict";

angular.module('App', [
    'ngRoute',
    // 'AppHome',
    // 'ngAnimate',
    // 'Service.utils',
    // 'cp.ngConfirm',
    // 'ngStorage',
]).config([
    '$routeProvider',
    '$locationProvider',
    '$httpProvider',
    function ($routeProvider, $locationProvider, $httpProvider) {
        $routeProvider.otherwise({
            redirectTo: '/'
        });
        $locationProvider.html5Mode(true);
    }
]).run([
    '$rootScope',
    // 'Utils',
    // '$localStorage',
    // '$ngConfirmDefaults',
    function ($rootScope, Utils, $localStorage, $ngConfirmDefaults) {
        // $ngConfirmDefaults.theme = 'light,gitftp';
        // $rootScope.utils = Utils;
        // $rootScope.$storage = $localStorage;
    }
]);