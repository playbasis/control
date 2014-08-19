function toggleRule($toggle) {

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

    var $container = $('.social-panel').isotope({
        itemSelector : '.social-container',
        masonry: {
            columnWidth: 180
        }
    });

});