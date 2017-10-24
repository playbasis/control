// rule_editor.js
var DEBUG = false;

//########################################
//Header
//########################################




//########################################
//Body
//########################################

//Section : oneRule
//Mod : one Rule manager(Cover sub_module)
oneRuleMan = {

    /* TODO :: Implementing one rule manage
     > create nodeList from blank
     > create nodeList from existing rule (load_to_edit/import)
     > discard current editing rule
     */

    nodeList:[],

    site_id:undefined,
    client_id:undefined,
    action_id:undefined,

    rule_id:undefined,
    name:undefined,
    description:undefined,
    rule_tags:undefined,

    rule_header:$('.pbd_box_content_head'),
    currentRuleContainer:"",

    //About node create dialog
    openNodeSelectionDialogType:undefined,
    openNodeSelectionDialogTargetId:undefined,
    openNodeSelectionDialogTargetType: undefined,// random | sequence | badge
    modalOptionSelectedItem:undefined,
    nodeInsertAfterPosition:undefined,

    setRuleHeader:function(json){

        if(DEBUG)console.log('reset rule head')
        if(DEBUG)console.log(json)

        this.rule_id = json.rule_id;
        if(this.rule_id == undefined || this.rule_id == '')this.rule_id = 'undefined';
        this.client_id = dataMan.clientId;
        this.site_id = dataMan.siteId;
        this.action_id = json.action_id;

        this.name = json.name;
        this.description = json.description;
        this.jigsaw_set = json.jigsaw_set;
        this.active_status = json.active_status;
        this.date_added = json.date_added;
        this.date_modified = json.date_modified;
        this.rule_tags = json.tags;

        //Convert Active Status to Number
        if(this.active_status == 1)
            this.active_status = "Enable";
        else
            this.active_status = "Disable";

        if(this.rule_tags=="undefined" || this.rule_tags == undefined)
            this.rule_tags="";

        //Set name-description-active_stutus-tags
        this.rule_header.find('.content_head_title .pbd_rule_data .pbd_rule_text').html(this.name)
        this.rule_header.find('.content_head_description .pbd_rule_data .pbd_rule_text').html(this.description)
        this.rule_header.find('.content_head_status .pbd_rule_data .pbd_rule_text').html(this.active_status)
        this.rule_header.find('.content_head_tags .pbd_rule_data .pbd_rule_text').html(this.rule_tags)

        $('.pbd_rulebox_name').html(this.name);

        //Sweep all Rule Header parameter
        var tableRow = $('.pbd_one_rule_holder .pbd_rule_param');
        var rowText  = tableRow.find('.pbd_rule_text');
        var rowField = tableRow.find('.pbd_rule_field');

        tableRow.removeClass('state_field');
        tableRow.addClass('state_text');

        tableRow.find('.pbd_rule_action span#pbd_rule_action_edit').show();
        tableRow.find('.pbd_rule_action span#pbd_rule_action_save').hide();
        tableRow.find('.pbd_rule_action span#pbd_rule_action_cancel').hide();
        tableRow.find('.pbd_rule_data .pbd_rule_text').show();
        tableRow.find('.pbd_rule_data .pbd_rule_field').hide();
        //End Sweep All Rule Header
    },

    updateRuleHeaderById:function(uid){
        // return 'Hello';
        for(var index in this.nodeList){
            if(this.nodeList[index].uid == uid)return this.nodeList[index].updateTitle();
        }
        return false;
    },

    oneRuleFromJSON:function(json){
        //split rule header
        this.setRuleHeader(json)
        chainMan.empty()
        //split jigsaw set
        this.nodeList = [];
        if(DEBUG)console.log('<< Node List')
        if(DEBUG)console.log(this.nodeList)
        var jigsaws = json.jigsaw_set;

        for(var index in jigsaws){
            if(DEBUG)console.log('Init >> ');
            if(DEBUG)console.log(jigsaws[index]);
            this.nodeList.push(new Node(jigsaws[index]));
        }

        //Test Render nodelist
        for(var index in this.nodeList){
            chainMan.chain.append(this.nodeList[index].getHTML());
            //TODO :: Next -> Let append to be responsibility of chainMan
            //And remove add Rule Btn if There is action-node already ->
            chainMan.hideAddActionButton();
        }
        
        groupMan.initEvent();

        chainMan.cleanNode()
    },

    oneRuleToJSON:function(){

    },

    openNodeSelectionDialog:function(targetDiv,jsonString,kind){
        targetDiv.empty();

        var detail = $('<ul class="pbd_list_selection "></ul>');
        var list = jsonString;
        /*temporary map : icon and itemlist*/

        var icons = jsonConfig_icons;
        if(icons != undefined){

            var cnt = 0;
            if(DEBUG)console.log(icons)
            // var len = iconClass.length;
            for(var index in list){
                var listItem = list[index];

                var ic = '';

                if(listItem.icon){
                    ic = listItem.icon;
                }else{
                    ic = icons[listItem.name];
                }

                var whereSpaceExist = listItem.name.indexOf(' ');
                if(whereSpaceExist>-1)
                    listItem.name = listItem.name.substr(0,whereSpaceExist);
                if(DEBUG)console.log('>>'+listItem.name+'<<')
                if(listItem.name == "customPointReward" || listItem.name == "specialReward"){
                    listItem.name = "specialReward";
                    listItem.description = "You can send dynamic reward on engine.";
                }
                detail.append('<li id="'+listItem.specific_id+'" title="'+
                    listItem.description+'" class="mini-round jigsaw_select_btn line" > <i class="fa '+ic+'"/>' +
                    '<div class="text">'+(listItem.name == "badge" ? "item" : listItem.name)+'</div><div class="text_description">'+listItem.description+
                    '</div></li>');
            }

            targetDiv.append(detail);
        }

        // if(kind =='action')
        // 	// var iconClass = ["fa-icon-map-marker","fa-icon-thumbs-up","fa-icon-share","fa-icon-flag","fa-icon-globe","fa-icon-comment","fa-icon-plus-sign","fa-icon-group","fa-icon-cogs","fa-icon-cogs"];
        // 	// For demo
        // 	var iconClass = ["fa-icon-map-marker","fa-icon-bookmark-empty","fa-icon-thumbs-up","fa-icon-share","fa-icon-thumbs-up","fa-icon-comment","fa-icon-facebook-sign","fa-icon-reorder","fa-icon-cogs","fa-icon-cogs"];
        // else if(kind =='condition')
        // 	var iconClass = ["fa-icon-time","fa-icon-sort-down","fa-icon-chevron-left","fa-icon-chevron-right","fa-icon-resize-small","fa-icon-globe","fa-icon-globe","fa-icon-globe","fa-icon-globe"];
        // else if(kind =='reward')
        // 	var iconClass = ["fa-icon-heart-empty","fa-icon-tasks","fa-icon-certificate","fa-icon-cogs"];

        // var cnt = 0;
        // var len = iconClass.length;

        // for(var index in list){
        // 	var listItem = list[index];
        // 	detail.append('<li id="'+listItem.specific_id+'" title="'+listItem.description+'" class="mini-round jigsaw_select_btn line" > <div class="text">'+listItem.name+'</div> <i class="'+iconClass[cnt++%len]+'"/></li>');
        // }

        // targetDiv.append(detail);
    },

    validateAtLeastOneReward:function() {
        for(var index in oneRuleMan.nodeList){
            if (['REWARD', 'REWARD_SEQUENCE', 'REWARD_CUSTOM', 'FEEDBACK'].indexOf(oneRuleMan.nodeList[index].category) > -1) return true;
            if( oneRuleMan.nodeList[index].category == 'GROUP'){
                var groupContainer = groupMan.findGroupContainerInNodeList(oneRuleMan.nodeList[index].uid);
                if( typeof groupContainer.value != 'undefined' && groupContainer.value.length > 0){
                    return true;
                }
            }
        }
        return false;
    },

    saveRule:function(){

        /*Gathering json string
         >from header
         >from each node re-check -> rule process
         */

        // TODO ::
        /*Prevent Rule saving
         -> rule status not been set yet
         -> no jigsaw-node in that rule
         -> first jigsaw-node is not action node
         */

        var output = {};
        output.rule_id = this.rule_id;
        output.site_id = this.site_id;
        output.client_id = this.client_id;
        output.action_id = this.action_id;
        output.name = this.name;
        output.description = this.description;
        output.date_added = this.date_added;
        output.date_modified = this.date_modified;
        output.active_status = this.active_status;
        output.tags = this.rule_tags;

        //Update action_id
        //Assuminf first node is action node
        var getActionID = oneRuleMan.nodeList[0].getJSON();
        if(getActionID.category =='ACTION' && getActionID.specific_id != undefined && getActionID.specific_id != '')
            output.action_id = getActionID.specific_id;

        //Update jigsaw_set
        output.jigsaw_set = [];//reset Jigsaw set to blank
        for(var index in oneRuleMan.nodeList){
//            console.log(oneRuleMan.nodeList[index]);
//            console.log(oneRuleMan.nodeList[index].getJSON());
            output.jigsaw_set.push(oneRuleMan.nodeList[index].getJSON());
        }

        //Convert Active Status to Number
        if(output.active_status == 'Enable')
            output.active_status = 1;
        else
            output.active_status = 0;

        if(DEBUG)console.log('Saving Rule As : ')
        if(DEBUG)console.log(output);

        return JSON.stringify(output);
    },

    loadRule:function(){//load target rule by url
        // dataMan.
    },

    clearOneRuleHeader:function(){
        if(DEBUG)console.log('event : Clear one rule Header')

        var blank = {};
        // blank.rule = undefined;//defined as string later

        blank.rule_id = 'undefined';
        blank.action_id = undefined;

        blank.name = 'Rule title here';
        blank.description = 'Description here';
        blank.jigsaw_set = undefined;
        blank.active_status = 'Enable';
        blank.date_added = '';
        blank.date_modified = '';
        blank.rule_tags = 'Add tags';

        this.setRuleHeader(blank);

        //Clear Node List
        oneRuleMan.nodeList = {};
        // //Show add_action button
        chainMan.showAddActionButton();
    },

    openRuleEditor:function(){
        $('.pbd_one_rule_holder').show('fast');
        $('.one_rule_actionbtn_holder').show('fast');
    },

    closeRuleEditor:function(){
        $('.pbd_one_rule_holder').hide('fast');
        $('.one_rule_actionbtn_holder').hide('fast');
    },

    forceHideBackDrop:function(){
        // $(this).modal('hide');
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove()
    },

    discardCurrentRule:function(){
        this.clearOneRuleHeader();
        chainMan.empty()
        // this.closeRuleEditor();
    },

    //About Node Creation Dialog
    clearSelectedItem:function(){
        $('.pbd_list_selection tr td').removeClass('pbd_selected_item');
        $('.pbd_list_selection tr td').find('i').removeClass('icon-white');
        oneRuleMan.modalOptionSelectedItem = undefined;
    },

    ruleActionPanelControl:function(command){
        if(command =='not_editing'){
            $('.one_rule_new_btn').show('fast')
            $('.export_rule_btn').show('fast');
            $('.import_rule_btn').show('fast');
            $('.one_rule_actionbtn_holder').hide('fast');
        }else if(command == 'editing'){
            $('.one_rule_new_btn').hide('fast')
            $('.export_rule_btn').hide('fast');
            $('.import_rule_btn').hide('fast');
            $('.one_rule_actionbtn_holder').show('fast');
        }else if(command == 'cancel'){
            $('.export_rule_btn').show('fast');
            $('.import_rule_btn').show('fast');
            $('.one_rule_new_btn').show('fast');

            $('.pbd_rule_import').hide('fast');
            $('.export_rule_actionbtn_holder').hide('fast');
            $('.import_rule_actionbtn_holder').hide('fast');

            var tableObj = $('table.' + ruleTableMan.targetTable);
            var index = 7,
                selector = "tbody tr td:nth-child("+index+")",  // selector for all body cells in the column
                column = tableObj.find(selector).add("#column_import_check"); // all cells in the column

            // toggle the "hidden" class on all the column cells
            column.addClass("hidden");

            $('#file-import').val('');
        }
    }
},//->

