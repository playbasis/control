/*
Author : Rokios
Date : 12:49 PM 3/12/2013
Summary : manipulate rules table -> render table from json data , made pagination , search by name and tag , sortable column head
*/
var DEBUG = false;

//Mod : rule table manager
var ruleTableMan = {
  targetTable:'rulelist',
  tableclothed: false,

  flushTable:function(){
    $('table.' + this.targetTable + ' tbody').empty();
  },

  reRenderTable:function(json){

  // Parse json input string to jsonObject and generate HTML element 
  // then setup parameter and append to Rule Tables
    if(typeof json === 'string') {
      try {
        json = $.parseJSON(json);
      }
      catch(e) {
        return;
      }
    }
    if(DEBUG)console.log(json);
    var tableObj = $('table.' + ruleTableMan.targetTable + ' tbody').empty();
    if(tableObj.find('tr').length > 0) {
      tableObj.empty();
    }

    // check for flag that doesn't appear when have data 
    // and will appear when don't have data 
    if(typeof json.success !== 'undefined' && !json.success) {
      return;
    }
    $.each(json, function() {
      // each rules
      var row = $('<tr>').attr('id', this.rule_id),
          ruleColumn = $('<td>').appendTo(row),
            title = $('<span>').html(this.name).appendTo(ruleColumn),
            description = $('<span>').html(this.description).appendTo(ruleColumn).hide(),
            tags = $('<span>').addClass('pull-right').appendTo(ruleColumn),
          actionNameColumn = $('<td>').appendTo(row);
          createDateColumn = $('<td>').appendTo(row),
          modifiedDateColumn = $('<td>').appendTo(row).hide(),
          statusColumn = $('<td>').appendTo(row),
          actionColumn = $('<td>').addClass('center').appendTo(row),
          export_rule = $('<td>').addClass('hidden tb_hilight center').appendTo(row);

      // error sign
      if(this.error != undefined && this.error != ''){
        var errorHtml = ' <a herf="javascript:void(0)" class="error-icon" title="'+this.error+'" data-toggle="tooltip"><i class="icon-warning-sign" ></i></a>';
        $('<span>').addClass('red').html(errorHtml).appendTo(ruleColumn);
        $('.error-icon').tooltip();
      }

      // Frequency
      var noFrequency = '';
      if(this.usage == undefined || this.usage == '' ){
        this.usage = 0;
        noFrequency = "no-frequency";
      }
      var str = 'This rule has been executed '+this.usage+' times';
      if (this.usage_sync_date) str += ' (last synced: '+this.usage_sync_date+')';
      var frequencyHtml = '<div style="display: none">'+this.name+'</div> <strong class="frequency-icon '+noFrequency+' label" title="'+str+'" data-toggle="tooltip">'+this.usage+'</strong> ';
      $(frequencyHtml).prependTo(ruleColumn);
      $('.frequency-icon').tooltip();

      // tags section
      if(this.tags !== undefined && this.tags !== '')
      $.each(this.tags.split(','), function() {
        tags.append(
          $('<span class="label">')
            .addClass(ruleTableMan.tagColorMap(this.toString()))
            .html(this.toString())
        );
      });

      // actionName section
      actionNameColumn.html(this.action_name);

      // date section
      createDateColumn.html(this.date_added.substring(0, 10));
      modifiedDateColumn.html(this.date_modified.substring(0, 10));

      // status section
      var status = (this.active_status == 1) ? [true, 'Enable', '#3B9900'] : [false, 'Disable', '#BF0016'];
      statusColumn.append(
        $('<span>')
          .addClass('slider-frame ' + status[1])
          .css('background', status[2])
          .append(
            $('<span>')
              .addClass('slider-button ' + (status[0] ? 'on': ''))
              .html(status[1])
          )
      );      
        
      // action section
      actionColumn.append(
        $('<span>')
          .addClass('dropdown mini-round')
          .append(
            $('<a>')
              .attr('id', 'action')
              .attr('role', 'button')
              .attr('data-toggle', 'dropdown')
              .addClass('dropdown-toggle btn btn-mini')
              .append(
                $('<i class="icon-edit "></i>')
              )
          )
          .append(
            $('<ul>')
              .attr('role', 'menu')
              .attr('aria-labelledby', 'action')
              .addClass('dropdown-menu')
              .append(
                $('<li><a tabindex="-1" href="#" class="gen_rule_edit_btn"><i class="icon-edit"></i>Edit</a></li>')
              )
              .append(
                $('<li class="divider"></li>')
              )
              .append(
                $('<li><a tabindex="-1" href="#" class="gen_rule_delete_btn"><i class="icon-trash"></i>Delete</a></li>')
              )
              .append(
                $('<li class="divider"></li>')
              )
              .append(
                $('<li><a tabindex="-1" href="#" class="gen_rule_play_btn"><i class="icon-play"></i>Play</a></li>')
              )
          )
      )

      export_rule.append(
          $('<input type="checkbox" name="import_selected"  />').attr('value', this.rule_id)
      )

      // add row to table
      tableObj.append(row);
    }); 

    // console.log('Table size is now : '+tableObj.find('tr').length);
    // Note : all rule in the table are now not display : it's will display after run pagination operate

    setTimeout(function() {
    if (jQuery().tablecloth) {
      $("table." + ruleTableMan.targetTable ).tablecloth({
        sortable: true
      });
      ruleTableMan.tableclothed = true;
      // fix sorting bug
      var active = $("li.page_index_number.active");
      if (active) {
        var children = active.children();
        if (children && children.length > 0) {
          children[0].click();
        }
      }
    }
    else {
      console.log('tablecloth.js is not loaded');
    }
    }, 2500);
  },

  sortify: function() {
    if(DEBUG)console.log('sortify');
    $("table." + ruleTableMan.targetTable ).trigger('update');
  },

  tagColorMap:function(tag) {
    switch(tag) {
      case 'new':
        return 'label-info';
      case 'basic':
        return 'label-success';
      case 'promotion':
        return 'label-important';
      case 'trash':
        return 'label-inverse';
      case 'draft':
        return 'label-warning';  
    }
  }
};

