
// rule_editor.js
var DEBUG = false;


//########################################
//Header
//########################################
urlConfig = {
  forgeCustomActionURL:function(customAction){
      var protocal = window.location.protocol+'//';
      var root = window.location.host+window.location.pathname;
      var paramset = (window.location.search).split('&');
//      for(var index in paramset){
//        if(paramset[index].indexOf('route')>-1){
//          paramset[index] = '?route=admin/rules/'+customAction
//        }
//      }
      paramset = paramset.join('&');
      var url = '';
      if(customAction){
          url = protocal+root+'/'+customAction+paramset;
      }else{
          url = protocal+root+paramset;
      }
      //console.log(url);
      return url;
  },

  URL_getRules: function(){ return this.forgeCustomActionURL('jsonGetRules')},
  URL_getRuleById: function(){ return this.forgeCustomActionURL('jsonGetRuleById')},
  URL_saveRule: function(){ return this.forgeCustomActionURL('jsonSaveRule')},
  URL_exportRule: function(){ return this.forgeCustomActionURL('jsonExportRule')},
  URL_importRule: function(){ return this.forgeCustomActionURL('jsonImportRule')},
  URL_cloneRule: function(){ return this.forgeCustomActionURL('jsonCloneRule')},
  URL_playRule: function(){ return this.forgeCustomActionURL('jsonPlayRule')},
  URL_deleteRule: function(){ return this.forgeCustomActionURL('deleteRule')},
  URL_changeRuleState: function(){ return this.forgeCustomActionURL('setRuleState')},
  URL_getBadges: function(){ return this.forgeCustomActionURL('loadBadges')},
  URL_getGoods: function(){ return this.forgeCustomActionURL('loadGoods')}
},



//########################################
//Body
//########################################

