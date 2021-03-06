app.factory('avatar', ['$http', function ($http) {
    return {
        update: function (inputData) {
            return $http({
                method: 'post',
                url: '/api/v1/profile/post-avatar/',
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
                data: $.param(inputData)
            });
        }
    }
}]);