//Mod : rule table search
ruleTable_SearchMan = {

  newOperateFilterSearch:function(jsonString) {

    $('input.rule_search_filter').on('change', function(){
      var search = this.value,
          json = $.parseJSON(jsonString),
          newRuleList = [];

      if(DEBUG) {
        console.log('operation > search > '+ search);
        console.log(json);
      }
          
      $.each(json, function() {
        if(this.name.indexOf(search) >= 0 
        || this.tags.indexOf(search) >= 0 ) {
          
          newRuleList.push(this);
          
          if(DEBUG) {
            console.log('found one : ');
            console.log(this);
          }
        }
      });

      json = JSON.stringify(newRuleList);

      ruleTableMan.reRenderTable(json); //Rerender by search result String
      ruleTable_PaginationMan.newOperatePagination(); //Set pagination by current table rows

    });
  }
}




//Mod : rule table row status
ruleTableRow_statusMan = {
  
  // Set Status_button in the with right value of it.
  sweepRuleStatus:function (obj){

    var classes = obj.attr('class');
    if( classes.indexOf('Enable') >= 0 ) {
      obj.addClass('Enable');
      obj.removeClass('Disable');
      
      this.sliderBtnForceToEnable(obj);
    }
    else if( classes.indexOf('Disable') >= 0 ) {
      obj.removeClass('Enable');
      obj.addClass('Disable');
      
      this.sliderBtnForceToDisable(obj);
    }
  },

  sliderBtnForceToDisable:function(frame){
    // if(DEBUG)console.log('trying to Disable this object : ');
    // if(DEBUG)console.log(frame)
    var obj = frame.find('.slider-button');
    obj.removeClass('on').html('Disable');
    frame.css('background','#BF0016');

  },

  sliderBtnForceToEnable:function(frame){
    // if(DEBUG)console.log('trying to Enable this object : ');
    // if(DEBUG)console.log(frame)
    var obj = frame.find('.slider-button');
    obj.addClass('on').html('Enable');
    frame.css('background','#3B9900');
  },


  operateSlideBtn:function(){ //Start : ruleTable_StatusMan

    // clear dupplicate binding data 
    $('.ruleslist_table').off('click', '.slider-frame');
    $('.ruleslist_table').on('click', '.slider-frame', function(){
      //start Click
      //define things 
        dataMan.currentRuleIdToChangeState = $(this).parent().parent().attr('id');
        var clickedSlideBtn = $(this);//Item which been clicking on
        var classes = clickedSlideBtn.attr('class');//All classes of that Item
        //end define things

        if( classes.indexOf('Enable') >= 0 ){
          if(DEBUG) {
            console.log('trying to disable of rule > id : '+dataMan.currentRuleIdToChangeState);
          }
          dataMan.setRuleStatus('disable', clickedSlideBtn);


        }else if( classes.indexOf('Disable') >= 0 ){
          if(DEBUG) {
            console.log('trying to enable of rule > id : '+dataMan.currentRuleIdToChangeState);
          }
          dataMan.setRuleStatus('enable',clickedSlideBtn);

      }//End else if value is disable
    });//End Click

  }
}