//Mod : node chaining manager
    chainMan = {

        chain:$('ul.pbd_rule_unit_wrapper'),

        empty:function(){
            this.chain.empty();
        },

        getLen:function(){
            return this.chain.children().length;
        },

        addNode:function(jsonString,nodePosition){

            var jigsaw = $.parseJSON(jsonString);

            /*if(DEBUG){
             console.log('===============================\nNL before append new node')
             console.log('Adding > '+jigsaw.name+' To Position'+nodePosition)
             console.log('NL:')
             console.log(oneRuleMan.nodeList)
             console.log('===============================\n')
             }*/

            if(this.getLen() > 0){ //If there is existing NodeList
                //START : Clone Addning new Node to oneRuleMan.nodeList
                var temp = [],
                    cnt = 0,
                    nextLen = oneRuleMan.nodeList.length+1;

                for(var i = 0;i<nextLen;i++){
                    if(i == nodePosition ){
                        temp[i]=(new Node(jigsaw));
                    }else{
                        temp[i] = oneRuleMan.nodeList[cnt++];
                    }
                }
                oneRuleMan.nodeList = temp;
                //END : Clone Addning new Node to oneRuleMan.nodeList

                /*if(DEBUG){
                 console.log('Append Node at position NL:')
                 console.log(oneRuleMan.nodeList)
                 }*/

                //Add to UI
                if(nodePosition == 0){
                    //Prepend action node
                    chainMan.chain.prepend(oneRuleMan.nodeList[nodePosition].getHTML());
                }else{
                    //Append other type of node
                    // var target = chainMan.chain.find('li.pbd_ul_child:nth-child('+nodePosition+')')
                    var preNodePosition = nodePosition-1;
                    var target = chainMan.chain.find('li.pbd_ul_child[id='+oneRuleMan.nodeList[preNodePosition].uid+']');
                    $(target).after(oneRuleMan.nodeList[nodePosition].getHTML());
                }
            }else{ //If start with blank node List
                oneRuleMan.nodeList = [];
                oneRuleMan.nodeList.push(new Node(jigsaw));
                chainMan.chain.append(oneRuleMan.nodeList[0].getHTML());
            }

            /*if(DEBUG){
             console.log('===============================\nNL after append new node')
             console.log('NL:')
             console.log(oneRuleMan.nodeList)
             console.log('===============================\n')
             }*/

            //Clean node connection
            chainMan.cleanNode();
            //Sweep data
        },

        deleteNode:function(uid){

            /*if(DEBUG){
             console.log('Expecting uid : '+uid)
             console.log('NodeList before deleting : ')
             console.log(oneRuleMan.nodeList);
             }*/

            var r = oneRuleMan.nodeList;
            var cnt = 0;
            var temp = [];
            for(var index in r){
                if(r[index].uid != uid)
                    temp[cnt++] = r[index];
            }

            oneRuleMan.nodeList = temp;
            /*if(DEBUG){
             console.log('NodeList after deleting : ')
             console.log(oneRuleMan.nodeList);
             }*/
        },

        showAddActionButton:function(){
            $('.pbd_initial_action_add').show('fast');
        },

        hideAddActionButton:function(){
            $('.pbd_initial_action_add').hide('fast');
        },

        cleanNode:function(){//sweap node conjucntuui
            var length = chainMan.getLen();
            var counter = 0;

            if(DEBUG)console.log('>>> Cleaning')

            $('ul.pbd_rule_unit_wrapper').children().each(function(){
                //// remove old sort order
                dataMan.removeClassAttrAfterPrefix($(this),'sort_');
                ////add new sort order
                $(this).addClass('sort_'+counter++);

                //modify connection button 	/*capture every child in list*/
                var obj = $(this).find('.connection .new_node_connect .new_node_connect_btn');
                obj.css('margin-top','-24px');

                if(length-- < 2){
                    obj.css('margin-top','-20px');
                }

                if(DEBUG)console.log('Cleaning Node !!!')
                /*more : set node postion number in to add button*/ /*This controll all positioning in chain*/
                obj.find('.dropdown-toggle').attr('id',chainMan.getLen() - length);
                /*setup date picker*/
                $('.datepickerx').datepicker({
                    changeMonth: true,
                    changeYear: true
                });
                /*setup time picker*/
                $('.timepickerx').timepicker();

                /*setup date_time picker*/
                $('.datetimepickerx').datetimepicker({
                    addSliderAccess: true,
                    sliderAccessArgs: { touchonly: false }
                });

            });//End each
            pb_sweapNodeDataRow();

            // //Set all tools tip message

            // if(tooltip.action !=undefined){

            // }else if(tooltip.condition !=undefined){

            // }else if(tooltip.reward !=undefined){

            // }

            //Toggle on rule attirbute tool tip
            $('[rel=tooltip]').tooltip();
        }
    },//->

