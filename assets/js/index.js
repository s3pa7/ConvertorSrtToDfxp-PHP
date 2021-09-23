$(function () {

    $('.btn').hide();
    $('input[type="file"]').bind('change', function() {
        $("#text").html($('input[type="file"]').val().replace('C:\\fakepath\\',''));
        $('.btn').show();
        $('.hide').hide();

    });
    $('.btn').on('click' , function () {
        $("#text").html('');
        $('.btn').hide();
    })
})