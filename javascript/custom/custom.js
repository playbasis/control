$(function() {
	template_functions();
	// sparkline_charts();
	charts();
	widthFunctions();
    //circle_progress();

	//prevent all #click
    $(document)
        .on('click', 'a[href="#"]', function (event) {
            event.preventDefault();
        })
        .on('click', "#submitdate_filter", function () {
            var startDate = new Date($('#start_date').val()).getTime();
            var endDate = new Date($('#end_date').val()).getTime();

            if (startDate > endDate) {

                $('.message-dialog .modal-body p').html('Invalid parameter! ,Start-date range shouldn\'t less than End-date');
                $('.message-dialog').modal('show');
                return;
            }

            var data_filter = "";

            var startStamp = $("#start_date").val().split("/");
            var yearStart = '';
            if (startStamp[2].length = 2) {
                yearStart = '20' + startStamp[2];
            }
            var startDate = new Date(yearStart, parseInt(startStamp[0]) - 1, startStamp[1]);
            var start_date = startDate.toString("dd-MM-yyyy");

            var endStamp = $("#end_date").val().split("/");
            var yearEnd = '';
            if (endStamp[2].length = 2) {
                yearEnd = '20' + endStamp[2];
            }
            var endDate = new Date(yearEnd, parseInt(endStamp[0]) - 1, endStamp[1]);
            var end_date = endDate.toString("dd-MM-yyyy");
            $.ajax({
                url: baseUrlPath + 'statistic/getStatisticData?date_start=' + start_date + '&date_expire=' + end_date,
                dataType: 'json',
                success: function (json) {
                    data_filter = [
                        {data: json.points, label: "Points"},
                        {data: json.levelup, label: "Level Up"},
                        {data: json.register, label: "Register"},
                        {data: json.badges, label: "Badges"}
                    ];
//                console.log(data_filter);
                    plot.setData(data_filter);
                    plot.setupGrid();
                    plot.draw();
                }
            });
        });
});

/* ---------- Datable ---------- */
function template_functions() {
    $(document)
        .on('click', '.btn-close', function (e) {
            e.preventDefault();
            $(this).parent().parent().parent().fadeOut();
        })
        .on('click', '.btn-minimize', function (e) {
            e.preventDefault();
            var $target = $(this).parent().parent().next('.box-content');
            if ($target.is(':visible')) {
                $('i', $(this)).removeClass('icon-chevron-up').addClass('icon-chevron-down');
                if ($.isFunction($.fn.getRuleText)) {
                    $(this).getRuleText();
                }
            } else {
                $('i', $(this)).removeClass('icon-chevron-down').addClass('icon-chevron-up');
                if ($.isFunction($.fn.getRuleText)) {
                    $(this).getRuleText({state: "hide"});
                }
            }
            $target.slideToggle();
        })
        .on('click', '.btn-setting', function (e) {
            e.preventDefault();
            $('#myModal').modal('show');
        });
}

/* ---------- Sparkline Charts ---------- */

/***function sparkline_charts() {

	//generate random number for charts
	randNum = function(){
		//return Math.floor(Math.random()*101);
		return (Math.floor( Math.random()* (1+40-20) ) ) + 20;
	}

  var chartColours = ['#2FABE9', '#FA5833', '#b9e672', '#bbdce3', '#9a3b1b', '#5a8022', '#2c7282'];

	//sparklines (making loop with random data for all 7 sparkline)
	i=1;
	for (i=1; i<9; i++) {
	 	var data = [[1, 3+randNum()], [2, 5+randNum()], [3, 8+randNum()], [4, 11+randNum()],[5, 14+randNum()],[6, 17+randNum()],[7, 20+randNum()], [8, 15+randNum()], [9, 18+randNum()], [10, 22+randNum()]];
	 	placeholder = '.sparkLineStats' + i;
		$(placeholder).sparkline(data, {
			width: 100,//Width of the chart - Defaults to 'auto' - May be any valid css width - 1.5em, 20px, etc (using a number without a unit specifier won't do what you want) - This option does nothing for bar and tristate chars (see barWidth)
			height: 30,//Height of the chart - Defaults to 'auto' (line height of the containing tag)
			lineColor: '#2FABE9',//Used by line and discrete charts to specify the colour of the line drawn as a CSS values string
			fillColor: '#f2f7f9',//Specify the colour used to fill the area under the graph as a CSS value. Set to false to disable fill
			spotColor: '#467e8c',//The CSS colour of the final value marker. Set to false or an empty string to hide it
			maxSpotColor: '#b9e672',//The CSS colour of the marker displayed for the maximum value. Set to false or an empty string to hide it
			minSpotColor: '#FA5833',//The CSS colour of the marker displayed for the mimum value. Set to false or an empty string to hide it
			spotRadius: 2,//Radius of all spot markers, In pixels (default: 1.5) - Integer
			lineWidth: 1//In pixels (default: 1) - Integer
		});
	}

}***/

