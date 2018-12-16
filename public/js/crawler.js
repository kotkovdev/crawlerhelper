$(function(){
    $('#crawler_form .processing').click(() => {
        var data = serializeFormAsJSON($('#crawler_form'));
        $.ajax({
            url: '/process',
            data: data,
            type: 'post',
            success: function() {

            }
        });
        return false;
    });

    $('#crawler_form .save').click(() => {
        var data = serializeFormAsJSON($('#crawler_form'));
        $.ajax({
            url: location.href  + '/save',
            data: data,
            type: 'post',
            success: (response) => {
                $('#success_alert').text('Changes saved').show();
            },
            error: (response) => {
                $('#success_error').text('Changes saved').show();
            }
        });
        return false;
    });
});

function serializeFormAsJSON(form) {
    var data = $(form).serializeArray();
    var outData = {};
    $.each(data, (key, item) => {
        outData[item.name] = item.value;
    });
    return JSON.stringify(outData);
}