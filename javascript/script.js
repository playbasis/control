// (function($) {
//   window.criteria = "level", window.lastClick = "";
//   var isFirst = true;
//   var current_index = 1;
//   $(function() {

//     // radio.btn-group toggle class active
//     $('.btn-group:not(.paging)').each(function() {
//       $(this).children().on('click', function(){
//         $(this).addClass('active').siblings().removeClass('active');
//       });
//     });

//     $('.btn-group.paging').each(function() {
//       $(this).children().on('click', function(){
//         // getIsoTope('', page);

//         console.log(this.id);
//         current_index += (this.id === 'next') ? 1 : -1;

//         $("#pagination").trigger(this.id, {current_page: current_index });
//       });
//     });    

//     function getIsoTope(sort, page) {
//       $.ajax({
//         url: 'index.php?route=statistic/isotope',
//         data: { 
//           token: location.search.split('&token=')[1].split('&')[0],
//           filter_page: page,
//           filter_sort: sort
//         },
//         dataType: 'json',
//         success: function(resp){ 
//           console.log(resp);
//           // do html
//           $("#player-masonary").html(resp.html);
//           // do paging text
//           // hardcode for test 
//           resp.limit = 100;

//           var current = ((current_index - 1 ) * resp.limit) + ' - ' + (((current_index - 1 ) * resp.limit) + resp.limit);

//           $('#current_result').html(current);
//           $('#max_result').html(resp.pages * resp.limit);

//           isotopeRun();

//           // do pagination things
//           if(isFirst) {
//             $("#pagination").jPaginator({ 
//               nbPages: resp.pages,
//               nbVisible: resp.pages, 
//               overBtnLeft:'#over_backward', 
//               overBtnRight:'#over_forward', 
//               maxBtnLeft:'#max_backward', 
//               maxBtnRight:'#max_forward', 
//               onPageClicked: function(a, num) {
//                 getIsoTope('', num);
//                 current_index = num;
//               } 
//             });
//             isFirst = false;
//           }
//         }
//       });
//     }
//     getIsoTope();

//     function setCriteria(key, value) {
//       $('ul.filter.breadcrumb').children().remove();
//       if(key === '') {
//         window.criteria = ''; 
//         return;
//       }

//       var criteria = window.criteria, 
//           params = window.criteria.split('|'),
//           param = (value === '') ? key : key + ':' + value + '|';

//       if(criteria.match(key)) {
//         console.log('match key !!');
//         if(value) {
//           var pattern = new RegExp("(" + key + ":\\d*|" + key + ")"),
//               gender_pattern = new RegExp("(gender:male|gender:female|gender)");
//           if(key.match("gender")) {
//             criteria = criteria.replace(gender_pattern, param, "gi");
//           }
//           else {
//             criteria = criteria.replace(pattern, param, "gi");
//           }
//           criteria = criteria.replace(/\|\|/, '|');
//         }
//       }
//       else {
//         if(params.length > 1) {
//           params[params.length-1] = param;
//           criteria = params.join('|');
//         }
//         else {
//           params = criteria = key;
//           $('ul.filter.breadcrumb').append($('<li>'+params+'</li>'));
//         }
//       }
//       // breadcrumb things       
//       $(params).each(function(k, v) {
//         var breadcrumb = $('<li>'+v+'</li>'),
//             seperator = $('<li><span class="divider">&gt</span></li>');

//         $('ul.filter.breadcrumb').append(breadcrumb);
//         if(params.length-1 !== k) $('ul.filter.breadcrumb').append(seperator);
//       });
//       // console.log(window.criteria = criteria);
//       console.log(window.criteria = criteria);
//     }

//     // Menu criteria
//     $('.navbar.criteria .nav').on('click', 'a', function(e){

//       e.preventDefault();
//       console.log(this.id);
//       if(this.id === 'reset') { 
//         setCriteria('', ''); 
//         loadPlayer('expand');
//         $('.navbar.criteria li').removeClass('active');
//         return;
//       }
      
//       var $this = $(this),
//           toggle = criteria.split('|');
//       if(toggle.length == 1) {
//         $('.navbar.criteria li').removeClass('active');
//       }
//       else {
//         $('.navbar.criteria li').removeClass('active');
//         $.each(toggle, function(k, v) {
//           var trigger = v.split(':');
//           if(trigger.length > 1) {
//             $('#' + v.split(':')[0]).parent().addClass('active');
//           }
//         });
//       }
//       $this.parent().addClass('active');
//       window.lastClick = this.id;
//       setCriteria(this.id, '');

//       getInfoGraph();
//     });

//     var $players = $('#masonry-item');

//     $('.btn-player-group').click(function(){
      