/* ---------- Charts ---------- */

function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });

    return vars;
}

function randNum(){
	return (Math.floor( Math.random()* (1+40-20) ) ) + 20;
};

function showBLTooltip (x, y, contents) {
	$('<div id="tooltip">' + contents + '</div>').css( {
		position: 'absolute',
		display: 'none',
		top: y + 5,
		left: x + 5,
		border: '1px solid #fdd',
		padding: '2px',
		'background-color': '#dfeffc',
		opacity: 0.80,
		'z-index': 9999
	}).appendTo("body").fadeIn(200);

};

function timeTickFormatter(val,axis) {
    var newDate = new Date();
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    newDate.setTime(val*1000);
    var year = newDate.getFullYear();
    var month = months[newDate.getMonth()];
    var date = newDate.getDate();

    var formattedTime = month + ' ' + date;
    return formattedTime;
}

function charts() {


    var plot = "";

  /* ---------- Chart with points ---------- */
  if($("#stats-chart").length) {

    var url = '';

    url = baseUrlPath+'statistic/getStatisticData';

      $.ajax({
        url: url,
        dataType: 'json',
        success: function(json) {
          //console.log(json);
          window.json = json;

          data_filter = [
              { data: json.points, label: "Points"},
              { data: json.levelup, label: "Level Up"},
              { data: json.register, label: "Register"},
              { data: json.badges, label: "Badges" }
          ];

          window.plot = plot = $.plot($("#stats-chart"),
            data_filter,
            {
             series: {
               lines: { show: true,
                  lineWidth: 3,
//                  fill: true, fillColor: { colors: [ { opacity: 0.08 }, { opacity: 0.01 } ] }
                },
               points: { show: true },
               shadowSize: 2
             },
             grid: { hoverable: true,
                 clickable: true,
                 tickColor: "#eee",
                 borderWidth: 0
              },
              colors: ["#FA5833", "#2FABE9"],
//              xaxis: {tickFormatter: function(utime){var time = new Date( utime*1000 );return dateFormat(time, "dd/mm/yyyy");}},
              xaxis: {tickFormatter: timeTickFormatter, tickLength: 5},
              yaxis: {ticks:11, tickDecimals: 0}
            });

            var previousPoint = null;
            $("#stats-chart").bind("plothover", function (event, pos, item) {

              $("#x").text(pos.x.toFixed(2));
              $("#y").text(pos.y.toFixed(2));

              if (item) {
                if (previousPoint != item.dataIndex) {
                  previousPoint = item.dataIndex;

                  $("#tooltip").remove();
                  var x = item.datapoint[0].toFixed(2),
                      y = item.datapoint[1].toFixed(2);

                  showBLTooltip(
                    item.pageX,
                    item.pageY,
//                    item.series.label + ", " + json.register[item.datapoint[0]-1][2] + " = " + item.datapoint[1]);
                    item.series.label + " <br /> " + new Date( item.datapoint[0] *1000).toDateString() + " <br /> " + item.datapoint[1]);
                }
              }
              else {
                $("#tooltip").remove();
                previousPoint = null;
              }
            });

        }
      });

    $("#sincos").bind("plotclick", function (event, pos, item) {
      if (item) {
        $("#clickdata").text("You clicked point " + item.dataIndex + " in " + item.series.label + ".");
        plot.highlight(item.series, item.datapoint);
      }
    });
  }


  /* ---------- Chart with points ---------- */
  if($("#sincos").length)
  {
    var sin = [], cos = [];

    for (var i = 0; i < 14; i += 0.5) {
      sin.push([i, Math.sin(i)/i]);
      cos.push([i, Math.cos(i)]);
    }

    var plot = $.plot($("#sincos"),
         [ { data: sin, label: "sin(x)/x"}, { data: cos, label: "cos(x)" } ], {
           series: {
             lines: { show: true,
                lineWidth: 2,
               },
             points: { show: true },
             shadowSize: 2
           },
           grid: { hoverable: true,
               clickable: true,
               tickColor: "#dddddd",
               borderWidth: 0
             },
           yaxis: { min: -1.2, max: 1.2 },
           colors: ["#FA5833", "#2FABE9"]
         });

    var previousPoint = null;
    $("#sincos").bind("plothover", function (event, pos, item) {
      $("#x").text(pos.x.toFixed(2));
      $("#y").text(pos.y.toFixed(2));

        if (item) {
          if (previousPoint != item.dataIndex) {
            previousPoint = item.dataIndex;

            $("#tooltip").remove();
            var x = item.datapoint[0].toFixed(2),
              y = item.datapoint[1].toFixed(2);
            showBLTooltip(item.pageX, item.pageY, item.series.label + " of " + x + " = " + y);
          }
        }
        else {
          $("#tooltip").remove();
          previousPoint = null;
        }
    });

    $("#sincos").bind("plotclick", function (event, pos, item) {
      if (item) {
        $("#clickdata").text("You clicked point " + item.dataIndex + " in " + item.series.label + ".");
        plot.highlight(item.series, item.datapoint);
      }
    });
  }

  /* ---------- Flot chart ---------- */
  if($("#flotchart").length)
  {
    var d1 = [];
    for (var i = 0; i < Math.PI * 2; i += 0.25)
      d1.push([i, Math.sin(i)]);

    var d2 = [];
    for (var i = 0; i < Math.PI * 2; i += 0.25)
      d2.push([i, Math.cos(i)]);

    var d3 = [];
    for (var i = 0; i < Math.PI * 2; i += 0.1)
      d3.push([i, Math.tan(i)]);

    $.plot($("#flotchart"), [
      { label: "sin(x)",  data: d1},
      { label: "cos(x)",  data: d2},
      { label: "tan(x)",  data: d3}
    ], {
      series: {
        lines: { show: true },
        points: { show: true }
      },
      xaxis: {
        ticks: [0, [Math.PI/2, "\u03c0/2"], [Math.PI, "\u03c0"], [Math.PI * 3/2, "3\u03c0/2"], [Math.PI * 2, "2\u03c0"]]
      },
      yaxis: {
        ticks: 10,
        min: -2,
        max: 2
      },
      grid: { tickColor: "#dddddd",
          borderWidth: 0
      },
      colors: ["#FA5833", "#2FABE9", "#FABB3D"]
    });
  }

  /* ---------- Stack chart ---------- */
  if($("#stackchart").length)
  {
    var d1 = [];
    for (var i = 0; i <= 10; i += 1)
    d1.push([i, parseInt(Math.random() * 30)]);

    var d2 = [];
    for (var i = 0; i <= 10; i += 1)
      d2.push([i, parseInt(Math.random() * 30)]);

    var d3 = [];
    for (var i = 0; i <= 10; i += 1)
      d3.push([i, parseInt(Math.random() * 30)]);

    var stack = 0, bars = true, lines = false, steps = false;

    function plotWithOptions() {
      $.plot($("#stackchart"), [ d1, d2, d3 ], {
        series: {
          stack: stack,
          lines: { show: lines, fill: true, steps: steps },
          bars: { show: bars, barWidth: 0.6 },
        },
        colors: ["#FA5833", "#2FABE9", "#FABB3D"]
      });
    }

    plotWithOptions();

    $(".stackControls input").click(function (e) {
      e.preventDefault();
      stack = $(this).val() == "With stacking" ? true : null;
      plotWithOptions();
    });
    $(".graphControls input").click(function (e) {
      e.preventDefault();
      bars = $(this).val().indexOf("Bars") != -1;
      lines = $(this).val().indexOf("Lines") != -1;
      steps = $(this).val().indexOf("steps") != -1;
      plotWithOptions();
    });
  }

  /* ---------- Pie chart ---------- */
  var data = [
  { label: "Internet Explorer",  data: 12},
  { label: "Mobile",  data: 27},
  { label: "Safari",  data: 85},
  { label: "Opera",  data: 64},
  { label: "Firefox",  data: 90},
  { label: "Chrome",  data: 112}
  ];

  if($("#piechart").length)
  {
    $.plot($("#piechart"), data,
    {
      series: {
          pie: {
              show: true
          }
      },
      grid: {
          hoverable: true,
          clickable: true
      },
      legend: {
        show: false
      },
      colors: ["#FA5833", "#2FABE9", "#FABB3D", "#78CD51"]
    });

    function pieHover(event, pos, obj)
    {
      if (!obj)
          return;
      percent = parseFloat(obj.series.percent).toFixed(2);
      $("#hover").html('<span style="font-weight: bold; color: '+obj.series.color+'">'+obj.series.label+' ('+percent+'%)</span>');
    }
    $("#piechart").bind("plothover", pieHover);
  }

  /* ---------- Donut chart ---------- */
  if($("#donutchart").length)
  {
    $.plot($("#donutchart"), data,
    {
        series: {
            pie: {
                innerRadius: 0.5,
                show: true
            }
        },
        legend: {
          show: false
        },
        colors: ["#FA5833", "#2FABE9", "#FABB3D", "#78CD51"]
    });
  }




   // we use an inline data source in the example, usually data would
  // be fetched from a server
  var data = [], totalPoints = 300;
  function getRandomData() {
    if (data.length > 0)
      data = data.slice(1);

    // do a random walk
    while (data.length < totalPoints) {
      var prev = data.length > 0 ? data[data.length - 1] : 50;
      var y = prev + Math.random() * 10 - 5;
      if (y < 0)
        y = 0;
      if (y > 100)
        y = 100;
      data.push(y);
    }

    // zip the generated y values with the x values
    var res = [];
    for (var i = 0; i < data.length; ++i)
      res.push([i, data[i]])
    return res;
  }

  // setup control widget
  var updateInterval = 30;
  $("#updateInterval").val(updateInterval).change(function () {
    var v = $(this).val();
    if (v && !isNaN(+v)) {
      updateInterval = +v;
      if (updateInterval < 1)
        updateInterval = 1;
      if (updateInterval > 2000)
        updateInterval = 2000;
      $(this).val("" + updateInterval);
    }
  });

  /* ---------- Realtime chart ---------- */
  if($("#serverload").length)
  {
    var options = {
      series: { shadowSize: 1 },
      lines: { show: true, lineWidth: 0.4, fill: true, fillColor: { colors: [ { opacity: 0.1 }, { opacity: 1 } ] }},
      yaxis: { min: 0, max: 100, tickFormatter: function (v) { return v + "%"; }},
      xaxis: { show: false },
      colors: ["#FA5833"],
      grid: { tickColor: "#dddddd",
          borderWidth: 0,
      },
    };
    var plot = $.plot($("#serverload"), [ getRandomData() ], options);
    function update() {
      plot.setData([ getRandomData() ]);
      // since the axes don't change, we don't need to call plot.setupGrid()
      plot.draw();

      setTimeout(update, updateInterval);
    }

    update();
  }

  if($("#realtimechart").length)
  {
    var options = {
      series: { shadowSize: 1 },
      lines: { fill: true, fillColor: { colors: [ { opacity: 1 }, { opacity: 0.1 } ] }},
      yaxis: { min: 0, max: 100 },
      xaxis: { show: false },
      colors: ["#F4A506"],
      grid: { tickColor: "#dddddd",
          borderWidth: 0
      },
    };
    var plot = $.plot($("#realtimechart"), [ getRandomData() ], options);
    function update() {
      plot.setData([ getRandomData() ]);
      // since the axes don't change, we don't need to call plot.setupGrid()
      plot.draw();

      setTimeout(update, updateInterval);
    }

    update();
  }
}

