// player.js
var DEBUG = true;
/* Global Variable JS */
globalVar = {
  init: function () {
    // $('#goto_isotope').live('click',function(){
    //     globalVar.currentPage = 'isotope';
    //   isotopeMan.renderGraph(dataMan.getCurrentParamSetForURL());
    // })
  },
  // urlForGraph: config_statistic_link,
  // urlForTotalPlayer:config_statistic_link_total_player,
  currentDataSet: undefined,  /*Current JSON DATA SET*/
  currentGraphSelect: undefined,  /*Current Section of graph which been selected*/
  currentSelectedRange: undefined,  /*Current Range Value of of graph-section which been selected*/
};

progressDialog = {
  show: function (text) {
    $('body').prepend('<div class="custom_blackdrop"><img src="./image/white_loading.gif" /><br><span>' + text + '</span></div>');
  },
  hide: function () {
    setTimeout(function () {
      $('.custom_blackdrop').remove();
    }, 1000)
  }
};

/*Data manager*/
dataMan = {
  fetchData: function (target) {
    //PHP : How to generate JSON from php
    // echo json_encode(
    //     array(
    //           "donut"=>array(array("label"=>"low:0-3","data"=>"33.33"),array("label"=>"low:4-7","data"=>"33.33"),array("label"=>"low:8-10","data"=>"33.33")),
    //           "detail"=>array(
    //             array(array("label"=>"male","data"=>"50.00"),array("label"=>"female","data"=>"50.00")),
    //             array(array("label"=>"male","data"=>"50.00"),array("label"=>"female","data"=>"50.00")),
    //             array(array("label"=>"male","data"=>"50.00"),array("label"=>"female","data"=>"50.00"))
    //           )
    //          )
    //     );
    //Will get this-> //'{"donut":[{"label":"low:0-3","data":"33.33"},{"label":"medium:4-7","data":"33.33"},{"label":"high:8-10","data":"33.33"}],"detail":[[{"label":"male","data":"70.00"},{"label":"female","data":"30.00"}],[{"label":"male","data":"90.00"},{"label":"female","data":"10.00"}],[{"label":"male","data":"50.00"},{"label":"female","data":"50.00"}]]}'
    //END PHP
    progressDialog.show('Fetching data...');
    progressDialog.hide();
    //Set up the url and call ajax
    var url = '';
    //TEMPORARY :: This part just temporary
    if (target == 'level') {
      //Set url and call AJAX
      //OR
      return '{"donut":[{"label":"low:0-3","data":"33.33"},{"label":"medium:4-7","data":"33.33"},{"label":"high:8-10","data":"33.33"}],"detail":[[{"label":"male","data":"70.00"},{"label":"female","data":"30.00"}],[{"label":"male","data":"90.00"},{"label":"female","data":"10.00"}],[{"label":"male","data":"50.00"},{"label":"female","data":"50.00"}]]}';
      //get String from here
    } else if (target == 'points') {
      //Set url and call AJAX
      //OR
      return '{"donut":[{"label":"low:0-3","data":"43.33"},{"label":"medium:4-7","data":"23.33"},{"label":"high:8-10","data":"33.33"}],"detail":[[{"label":"male","data":"70.00"},{"label":"female","data":"30.00"}],[{"label":"male","data":"90.00"},{"label":"female","data":"10.00"}],[{"label":"male","data":"50.00"},{"label":"female","data":"50.00"}]]}';
      //get String from here
    } else if (target == 'actions') {
      //Set url and call AJAX
      //OR
      return '{"donut":[{"label":"low:0-3","data":"53.33"},{"label":"medium:4-7","data":"13.33"},{"label":"high:8-10","data":"33.33"}],"detail":[[{"label":"male","data":"70.00"},{"label":"female","data":"30.00"}],[{"label":"male","data":"90.00"},{"label":"female","data":"10.00"}],[{"label":"male","data":"50.00"},{"label":"female","data":"50.00"}]]}';
      //get String from here
    }
    // $.ajax({
    //   url: url,
    //   data: '',
    //   type:'GET',
    //   beforeSend:function(){
    //     // alert('send')
    //     progressDialog.show('Fetching data...');
    //   },
    //   success:function(data){
    //     console.log('Request success');
    //     console.log(data);
    //   },
    //   error:function(err){
    //     console.log('Request fail');
    //     console.log(err);
    //     // return err;
    //   },
    //   complete:function(){
    //     console.log('on complete')
    //     progressDialog.hide();
    //   }
    // });
  }
};

