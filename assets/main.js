jQuery(function($) {
    $('#formularz').on('submit', function(e) {
        if ($("#md5sum").val() == "" || $("#crc").val() == "" || $('.validError').length > 0) {
            e.preventDefault();
            if ($('#dp_kwota').val().length == 0) {
                $('.amountContainer').append('<div><b>Wprowadź lub wybierz kwotę</b></div>');
                return false;
            }
            $.ajax({
                type: "post",
                url: "/wp-admin/admin-ajax.php",
                data: {
                    action: "make_payment",
                    form_data: $("#formularz").serialize()
                },
                success: function(responseData) {
                    var obj = JSON.parse(responseData);
                    $("#md5sum").val(obj.md5);
                    $("#crc").val(obj.crc);
                    $('#URL_back').val(window.location.origin + '/dziekujemy_za_wplate/?ido=' + obj.crc);
                    $('#fullname').val($('input#name').val() + ' ' + $('input#lastname').val());
                    $('#_wpnonce').remove();
                    $('input[name="_wp_http_referer"]').remove();
                    if (obj.md5) {
                        $("#formularz").submit();
                    }
                }
            });
        }
    });



    $('.amountChoose').on('click', function() {
        $('.amountChoose').not($(this)).removeClass('active');
        $('#dp_other_amount').removeClass('active');
        $(this).addClass('active');
        $('#dp_kwota').val($(this).data('amount'));
        $('#dp_kwota').prop('readonly', true);
        $('#dp_kwota').attr('style', 'color:#2a1f59');
        $('#dp_other_amount_txt').html('Wybrana Kwota')
    });
    $('#dp_other_amount').on('click', function() {
        $('.amountChoose').not($(this)).removeClass('active');
        $(this).addClass('active');
        $('#dp_kwota').prop('readonly', false);
        $('#dp_kwota').attr('style', 'color:brown');
        $('#dp_other_amount_txt').html('<span style=\'color:brown\'>Wprowadź kwotę</span>')
    });

    $('.tbl_center #dp_kwota').on('input', function() {
        $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
        $(this).val($(this).val().replace(/(\..*)\./g, '$1'));
    });
});