/**function growlLikeNotifications() {

  $('#add-sticky').click(function(){

    var unique_id = $.gritter.add({
      // (string | mandatory) the heading of the notification
      title: 'This is a sticky notice!',
      // (string | mandatory) the text inside the notification
      text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus eget tincidunt velit. Cum sociis natoque penatibus et <a href="#" style="color:#ccc">magnis dis parturient</a> montes, nascetur ridiculus mus.',
      // (string | optional) the image to display on the left
      image: 'img/avatar.jpg',
      // (bool | optional) if you want it to fade out on its own or just sit there
      sticky: true,
      // (int | optional) the time you want it to be alive for before fading out
      time: '',
      // (string | optional) the class name you want to apply to that specific message
      class_name: 'my-sticky-class'
    });

    // You can have it return a unique id, this can be used to manually remove it later using


    return false;

  });

  $('#add-regular').click(function(){

    $.gritter.add({
      // (string | mandatory) the heading of the notification
      title: 'This is a regular notice!',
      // (string | mandatory) the text inside the notification
      text: 'This will fade out after a certain amount of time. Vivamus eget tincidunt velit. Cum sociis natoque penatibus et <a href="#" style="color:#ccc">magnis dis parturient</a> montes, nascetur ridiculus mus.',
      // (string | optional) the image to display on the left
      image: 'img/avatar.jpg',
      // (bool | optional) if you want it to fade out on its own or just sit there
      sticky: false,
      // (int | optional) the time you want it to be alive for before fading out
      time: ''
    });

    return false;

  });

    $('#add-max').click(function(){

        $.gritter.add({
            // (string | mandatory) the heading of the notification
            title: 'This is a notice with a max of 3 on screen at one time!',
            // (string | mandatory) the text inside the notification
            text: 'This will fade out after a certain amount of time. Vivamus eget tincidunt velit. Cum sociis natoque penatibus et <a href="#" style="color:#ccc">magnis dis parturient</a> montes, nascetur ridiculus mus.',
            // (string | optional) the image to display on the left
            image: 'img/avatar.jpg',
            // (bool | optional) if you want it to fade out on its own or just sit there
            sticky: false,
            // (function) before the gritter notice is opened
            before_open: function(){
                if($('.gritter-item-wrapper').length == 3)
                {
                    // Returning false prevents a new gritter from opening
                    return false;
                }
            }
        });

        return false;

    });

  $('#add-without-image').click(function(){

    $.gritter.add({
      // (string | mandatory) the heading of the notification
      title: 'This is a notice without an image!',
      // (string | mandatory) the text inside the notification
      text: 'This will fade out after a certain amount of time. Vivamus eget tincidunt velit. Cum sociis natoque penatibus et <a href="#" style="color:#ccc">magnis dis parturient</a> montes, nascetur ridiculus mus.'
    });

    return false;
  });

    $('#add-gritter-light').click(function(){

        $.gritter.add({
            // (string | mandatory) the heading of the notification
            title: 'This is a light notification',
            // (string | mandatory) the text inside the notification
            text: 'Just add a "gritter-light" class_name to your $.gritter.add or globally to $.gritter.options.class_name',
            class_name: 'gritter-light'
        });

        return false;
    });

  $('#add-with-callbacks').click(function(){

    $.gritter.add({
      // (string | mandatory) the heading of the notification
      title: 'This is a notice with callbacks!',
      // (string | mandatory) the text inside the notification
      text: 'The callback is...',
      // (function | optional) function called before it opens
      before_open: function(){
        alert('I am called before it opens');
      },
      // (function | optional) function called after it opens
      after_open: function(e){
        alert("I am called after it opens: \nI am passed the jQuery object for the created Gritter element...\n" + e);
      },
      // (function | optional) function called before it closes
      before_close: function(e, manual_close){
                var manually = (manual_close) ? 'The "X" was clicked to close me!' : '';
        alert("I am called before it closes: I am passed the jQuery object for the Gritter element... \n" + manually);
      },
      // (function | optional) function called after it closes
      after_close: function(e, manual_close){
                var manually = (manual_close) ? 'The "X" was clicked to close me!' : '';
        alert('I am called after it closes. ' + manually);
      }
    });

    return false;
  });

  $('#add-sticky-with-callbacks').click(function(){

    $.gritter.add({
      // (string | mandatory) the heading of the notification
      title: 'This is a sticky notice with callbacks!',
      // (string | mandatory) the text inside the notification
      text: 'Sticky sticky notice.. sticky sticky notice...',
      // Stickeh!
      sticky: true,
      // (function | optional) function called before it opens
      before_open: function(){
        alert('I am a sticky called before it opens');
      },
      // (function | optional) function called after it opens
      after_open: function(e){
        alert("I am a sticky called after it opens: \nI am passed the jQuery object for the created Gritter element...\n" + e);
      },
      // (function | optional) function called before it closes
      before_close: function(e){
        alert("I am a sticky called before it closes: I am passed the jQuery object for the Gritter element... \n" + e);
      },
      // (function | optional) function called after it closes
      after_close: function(){
        alert('I am a sticky called after it closes');
      }
    });

    return false;

  });

  $("#remove-all").click(function(){

    $.gritter.removeAll();
    return false;

  });

  $("#remove-all-with-callbacks").click(function(){

    $.gritter.removeAll({
      before_close: function(e){
        alert("I am called before all notifications are closed.  I am passed the jQuery object containing all  of Gritter notifications.\n" + e);
      },
      after_close: function(){
        alert('I am called after everything has been closed.');
      }
    });
    return false;

  });


}**/


