app.factory('sumber_dana', ['$http', function ($http) {
    return {
        // get data dengan pagination dan pencarian data
        get: function (page, term) {
            //return $http.get('/api/v1/sumber-dana?page=' + page + '&term=' + term);

            return $http({
                method: 'get',
                url: '/api/v1/sumber-dana?page=' + page + '&term=' + term,
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'}
            });

        },
        //Simpan data
        store: function (inputData) {
            return $http({
                method: 'POST',
                url: '/api/v1/sumber-dana',
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
                data: $.param(inputData)
            });
        },
        //Tampil Data
        show: function (_id) {
            //return $http.get('/api/v1/sumber-dana/' + _id);
            return $http({
                method: 'get',
                url: '/api/v1/sumber-dana/' + _id,
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'}
            });
        },

        update: function (inputData) {
            return $http({
                method: 'PUT',
                url: '/api/v1/sumber-dana/'+ inputData._id,
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
                data: $.param(inputData)
            });
        },

        // destroy a comment
        destroy: function (_id) {
            //return $http.delete('/api/v1/sumber-dana/' + _id);
            return $http({
                method: 'delete',
                url: '/api/v1/sumber-dana/' + _id,
                headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'}
            });
        }
    }
}]);
