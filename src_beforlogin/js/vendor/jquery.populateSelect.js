/*!
 * jQuery plugin to populate form select fields
 *
 * Author: Jonathan Sarmiento
 */

;(function($) {

  // Populate pre-defined selects
  $('select[data-populate]').each( function() {
    $(this).populateSelect({
      'url': $(this).attr('data-populate')
    });
  });

  /**
  * Populates target select with "name" fields from JSON file
  *
  * NOTES
  * If "value" exists it will populate the value field
  * If "name" starts with '-' field will be disabled
  *
  * @param url - JSON file url
  * @param indent - number of spaces to indent content
  */
  $.fn.populateSelect = function( options ) {
    var settings = $.extend({
      'url'  : 'assets/json/form-countries.json',
      'indent' : 1
    }, options);

    $.ajax({
      url: settings.url,
      context: this,
      dataType: 'json',
      success: function(data) {
        var value, items = [], prefix = '', t = '';

        for ( var i=0; i < settings.indent; i++ ) {
          prefix += '&nbsp;';
        }

        $.each(data, function(key, val) {
          if(val.tnamespace){
            t = val.tnamespace;
          }
          if( val.name[0] === '-' ) {
            items.push('<option value="" disabled data-i18n="'+t+'" >'+ prefix + val.name + '</option>');
          } else {
            value = (typeof val.value === 'undefined') ? val.name : val.value;
            items.push('<option value="' + value + '" data-i18n="'+t+'" >'+ prefix + val.name + '</option>');
          }
        });

        return this.append( items.join('') );
      }//,
      // complete: function(xhr, status) {
      //   console.log('load: ' + status + ' %c' + url, 'color: #999');
      // }
    });

  };

})( jQuery );
