/*!
 * jQuery plugin to generate alerts
 *
 * Author: Jonathan Sarmiento
 */

;(function($) {

  /*!
   * Add an alert to the container
   *
   * @param type - alert, error or success
   * @param content - alert content
   * @param closeButton - whether alert has close button
   * @param timed - dismiss alert after a delay
   */
  $.fn.pbAlert = function( options ) {

    var $container = $(this),

    methods = {

      // Create an alert and add it to the container
      addAlert : function( options ) {
        var settings = $.extend({
          type        : '', // alert, error or success
          content     : 'undefined', // alert content
          closeButton : true, // show alert close button
          timed       : 4000 // timed alert dismissal, 0 = false
        }, options),

        // Check if type is set
        type = (settings.type) ? ' pb-alert--' + settings.type : '',

        // Create alert
        $alert = $('<div>', {
          class : 'pb-alert pb-alert--add' + type,
          html  : settings.content
        }),

        // Create close button
        $close = $('<div>', {
          class : 'pb-alert--close',
          title : 'Close Alert'
        });

        // Initialize close button
        if ( settings.closeButton ) {
          // Attach close button
          $close.prependTo( $alert );

          // Close alert on close button click
          $close.on('click', function(event) {
            event.preventDefault();
            methods.removeAlert( $alert );
          });
        }

        $alert.hide();
        // Attach alert (optional override)
        $.fn.pbAlert.attach($alert, $container);
        $alert.slideDown();

        // Close alert after a delay
        if ( settings.timed ) {
          setTimeout( function() {
            methods.removeAlert( $alert );
          }, settings.timed);
        }

        return $alert;
      },

      // Remove an alert
      removeAlert : function( $alert ) {
        // animate alert removal
        $alert.removeClass('pb-alert--add');
        $alert.addClass('pb-alert--remove');
        $alert.slideUp(function(){
          $alert.remove();
        })
        // remove alert
        // setTimeout( function() { $alert.remove(); }, 1000);
      }

    };

    if ( ! $container.length ) {

      // Alert if container not found
      console.log('%cpbAlert: container not found', 'color: #f00');

    } else if ( $.type( options ) === 'string' ) {

      // If called with a string, check string value
      if ( options === 'remove' ) {

        // Remove specified alert
        methods.removeAlert( $container );

      } else {

        // Add alert with specified text
        return methods.addAlert({
          content : options
        });

      }

    } else {

      // Add alert with specified options
      return methods.addAlert( options );

    }

    return this;
  };

  // Control how alert is attached
  $.fn.pbAlert.attach = function($alert, $container) {
    var offset;

    $alert.insertBefore($container);

    offset = $alert.offset();

    if ( offset.top < $('body').scrollTop() ) {
      $('body').animate({ scrollTop: offset.top - 20 });
    }
  };

})( jQuery );