//Mod : all about one node in chain 
    nodeMan = {
        newNode:function(){

        }
    },//->

//Mod : all about fields in node , field validation , dataset detail render control
    fieldMan = {
        renderDataSet:function(){

        }
    }//->
//End Section : oneRule

perventDialogMan = {
    confirmDialog:function(title,message,direction,targetElement){
        /*
         title : string -> modal dialog title
         message : string -> modal dialog message
         direction : string -> function name pointer
         */

        /* Clone the general purpose question to the object*/
        var obj = $('#pbd_general_purpose_question').clone();

        /* Reset its id to blank*/
        obj.attr('id','');

        /* Set title and question message*/
        obj.find('.pbd_gpq_title').html(title);
        obj.find('.pbd_gpq_msg').html(message);

        /*pop up dialog*/
        obj.modal('show');

        /*add 'say yes' set yes value to id */
        obj.on('click','.pbd_gpq_yes',function(){
            obj.attr('id','pbd_gpq_yes');
            // trace('set modal id to > '+obj.attr('id'));
        });

        /*after dialog is dismiss -> read the answer and send to delegetor*/
        obj.on('hidden',function(){
            if(DEBUG)console.log('Hidding current dialog')
            /*invoke callback function of this question*/
            var answer = false;
            if(obj.attr('id')=='pbd_gpq_yes')
                answer = true;
            perventDialogMan.updateUIAfterGetAnswer(direction,answer,targetElement);
        })
    },

    updateUIAfterGetAnswer:function(direction,answer,targetElement){
        /*
         direction : string -> function name pointer
         answer : boolean -> ok=true;  and  cancel=false;
         */

        /*action for DIRECTION_clearNodeList answer*/
        if(direction=='DIRECTION_clearNodeList'){

            if(answer){//If user say yes to wipe all node in ruleEditor
                // if(DEBUG)alert('Clear all Node : '+answer)
                chainMan.empty();
                oneRuleMan.clearOneRuleHeader();

            }
        }else if(direction=='DIRECTION_removeNode'){/*action for removeNode answer*/

            if(answer){
                // if(DEBUG)alert('RemoveNode Node : '+answer)
                var entireNode = targetElement.parent().parent().parent().parent();
                var forceAllowAddNewAction = false;

                //if node to delete is action node ?
                forceAllowAddNewAction = ('ACTION' == dataMan.getClassAttrAfterPrefix('cate_',dataMan.objClassInspect(entireNode)))

                //Delete Data sturct
                //Check if group
                if(entireNode.parent().hasClass('pbd_ul_group')){
                    var group_id = entireNode.parent().parent().attr('id');
                    groupMan.deleteNode(entireNode.attr('id'), group_id);
                }else{
                    chainMan.deleteNode(entireNode.attr('id'))
                }

                //Delete UI
                entireNode.remove();

                chainMan.cleanNode();
                // oneRuleMan.forceHideBackDrop();

                //check if UI is blank
                if(chainMan.getLen() < 1){
                    oneRuleMan.nodeList = {};
                    chainMan.showAddActionButton();
                }

                if(forceAllowAddNewAction)chainMan.showAddActionButton();
            }
        }else if(direction=='DIRECTION_discardRule'){
            if(answer){
                // if(DEBUG)alert('Discard change current rule : '+answer)
                oneRuleMan.ruleActionPanelControl('not_editing');

                chainMan.empty();
                oneRuleMan.clearOneRuleHeader();
                /* for sure have functon */
                if($.isFunction($.fn.slidePanel)){
                    $().slidePanel();
                    $(".pbd_one_rule_holder").hide();
                }
                if($.isFunction($.fn.disableFixMenu)){
                    $(".fixMenu").disableFixMenu();
                }
            }
        }else if(direction=='DIRECTION_deleteRule'){
            if(answer && dataMan.currentRuleIdToDelete != undefined && dataMan.currentRuleIdToDelete !=''){
                // if(DEBUG)alert('Delete Rule : '+answer)

                var dialogMsg = 'no MGS';
                //doing Ajax stuff
                dataMan.deleteRule();
            }
        }
    }//end function
}

