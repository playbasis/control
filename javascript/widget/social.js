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
                if ( $social.is('#add-social') ) {
                    savesocial( $social, $addsocial );
                } else {
                    savesocial( $social );
                }

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
}

function saveSocial(){
    console.log("55555");

    $(".social").each(function(){
        console.log($(this));
        console.log($(this).find(".social-name h4").attr("tittle"));
    });
}