//Section : data
//Mod : all data communication with server
dataMan = {

  siteId:jsonConfig_siteId,
  clientId:jsonConfig_clientId,

  loadPrerender_jigsawConfig: function(){},
  loadPrerender_jigsawBadges: function(){},
  loadRulesTable:function() {
      $.ajax({
          url: urlConfig.URL_getRules(),
          data: 'ts='+(new Date()).getMilliseconds(),
          type:'GET',
          dataType:'json',
          beforeSend:function(){
              progressDialog.show('Fetching rules ...');
          },
          success:function(data){
              ruleTableMan.reRenderTable(data);
              ruleTable_PaginationMan.newOperatePagination();
              ruleTable_SearchMan.newOperateFilterSearch(data);

              /*
               *  bad cast methods
               *  should use cascade binding that you
               *  see in the rule_editor_table.js for cascade binding methods
               *  call this every time when loadRulesTable made dupplicate binding
               *  should be refactor in the future
               *  now I fix by unbinding before each bind for the hotfix
               *  9 apr 2013 - Piya refactor
               */
              ruleTableRow_statusMan.operateSlideBtn();

              dialogMsg = 'Rules Table load successful';
          },
          error:function(){
              dialogMsg = 'Cannot load rule from server,\n Please try again later';
              return false;
          },
          complete:function(){
              notificationManagerJS.showAlertDialog('loadtable', dialogMsg);
              progressDialog.hide();
          }
      });
  },

  loadForEdit:function(){
    $.ajax({
          url: urlConfig.URL_getRuleById(),
          data: 'ruleId='+dataMan.currentRuleIdToLoad +'&siteId='+jsonConfig_siteId+'&clientId='+jsonConfig_clientId+'&ts='+(new Date()).getMilliseconds(),
          type:'GET',
          dataType:'jsonp',
          beforeSend:function(){
            progressDialog.show('Fetching rule ...');
          },
          success:function(data){
            // if(($.parseJSON(data)).success==true){
              if(DEBUG)console.log('Current Rule : ')
              if(DEBUG)console.log(data)
              dialogMsg = 'Load <b style="color:green">'+data.name+'</b> from server successful';


            // }else{
              // dialogMsg = 'Unable to load rule , Please try agian later';
            // }
            // if(DEBUG)alert('load succecc -> render')
              oneRuleMan.discardCurrentRule();
              oneRuleMan.openRuleEditor();
              oneRuleMan.oneRuleFromJSON(data)

          },
          error:function(){
            dialogMsg = 'Cannot load rule from server,\n Please try again later';
            return false;
          },
          complete:function(){
            notificationManagerJS.showAlertDialog('loadrule',dialogMsg);
            progressDialog.hide();
            dataMan.currentRuleIdToLoad = undefined;


            $.each($(".name_interval_unit"), function(index, value) {
              var newText = $(this).parent().find(".pbd_rule_data .view_as_hidden").html();

              var newVal = $(this).parent().parent().find(".name_interval").parent().find(".pbd_rule_data .pbd_rule_field .timeout").val();
              $(this).parent().parent().find(".name_interval").parent().find(".pbd_rule_data .view_as_timeout").html(newVal+" "+newText[0].toUpperCase() + newText.slice(1));
            });
          }
    });


  },

  cloneRule:function(rule_id){
        var save_status = false;
        var _data = {
            'id': rule_id,
            'site_id': jsonConfig_siteId,
            'client_id' : jsonConfig_clientId
        };
        _data[csrf_token_name] = csrf_token_hash;
        $.ajax({
          url: urlConfig.URL_cloneRule(),
          data:_data ,
          type:'POST',
          // dataType:'json',
          beforeSend:function(){
            progressDialog.show('Saving rule ...');
          },
          success:function(data){
            //console.log('on success')
            //console.log(data);

            if(($.parseJSON(data)).success==true)
              dialogMsg = 'Save rule to server successfully';
            else
              dialogMsg = 'Unable to save rule to server , Please make sure all field been filled';

            save_status = true;
          },
          error:function(){
            //console.log('on error')
            dialogMsg = 'Cannot save file to server,\n Please try again later';

          },
          complete:function(){
            //console.log('on complete')
            notificationManagerJS.showAlertDialog('save',dialogMsg);
            progressDialog.hide();

            if(save_status){
              //Reload - Update Rule table on left
              setTimeout(function(){dataMan.loadRulesTable()},500);

              //Prevent user to saving the same rule again
              oneRuleMan.discardCurrentRule();
              // $('.one_rule_discard_btn').trigger('click');

                            /* for sure have functon */
                            if($.isFunction($.fn.slidePanel)){
                                $().slidePanel();
                                $(".pbd_one_rule_holder").hide();
                            }
                            if($.isFunction($.fn.disableFixMenu)){
                                $(".fixMenu").disableFixMenu();
                            }

                            oneRuleMan.ruleActionPanelControl('not_editing');
            }

          }
        });
  },

  playRule:function(rule_id){
        var _data = {
          'id': rule_id,
          'site_id': jsonConfig_siteId,
          'client_id' : jsonConfig_clientId
        };
        _data[csrf_token_name] = csrf_token_hash;
        $.ajax({
          url: urlConfig.URL_playRule(),
          data: _data,
          type:'POST',
          beforeSend:function(){
            progressDialog.show('Playing rule ...');
          },
          success:function(data){
            var j = JSON.parse(data);
            var pretty = prettyPrint(j, {maxDepth:10});
            $("#playModal").empty();
            $("#playModal").append(pretty);
            $("#playModal").dialog({draggable: false, resizable: false, width: "auto"});
          },
          error:function(){
            //console.log('on error')
            dialogMsg = 'Cannot play rule,\n Please try again later';

          },
          complete:function(){
            //console.log('on complete')
            notificationManagerJS.showAlertDialog('played',dialogMsg);
            progressDialog.hide();

            // if(play_status){
            //   //Reload - Update Rule table on left
            //   setTimeout(function(){dataMan.loadRulesTable()},500);

            //   //Prevent user to saving the same rule again
            //   oneRuleMan.discardCurrentRule();
            //   // $('.one_rule_discard_btn').trigger('click');

            //                 /* for sure have functon */
            //                 if($.isFunction($.fn.slidePanel)){
            //                     $().slidePanel();
            //                     $(".pbd_one_rule_holder").hide();
            //                 }
            //                 if($.isFunction($.fn.disableFixMenu)){
            //                     $(".fixMenu").disableFixMenu();
            //                 }

            //                 oneRuleMan.ruleActionPanelControl('not_editing');
            // }

          }
        });
  },


  saveRule:function(rule_JSONString){
        var save_status = false;
        var _data = {
            'json': rule_JSONString ,
            'siteId': jsonConfig_siteId,
            'clientId' : jsonConfig_clientId
        };
        _data[csrf_token_name] = csrf_token_hash;
        $.ajax({
          url: urlConfig.URL_saveRule(),
          data: _data,
          type:'POST',
          // dataType:'json',
          beforeSend:function(){
            progressDialog.show('Saving rule ...');
          },
          success:function(data){
            //console.log('on success')
            //console.log(data);

            if(($.parseJSON(data)).success==true)
              dialogMsg = 'Save rule to server successfully';
            else
              dialogMsg = 'Unable to save rule to server , Please make sure all field been filled';

            save_status = true;
          },
          error:function(){
            //console.log('on error')
            dialogMsg = 'Cannot save file to server,\n Please try again later';
            
          },
          complete:function(){
            //console.log('on complete')
            notificationManagerJS.showAlertDialog('save',dialogMsg);
            progressDialog.hide();

            if(save_status){
              //Reload - Update Rule table on left
              setTimeout(function(){dataMan.loadRulesTable()},500);
              
              //Prevent user to saving the same rule again
              oneRuleMan.discardCurrentRule();
              // $('.one_rule_discard_btn').trigger('click');

                /* for sure have functon */
                if($.isFunction($.fn.slidePanel)){
                    $().slidePanel();
                    $(".pbd_one_rule_holder").hide();
                }
                if($.isFunction($.fn.disableFixMenu)){
                    $(".fixMenu").disableFixMenu();
                }

                oneRuleMan.ruleActionPanelControl('not_editing');
            }
            

            // notificationManagerJS.showAlertDialog('','Updateing Rule table...');
            // // alert('refresing rule table')
            // setTimeout(function(){
            //  initRulelist(); 
            // },1000);

            /*TODO : Implement Rule set updating*/

          }
        });
  },

  exportRule:function(array_rules){
        var export_status = false;
        var _data = {
            'array_rules': array_rules ,
            'site_id': jsonConfig_siteId,
            'client_id' : jsonConfig_clientId
        };
        _data[csrf_token_name] = csrf_token_hash;
        $.ajax({
            url: urlConfig.URL_exportRule(),
            data: _data,
            type:'POST',
            // dataType:'json',
            beforeSend:function(){
                progressDialog.show('Exporting rule ...');
            },
            success:function(data){

                if(($.parseJSON(data))){
                    var output_data = "text/json;charset=utf-8," + encodeURIComponent(JSON.stringify($.parseJSON(data)));
                    link = document.createElement('a');
                    link.setAttribute('href', 'data:' +output_data);
                    link.setAttribute('download', 'rules.json');
                    link.click();
                    export_status = true;
                    dialogMsg = 'Export rule successfully';}
                else {
                    dialogMsg = 'Unable to Export rule from server ';
                }


            },
            error:function(){
                //console.log('on error')
                dialogMsg = 'Unable to Export rule from server,\n Please try again later';

            },
            complete:function(){
                //console.log('on complete')
                notificationManagerJS.showAlertDialog('save',dialogMsg);
                progressDialog.hide();

                if(export_status){
                    oneRuleMan.ruleActionPanelControl('cancel');
                }
            }
        });
  },

  importRule:function(array_rules){
        var import_status = false;
        var _data = {
            'array_rules': array_rules ,
            'site_id': jsonConfig_siteId,
            'client_id' : jsonConfig_clientId
        };
        _data[csrf_token_name] = csrf_token_hash;
        $.ajax({
            url: urlConfig.URL_importRule(),
            data: _data,
            type:'POST',
            // dataType:'json',
            beforeSend:function(){
                progressDialog.show('Importing rule ...');
            },
            success:function(data){
                //console.log('on success')
                //console.log(data);

                if(($.parseJSON(data).status=='success')){

                    import_status = true;
                    dialogMsg = 'Import rule successfully';
                }
                else {
                    dialogMsg = $.parseJSON(data).message;
                }


            },
            error:function(){
                //console.log('on error')
                dialogMsg = 'Unable to Export rule from server,\n Please try again later';

            },
            complete:function(){
                //console.log('on complete')
                notificationManagerJS.showAlertDialog('save',dialogMsg);
                progressDialog.hide();

                if(import_status){
                    //Reload - Update Rule table on left
                    setTimeout(function(){dataMan.loadRulesTable()},500);

                    oneRuleMan.ruleActionPanelControl('cancel');

                    if($.isFunction($.fn.slidePanel)){
                        $().slidePanel();
                    }

                }


                // notificationManagerJS.showAlertDialog('','Updateing Rule table...');
                // // alert('refresing rule table')
                // setTimeout(function(){
                //  initRulelist();
                // },1000);

                /*TODO : Implement Rule set updating*/

            }
        });
  },


  deleteRule:function(){
    // console.log('deleting : '+'ruleId='+dataMan.currentRuleIdToDelete +'&siteId='+jsonConfig_siteId+'&clientId='+jsonConfig_clientId);
        var _data = {
            'ruleId': dataMan.currentRuleIdToDelete ,
            'siteId': jsonConfig_siteId,
            'clientId' : jsonConfig_clientId
        };
        _data[csrf_token_name] = csrf_token_hash;
        $.ajax({
          url: urlConfig.URL_deleteRule(),
          data: _data,
          type:'POST',
          // dataType:'json',
          beforeSend:function(){
            progressDialog.show('Deleting rule ...');
          },
          success:function(data){
            if(($.parseJSON(data)).success==true){
              dialogMsg = 'Rule delete successfully';
              
              //remove view from display
              $('table.' + ruleTableMan.targetTable + ' tbody tr#'+dataMan.currentRuleIdToDelete).remove()

            }else{
              dialogMsg = 'Unable to delete rule , Please try agian later';
            }
          },
          error:function(){
            dialogMsg = 'Cannot delete rule from server,\n Please try again later';
            return false;
          },
          complete:function(){
            notificationManagerJS.showAlertDialog('delete',dialogMsg);
            progressDialog.hide();
            dataMan.currentRuleIdToDelete = undefined;
          }
        });
  },


  setRuleStatus:function(toState, targetObj){
      var _data = {
          ruleId:   dataMan.currentRuleIdToChangeState,
          state:    toState,
          siteId:   jsonConfig_siteId,
          clientId: jsonConfig_clientId
      };
      _data[csrf_token_name] = csrf_token_hash;
     $.ajax({
      url: urlConfig.URL_changeRuleState()+'?ts='+(new Date()).getMilliseconds(),//Add time stamp to avoid the cash data in any cases
      data: _data,
      type:'POST',
      beforeSend:function(){
        progressDialog.show('Apply change ...');
      },
      success:function(data){

        if(($.parseJSON(data)).success==true){
          dialogMsg = $(targetObj.parent().siblings()[0]).find('span')[0].innerHTML + ' Rule has been ' + toState + '!';
          window.x = targetObj;
          if(toState=='disable'){
            targetObj.removeClass('Enable');
            targetObj.addClass('Disable');
          }else if(toState =='enable'){
            targetObj.addClass('Enable');
            targetObj.removeClass('Disable');
          }
          ruleTableRow_statusMan.sweepRuleStatus(targetObj);

        }else{
          dialogMsg = 'Unable to change rule state!';
        }
          
      },
      error:function(){
        dialogMsg = 'Cannot change rule state!,\n Please try again later';
        return false;
      },
      complete:function(){
        notificationManagerJS.showAlertDialog('', dialogMsg);
        progressDialog.hide();

        dataMan.currentRuleIdToChangeState = undefined;
      }
    });//end Ajaxing

  },


  getClassAttrAfterPrefix:function(prefixString,setOfClasses){
    var len = setOfClasses.length;
    for(var i =0; i<len; i++){
      if(setOfClasses[i].indexOf(prefixString) == 0){
        return setOfClasses[i].substr(prefixString.length);
      }
    } 
  },

  objClassInspect:function(targetObject){
    var classesString = targetObject.attr('class')+'';
    var arrayOutout = classesString.split(' ');
    return arrayOutout;
  },

  removeClassAttrAfterPrefix:function(targetObject,prefixString){
    var classArr = this.objClassInspect(targetObject);
    targetObject.attr('class','');
    
    var len = classArr.length;
    var cloneArr=[];
    //using array clone instead of remove/delete
    for(var i =0; i<len; i++){
      if(classArr[i].indexOf(prefixString) != 0){
        cloneArr.push(classArr[i]);
      }
    } 

    for(var idxx in cloneArr){
      targetObject.addClass(cloneArr[idxx]);
    }
    return targetObject;
  },

  isObjClassExist:function(targetElement,className){
    var getClass = targetElement.attr('class');
    if(getClass==undefined)
      if(DEBUG)('unable to extract class name from undefined object');

    if(getClass!=undefined && getClass.indexOf(className)>-1)
      return true;
    return false;
  },


  masterTable:'masterTable',
  masterOneRule:'masterOneRule',
  currentRuleIdToChangeState:undefined,
  currentRuleIdToDelete:undefined,
  currentRuleIdToLoad:undefined,
}
//->
//End Section : data