preventUnusual ={
    message:function(msg,title){
        if(msg=='' || msg== undefined)return;
        if(title!='' && title!= undefined) {
            $('#errorModal').find('#myModalLabel').html(title);
        }else{
            $('#errorModal').find('#myModalLabel').html("Warning !");
        }

        $('#errorModal').modal({'backdrop': false});
        $('#errorModal .modal-body').html(msg);
    }
}

//########################################
//Event 
//########################################
//Rule Editor main Event 

//Event: select template
$(".template_sel").click(function(){
    var id = $(this).data("id");
    var name = $(this).data("name");
    $("<div></div>").appendTo("body")
    .html(
        "<div>" +
        "<h6>Are you sure?</h6></div>")
    .dialog({
        modal: true,
    title: 'Do you want to use "' + name + '"',
    zIndex: 10000,
    autoOpen: true,
    width: "auto",
    resizable: false,
    buttons: {
        Yes: function () {
                 dataMan.cloneRule(id);
                 $(this).dialog("close");
             },
    No: function () {
            $(this).dialog("close");
        }
    },
    close: function (event, ui) {
               $(this).remove();
           }
    });
});

//Event : prevent user
$('.one_rule_save_btn').live('click',function(){
    var valid = true;
    if(chainMan.getLen()<1){
        valid = false;
        //Event : prevent user : save : saving Rule from Rule Editor
        preventUnusual.message('you should add some rule jigsaw!');
        // else if(){//Event : prevent user : save with out edit rule header
    }
    if (valid && !oneRuleMan.validateAtLeastOneReward()) {
        valid = false;
        preventUnusual.message('rule should be set to have at least one reward');
    }
    if (valid) {
        dataMan.saveRule(oneRuleMan.saveRule());
    }
    return true;
})