/*Graph manager*/
graphMan = {
  setOnGraphSectionClick: function () {
    $("#player-summary").on("plotclick", function (event, pos, obj) {
      //Go back if obj not exist
      if (!obj) return;
      // clear all dialog from screen
      $('.chart_popover').popover('hide');
      // show dialog
      $('.popover').removeClass('popover_move');
      $('.popover').removeClass('popover_move_minor');

      $('.popover').on('click', 'span.close_popover', function(){
        $('.chart_popover').popover('hide');
      });

      // var position = $.trim(obj.series.label.split(':')[1]); //splitting from 'level: 1'
      // alert('grahp pos > ' + position);
      obj.series.label = '' + obj.series.label;
      var position = obj.series.label.replace(/ /g, '');
      graphMan.forceShowContextMenu(position, obj.seriesIndex);
    });
  },

  forceShowContextMenu: function (position, seriesIndex) {
    // globalVar.currentGraphSelect = position;

    // setTimeout(function () {
    //   position = position.replace('.', 'dot');
    //   $('#' + position).popover('show');
    //   //Modify pop-over object
    //   var items = globalVar.currentDataSet;
    //   var obj = $('.filter_popover');

    //   //setup header
    //   $('.popover-title').append(
    //     $('<span class="pull-right close_popover"> &times</span>').on('click', function() {
    //       $('.chart_popover').popover('hide');
    //    })
    //   );
    //   //modify dialog content
    //   var list = obj.find('ol');
    //   list.empty();

    //   // console.log('Trace population of each section : '+seriesIndex);
    //   // console.log(globalVar.currentDataSet.detail[seriesIndex]);

    //   var population = globalVar.currentDataSet.detail[seriesIndex];
    //   for (var index in population) {
    //     var item = population[index];
    //     list.append('<li id="' + item.label + '"><span class="population_lable less_round">' + item.label + '  </span><span class="population_value pull-right">' + item.value + '</span></li>')
    //   }

    //   $('.load_menu').html(obj.html());
    // }, 500);
  },

  reRenderGraph: function (data) {
    // console.log(data);
    globalVar.currentDataSet = data;
    //  console.log(globalVar.currentDataSet);
    //render graph again
    var count = 0;
    var resp = globalVar.currentDataSet;
    window.plot = $.plot($("#player-summary"), data.donut, {
      series: {
        pie: {
          innerRadius: 0.4,
          show: true,
          label: {
            show: true,
            radius: 0.99,
            formatter: function (label, series) {
              // var valueRangeToBeID = $.trim(label.split(':')[1]);
              label = ''+ label;
              var valueRangeToBeID = $.trim(label.replace(/ /g, ''));
              // console.log(' render time : '+ ++count);
              //. to dot
              valueRangeToBeID = valueRangeToBeID.replace('.', 'dot');
              return '<span style="font-size:.8em;text-align:center;padding:2px 8px;margin:4px;color:#999;flaot:left !important;-webkit-border-radius: 8px;-moz-border-radius: 8px;border-radius: 8px;" class="pull-left label_range">' + label + '<br/><span class="percentage" style="font-size:1.4em;font-weight:bold">' + Math.round(series.percent) + '%</span></span><span style="flaot:left !important" class="pull-left chart_popover" id="' + valueRangeToBeID + '" data-html="true" data-placement="right" data-content="&lt;div class=\'load_menu\' &gt;This is your div content&lt;/div&gt;" title="" data-original-title="Filter Select" ></span>';
            },
            background: {
              opacity: 0.3
            }
          }
        }
      },
      legend: {
        show: true
      },
      grid: {
        clickable: true
      },
      colors: ["#FA5833", "#2FABE9", "#FABB3D", "#78CD51"]
    })
  }
};

