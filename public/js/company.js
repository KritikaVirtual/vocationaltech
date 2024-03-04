$(document).ready(function () {

    $.fn.editable.defaults.mode = 'popup';
    $('#username').editable({
        validate: function (value) {
            if ($.trim(value) == '')
                return 'This field is required';
        }
    });
    $('.buiding').editable({
        validate: function (value) {
            if ($.trim(value) == '')
                return 'This field is required';
        }
    });

    $('.address').editable({
        validate: function (value) {
            if ($.trim(value) == '')
                return 'This field is required';
        }
    });

    $('.address2').editable();

    $('.city').editable({
        validate: function (value) {
            if ($.trim(value) == '')
                return 'This field is required';
        }
    });

    $('.state').editable({
        validate: function (value) {
            if ($.trim(value) == '')
                return 'This field is required';
        }
    });

    $('.postal').editable({
        validate: function (value) {
            if ($.trim(value) == '')
                return 'This field is required';
        }
    });

    $('.phone').editable({
        validate: function (value) {
            if ($.trim(value) == '')
                return 'This field is required';
            var isValid = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/.test(value);
            if (!isValid) {
                return 'The \'Phone Number\' is not a vaild, please try again!';
            }

        }
    });

    $('.phoneExt').editable({
        validate: function (value) {
            if ($.trim(value) != '') {
                var isValid = /^[0-9-+]+$/.test(value);
                if (!isValid) {
                    return 'The \'Phone Extension\' is not a vaild, please try again!';
                }
            }

        }
    });

    $('.fax').editable({
        validate: function (value) {
            if ($.trim(value) != '') {
                var isValid = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/.test(value);
                if (!isValid) {
                    return 'The \'Fax Number\' is not a vaild, please try again!';
                }
            }
        }
    });

    $('.billCompany').editable({
        validate: function (value) {
            if ($.trim(value) == '')
                return 'This field is required';
        }
    });

    $('.billAddress').editable({
        validate: function (value) {
            if ($.trim(value) == '')
                return 'This field is required';
        }
    });

    $('.billAddress2').editable();

    $('.billSuite').editable({
        validate: function (value) {
            if ($.trim(value) == '')
                return 'This field is required';
        }
    });

    $('.billCity').editable({
        validate: function (value) {
            if ($.trim(value) == '')
                return 'This field is required';
        }
    });

    $('.billState').editable({
        validate: function (value) {
            if ($.trim(value) == '')
                return 'This field is required';
        }
    });

    $('.billPostal').editable({
        validate: function (value) {
            if ($.trim(value) == '')
                return 'This field is required';
        }
    });

    $('.billPhone').editable({
        validate: function (value) {
            if ($.trim(value) == '')
                return 'This field is required';
            var isValid = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/.test(value);
            if (!isValid) {
                return 'The \'Phone Number\' is not a vaild, please try again!';
            }
        }
    });

    $('.billPhoneExt').editable({
        validate: function (value) {
            if ($.trim(value) != '') {
                var isValid = /^[0-9-+]+$/.test(value);
                if (!isValid) {
                    return 'The \'Phone Extension\' is not a vaild, please try again!';
                }
            }

        }
    });

    $('.billFax').editable({
        validate: function (value) {
            if ($.trim(value) != '') {
                var isValid = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/.test(value);
                if (!isValid) {
                    return 'The \'Fax Number\' is not a vaild, please try again!';
                }
            }
        }
    });

    $('.attention').editable({
        validate: function (value) {
            if ($.trim(value) == '')
                return 'This field is required';
        }
    });

    var remit_cont = '';
    $('.remit_address').editable({
        validate: function (value) {
            if ($.trim(value) == '')
                return 'This field is required';
            //setValue:$(this).html()

        },
        rows: 10,
        success: function (response, newValue) {
            //console.log(this.id);
            remit_cont = newValue.nl2br();
            $('a#' + this.id).css("white-space", "pre-wrap");
            //alert(newValue);
            //alert(newValue.nl2br());
            //$('.remit_address').html(newValue.nl2br());
            //$('#remit_address').editable('setValue', newValue.nl2br(), true);
        }

    });

});

String.prototype.nl2br = function ()
{
    return this.replace(/\n/g, "<br />");
}

function setBuildingCookie(bId) {
    $.ajax({
        url: baseUrl + 'company/setbuildingcookie',
        type: 'post',
        data: {
            bId: bId
        },
    });
}