//Event : Click on [Export] button
$('.export_execute_btn').live('click',function(){

    var array_rules = new Array();
    $("input:checkbox[name=import_selected]:checked").each(function(){
        array_rules.push($(this).val());
    });
    if(array_rules.length == 0){
        preventUnusual.message('Please select at least 1 rule to import!');
    }
    else{
        dataMan.exportRule(array_rules);
        var test ="test";
    }
    return true;

})

//Event : Click on [Import] button
$('.import_execute_btn').live('click',function(){

    var myfile = document.getElementById("file-import");

    if(myfile.files[0] != undefined){
        //
        //var textType = 'text/csv';

        if (myfile.value.match(/\.json/gi)==".json") {
            var file = myfile.files[0];
            var reader = new FileReader();
            reader.readAsText(file);
            reader.onload = (function (theFile) {
                return function (e) {
                    try {
                        json = JSON.parse(e.target.result);
                        var rules_data =  JSON.stringify(json);
                        dataMan.importRule(rules_data);
                    } catch (ex) {

                        preventUnusual.message('Error when trying to parse json : ' + ex);
                    }
                }
            })(file);
            reader.onerror = function() {
                preventUnusual.message('Unable to read ' + file.fileName);
            };


        } else {
            preventUnusual.message('File type is invalid! ( only JSON file is supported)');
        }

    }else{
        preventUnusual.message('Please choose a file to execute!');
    }
    //
    //$('.file-import').html();

    return true;
})

//Event : Click on [+New Rule] button
$('.one_rule_new_btn').live('click',function(){

    if(  chainMan.chain.children().length > 0 ){
        //if node exist : alert('rule editor contain something')
        perventDialogMan.confirmDialog('Confirm','Do you really want to discard unsaved game rules ?','DIRECTION_clearNodeList');

    }else{
        //if node not exist : alert('rule editor NOT contain something')
        oneRuleMan.clearOneRuleHeader();
        oneRuleMan.openRuleEditor();
    }

    oneRuleMan.ruleActionPanelControl('editing')
})

//Event : Click on [Export Rule] button
$('.export_rule_btn').live('click',function(){

    $('.export_rule_btn').hide('fast');
    $('.import_rule_btn').hide('fast');
    $('.one_rule_new_btn').hide('fast');

    $('.export_rule_actionbtn_holder').show('fast');

    $('input[name=\'import_selected_head\']').attr('checked', false);
    $('input[name*=\'import_selected\']').attr('checked', false);

    var tableObj = $('table.' + ruleTableMan.targetTable);
    var index = 7,
        selector = "tbody tr td:nth-child("+index+")",  // selector for all body cells in the column
        column = tableObj.find(selector).add("#column_import_check"); // all cells in the column

    // toggle the "hidden" class on all the column cells
    column.removeClass("hidden");
})

//Event : Click on [Import Rule] button
$('.import_rule_btn').live('click',function(){

    $('.export_rule_btn').hide('fast');
    $('.import_rule_btn').hide('fast');
    $('.one_rule_new_btn').hide('fast');

    $('.pbd_rule_import').show('fast');
    $('.import_rule_actionbtn_holder').show('fast');

})

//Event : Click on [Discard] button
$('.one_rule_discard_btn').live('click',function(){
    perventDialogMan.confirmDialog('Confirm','Do you really want to discard unsaved game rules ?','DIRECTION_discardRule');
})

//Event : Click on [Cancel] button
$('.export_import_cancel_btn').live('click',function(){
    oneRuleMan.ruleActionPanelControl('cancel');

    if($.isFunction($.fn.slidePanel)){
        $().slidePanel();
    }
})

//Event : On disable Each node 
$('.pbd_btn_close').live('click',function(){
    perventDialogMan.confirmDialog('Confirm','Are you sure to delete this node ?','DIRECTION_removeNode',$(this));
})

//Event : delete Rule From Table -> move this to table file 
$('.gen_rule_delete_btn').live('click',function(event){
    event.preventDefault();
    var tableRow = $(this).parent().parent().parent().parent().parent();
    dataMan.currentRuleIdToDelete =  tableRow.attr('id');
    // P'a was remove all gen_rule_classes -> so get its name using ->
    var gameRuleName = tableRow.find('td span:eq(0)').html();

    perventDialogMan.confirmDialog('Deletion Confirm','You are going to permanently delete <b style="color:orange"> '+gameRuleName+' </b> game rule <br/>, Do you comfirm ? <span style="font-size:.85em;color:orange"> or you can just set rule state to "Disable" without deletion</span>','DIRECTION_deleteRule');
})

