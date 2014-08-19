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

});

/**
 * Initialize social isotopes and register listeners
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

    /**
     * social Creation
     */
    $container.on( 'click', '#add-social .add-social, #add-social .social-template', function(event) {

        // TODO: Garbage Collection!
        var template, newsocial, $social = $('#add-social'),
            action_name, action_id = 1;

        if ( $(this).hasClass('add-social') ) {

            $(this).hide();
            $social.find('input').focus();
            $social.find('.social-content').css('opacity', '1').css('margin-left', '0');

        } else {

            template = Handlebars.templates['social'];

            action_name = $(this).find('.social-template-action > span').html().toLowerCase();
            $.each( $.parseJSON(jsonString_Action), function(i,v) {
                if ( v.name == action_name ) {
                    action_id = v.specific_id;
                    return false;
                }
            });

            newsocial = [{
                'addsocial' : true,
                'color' : $(this).parent().attr('class'),
                'name' : $social.find('#add-social-title').val(),
                'description' : $social.find('#add-social-description').val(),
                'action_id' : action_id,
                'rewards' : [
                    {
                        'icon' : 'icon-heart',
                        'title' : $(this).find('.social-template-reward-type').html(),
                        'quantity' : $(this).find('.social-template-reward-quantity').html()
                    }
                ]
            }];

            $social.find('.social-content').css('opacity', '0').css('margin-left', '-10px');

            setTimeout( function() {
                $social.html( template(newsocial) );
                $social.find('.social-description').show().css('opacity', '1');
                $social.find('.social-content').css('opacity', '1').css('margin-left', '0');
            }, 100);

        }

    }).on( 'keydown', '#add-social :input', function(event) {

            if ( event.keyCode === 13 || event.keyCode === 27 ) {
                event.preventDefault();
                $(this).blur();
            }

        }).on( 'blur', '#add-social-title, #add-social-description', function(event) {

            var $social = $('#add-social');

            // TODO: Validate inputs!
            if ( $(this).is('#add-social-title') ) {

                if ( !$(this).val() ) $(this).val('Untitled social');
                $social.find('.social-description').show().css('opacity', '1').find('textarea').focus();

            } else {

                $social.find('.social-templates').show().css('opacity', '1');

            }

        });


    /**
     * social Interaction
     *
     * TODO: Only register rest of the listeners for
     * .large socials, split up the list a bit.
     *
     * TODO: Unregister .social for .large socials, can get rid
     * of all those stopPropagations...
     */
    $container.on( 'click',
        '.social, .social-status, .social-minimize, .social-controls-save, .social-controls-cancel, .social-controls-duplicate, .social-controls-delete, .social-controls-toggle, .toggle-social-action-date-restrictions, .add-social-reward, .social-reward, .close-social-input-well',
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

                // FIX: Duplicate here
            } else if ( $(this).hasClass('social-controls-duplicate') ) {

                event.stopPropagation();
                // $social.removeClass('large');
                $container.isotope( 'insert', $social.clone().removeClass('large') );

                // FIX: Delete here / Alerts
            } else if ( $(this).hasClass('social-controls-delete') ) {

                event.stopPropagation();
                $social.removeClass('large');
                $container.isotope( 'remove', $social );

            } else if ( $(this).hasClass('toggle-social-action-date-restrictions') ) {

                $social.find('.social-action-date-restrictions').slideToggle(200);

                // Populate well, save on close
            } else if ( $(this).hasClass('social-reward') ) {

                $social.find('.social-reward-editor').slideToggle(200);

            } else if ( $(this).hasClass('close-social-input-well') ) {

                $(this).parent().slideUp(200);

            } else {

                $social.addClass('large');

                // template = Handlebars.templates['social-content'];
                // $(this).find('.social-content').append( template(socials) );

                $container.isotope('reLayout');

            }

        }).on( 'mousedown', '.social-input > label', function(event) {

            // Avoid animation flicker when label is clicked
            event.preventDefault();

        }).on( 'focus', '.social-input', function(event) {

            // Toggle extra info when inputs are focused
            var $inputs = $(this).parent().find('.social-input');
            $inputs.removeClass('active').find('.social-input-help').slideUp(200);
            $(this).addClass('active').find('.social-input-help').slideDown(200);

        });

}