/* ---------- Page width functions ---------- */

$(window).bind("resize", widthFunctions);

function widthFunctions( e ) {
  // hack for flot.resize is not working correctly
  if(window.plot) {
    window.plot.resize();
    window.plot.setupGrid();
    window.plot.draw();
  }

  var winHeight = $(window).height();
  var winWidth = $(window).width();

  if (winHeight) {

      var marginTopbar = 20,
          menuBottom = 30,
          menuHeight = $('.main-menu-span').height() + marginTopbar + menuBottom,
          gapTopbar = 59;
      
      if ((winHeight-gapTopbar) < menuHeight)
          $("#content").css("min-height", menuHeight);
      else
          $("#content").css("min-height", winHeight - gapTopbar);
  }

  if (winWidth < 980 && winWidth > 767) {

    if($(".main-menu-span").hasClass("span2")) {

      $(".main-menu-span").removeClass("span2");
      $(".main-menu-span").addClass("span1");

    }

    if($("#content").hasClass("span10")) {

      $("#content").removeClass("span10");
      $("#content").addClass("span11");

    }


    $("a").each(function(){

      if($(this).hasClass("quick-button-small span1")) {

        $(this).removeClass("quick-button-small span1");
        $(this).addClass("quick-button span2 changed");

      }

    });

      $(".circleStatsItem").each(function() {

          var getOnTablet = $(this).parent().attr('onTablet');
          var getOnDesktop = $(this).parent().attr('onDesktop');

          if (getOnTablet) {

              $(this).parent().removeClass(getOnDesktop);
              $(this).parent().addClass(getOnTablet);

          }

      });

      $(".box").each(function(){

          var getOnTablet = $(this).attr('onTablet');
          var getOnDesktop = $(this).attr('onDesktop');

          if (getOnTablet) {

              $(this).removeClass(getOnDesktop);
              $(this).addClass(getOnTablet);

          }

      });

  } else {

    if($(".main-menu-span").hasClass("span1")) {

      $(".main-menu-span").removeClass("span1");
      $(".main-menu-span").addClass("span2");

    }

    if($("#content").hasClass("span11")) {

      $("#content").removeClass("span11");
      $("#content").addClass("span10");

    }

    $("a").each(function(){

      if($(this).hasClass("quick-button span2 changed")) {

          $(this).removeClass("quick-button span2 changed");
          $(this).addClass("quick-button-small span1");

      }

    });

      $(".circleStatsItem").each(function() {

          var getOnTablet = $(this).parent().attr('onTablet');
          var getOnDesktop = $(this).parent().attr('onDesktop');

          if (getOnTablet) {

              $(this).parent().removeClass(getOnTablet);
              $(this).parent().addClass(getOnDesktop);

          }

      });

      $(".box").each(function(){

          var getOnTablet = $(this).attr('onTablet');
          var getOnDesktop = $(this).attr('onDesktop');

          if (getOnTablet) {

              $(this).removeClass(getOnTablet);
              $(this).addClass(getOnDesktop);

          }

      });

  }
}