//Event : load rule From table -> To edit in rule editor
$('.gen_rule_edit_btn').live('click',function(event){
    event.preventDefault();
    var id = $(this).parent().parent().parent().parent().parent().attr('id');
    if(DEBUG)console.log('Attempt to load rule id : '+id)
    dataMan.currentRuleIdToLoad = id;
    dataMan.loadForEdit();

    $('.pbd_rule_import').hide('fast');
    $('.export_rule_actionbtn_holder').hide('fast');
    $('.import_rule_actionbtn_holder').hide('fast');

    var tableObj = $('table.' + ruleTableMan.targetTable);
    var index = 7,
        selector = "tbody tr td:nth-child("+index+")",  // selector for all body cells in the column
        column = tableObj.find(selector).add("#column_import_check"); // all cells in the column

    // toggle the "hidden" class on all the column cells
    column.addClass("hidden");

    oneRuleMan.ruleActionPanelControl('editing');
})

//Event : Play rule
$(".gen_rule_play_btn").live("click", function(event){
    event.preventDefault();
    var id = $(this).parents("tr").attr("id");
    dataMan.playRule(id);
});

//Event : Insert new Action
$('.init_action_btn').live('click',function(event){
    event.preventDefault();
    var theModal = $('#newrule_modal');

    oneRuleMan.openNodeSelectionDialog(theModal.find('.modal-body .selection_wrapper'),jsonString_Action,'action');
    theModal.modal('show');
    oneRuleMan.openNodeSelectionDialogType = 'ACTION';
    oneRuleMan.nodeInsertAfterPosition = 0;
})

//Event : Insert new Condition
$('#new_condition_btn').live('click',function(event){
    event.preventDefault();
    var theModal = $('#newrule_condition_modal');

    oneRuleMan.openNodeSelectionDialog(theModal.find('.modal-body .selection_wrapper'),jsonString_Condition,'condition');
    theModal.modal('show');
    oneRuleMan.openNodeSelectionDialogType = 'CONDITION';
})
//Event : Insert new Condition
$('#new_condition_group_btn').live('click',function(event){
    event.preventDefault();
    var theModal = $('#newrule_condition_modal');

    oneRuleMan.openNodeSelectionDialog(theModal.find('.modal-body .selection_wrapper'),jsonString_ConditionGroup,'condition_group');
    theModal.modal('show');
    oneRuleMan.openNodeSelectionDialogType = 'CONDITION_GROUP';
})

//Event : Insert new Reward
$('#new_reward_btn').live('click',function(event){
    event.preventDefault();
    var theModal = $('#newrule_reward_modal');

    //oneRuleMan.openNodeSelectionDialog(theModal.find('.modal-body .selection_wrapper'),jsonString_Reward,'reward');
    oneRuleMan.openNodeSelectionDialog(theModal.find('.modal-body .selection_wrapper'),jsonString_Feedback,'feedback');
    theModal.modal('show');
    //oneRuleMan.openNodeSelectionDialogType = 'REWARD';
    oneRuleMan.openNodeSelectionDialogType = 'FEEDBACK';
})

//Event : Insert new Reward Sequence
$('#new_reward_sequence_btn').live('click',function(event){
    event.preventDefault();
    var theModal = $('#newrule_reward_modal');

    oneRuleMan.openNodeSelectionDialog(theModal.find('.modal-body .selection_wrapper'),jsonString_RewardSequence,'reward');
    //oneRuleMan.openNodeSelectionDialog(theModal.find('.modal-body .selection_wrapper'),jsonString_Feedback,'feedback');
    theModal.modal('show');
    oneRuleMan.openNodeSelectionDialogType = 'REWARD_SEQUENCE';
    //oneRuleMan.openNodeSelectionDialogType = 'FEEDBACK';
})

//Event : Insert new Reward custom control
$('#new_reward_custom_btn').live('click',function(event){
    event.preventDefault();
    var theModal = $('#newrule_reward_custom_modal');

    //oneRuleMan.openNodeSelectionDialog(theModal.find('.modal-body .selection_wrapper'),jsonString_Reward,'reward');
    oneRuleMan.openNodeSelectionDialog(theModal.find('.modal-body .selection_wrapper'),jsonString_CustomReward,'reward');
    theModal.modal('show');
    //oneRuleMan.openNodeSelectionDialogType = 'REWARD';
    oneRuleMan.openNodeSelectionDialogType = 'REWARD_CUSTOM';
})

//Event : Insert new Group
$('#new_group_btn').live('click',function(event){
    event.preventDefault();
    var theModal = $('#newrule_group_modal');

    //oneRuleMan.openNodeSelectionDialog(theModal.find('.modal-body .selection_wrapper'),jsonString_Reward,'reward');
    oneRuleMan.openNodeSelectionDialog(theModal.find('.modal-body .selection_wrapper'),jsonString_Group,'group');
    theModal.modal('show');
    //oneRuleMan.openNodeSelectionDialogType = 'REWARD';
    oneRuleMan.openNodeSelectionDialogType = 'GROUP';
})

//Event : Select option between 'Create Condition Node' and 'Create Reward Node'
$('.dropdown-toggle').live('click',function(){
    cancelEdit();
    oneRuleMan.nodeInsertAfterPosition = $(this).attr('id');
})

