//Set Date picker for Insight day range
$(function(){

  circleDay();
  circleWeek();
  circleMonth();

  $(".panel-carousel").hide();
  $(".panel-carousel").first().show();

  $(document).on("click", '#stats-sort button', function (evt) {
      var e = $(this).attr('rel');
      $(".panel-carousel").hide();
      var s = '#panel-carousel-'+e;

      $(s).show();
      $(s).children().show();

      if($('.carousel-control').hasClass('hidden')){
          $('.carousel-control').removeClass('hidden');
      }
      $('.carousel-control').addClass('hidden');

      $(s).find('.carousel-control').removeClass('hidden');

      $('#stats-carousel-'+e).carouFredSel({
          prev: '#carousel-prev-'+e,
          next: '#carousel-next-'+e,
          width: '100%',
          auto: false,
          scroll: 2,
          // mousewheel: true,
          swipe: {
              onMouse: true,
              onTouch: true
          }
      });
  });

  // Create latest players isotopes
  $('#latest-masonry').isotope({
    layoutMode: 'straightDown'
  });

  // Create leader board isotopes
  $('#leader-masonry').isotope({
    layoutMode: 'straightDown'
  });

  // FIX: Kill the clicks, register .on container
  // TODO: Get rid of the isotopes here, overkill

  // Enlarge player isotope on click
  $('.isot-player-container').click( function() {
    $(this).addClass('large');
    $('.isotope').isotope('reLayout');
  });
  // Minimize player isotope
  $('.isot-player .isot-player-minimize').click( function(e) {
    e.stopPropagation();
    $(this).parent().parent().removeClass('large');
    $('.isotope').isotope('reLayout');
  });

  //Setup date picker
  $('.datepicker').datepicker({
    // dateFormat: 'dd-mm-yy'
  });
  //console.log('set date')

  $("#noti-stream-container").mCustomScrollbar({
    advanced:{
      updateOnContentResize: true
    }
  });

  var token = getUrlVars()["token"];

  /*$.ajax({
      url: baseUrlPath+'dashboard/liveFeed',
      data: {
          token: token
      },
      dataType: 'text',
      success: function(data) {
        $("#noti-stream").html(data);
      }
  });*/
});

function circleDay(){
    $.ajax({
        url: baseUrlPath+'statistic/getDailyActionmeaturement',
        dataType: 'html',
        beforeSend: function (  ) {
            $("#stats-carousel-day").html('<img width="50" height="50" src="image/white_loading.gif">');
        }
    }).done(function ( d ) {
            $("#stats-carousel-day").html(d);
            $('#stats-carousel-day').carouFredSel({
                prev: '#carousel-prev-day',
                next: '#carousel-next-day',
                width: '100%',
                auto: false,
                scroll: 2,
                // mousewheel: true,
                swipe: {
                    onMouse: true,
                    onTouch: true
                }
            });
            circle_progress();
        });
}

function circleWeek(){
    $.ajax({
        url: baseUrlPath+'statistic/getWeeklyActionmeaturement',
        dataType: 'html',
        beforeSend: function (  ) {
            $("#stats-carousel-weekly").html('<img width="50" height="50" src="image/white_loading.gif">');
        }
    }).done(function ( d ) {
            $("#stats-carousel-weekly").html(d);
            $('#stats-carousel-weekly').carouFredSel({
                prev: '#carousel-prev-weekly',
                next: '#carousel-next-weekly',
                width: '100%',
                auto: false,
                scroll: 2,
                // mousewheel: true,
                swipe: {
                    onMouse: true,
                    onTouch: true
                }
            });
            circle_progress();
        });
}

function circleMonth(){
    $.ajax({
        url: baseUrlPath+'statistic/getMonthlyActionmeaturement',
        dataType: 'html',
        beforeSend: function (  ) {
            $("#stats-carousel-month").html('<img width="50" height="50" src="image/white_loading.gif">');
        }
    }).done(function ( d ) {
            $("#stats-carousel-month").html(d);
            $('#stats-carousel-month').carouFredSel({
                prev: '#carousel-prev-month',
                next: '#carousel-next-month',
                width: '100%',
                auto: false,
                scroll: 2,
                // mousewheel: true,
                swipe: {
                    onMouse: true,
                    onTouch: true
                }
            });
            circle_progress();
        });
}
