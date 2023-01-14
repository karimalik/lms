$( document ).ready(function() {
    $('#add_city_modal').hide();
});
function open_add_city_modal(el){
    $('#add_city_modal').modal('show');
    $('#city_add').modal('show');
}
function edit_city_modal(el){
    let url = $('.city_edit').val();
    let token = $('.csrf_token').val();
    $.post(url, {_token:token, id:el}, function(data){
        $('#edit_form').html(data);
        $('#Item_Edit').modal('show');
    });
}


$(document).ready(function () {
    let baseUrl = $('#url').val();
    $('.cityList').select2({
        ajax: {
            url: baseUrl + '/ajaxCounterCity',
            type: "GET",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1,
                    id: $('#state').find(':selected').val(),
                }
            },
            cache: false
        }
    });
    $('.stateList').select2({
        ajax: {
            url: baseUrl + '/ajaxCounterState',
            type: "GET",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1,
                    id: $('#country').find(':selected').val(),
                }
            },
            cache: false
        }
    });
    $('.select2').css('width', '100%');


});