/* ---------- Circle Progess Bars ---------- */

// Check for large values then draw circles
function initialize_circle(circleStatsItem) {

  var circle, color, eventValue, eventPercent;

  // find input, used by jquery knob
  circle = $(circleStatsItem).find('input');

  // find color class
  color = $(circle).attr('class');

  // swap color name for corresponding hex
  switch (color) {
    case 'redCircle':
      color = "#ff0000";
      break;
    case 'orangeCircle':
      color = "#fa5833";
      break;
    case 'lightOrangeCircle':
      color = "#f4a70c";
      break;
    case 'blueCircle':
      color = "#2fabe9";
      break;
    case 'greenCircle':
      color = "#b9e672";
      break;
    case 'yellowCircle':
      color = "#e7e572";
      break;
    case 'pinkCircle':
      color = "#e42b75";
      break;
    default:
      color = "#87ceeb";
  }

  // value > 100, max dial at value
  // value < 100, fill to percent
  eventValue = $(circle).val();
  eventPercent = (eventValue < 100) ? true : false;

  // fill knob
  $(circle).knob({
    'min': 0,
    'max': 100,
    'readOnly': true,
    'width': 120,
    'height': 120,
    'fgColor': color,
    'dynamicDraw': eventPercent,
    'thickness': 0.2,
    'tickColorizeValues': true,
    'skin': 'tron'
  });

  // override knob styles
  $(circle).css({'width' : '100%'});

  // reposition signs for large numbers
  if (!eventPercent) {
    $(circleStatsItem).find('.plus').css({'margin-left' : 0});
    $(circleStatsItem).find('.percent').css({'margin-left' : '106px'});
  }

}

