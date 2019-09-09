// var app = angular.module('BudgeTinQ', ['ngRoute']);
var app = angular.module('BudgeTinQ', ['ngRoute'], function ($interpolateProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
});

app.config(function ($routeProvider) {

    $routeProvider
        // HOME
        .when('/', {
            templateUrl: '/BudgetinQ/home'
        })
        .when('/home/:date', {
            templateUrl: function (urlattr) {
                return '/BudgetinQ/home/' + urlattr.date;
            },
        })

        // KATEGORI
        // start nanti hapus
        .when('/kategori/danamasuk', {
            templateUrl: '/kategori/danamasuk'
        })
        .when('/kategori/danakeluar', {
            templateUrl: '/app_keuangan/kategori'
        })
        // .when('/kategori/:ID', {
        //     templateUrl: function(urlattr){ 
        //         return '/kategori/' + urlattr.ID;
        //     },
        // })
        // end nanti hapus

        .when('/kategori', {
            templateUrl: '/kategori/'
        })
        .when('/kategori/:group_category', {
            templateUrl: function (urlattr) {
                return '/kategori/' + urlattr.group_category;
            },
        })
        // .when('/kategori/:group_category/edit/:id_kategori', {
        //     templateUrl: function(urlattr){ 
        //         return '/kategori/' + urlattr.group_category + '/edit/' + urlattr.id_kategori;
        //     },
        // })

        .when('/kategori/:group_category/:id_kategori/:time', {
            templateUrl: function (urlattr) {
                return '/kategori/' + urlattr.ID + "/" + urlattr.IDJPG + "/" + urlattr.time;
            },
        })




        // KATEGORI PENGELUARAN
        .when('/danakeluar', {
            templateUrl: '/danakeluar'
        })
        .when('/danakeluar/:date', {
            templateUrl: function (urlattr) {
                return '/danakeluar/' + urlattr.date;
            },
        })
        .when('/kategori/danakeluar/:ID', {
            templateUrl: function (urlattr) {
                return '/kategori/danakeluar/' + urlattr.ID;
            },
        })

        // KATEGORI PENDAPATAN
        .when('/danamasuk', {
            templateUrl: '/danamasuk'
        })


        .when('/danamasuk/:date', {
            templateUrl: function (urlattr) {
                return '/danamasuk/' + urlattr.date;
            },
        })


        .when('/kategori/:ID/:time', {
            templateUrl: function (urlattr) {
                return '/kategori/' + urlattr.ID + "/" + urlattr.time;
            },
        })

        // .when('/jenis_pendapatan/:id', {
        //     // templateUrl: '/app_keuangan/jenis_pendapatan/',
        //     templateUrl : '/app_keuangan/jenis_pendapatan/id',
        //     controller: 'KategoriPendapatan'
        // })
        // .when('/jenis_pengeluaran/:id', {
        //     // templateUrl: '/app_keuangan/jenis_pendapatan/',
        //     templateUrl : '/app_keuangan/jenis_pengeluaran/id',
        //     controller: 'KategoriPengeluaran'
        // })

        .when('/search/:text', {
            templateUrl: '/app_keuangan/search',
            controller: 'searchController'
        })
        .when('/statistics', {
            templateUrl: '/statistics'
        })
        .when('/reset-data', {
            templateUrl: '/reset_data'
        })
        .when('/history', {
            templateUrl: '/history'
        })
});


app.run(function ($rootScope, $location) {
    var url = window.location.href;
    var host = new URL(url).host;
    var pathname = new URL(url).pathname.split("/");
    // var pathname = new URL(url).pathname.split("/")[1];
    $rootScope.host = host;
    $rootScope.pathname = pathname;
    $rootScope.url = pathname[1];
    datePickerForm('.datepickerForm');
    // input_rupiah("input_rupiah");
    $rootScope.$on("$routeChangeSuccess", function (event, next, current) {
        // $rootScope.url = $location.$$path.replace('/', '');



        $rootScope.rupiah = function (angka) {
            if (angka != undefined) {
                return formatRupiah(angka);
            }
        };

        $rootScope.findDataGCAll = function (object, group_category) {
            var a = object.filter(function (b) {
                return b.group_category == group_category;
            });
            // console.log(a);
            return a;
        };



        // urus themes
        $rootScope.skins = [
            "red",
            'pink',
            'purple',
            'deep-purple',
            'indigo',
            'blue',
            'light-blue',
            'cyan',
            'teal',
            'green',
            'light-green',
            'lime',
            'yellow',
            'amber',
            'orange',
            'deep-orange',
            'brown',
            'grey',
            'blue-grey',
            'black',
        ];

        $rootScope.skinChanger = function (color, $event) {
            $rootScope.theme = 'theme-' + color;
            data = {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                'theme': $rootScope.theme
            };
            ajaxPost('/data/settings', data);
        }

        ajaxGet('/data/settings', returnData);
        function returnData(data) {
            $rootScope.theme = data['theme'];
            $rootScope.$digest();
        }
    });
});