//Event : On select node item -> need to be confirm later 
$('.pbd_list_selection li').live('click',function(event){

    $('.pbd_list_selection li').removeClass('pbd_selected_item');
    $(this).addClass('pbd_selected_item');

    oneRuleMan.modalOptionSelectedItem = $(this).attr('id');
})

//Event : On confirm to Create Dialog 
/*on submit to create node form dialog*/
$('.pbd_rule_editor_modal .pbd_modal_confirm_btn').live('click',function(event){

    event.preventDefault();
    var id = oneRuleMan.modalOptionSelectedItem;
    var type = oneRuleMan.openNodeSelectionDialogType;
    var targetId = oneRuleMan.openNodeSelectionDialogTargetId;
    var targetType = oneRuleMan.openNodeSelectionDialogTargetType;

    if(id!=undefined &&  id!='' && id!=' '){

        //Prepare render set of item to render
        var jsonItemSet = undefined;
        if(type === 'ACTION')
            jsonItemSet = jsonString_Action;
        else if(type === 'CONDITION')
            jsonItemSet = jsonString_Condition;
        else if (type == 'CONDITION_GROUP')
            jsonItemSet = jsonString_ConditionGroup;
        else if(type === 'REWARD')
            jsonItemSet = jsonString_Reward;
        else if(type === 'REWARD_SEQUENCE')
            jsonItemSet = jsonString_RewardSequence;
        else if(type === 'REWARD_CUSTOM')
            jsonItemSet = jsonString_CustomReward;
        else if(type === 'FEEDBACK')
            jsonItemSet = jsonString_Feedback;
        else if(type === 'GROUP')
            jsonItemSet = jsonString_Group;
        else if(type === 'GROUP_ITEM')
            jsonItemSet = jsonString_Feedback;
        else if(type === 'CONDITION_GROUP_ITEM')
            jsonItemSet = jsonString_Condition;

        for(var index in jsonItemSet){
            var item = jsonItemSet[index];
            if((id+'') == (item.specific_id+'')){
                selected_jsonstring = JSON.stringify(item);
            }
        }

        /* Append new node  */
        if(type=='ACTION'){
            // Force add item to sort_index 0
            cancelEdit();
            chainMan.addNode(selected_jsonstring,oneRuleMan.nodeInsertAfterPosition)
            oneRuleMan.nodeInsertAfterPosition = undefined;

            chainMan.hideAddActionButton()
        }else if(type=='GROUP_ITEM' || type=='CONDITION_GROUP_ITEM'){

            groupMan.addItem(selected_jsonstring, targetId, targetType)
            oneRuleMan.nodeInsertAfterPosition = undefined;
            oneRuleMan.openNodeSelectionDialogType = undefined;
            oneRuleMan.openNodeSelectionDialogTargetId = undefined;
            oneRuleMan.openNodeSelectionDialogTargetType = undefined;

        }else if( selected_jsonstring!='' ){
              // pbnode.appendNode(selected_jsonstring,oneRuleMan.nodeInsertAfterPosition);
            chainMan.addNode(selected_jsonstring,oneRuleMan.nodeInsertAfterPosition)
            oneRuleMan.nodeInsertAfterPosition = undefined;
        }


        $('.close').trigger('click');
        //clear current selected options in modal dialog
        oneRuleMan.clearSelectedItem();
    }
})

//Event : onclick slide button in rule_header
$('.pbd_one_rule_holder .slider-frame').live('click',function(){

    //Force slider frame : to be disable
    if(dataMan.isObjClassExist($(this),'value_enable')){
        $(this).removeClass('value_enable')
        $(this).addClass('value_disable');

        var objx = $(this).find('.slider-button');
        objx.removeClass('on').html('Disable');
        $(this).css('background','#BF0016');

        //Force slider frame : to be enable
    }else if(dataMan.isObjClassExist($(this),'value_disable')){

        $(this).addClass('value_enable')
        $(this).removeClass('value_disable');

        var objx = $(this).find('.slider-button');
        objx.addClass('on').html('Enable');
        $(this).css('background','#3B9900');

    }
})

function cancelEdit() {
    var $rule_param = $('.pbd_rule_param');
    var $rule_action = $('.pbd_rule_action');
    var $rule_data = $('.pbd_rule_data');

    $rule_param.removeClass('state_field');
    $rule_param.addClass('state_text');
    $rule_action.find('span#pbd_rule_action_edit').show();
    $rule_action.find('span#pbd_rule_action_save').hide();
    $rule_action.find('span#pbd_rule_action_cancel').hide();
    $rule_data.find('pbd_rule_field').hide();
    $rule_data.find('pbd_rule_text').show();

}

