(function ($) {
    $('.ovoform-deactivate-link').click(function (e) { 
        e.preventDefault();
        let modal = $('#ovoformDeactivateModal');
        modal.modal('show');

        let actionUrl = $(this).attr('href');
        $('.ovoform-skip-btn').attr('href',actionUrl);

        $('.ovoform-deactivate-form').submit(function (e) { 
            e.preventDefault();
            $('.ovoform-submit-reason-btn').text('Processing...');
            setTimeout(() => {
                window.location.href = actionUrl;
            }, 3000);

        });

    });
    $('[name=deactivate_reason]').click(function () { 
        let v = $(this).val();
        $('.reason_input').addClass('d-none');
        if (v == 'found_better' || v == 'others') {
            $(this).closest('.form-check').find('.form-control').removeClass('d-none');
        }
        
    });

    $(document).on("click", ".ovoform_wp_media_btn", function(e) {
        e.preventDefault();
        var t = $(this);
        var n;
        if (n) {
            n.open();
            return
        }
        var m = wp.i18n.__;
        n = wp.media({
            title: m("Select or Upload Media Of Your Choice", "tutor"),
            button: {
                text: m("Upload media", "tutor")
            },
            multiple: false
        });
        n.on("select", function() {
            var e = n.state().get("selection").first().toJSON();
            t.closest('.ovoform_media_uploader').find('.ovoform_media_upload').removeClass('d-none');
            t.closest('.ovoform_media_uploader').find('.ovoform_media_upload').attr('src',e.url);
            t.closest('.ovoform_media_uploader').find('.ovoform_media_input').val(e.id);
        });
        n.open()
    });



    var ovoformSideBar = $('#toplevel_page_ovoform .wp-submenu-wrap li:last');
    ovoformSideBar.addClass('ovoform-admin-pro');
    ovoformSideBar.find('a').attr('target','_blank');


    $(document).on('click','.copy-element',function () {
        var text = $(this).text();
        var vInput = document.createElement("input");
        vInput.value = text;
        document.body.appendChild(vInput);
        vInput.select();
        document.execCommand("copy");
        document.body.removeChild(vInput);
        $(this).addClass('copied');
        setTimeout(() => {
            $(this).removeClass('copied');
        }, 1000);
    });


})(jQuery)


jQuery(document).ready(function($) {
    "use strict";
    function notify(status, message) {
        iziToast[status]({
            message: message,
            position: "topRight"
        });
    }
});