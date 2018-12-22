$(function(){
    $('#crawler_form .processing').click(() => {
        var data = serializeFormAsJSON($('#crawler_form'));
        $.ajax({
            url: '/process',
            data: data,
            type: 'post',
            beforeSend: function() {
                $('#processing_modal .modal-body').empty().append(preloader());
                $('#processing_modal').modal('show');
            },
            success: function() {
                $('#processing_modal').modal('hide');
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

function preloader() {
    var img = new Image();
    img.src = '/images/preloader.gif';
    img.classList.add('preloader');
    return img;
}

function serializeFormAsJSON(form) {
    var data = $(form).serializeArray();
    var outData = {};
    $.each(data, (key, item) => {
        outData[item.name] = item.value;
    });
    return JSON.stringify(outData);
}