crmTab = {
  init_crmTab: function () {

    // slider target, min value, max value
    initializeSlider($('#input-set-level .sliderRange'), 1, 100);
    initializeSlider($('#input-set-action .sliderRange'), 1, 500);
    initializeSlider($('#input-set-reward .sliderRange'), 1, 50);

    // Update slider when input fields are changed
    $('.sliderRangeLabel').change(function() {
      var min, max, inputSet, values = $(this).val().split('-');
      // Only accept integer values
      min = parseInt(values[0],10);
      max = parseInt(values[1],10);
      if (!isNaN(min) && !isNaN(max)) {
        inputSet = $(this).closest('.input-set');
        inputSet.find('.sliderRange').slider( 'values', [ min, max ] );
        inputSet.find('.input-set-toggle').addClass('active');
        inputSet.find('.sliderRangeLabel').addClass('active');
      }
    });

    $('.input-set-toggle').click( function() {
      $(this).closest('.input-prepend').find('.sliderRangeLabel').toggleClass('active');
    });

    // Update buttons with value of dropdown
    $('.dropdown-menu > li > a').click( function() {
      var title = $(this).text();
      $(this).closest('.btn-group').find('.dropdown-title').text(title);
    });

    //On click submit button
    $('.submit_filter_btn').on('click', function () {

      var errorMessage = '', value;

      // Validate field
      $('.input-set .input-set-toggle').each(function() {
        if ( $(this).hasClass('active') ) {

          if ( $(this).html() == 'Reward' || $(this).html() == 'Action' ) {
            errorMessage += 'Please choose an option for ' + $(this).html() + '.<br>';
          }
          else if ( ( value = $(this).parent().find('input').val() )
                      && !/^-?(?:\d+)?(?:-\d+)?$/.test( value ) ) {
            errorMessage += "Please format " + $(this).html() + " input as 1 or 1-100.<br>";
          }

        }
      });

      if ( errorMessage ) {
        $('#myModal p.error-dialog').html( errorMessage );
        $('#myModal').modal( {'backdrop': false} )
      } else {
        isotopeMan.renderGraph(isotopeMan.getFilterString());
      }

    });

  },

  init_crmSubTab: function () {
    // console.log('Initail sub tab ');
    $('#sort-options button').on('click', function () {
      // var data = dataMan.fetchData(this.id);
      // graphMan.reRenderGraph(data);

      //Which button been clicked
      // alert('click on : '+$(this).attr('id'));
      if($(this).attr('id') == 'sample_drill'){

      }else{
              $.ajax({
                url: 'index.php?route=statistic/circle/' + this.id,
                data: {
                  token: location.search.split('&token=')[1].split('&')[0],
                },
                dataType: 'json',
                beforeSend:function(){
                // alert('send')
                progressDialog.show('Fetching data...');
              },
              success: function(resp) {
                // console.log(resp);
                if(resp.total <= 0) {
                  alert('Sorry no data at this time');
                }
                graphMan.reRenderGraph(resp);
              },
              error:function(err){
                console.log('Request fail');
                console.log(err);
              },
              complete:function(){
                //console.log('on complete')
                progressDialog.hide();
              }
            });
      }




    });
  },

  sample_with_field: function () {
    //console.log('sample with field');
    // $('#input_set_level').find('.chk_field input').attr('checked', 'checked')
    //if user check -> enable that field and collect data to be parameter
    // $('#input_set_reward').find('.chk_field input').removeAttr('checked')
    //if user not check -> disable that field and collect data to be parameter
    //force set male
    // $('#input_set_gender').find('.input_select option').eq(2).attr('selected', 'selected');
    // console.log('gender select > ' + $('#input_set_gender').find('.input_select').val())
    //force set action
    // $('#input_set_action').find('.input_select option').eq(1).attr('selected', 'selected');
    // $('#input_set_action').find('.input_field_range input').val('1999');
    // console.log('action select > ' + $('#input_set_action').find('.input_select').val() + ":" + $('#input_set_action').find('.input_field_range input').val());
    //select new object everytime cuz just a demo krub
  }
}

