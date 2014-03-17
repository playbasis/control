(function($){

  //Global variable
  var parameters = "", 
      starter = false,
      criteria, criteriaData;
  

  // graph business
  var donutGraph = { //Donut graph options 
  
    //init donu graph
    init: function(data) {
      
      $("#player-summary").on("plotclick", function (event, pos, obj) {


        //Go back if obj not exist
        if (!obj) return;

        //Extract graph position been click 
        var position = obj.series.label.replace(/ /g, '');
        //get range
        var titlePosition = (position.split(':'))[0];
            position = (position.split(':'))[1];        
                    // console.log('click on pos : ' + position);
        //set range value to last criteria element on bar

          if(titlePosition == "action_value" || titlePosition == "reward_value"){
              bar.setCriteria(titlePosition+':'+position);
          }else{
              bar.updateLastItemRange(position);
          }


        //Re render chart if starter == true 
          // meant user now click on starter chart
        if(starter){                  
            bar.clear();
            bar.setCriteria('level:'+position);

            //re-render chart
            //donutAPI.get(bar.getAllCriteria());
            //turnoff starter flag
            starter = false;
          return ;  
        }


        //Enable bootstrap popover to render HTML element
        // popover = $('#' + position).popover({
        //     html: true
        // });


        //Build context menu content(popover content)
        var content = function(){
          
          var list = $('<ul>');
              list.attr('id','root');

          //Iteration -> render context menu items
            $.each(data,function(k,v){

                //If any item existing on criteria bar -> hide it
                if( (bar.getAllCriteria()).indexOf(k.toLowerCase()) > -1 )
                  return true ;//return true inside 'each' : effect the same with using continue inside 'for'

                //Else
                var item = $('<li>');
                    item.addClass('context-'+k.toLowerCase()); //Define context memu item class

                    //If parent item has child
                    if(v.length > 0){
                          item.addClass('parent')
                          var child = $('<ul>');//Child container generated

                            child.attr('id',k.toLowerCase());
                          $.each(v,function(k1,v1){
                            var childitem = $('<li>');//Child element generated
                              childitem.attr('id',v1.id)
                              childitem.addClass('context-menu-option');//Set clickable class
                              childitem.html(v1.name);
                              child.append(childitem);
                          })
                          item.html(k);//Set text
                          item.append(child);
                    }else{
                          item.html(k);//Set text
                          item.addClass('context-menu-option');//Set clickable class
                    }

                list.append(item);
              });

              return list;
          }//END : Build context menu content(popover content)

          $('.context-menu').hide('fast');
          $('.context-menu .box-content').html(content);
          $('.context-menu').show('fast');

        // //Hide all currently appear popover element 
        //   $('.chart-popover').popover('hide')



        //Set time out to show popover element 
        setTimeout(function(){

        //   $('#' + position).popover('show'); 
        //   $('.popover-content').empty();
        //   $('.popover-content').html(content);//Append contend which been built




        // //Popover position adjustment
        // var height = $('.popover').height();
        //   if(height > 500)
        //     $('.popover').addClass('popover-move-max')
        //   else if(height < 500 && height > 280)
        //     $('.popover').addClass('popover-move')
        //   else if(height < 280 && height >120)
        //     $('.popover').addClass('popover-move_minor')









        //Context menu item : click event 
        $('.context-menu-option').on('click',function(){



                //start : trigger - view user
                if(  ($(this).attr('class')).indexOf('context-view_user') > -1 ){
                  //TODO :: implement if user click on view user 
                    console.log('View user on isotope view : '+bar.getAllCriteria())

                    var currentParamSet = bar.getAllCriteria().split('|');
                    console.log(currentParamSet);//return ;

                    resetFilter();

                    for(var index in currentParamSet){


                      var startPoint =0;
                      var endPoint = 100;
                      var parentId = '';
                      // var isLevelRange = false;
                      var p = currentParamSet[index];
                      // console.log(p);

                        if(p==undefined || p=="")
                          continue;

                        p = p.split(':');
                        // console.log('Add : '+p[0]+'-'+p[1]+'-'+p[2]);

                        if(p[0]=='level' && p[2]==undefined){
                          
                            parentId = '#input-set-level ';

                            $('#input-set-level .input-set-toggle').addClass('active');
                            $('#input-set-level .ipt-primary').val(p[1]);
                            $('#input-set-level .ipt-primary').addClass('active');

                        }else if(p[0]=='action_id'){

                          parentId = '#input-set-action ';

                            $.each($('#input-set-action .dropdown-menu li'), function() {
                                if($(this).find('a').attr('value') == p[1]){
                                    $(this).find('a').click();
                                }
                            });

                          $('#input-set-action .input-set-toggle').addClass('active');

                        }else if(p[0]=='action_value'){

                            parentId = '#input-set-action ';

                            $('#input-set-action .ipt-danger').val(p[1]);
                            $('#input-set-action .ipt-danger').addClass('active');

                        }else if(p[0]=='reward_id'){

                            parentId = '#input-set-reward ';

                            $.each($('#input-set-reward .dropdown-menu li'), function() {
                                if($(this).find('a').attr('value') == p[1]){
                                    $(this).find('a').click();
                                }
                            });

                            $('#input-set-reward .input-set-toggle').addClass('active');

                        }else if(p[0]=='reward_value'){

                          parentId = '#input-set-reward ';

                          $('#input-set-reward .ipt-success').val(p[1]);
                          $('#input-set-reward .ipt-success').addClass('active');
                         
                        }else if(p[0]=='gender'){
                          
                          //Defined key
                          key = {"m" : 1, "f" : 2 , "*" : 0,"Male" : 1, "Female" : 2 , "Unknow" : 0}

                          $('.common-filter div').each(function(){
                            if( $(this).attr('data-option-key')=='filter' ){
                              $(this).find('button:eq(0)').trigger('click');
                              //Real use
                                $(this).find('button:eq('+key[p[1]]+')').trigger('click');
                            }
                              
                          })

                          continue;

                        }

                        //Get range for level
                        var range = p[1].split('-');
                        if(range.length > 1){
                            startPoint = range[0];endPoint = range[1];
                        }else{
                            startPoint = endPoint = range[0];
                        }

                        $(parentId+'.sliderRangeLabel').val(startPoint+'-'+endPoint);
                        $(parentId+'.sliderRange a:eq(0)').attr('style','left: '+startPoint+'%');
                        $(parentId+'.sliderRange a:eq(1)').attr('style','left: '+endPoint+'%');
                        $(parentId+'.ui-slider-range').css('left',startPoint+'%');
                        $(parentId+'.ui-slider-range').css('width',(endPoint-startPoint)+'%');

                    }//end for 

                    
                    setTimeout(function(){$('.submit_filter_btn').trigger('click')},1200);
                    setTimeout(function(){$('a[href="#crm"]').trigger('click')},0);

                    return ;
                }//end : trigger - view user




                
                //Get parent id
                var parentId = $(this).parent().attr('id');

                var id_k = parentId;

                if(parentId == "action" || parentId == "reward"){
                    id_k = parentId+"_id";
                }

                if( parentId != 'root' ){ //If not child of root parent (action/reward)
                  newCriteria = parentId+':'+$(this).html().toLowerCase();
                  newCriteriaText = id_k+':'+$(this).attr('id').toLowerCase();
                }else{ //If not child of root parent (gender/view_user)
                  newCriteria = $(this).html().toLowerCase()+':';
                  newCriteriaText = $(this).html().toLowerCase()+':';
                }

                //Append new criteria to the bar
                bar.setCriteria(newCriteria, newCriteriaText);
                //Re-render chart
                console.log(bar.getAllCriteria());

                donutAPI.get(bar.getAllCriteria())
       })//End : context menu item : click event 



      }, 800); //End : Set time out to show popover element
          
      });
    },



    //Circular graph render
    generate: function(data) {
      //console.log('donut graph generate');
      //console.log(data);
      window.plot = $.plot($("#player-summary"), data, {
                series: {
                  pie: {
                    innerRadius: 0.4,
                    show: true,
                    label: {
                      show: true,
                      radius: 0.99,
                      formatter: function (label, series) {
                      
                      label = ''+ label;
                      var labelWithRange = $.trim(label.replace(/ /g, '')), //remove white spaces
                          labelToDeterminePosition = (labelWithRange.split(':'))[1], //get range
                          labelElem = $('<span>')
                                          .css({
                                            'font-size' :'.8em',
                                            'text-align':'center',
                                            'padding'   :'2px 8px',
                                            'margin'    :'4px',
                                            'color'     :'#999',
                                            'float'     :'left',
                                            '-webkit-border-radius': '8px',
                                            '-moz-border-radius': '8px',
                                            'border-radius': '8px'
                                          })
                                          .addClass('pull-left label_range')
                                          .html(label)
                                          .append('<br />')
                                          .append(
                                            $('<span>')
                                                .addClass('percentage')
                                                .css({
                                                  'font-size'   :'1.4em',
                                                  'font-weight' :'bold'
                                                })
                                                .html(Math.round(series.percent) + '%')
                                          ),
                          popOver = $('<span>')
                                          .attr({
                                            'id': labelToDeterminePosition,
                                            'data-html': true,
                                            'data-placement': 'right',
                                            // 'data-content': '<div class="load_menu"></div>',
                                            'data-original-title': 'Filter Select'  
                                          })
                                          .css({
                                            'float': 'left'
                                          })
                                          .addClass('pull-left chart-popover')
                                          .appendTo(labelElem);

                      return $('<div>').append(labelElem).html();

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
            });      
    }
  };
  // window.donut = donutGraph; // for test purpose

    var resetFilter = function() {
        $('#input-set-level .input-set-toggle').removeClass('active');
        $('#input-set-level .ipt-primary').val("");
        $('#input-set-level .ipt-primary').removeClass('active');
        $('#input-set-action .input-set-toggle').removeClass('active');
        $('#input-set-action .ipt-danger').val("");
        $('#input-set-action .ipt-danger').removeClass('active');
        $('#input-set-reward .input-set-toggle').removeClass('active');
        $('#input-set-reward .ipt-success').val("");
        $('#input-set-reward .ipt-success').removeClass('active');
        $(this).find('button:eq(*)').trigger('click');
    };


  // query business
  var donutAPI = {
    get: function(criteria) {
      // console.log('donut api get :> '+config_statistic_link+'&filter='+criteria);
      // console.log(criteria);
      return  $.ajax({
                url: config_statistic_link,
                data: {filter_sort:criteria},
                type:'GET',
                dataType: "json",
                beforeSend: function(){
                  // progressDialog is global object
                  progressDialog.show('Fetching data...');
                },
                success:function(data){
                  //console.log('Request success');
                  //console.log(data);
                  
                  //hide context menu
                  $('.context-menu').hide();

                  donutGraph.generate(data.donut);

                  //Unbind previous event handler 
                  $("#player-summary").off("plotclick");
                  //Bind new event handler
                  donutGraph.init(data.options);
                },
                error:function(err){
                  console.log('Request fail');
                  console.log(err);
                },
                complete:function(){
                  // console.log('on complete')
                  progressDialog.hide();
                }
              });

    }
  };
  // window.api = donutAPI; // for test purpose

  // criteria's bar business
  var bar = {
    barClass: '.filter-collection',
    containerClass: '.filter-collection ul',

    init: function() {
      var container = $(this.containerClass);

      // binding data
      container.on('click', '.removeFilter', function(){
        // console.log('removeFilter click');
        // remove its self
          $(this).parent().remove();
        

        // If delete last criteria on bar -> will going to reset bar and start over drill down progress
        if(bar.memberCount()<1){
          $('.resetFilter').trigger('click')
        }

        // call donutAPI to re-query
            // bar.getAllCriteria();
            // console.log('re-render with : '+bar.getAllCriteria());
        //Re-render 
        donutAPI.get(bar.getAllCriteria())

      });



      $(this.barClass + ' .resetFilter').on('click', function() {
        // console.log('resetFilter click');
        //hide context menu
        $('.context-menu').hide();

        container.empty();
        //Set default criteria
        bar.setCriteria('level:');
        //turn-on starter flag
        starter = true;

      //Re-render
      donutAPI.get(bar.getAllCriteria())
        // call donutAPI default query
      });

    },

    //retrive all criterai to be filterList parameter
    getAllCriteria: function() {
      var container = $(this.containerClass),
          criteria = "";

      container.children().each(function() {
          criteria += $(this).attr('data-criteria') + '|';
      });
      return criteria;
    },

    //Append new criteria element
    setCriteria: function(criteria, criteriaData) {
        if(!criteriaData){
            criteriaData = criteria;
        }

        var container = $(this.containerClass),
            check_all = "";

        container.children().each(function() {
            check_all += $(this).attr('data-criteria') + '|';
        });

      check_all = check_all.split("|");

      var check = criteriaData;
      var hasCriteria = false;

      $.each(check_all,function(i, c){
          if(c.split(":")[0] == check.split(":")[0]){
            hasCriteria = true;
            return false;
          }
      });

      if(!hasCriteria){
          var container = $(this.containerClass),
              listItem = $('<li class="filter-pill more-round">'),
              removeBtn = $('<i class="removeFilter remove-filter-btn fa-icon-remove-sign"></i>');
          criteria = criteria.split(':');
          criteriaData = criteriaData.split(':');

          listItem
              .attr('data-criteria', criteriaData.join(':'))
              .attr('title', (criteriaData.length >= 2) ? criteriaData[1] : criteriaData[0])
              .append('<span>' + criteria.join(':') + '</span>')
              .append(removeBtn);

          container.append(listItem);
      }
    },


    //Update range value for last criteria element
    updateLastItemRange: function(position){
      var target = $('.filter-collection ul li:last');
      var data = target.attr('data-criteria');
      var dataText = target.find("span").html();

      data = data.split(':');
      dataText = dataText.split(':');
      if(data.length <= 2 ){
        data[1] = position;
        dataText[1] = position;
      }else if(data.length <= 3 ){
        data[2] = position;
        dataText[2] = position;
      }
      data = data.join(':');
      dataText = dataText.join(":");

      target.remove();
      bar.setCriteria(dataText, data);


    },

    memberCount:function(){
      return $('.filter-collection ul li').length;
    },

    clear:function(){
      $(this.containerClass).empty();
    }
  };
  // window.bar = bar; // for test purpose

  // init
  $(document).ready(function() {

    // console.log('player/script.js');
      bar.init();
    $('.resetFilter').trigger('click');

    //Re-render
    //donutAPI.get(bar.getAllCriteria());

    
    $('.context-menu-close').on('click',function(){
      $('.context-menu').hide('fast');
    })




  });

})(jQuery);;