$(document).ready(function () {
    $("#order,  .type,  .language, .level, .category").on('change keyup paste', function (e) {
        ApplyFilter();
    });
});

function ApplyFilter() {
    var order = $('#order').find(":selected").val();
    let url = $('.class_route').val();
    let search = $('.search').val();
    var type = [];
    $('.type:checked').each(function (i) {
        type[i] = $(this).val();
    });
    url += '/?type=' + type.toString();



    var language = [];
    $('.language:checked').each(function (i) {
        language[i] = $(this).val();
    });
    url += '&language=' + language.toString();


    var level = [];
    $('.level:checked').each(function (i) {
        level[i] = $(this).val();
    });
    url += '&level=' + level.toString();

    var category = [];
    $('.category:checked').each(function (i) {
        category[i] = $(this).val();
    });
    url += '&category=' + category.toString();
    url += '&order=' + order.toString();

    if (search != "") {
        url += '&query=' + search;
    }
    window.location.href = url;

}
