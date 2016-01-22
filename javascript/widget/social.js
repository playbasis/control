function togglesocial($toggle) {

    // Toggle: disabled -> enabled
    if ( $toggle.hasClass('disabled') ) {

        $toggle.removeClass('disabled').addClass('enabled')
        $toggle.find('span').html('Enabled');

        // Toggle: enabled -> disabled
    } else if ( $toggle.hasClass('enabled') ) {

        $toggle.removeClass('enabled').addClass('disabled')
        $toggle.find('span').html('Disabled');

    }

}

$(document).ready(function(){

    initSocialIsotopes( $('#social-panel') );

    $("#social-facebook").click();
});

/**
 * Initialize Social isotopes and register listeners
 */
function initSocialIsotopes($container) {

    // FIX: Store this externally
    var $addsocial = $('#add-social').clone();

    $container.isotope({
        itemSelector : '.social-container',
        masonry: {
            columnWidth: 180
        }
    });

    $container.on( 'click',
        '.social, .social-status, .social-minimize, .social-controls-save, .social-controls-cancel, .social-controls-toggle',
        function(event) {

            var $social = $(this).closest('.social-container');

            if ( $(this).hasClass('social-status') ) {

                event.stopPropagation();
                togglesocial( $(this) );

                // FIX: Unsave here / Alerts? (Maybe not, alerts are annoying)
            } else if ( $(this).hasClass('social-minimize') || $(this).hasClass('social-controls-cancel') ) {

                event.stopPropagation();

                if ( $social.is('#add-social') ) {
                    $social.find('.social-content').css('opacity', '0').css('margin-left', '-10px');
                    $social.html( $addsocial.html() );
                }

                $social.removeClass('large');
                $container.isotope('reLayout');

                // FIX: Save here
            } else if ( $(this).hasClass('social-controls-save') ) {

                event.stopPropagation();
                saveSocial();

            } else {

                $social.addClass('large');
                $container.isotope('reLayout');

            }

        }).on( 'mousedown', '.social-input > label', function(event) {

            event.preventDefault();

        }).on( 'focus', '.social-input', function(event) {

            var $inputs = $(this).parent().find('.social-input');
            $inputs.removeClass('active').find('.social-input-help').slideUp(200);
            $(this).addClass('active').find('.social-input-help').slideDown(200);

    });

    $(".social-sort_order input").live('keyup', function(){
        $(this).val($(this).val().replace(/[^0-9]/g,''));
    });
}

function saveSocial(){

    var data = new Array();
    $(".social").each(function(){
        var social = new Object();
        social.name = $(this).find(".social-name h4").attr("title").toLowerCase();
        social.key = $(this).find(".social-content .social-key input").val();
        social.secret = $(this).find(".social-content .social-secret input").val();
        social.sort_order = $(this).find(".social-content .social-sort_order input").val();
        social.status = $(this).find(".social-status").hasClass("enabled");
        data.push(social);
    });

    var sCallback = $("#social-callback").val();
    var _data = {'socials' : data, 'socials_callback' : sCallback};
    _data[csrf_token_name] = csrf_token_hash;
    $.ajax({
        url: baseUrlPath+"widget/social_manage",
        type: "POST",
        data: _data,
        dataType: 'json',
        cache: false,
        beforeSend: function() {
            $(".ajax-loading").remove();
            $(".messages").remove();
            $("#top-header").prepend('<div class="ajax-loading"><span class="text-ajax-loading">Loading...</span></div>');
        },
        error: function() {
            $(".ajax-loading").remove();
            $(".content").prepend('<div class="content messages half-width"><div class="error">Connection to server lost, Save again</div></div>');
        },
        success: function(res) {
            $(".ajax-loading").remove();
            $(".content").prepend('<div class="content messages half-width"><div class="success">You have successfully updated Social widget!</div></div>');
        }
    });
}