/* isotope part : @fake-or-dead code it */
var current_index = 1;
var isFirst = true;
isotopeMan = {
  renderGraph: function (filter, page) {
    if (typeof filter !== "object") {
      console.log('filter is not correct : ' + filter);
      return;
    }

    var sortOrder = isotopeMan.getSortOrder();
    $.ajax({
      url: baseUrlPath+'statistic/isotope',
      data: {
        filter_page: page,
        filter_sort: filter[0].replace('?filter_sort=', ''),
        filter_name: isotopeMan.getSearchName(),
        filter_order: sortOrder[1],
        sort: sortOrder[0]
        // filter_selected: filter[1].replace('?filter_selected=', '')
      },
      dataType: 'json',
      beforeSend: function () {
        //console.log('isotope render > sort: ' + filter.join(' filter_selected: ') + ' page: ' + page);
        //console.log('call url : ' + 'index.php?route=statistic/isotope&filter_page=' + page + '&filter_sort=' +filter[0]+'&token='+location.search.split('&token=')[1].split('&')[0]);
        progressDialog.show('Fetching data...');
      },
      success: function (resp) {
        // do html
        // console.log($("#player-isotopes").html());
        // console.log(resp.html.replace('\\', ''));
        // console.log(JSON.parse('{"regex": "' + resp.html + '"}');
        // var html = JSON.parse('{"regex" : "' + resp.html + '"}');
        // console.log(test);

        $("#player-isotopes").html(resp.html);
        current_index = parseInt(resp.current_page, 10);
        var max = (((current_index - 1) * resp.limit) + resp.limit);
        max = (max > resp.total_players) ? resp.total_players : max;
        var current = (((current_index - 1) * resp.limit) + 1) + ' - ' + max;
        if(max == 0) { current = '0'; }
        $('#current_result').html(current);
        $('#max_result').html(resp.total_players);
        isotopeRun();
        // // do pagination things
          if(isFirst){
            $("#pagination").jPaginator({
                nbPages: resp.total_page,
                nbVisible: resp.total_page,
                overBtnLeft:'#over_backward',
                overBtnRight:'#over_forward',
                maxBtnLeft:'#max_backward',
                maxBtnRight:'#max_forward',
                onPageClicked: function(a, num) {
                // self call
                current_index = num;
                isotopeMan.renderGraph(isotopeMan.getFilterString(), num);
            }
            });
            isFirst=false;
            // setup bar, event binding
            isotopeMan.setUpGraphEvent();
          }else{
              $("#pagination").trigger("reset",{
                  selectedPage:null,
                  nbPages: resp.total_page,
                  nbVisible: resp.total_page
              });
          }
      },
      error: function (err) {
        console.log('Request fail');
        console.log(err);
      },
      complete: function () {
        progressDialog.hide();
        // hack from flot.pie.js to remove pie
        // setTimeout(function() {
        //     $("#player-isotopes").children().filter(".pieLabel, .pieLabelBackground, .legend").remove();
        //     if(plot) plot.shutdown();
        // }, 50);
      }
    });
  },
  setUpGraphEvent: function () {

    // radio.btn-group toggle class active
    // $('.btn-group:not(.paging)').each(function () {
    //   $(this).children().on('click', function () {
    //     $(this).addClass('active').siblings().removeClass('active');
    //   });
    // });

    $('.btn-group.paging').each(function () {
      $(this).children().on('click', function () {
        // getIsoTope('', page);
        //console.log(this.id);
        current_index += (this.id === 'next') ? 1 : -1;
        current_index = (current_index <= 0) ? 1 : current_index;
        $("#pagination").trigger(this.id, {
          current_page: current_index
        });
      });
    });

    // Menu criteria
    $('.navbar.criteria .nav').on('click', 'a', function (e) {
      e.preventDefault();
      var $this = $(this),
        toggle = criteria.split('|');
      if (toggle.length == 1) {
        $('.navbar.criteria li').removeClass('active');
      } else {
        $('.navbar.criteria li').removeClass('active');
        $.each(toggle, function (k, v) {
          var trigger = v.split(':');
          if (trigger.length > 1) {
            $('#' + v.split(':')[0]).parent().addClass('active');
          }
        });
      }
      $this.parent().addClass('active');
    });

    $('.btn-player-group').click(function () {
      value = $(this).attr('data-option-value');
      //console.log('hi ! ' + value);
      // loadPlayer(value);
    });

    $('.common-filter').show();
    $('#pagination').show();
    $('.paginator').show();
  },

  getFilterString: function() {

    var filter_sort = '', filter_selected = '', input, values;

    $('.input-wrapper input').each(function() {

      if ( $(this).hasClass('active') ) {

        if ( this.name === 'level' ) {
          filter_sort += this.name + ':' + this.value + '|';
          filter_selected = this.name + ':' + this.value;
        } else {
          var text_filter = $(this).parent().parent().find(".btn-group .input-set-toggle").html();

          var value_filter = '';

          $.each($(this).parent().parent().find(".btn-group .dropdown-menu li"), function(i, v) {
            if($(v).find('a').html() == text_filter){
                value_filter = $(v).find('a').attr('value');
            }
          });

          filter_sort += this.name + '_id:' + value_filter + '|'
                       + this.name + '_value:' + this.value + '|';
        }
      }

    });

    var gender = $('.gender.active').val();
    if(gender && gender != "None"){
        filter_sort += 'gender:' + gender + '|';
    }

    // validate field
      // level required ? number only,  format xx - xx
      // action required ? number only, format xx - xx

    // manipulate parameters


//    console.log('filter_sort : ' + filter_sort);
//    console.log('filter_selected : ' + filter_selected);
    return [filter_sort, filter_selected];
  },

  getSortOrder: function() {

    var sort = $('.sort.active').val();
    if(sort && sort != "None"){
        if(sort == 'Name'){
            sort = 'first_name';
        }else{
            sort = sort.toLowerCase();
        }
    }else{
        sort = '';
    }
    var filter_order = $('.order.active').val();
    if(filter_order){
        if(filter_order == "High-Low"){
            filter_order = 'desc';
        }else{
            filter_order = 'asc';
        }
    }else{
        filter_order = 'asc';
    }

    return [sort, filter_order];
  },

  getSearchName: function() {
    return $(".filter_name").val();
  }
}

