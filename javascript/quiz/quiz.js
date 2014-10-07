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

$(document).ready(function(){

    $(document).on( 'click',
        '.quiz-status',
        function(event) {
            event.stopPropagation();
            togglesocial( $(this) );
        }
    );

});