//       value = $(this).attr('data-option-value');
//       console.log('hi ! ' + value);
//       // loadPlayer(value);
//     });
    
//     $players.infinitescroll({
//       navSelector  : '#page-nav',    // selector for the paged navigation 
//       nextSelector : '#page-nav a',  // selector for the NEXT link (to page 2)
//       itemSelector : '.element',     // selector for all items you'll retrieve
//       loading: {
//         finishedMsg: 'No more pages to load.',
//         img: 'http://i.imgur.com/6RMhx.gif'
//       }
//     },
//     // trigger Masonry as a callback
//     function( newElements ) {
//       // hide new items while they are loading
//       var $newElems = $( newElements ).css({ opacity: 0 });
      
//       // ensure that images load before adding to masonry layout
//       $newElems.imagesLoaded(function(){
      
//         // show elems now they're ready
//         $newElems.animate({ opacity: 1 });
//         $players.masonry( 'appended', $newElems, true ); 
//       });
//     });

//     $('#zoomin').on('click', function() {
//       loadPlayer();
//     });
//   });

// function filterBy(elem) {
//   $('#masonry-item').isotope({ filter: '.level-' + elem.id }); 
// }

// function loadSuccess(json) {
//   $('#player-masonary').html(json);
// }

// function loadPlayer(type, elem) {
//   var url, token = location.search.split('&token=')[1].split('&')[0];

//   if (type === 'group') {
//     url = 'index.php?route=player/player/getGroupPlayer&token=' + token;
//   } 
//   else {
//     url = 'index.php?route=player/player/getPlayer&token=' + token;
//   }

//   $.ajax({
//     url: url, 
//     success: loadSuccess
//   })
//   .done(function() {
//     isotopeRun(elem);
//   });
// }

// function isotopeRun(elem) {
//   var $players = $('#masonry-item'), buffer;

//   $players.isotope({
//       masonry: {
//           columnWidth: 120
//       },
//       sortBy: 'point',
//       getSortData: {
//           point: function( $elem ) {
//             // console.log(parseInt($elem.find('.point').text(), 10));
//               var number = $elem.hasClass('element') ? 
//                   $elem.find('.isot_point_con span').text() : $elem.attr('data-number');
//               return parseInt( number, 10 );
//           },

//           level: function( $elem ) {
//               var number = $elem.hasClass('element') ? 
//                   $elem.find('.isot_level_con span').text() : $elem.attr('data-number');
//               return parseInt( number, 10 );
//           },

//           name: function( $elem ) {
//               return $elem.find('.isot_info_name .value').text();
//           }
//       }
//   });

//   // recieve event from infograph
//   if(typeof elem === 'object') {
//     filterBy(elem);
//   }  

//   var $optionSets = $('.btn.options');

//   $optionSets.click(function() {

//     var $this = $(this),
//     options = {},
//     value = $this.attr('data-option-value'),
//     key = $this.parents('.btn-group').attr('data-option-key');

//     value = (value === 'false') ? false : value;
//     options[ key ] = value;

//     if ( key === 'layoutMode' && typeof changeLayoutMode === 'function' ) {
//       // changes in layout modes need extra logic
//       changeLayoutMode( $this, options )
//     } 
//     else {
//       // otherwise, apply new options
//       $players.isotope( options );
//     }

//     return false;
//   });

//   // Click and large element
//   $players.on( 'click', 'ul.ison_action_set li', function(e) {
//     // Stop event buble to .element
//     e.stopPropagation();


//     console.log($(this).attr('action'));
//   });

//   $players.on( 'click', '.element', function() {
//     $buffer = $(this).toggleClass('large');

//     if($buffer === $(this)) { 
//       $buffer.removeClass('large');
//       $buffer.find('.player-more-information').hide();
//     }
//     $buffer.find('.player-more-information').toggle();
//     $players.isotope('reLayout');
//   });
// }

// // change layout
// var isHorizontal = false;
// function changeLayoutMode( $link, options ) {
//   var wasHorizontal = isHorizontal;
//   isHorizontal = $link.hasClass('horizontal');

//   if ( wasHorizontal !== isHorizontal ) {
//     // orientation change
//     // need to do some clean up for transitions and sizes
//     var style = isHorizontal ? 
//       { height: '80%', width: $container.width() } : 
//       { width: 'auto' };
    
//     // stop any animation on container height / width
//     $container.filter(':animated').stop();
    
//     // disable transition, apply revised style
//     $container.addClass('no-transition').css( style );
//     setTimeout(function(){
//       $container.removeClass('no-transition').isotope( options );
//     }, 100 );
//   } 
//   else {
//     $container.isotope( options );
//   }
// }
// })(jQuery);