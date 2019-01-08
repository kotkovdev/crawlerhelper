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

    $('.job-remove').click(() => {
        $.post({
            type: 'post',
            url : '/jobs/remove',
            data: JSON.stringify({id: $(event.target).data('id')}),
            success: function() {
                $(event.target).closest('tr').remove();
            },
            error: function() {
                alert('Can not remove this job. Try later');
            }

        });
    });

    $('.show-log').click((event) => {
        $.ajax({
            url: '/jobs/log',
            type: 'get',
            data: {name: $(event.target).data('name')},
            success: function(response) {
                $('#log_modal textarea').text(response);
                $('#log_modal').modal('show');
            }
        });
    });

    $('#instances .delete').click(function(event){
        $.ajax({
            url: '/instlist/remove',
            type: 'post',
            data: {id: $(event.target).data('id')},
            success: function() {
                $(event.target).closest('tr').find('.status').text('Deleted');
            }
        });
    });

    $('#run-crawler').click((event) => {
        $('#processing_modal .modal-body').html(preloader());
        $('#processing_modal').modal('show');
        $.ajax({
            type: 'get',
            url: '/process',
            dataType: 'json',
            success: function(response) {
                if (response.status == 'done') {
                    $('#processing_modal').modal('hide');
                }
            },
            error: function (response) {
                alert('Crawler error. See log for get more info.');
            }
        });
        return false;
    });
});



function preloader()
{
    var img = new Image();
    img.src = '/images/preloader.gif';
    img.classList.add('preloader');
    return img;
}

function serializeFormAsJSON(form)
{
    var data = $(form).serializeArray();
    var outData = {};
    $.each(data, (key, item) => {
        outData[item.name] = item.value;
    });
    return JSON.stringify(outData);
}

function removeJob(id)
{
    $.post({
        type: 'post',
        url : '/jobs/remove',
        data: JSON.stringify({id: id}),
        success: function() {
            $('#job-' + id).remove();
        },
        error: function() {
            alert('Can not remove this job. Try later');
        }

    });
}