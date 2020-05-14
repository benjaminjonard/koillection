//Image

let $croppie = $('#cropArea').croppie({
    viewport: { width: 150, height: 150, type: 'circle' },
    boundary: { width: 200, height: 200 },
    showZoomer: false,
    update: function (){
        $('#cropArea').triggerHandler('mouseup');
    }
});

/* Removes alt on preview image, causes a bug in Firefox */
$croppie.find('.cr-image').attr('alt', '');
/* Add crosshair to cropper */
$croppie.find('.cr-vp-circle').addClass('fa fa-plus fa-fw');


function readFile(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            $croppie.croppie('bind', {
                url : e.target.result,
            });
        };
        reader.readAsDataURL(input.files[0]);
    }
}

$('.suggestion').on('click', function () {
    $(this).closest('.input-field').find('input').first().val($(this).data('suggestion'));
    $(this).closest('.input-field').find('label').first().addClass('active');
});

$('#cropInput').on('change', function () {
    readFile(this);
    $(this).triggerHandler('mouseup');
});

function refreshImage() {
    if ($('#cropInput').val() == '') {
        return;
    }

    let $form = $('#cropArea').closest('.row-file').find('.file-input');
    $croppie.croppie('result', {
        type: "canvas",
        size: { width: 200, height: 200 }
    })
    .then(function(imgBase64) {
        $form.val(imgBase64);
        $('#cropPreview').html('<img src="' + imgBase64 + '">');
    });
}

$('#cropArea').on('mouseup', function (e) {
    refreshImage();
});

$('#cropArea').on('mousewheel', function (e) {
    refreshImage();
});