function filterBy(elem) {
  $('#masonry-item').isotope({
    filter: '.level-' + elem.id
  });
}

function loadSuccess(json) {
  $('#player-summary').html(json);
}

function isotopeRun(elem) {
  var $players = $('#masonry-item'),
    buffer;
  $players.isotope({
    masonry: {
      columnWidth: 110
    },
    //sortBy: 'point',
    getSortData: {
      point: function ($elem) {
        var number = ($elem.find('.isot-player-points span').length > 0) ? $elem.find('.isot-player-points span').text().replace(/points/g, '') : $elem.attr('data-number');
        return parseInt((number === '') ? 0 : number, 10);
      },
      level: function ($elem) {
        var number = ($elem.find('.isot-player-level span').length > 0) ? $elem.find('.isot-player-level span').text() : $elem.attr('data-number');
        return parseInt((number === '') ? 0 : number, 10);
      },
      name: function ($elem) {
        return $elem.find('.isot-player-name').text();
      }
    }
  });
  // recieve event from infograph
  if (typeof elem === 'object') {
    filterBy(elem);
  }
  var $optionSets = $('.btn.options');
  $optionSets.click(function () {
    var $this = $(this),
      options = {},
      value = $this.attr('data-option-value'),
      key = $this.parents('.btn-group').attr('data-option-key');
    value = (value === 'false') ? false : value;
    options[key] = value;
    if (key === 'layoutMode' && typeof changeLayoutMode === 'function') {
      // changes in layout modes need extra logic
      changeLayoutMode($this, options)
    } else {
      // otherwise, apply new options
      $players.isotope(options);
    }
    return false;
  });
  // Click and large element
  $players.on('click', 'ul.ison_action_set li', function (e) {
    // Stop event buble to .element
    e.stopPropagation();
    //console.log($(this).attr('action'));
    alert('Sorry, this feature still not available. ;___; ');
  });

  // Enlarge player isotope on click
  $players.on('click', '.isot-player-container', function() {
    $(this).addClass('large');
    $players.isotope('reLayout');
  });
  // Minimize player isotope
  $players.on('click', '.isot-player .isot-player-minimize', function(e) {
    e.stopPropagation();
    $(this).parent().parent().removeClass('large');
    $players.isotope('reLayout');
  });

}
// change layout
var isHorizontal = false;

