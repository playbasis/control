$(function(){
        for(var i=0; i<18; i++) {
            $('#container').append(fakeUser.getGroup());
        }

        var $container = $('#container');

        $container.isotope({
            masonry: {
                columnWidth: 120
            },
            sortBy: 'level',
            getSortData: {
                point: function( $elem ) {
                    var number = $elem.hasClass('element') ? 
                        $elem.find('.point').text() :
                        $elem.attr('data-number');
                    return parseInt( number, 10 );
                },

                level: function( $elem ) {
                    var number = $elem.hasClass('element') ? 
                        $elem.find('.level').text() :
                        $elem.attr('data-number');
                    return parseInt( number, 10 );
                },

                name: function( $elem ) {
                    return $elem.find('.name').text();
                }
            }
        });

        var $optionSets = $('#options .option-set'),
            $optionLinks = $optionSets.find('a');

        $optionLinks.click(function() {
            //console.log('hello');
            var $this = $(this);
            // don't proceed if already selected
            if ( $this.hasClass('selected') ) {
                return false;
            }
            var $optionSet = $this.parents('.option-set');
                $optionSet.find('.selected').removeClass('selected');
                $this.addClass('selected');
      
            // make option object dynamically, i.e. { filter: '.my-filter-class' }
            var options = {},
                key = $optionSet.attr('data-option-key'),
                value = $this.attr('data-option-value');

            // parse 'false' as false boolean
            value = value === 'false' ? false : value;
            options[ key ] = value;
            if ( key === 'layoutMode' && typeof changeLayoutMode === 'function' ) {
              // changes in layout modes need extra logic
              changeLayoutMode( $this, options )
            } 
            else {
              // otherwise, apply new options
              $container.isotope( options );
            }
            
            return false;
        });

        $container.infinitescroll({
            navSelector  : '#page_nav',    // selector for the paged navigation 
            nextSelector : '#page_nav a',  // selector for the NEXT link (to page 2)
            itemSelector : '.element',     // selector for all items you'll retrieve
            loading: {
                finishedMsg: 'No more pages to load.',
                img: 'http://i.imgur.com/qkKy8.gif'
            },
            debug: true,
            behavior: 'local'
        },
        // call Isotope as a callback
        function( newElements ) {
            //console.log('hello');
            $container.isotope( 'appended', $( newElements ) ); 
        });
    });