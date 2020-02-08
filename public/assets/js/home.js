/**
 * Created by howard.salter on 2/22/17.
 */
(function home(){

    $('#btnModalLogin').on('click', function(e) {
        e.preventDefault();
        var paramObj = [];
        paramObj['email'] = $('#inputEmail').val();
        paramObj['password'] = $('#inputPassword').val();

        $.ajax({
            url: "/login",
            type: "POST",
            dataType: "json",
            data: {
                email: paramObj['email'],
                password: paramObj['password']
            },
            complete: function(retData) {
                console.log(retData);

                if (typeof retData.responseJSON == 'undefined') {
                    $('.errortext').html('Server Error! Please Try Again Later.');
                    $('.errordiv').show();
                } else {
                    if (typeof retData.responseJSON.egserror != 'undefined') {
                        $('.errortext').html('Invalid Login! Please Try Again.');
                        $('.errordiv').show();
                    } else {
                        $('#userid').val(retData.responseJSON.usrid);
                        $('#hiddenLogin').submit();
                    }
                }
            }
        });

    });

})();
