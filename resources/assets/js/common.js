var commonModule = (function() {

    var datatableBaseURL = 'http://adm.trimitrakemasindo.com/datatable/';

    var select2BaseURL = '/select2/';

    /* Auto Log Out Setelah Sekian Detik */
    var autoLogOut = function() {
        var delay = 1000; // 10 menit (10 * 60 detik * 1000 untuk miliseconds)
        window.setTimeout(function() {
            window.location.href = '/logout';
        }, delay);
    };

    return {
        autoLogOut: autoLogOut,
        datatableBaseURL: datatableBaseURL,
        select2BaseURL: select2BaseURL
    };

    autoLogOut();

})();