//Event : rule_header_edit -> Only for rule_header
$('.pbd_one_rule_holder .pbd_box_content_head .pbd_rule_action .btn').live('click',function(){

    if( $(this).attr('id')=='pbd_rule_action_edit' ){
        //On state save to edit
        var tableRow = $(this).parent().parent().parent();
        var rowText  = tableRow.find('.pbd_rule_text');
        var rowField = tableRow.find('.pbd_rule_field');
        var button = $(this);

        tableRow.removeClass('state_text');
        tableRow.addClass('state_field');

        tableRow.find('.pbd_rule_action span#pbd_rule_action_edit').hide();
        tableRow.find('.pbd_rule_action span#pbd_rule_action_save').show();
        tableRow.find('.pbd_rule_action span#pbd_rule_action_cancel').show();
        tableRow.find('.pbd_rule_data .pbd_rule_text').hide();
        tableRow.find('.pbd_rule_data .pbd_rule_field').show();

        //Load data to field
        if(dataMan.isObjClassExist(button,'status_btn')){
            //In case that field is slide button
            var slideframe = tableRow.find('.slider-frame');

            slideframe.removeClass('value_enable');
            slideframe.removeClass('value_disable');

            if(rowText.html()=='Enable' || rowText.html()=='1'){
                rowText.html('Enable');
                slideframe.addClass('value_enable');

                var objx = slideframe.find('.slider-button');
                objx.addClass('on').html('Enable');
                slideframe.css('background','#3B9900');
            }else if(rowText.html()=='Disable' || rowText.html()=='0'){
                rowText.html('Disable');
                slideframe.addClass('value_disable');

                var objx = slideframe.find('.slider-button');
                objx.removeClass('on').html('Disable');
                slideframe.css('background','#BF0016');
            }
        }else{
            //In case that field is just Text
            rowField.find('input').val(rowText.html());
        }

        //Auto Select
        tableRow.find('.pbd_rule_data .pbd_rule_field input').focus();
        tableRow.find('.pbd_rule_data .pbd_rule_field input').select();
    }else if( $(this).attr('id')=='pbd_rule_action_save' ){

        //On state edit to save
        var tableRow = $(this).parent().parent().parent();
        var rowText  = tableRow.find('.pbd_rule_text');
        var rowField = tableRow.find('.pbd_rule_field');
        var button = $(this);

        tableRow.removeClass('state_field');
        tableRow.addClass('state_text');

        //Prevent user input escape char and symbolic
        var regex = /['"]/g;
        var inputText = rowField.find('input[type="text"]').val();
        if(regex.test(inputText) ){
            preventUnusual.message('rule attribute can not contain these character \' " : ');
            return ;
        }else{
            //Continue
        }

        tableRow.find('.pbd_rule_action span#pbd_rule_action_edit').show();
        tableRow.find('.pbd_rule_action span#pbd_rule_action_save').hide();
        tableRow.find('.pbd_rule_action span#pbd_rule_action_cancel').hide();
        tableRow.find('.pbd_rule_data .pbd_rule_text').show();
        tableRow.find('.pbd_rule_data .pbd_rule_field').hide();

        //Load field to data
        if(dataMan.isObjClassExist(button,'status_btn')){
            //In case that field is slide button
            var slideframe = rowField.find('.slider-frame');
            if(dataMan.isObjClassExist(tableRow.find('.slider-frame'),'value_enable')){
                rowText.html('Enable');

            }else if(dataMan.isObjClassExist(tableRow.find('.slider-frame'),'value_disable')){
                rowText.html('Disable');
            }
        }else{
            //In case that field is just Text
            rowText.html(rowField.find('input').val());
        }

        //Update Rule box title
        oneRuleMan.name = $('.pbd_box_content_head .content_head_title .pbd_rule_text').html();
        oneRuleMan.description = $('.pbd_box_content_head .content_head_description .pbd_rule_text').html();
        oneRuleMan.active_status = $('.pbd_box_content_head .content_head_status .pbd_rule_text').html();
        oneRuleMan.rule_tags = $('.pbd_box_content_head .content_head_tags .pbd_rule_text').html();
        $('.pbd_one_rule_holder #rule_box_name').html(oneRuleMan.name);

        //Update Rule Object Attribute
    }else if( $(this).attr('id')=='pbd_rule_action_cancel' ){

        //On state edit to save
        var tableRow = $(this).parent().parent().parent();
        var rowText  = tableRow.find('.pbd_rule_text');
        var rowField = tableRow.find('.pbd_rule_field');
        var button = $(this);

        tableRow.removeClass('state_field');
        tableRow.addClass('state_text');

        //Prevent user input escape char and symbolic
        var regex = /['"]/g;
        var inputText = rowField.find('input[type="text"]').val();
        if(regex.test(inputText) ){
            preventUnusual.message('rule attribute can not contain these character \' " : ');
            return ;
        }else{
            //Continue
        }

        tableRow.find('.pbd_rule_action span#pbd_rule_action_edit').show();
        tableRow.find('.pbd_rule_action span#pbd_rule_action_save').hide();
        tableRow.find('.pbd_rule_action span#pbd_rule_action_cancel').hide();
        tableRow.find('.pbd_rule_data .pbd_rule_text').show();
        tableRow.find('.pbd_rule_data .pbd_rule_field').hide();

        //Load field to data
        if(dataMan.isObjClassExist(button,'status_btn')){
            //In case that field is slide button
            var slideframe = rowField.find('.slider-frame');
            if(rowText.html() == 'Enable'){
                tableRow.find('.slider-frame').addClass('value_enable').removeClass('value_disable');

            }else if(rowText.html() == 'Disable'){
                tableRow.find('.slider-frame').addClass('value_disable').removeClass('value_enable');
                tableRow.find('.slider-frame .slider-button').removeClass('on');
            }
        }else{
            //In case that field is just Text
            rowField.html(rowText.find('input').val());
        }
    }
})

// }

$("input").keypress( function(evt){
    var theEvent = evt || window.event;
    var key = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode( key );

    if($(this).hasClass('url')){    
        var regex = null;
    }else if($(this).hasClass('number')){
        var regex = /^-?(?:\d+|\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/g;
    }else{
        var regex = /[^0-9a-zA-Z\,\_\.\- ]/g;
    }
    
    if ( regex != null && regex.test(key) ) {
        theEvent.returnValue = false;
        if(theEvent.preventDefault) theEvent.preventDefault();
    }

});