function changeLayoutMode($link, options) {
  var wasHorizontal = isHorizontal;
  isHorizontal = $link.hasClass('horizontal');
  if (wasHorizontal !== isHorizontal) {
    // orientation change
    // need to do some clean up for transitions and sizes
    var style = isHorizontal ? {
      height: '80%',
      width: $container.width()
    } : {
      width: 'auto'
    };
    // stop any animation on container height / width
    $container.filter(':animated').stop();
    // disable transition, apply revised style
    $container.addClass('no-transition').css(style);
    setTimeout(function () {
      $container.removeClass('no-transition').isotope(options);
    }, 100);
  } else {
    $container.isotope(options);
  }
}
/* end of isotope part */

// Simplify slider creation
function initializeSlider(slider, min, max) {

  var sliderLabel = $(slider).closest('.input-set').find('.sliderRangeLabel');

  // Register slider
  $(slider).slider({
    range: true,
    min: min,
    max: max,
    values: [min, max],
    create: function(event, ui) {
      $(sliderLabel).val( min + "-" + max );
    },
    slide: function(event, ui) {
      $(sliderLabel).val( ui.values[0] + "-" + ui.values[1] );
    },
    stop: function(event, ui) {
      $(sliderLabel).closest('.input-prepend').find('.input-set-toggle').addClass('active');
      $(sliderLabel).addClass('active');
    }
  });

}

//MAIN
$(document).ready(function () {
  //call this just one time -> binding click event for tabs
  crmTab.init_crmTab();
  crmTab.init_crmSubTab();
  //remove it later -> just sample how to work with field
  crmTab.sample_with_field();
  //Force goto summary tab
  // $('#display_chart a').trigger('click')
  //Force go to point button
  $('#level ').trigger('click')
  //Binding event on section graph click
  graphMan.setOnGraphSectionClick();

  $('#player-tabs a:first').tab('show');
  $('#player-tabs a').click(function (e) {
    e.preventDefault();
    $(this).tab('show');
  });

  // slider target, min value, max value
  initializeSlider($('#input-set-level .sliderRange'), 1, 100);
  initializeSlider($('#input-set-action .sliderRange'), 1, 500);
  initializeSlider($('#input-set-reward .sliderRange'), 1, 50);

  // Update slider when input fields are changed
  $('.sliderRangeLabel').change(function() {
    var min, max, inputSet, values = $(this).val().split('-');
    // Only accept integer values
    min = parseInt(values[0],10);
    max = parseInt(values[1],10);
    if (!isNaN(min) && !isNaN(max)) {
      inputSet = $(this).closest('.input-set');
      inputSet.find('.sliderRange').slider( 'values', [ min, max ] );
      inputSet.find('.input-set-toggle').addClass('active');
    }
  });

  // Update buttons with value of dropdown
  $('.dropdown-menu > li > a').click( function() {
    var title = $(this).text();
    $(this).closest('.btn-group').find('.dropdown-title').text(title);
  });

  // radio.btn-group toggle class active
  $('.btn-group:not(.paging)').each(function() {
    $(this).children().on('click', function(){
      $(this).addClass('active').siblings().removeClass('active');
    }); 
  }); 
});