// Draw circles
function circle_progress() {

  $.each($('.circleStatsItem'), function() {
    initialize_circle(this);
  });

}

$(document).ready(function() {
  // alert('cach');
  defaultWidth  = 271; //pixels
  transition    = 500; //millisecond

  function resetMargin(width) {

    divLeftMargin = 0;

    $('.additional-block').each(function(index) {


      thisLeftMargin  = divLeftMargin + 'px';

      $(this).css('margin-left', thisLeftMargin);

      divLeftMargin = divLeftMargin + width;

    });
  }

  resetMargin(defaultWidth);

  $('.menu a').each(function() {

    thisHref  = $(this).attr('href');

    if($(thisHref).length > 0) {
      $(this).addClass('has-child');
    }

  });

  $('.menu a').click(function(event) {

    // event.preventDefault();

    selectedDiv     = $(this).attr('href');
    selectedMargin    = $(selectedDiv).css('margin-left');
    selectedParent    = $(this).parents('.additional-block');
    sliderMargin    = $('.customslider').css('margin-left');
    slidingMargin   = (parseInt(sliderMargin) - defaultWidth) + 'px';


    if(selectedMargin.length > 0) {

      $(selectedDiv).children('.header').prepend('<span class="back"></span>').bind('click', function () {

        selectedParent  = $(this).parents('.additional-block');
        sliderMargin  = - (parseInt(selectedParent.css('margin-left')) - defaultWidth) + 'px';
        $('.customslider').animate({marginLeft: sliderMargin}, transition);

      });

      if((parseInt(selectedMargin) - defaultWidth) >= defaultWidth) {

        selectedParent.after($(selectedDiv));

        resetMargin(defaultWidth);

        $('.customslider').animate({marginLeft: slidingMargin}, transition);

      } else {

        $('.customslider').animate({marginLeft: slidingMargin}, transition);

      }
    }

    return false;
  });

});



