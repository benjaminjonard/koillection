//Image
var formSelector = '#'+$('#cropArea').attr('formName')+"_image";

var $croppie = $('#cropArea').croppie({
    viewport: { width: 150, height: 150, type: 'circle' },
    boundary: { width: 200, height: 200 },
    update: function (){
        $('#cropArea').triggerHandler('mouseup');
    }
});

function readFile(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $croppie.croppie('bind', {
                url : e.target.result,
            });
        };
        reader.readAsDataURL(input.files[0]);
    }
}

$('.suggestion').on('click', function () {
    $(this).parent().find('input').first().val($(this).data('suggestion'));
    $(this).parent().find('label').first().addClass('active');
});

$('#cropInput').on('change', function () {
    readFile(this);
    $(this).triggerHandler('mouseup');
});

$('#cropArea').on('mouseup', function (e) {
    $croppie.croppie('result', {
            type: "canvas",
            size: "viewport"
        })
        .then(function(imgBase64) {
            $(formSelector).val(imgBase64);
            if ($('#cropInput').val() != '') {
                $('#cropPreview').html('<img src="' + imgBase64 + '">');
            }
        });
});

$('#cropArea').on('mousewheel', function (e) {
    $croppie.croppie('result', {
            type: 'canvas',
            size: 'viewport'
        })
        .then(function (imgBase64) {
            $(formSelector).val(imgBase64);
            if ($('#cropInput').val() != '') {
                $('#cropPreview').html('<img src="' + imgBase64 + '">');
            }
        });
});
