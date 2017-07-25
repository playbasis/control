function togglesocial($toggle) {
    // Toggle: disabled -> enabled
    if ( $toggle.hasClass('disabled') ) {

        $toggle.removeClass('disabled').addClass('enabled')
        $toggle.find('span').html('Enabled');

        $toggle.find("input").val(true);

        // Toggle: enabled -> disabled
    } else if ( $toggle.hasClass('enabled') ) {

        $toggle.removeClass('enabled').addClass('disabled')
        $toggle.find('span').html('Disabled');

        $toggle.find("input").val(false);

    }

}

function togglesocial_multiplechoice($toggle) {
    // Toggle: disabled -> enabled
    if ( $toggle.hasClass('false') ) {

        $toggle.removeClass('false').addClass('true')
        $toggle.find('span').html('True');

        $toggle.find("input").val(true);

        // Toggle: enabled -> disabled
    } else if ( $toggle.hasClass('true') ) {

        $toggle.removeClass('true').addClass('false')
        $toggle.find('span').html('False');

        $toggle.find("input").val(false);

    }

}

$(document).ready(function(){

    $(document).on( 'click',
        '.quiz-status',
        function(event) {
            event.stopPropagation();
            togglesocial( $(this) );
        }
    );

    $(document).on( 'click',
        '.multiple-choices',
        function(event) {
            event.stopPropagation();
            togglesocial_multiplechoice( $(this) );
        }
    );

});