//Mod : rule table pagination
ruleTable_PaginationMan = {
  
  pageSize: 10,
  currentPage: 1,
  maxPage: 0,

  newOperatePagination:function(){

    //first call : pagination setup 
    var paginationBar = $('.ul_rule_pagination_container').empty(),
        all_rules = $('table.' + ruleTableMan.targetTable + ' tbody tr').length;
    
    this.maxPage = Math.ceil(all_rules/this.pageSize);

    // set prev button
    paginationBar.append('<li class="page_index_nav prev"><a href="#prev">Prev</a></li>');   

    for(i = 1; i <= this.maxPage ; i++){
      if(i == 1){
        paginationBar.append('<li class="page_index_number active"><a href="#">' + i + '</a></li>');
      }
      else{
        paginationBar.append('<li class="page_index_number"><a href="#">'+ i +'</a></li>');
      }
    }
    paginationBar.append('<li class="page_index_nav next"><a href="#next">Next</a></li>');

    $('.page_index_number').on('click',function(event){
      event.preventDefault();

      $('.page_index_number').removeClass('active');
      $(this).addClass('active');
      ruleTable_PaginationMan.showPage(parseInt($(this).find('a').html()),ruleTable_PaginationMan.pageSize);  
    });


    //Control prevent default
    $('.page_index_nav').on('click', 'a', function(event){
      event.preventDefault();

      if($(this).parent().hasClass('next')) {
        if(ruleTable_PaginationMan.currentPage < ruleTable_PaginationMan.maxPage) {
          ruleTable_PaginationMan.currentPage += 1;            
        }
      }
      else {
        // we assume it's prev
        if(ruleTable_PaginationMan.currentPage > 1) {
          ruleTable_PaginationMan.currentPage -= 1;
        }
      }
      // number things
      $('.page_index_number').removeClass('active');
      $('.page_index_number a').each(function() {
        if(this.text == ruleTable_PaginationMan.currentPage) {
          $(this).parent().addClass('active');
        }
      });
      ruleTable_PaginationMan.showPage(ruleTable_PaginationMan.currentPage, ruleTable_PaginationMan.pageSize);
    }); 
    
    this.showPage(this.currentPage, ruleTable_PaginationMan.pageSize);

  },

  getPaginationPage: function() {

  },


  showPage:function(page, pageSize){
    if(DEBUG) console.log('show page > ' + page);

    var obj = $('table.' + ruleTableMan.targetTable + ' tbody tr').hide();

    obj.each(function(row){
      if(row >= ruleTable_PaginationMan.pageSize * ( page - 1 ) 
        && row < ruleTable_PaginationMan.pageSize * page)
        $(this).show();
    });
    ruleTableMan.sortify();
  }
}


//########################################
//Event 
//########################################
// Rule Table Pagination : Event 

setTimeout(function(){
    ruleTable_PaginationMan.showPage(ruleTable_PaginationMan.currentPage, ruleTable_PaginationMan.pageSize);
},200); 
//End Section : ruleTables
