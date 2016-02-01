var DEBUG = false;

Node = function(json){
    var idgen = '';
    if(json.jigsaw_index !== undefined){
        if(json.jigsaw_index['$id']){
            idgen = json.jigsaw_index['$id'];
        }else{
            idgen = json.jigsaw_index;
        }
    }else{
        idgen = mongoIDjs();
    }
    this.uid = idgen;
    this.jigsawId = json.id;
    this.specificId = json.specific_id;
    this.jigsawName = json.name;
    this.jigsawDescription = json.description;
    this.category = json.category;
    this.sortOrder = json.sort_order;

    if(json.is_group_item){
        this.isGroupItem = json.is_group_item;
    }
    
    // this.currentJSON = json;



    //Insert tools tip key
    for(var index in json.dataSet){
        var tr_name = json.dataSet[index].param_name;
        // console.log('read : '+this.category.toLowerCase()+':'+this.jigsawName+':'+tr_name)

        if(this.category.toLowerCase() == "action"){
            var target_element = (toolstip[this.category.toLowerCase()]);
        }else{
            var target_element = ((toolstip[this.category.toLowerCase()])[this.jigsawName]);
        }

        if( target_element != undefined && target_element != '')
            json.dataSet[index].tooltips = (target_element['field_desc'])[tr_name];
        else
            json.dataSet[index].tooltips = '';
    }



    
    // if( this.category.toLowerCase() == "group" ){
    //     json.group_id = this.uid;
    //     this.currentDataSet = new DataSet(json.dataSet,this.uid);
    //     // this.currentDataSet = new DataSetGroup(json.dataSet,this.uid, json);
    // }else{
    //     this.currentDataSet = new DataSet(json.dataSet,this.uid);
    // }

    this.currentDataSet = new DataSet(json.dataSet,this.uid, json);


    this.mRuleHTML = undefined;
    //Start : Init statement
    //TODO : Implement object initialzed stuff

    //End : Init statement
    this.private_generateConfigJSON = function(){
        if(DEBUG)console.log('read json config for rules engin');
    }
}




Node.prototype.getHTML = function(){
    var boxStyle = '';
    var boxIcon = '';
    switch(this.category){
        case 'ACTION':boxStyle = 'pbd_boxstyle_action'; boxIcon= 'fa-icon-bolt';break;
        case 'CONDITION':boxStyle = 'pbd_boxstyle_condition';boxIcon= 'fa-icon-time';break;
        case 'REWARD':boxStyle = 'pbd_boxstyle_reward';boxIcon= 'fa-icon-trophy';break;
        case 'FEEDBACK':boxStyle = 'pbd_boxstyle_reward';boxIcon= 'fa-icon-trophy';break;
        case 'GROUP':boxStyle = 'pbd_boxstyle_group';boxIcon= 'fa-icon-tasks';break;
        case 'CONDITION_GROUP':boxStyle = 'pbd_boxstyle_group';boxIcon= 'fa-icon-tasks';break;
    }

    var htmlElement = '';
    if(this.jigsawName == "customPointReward" || this.jigsawName == "specialReward"){
        this.jigsawName = "specialReward";
        this.jigsawDescription = "You can send dynamic reward on engine.";
        htmlElement = '<table class="table table-bordered"><tbody>' +
            '<tr class="pbd_rule_param state_text parent_id_'+this.uid+'">' +
            '<td class="pbd_rule_label field_type_text sort_0 name_reward_name hide">Name' +
            '<a rel="tooltip" data-original-title="name of point to award"><i class="icon-question-sign icon-white help"></i></a></td>' +
            '<td class="pbd_rule_data hide"><span class="pbd_rule_text view_as_text" style="display: inline;"></span>' +
            '<span class="pbd_rule_field" style="display: none;">' +
            '<input type="text" class="" placeholder="" value="" maxlength="60"></span>' +
            '<span class="pbd_rule_action parent_id_'+this.uid+'">' +
            '<span class="btn btn-info btn-mini" id="pbd_rule_action_edit"><i class="icon-edit icon-white"></i></span>' +
            '<span class="btn btn-info btn-mini" id="pbd_rule_action_save" style="display: none;">' +
            '<i class="icon-ok icon-white"></i></span><span class="btn btn-info btn-mini" id="pbd_rule_action_cancel" style="display: none;">' +
            '<i class="icon-remove icon-white"></i></span></span></td></tr>' +
            '<tr class="pbd_rule_param state_text parent_id_'+this.uid+'">' +
            '<td class="pbd_rule_label field_type_number sort_0 name_quantity hide">Quantity<a rel="tooltip" data-original-title="amount of point to award">' +
            '<i class="icon-question-sign icon-white help"></i></a></td>' +
            '<td class="pbd_rule_data hide"><span class="pbd_rule_text view_as_number" style="display: inline;"></span>' +
            '<span class="pbd_rule_field" style="display: none;"><input type="text" class="input_number number" placeholder="How many ..." value="" maxlength="20"></span>' +
            '<span class="pbd_rule_action parent_id_'+this.uid+'">' +
            '<span class="btn btn-info btn-mini" id="pbd_rule_action_edit"><i class="icon-edit icon-white"></i></span>' +
            '<span class="btn btn-info btn-mini" id="pbd_rule_action_save" style="display: none;"><i class="icon-ok icon-white"></i></span>' +
            '<span class="btn btn-info btn-mini" id="pbd_rule_action_cancel" style="display: none;"><i class="icon-remove icon-white"></i></span></span></td></tr></tbody></table>';
    }
    else if (this.category == "ACTION"){
        this.jigsawDescription = "You can add condition for rewarding";
        htmlElement = '<table class="table table-bordered"><tbody>' +
        '<tr class="pbd_rule_param state_text parent_id_'+this.uid+'">' +
        '<td class="pbd_rule_label field_type_text sort_0 name_reward_name hide">Name' +
        '<a rel="tooltip" data-original-title="name of point to award"><i class="icon-question-sign icon-white help"></i></a></td>' +
        '<td class="pbd_rule_data hide"><span class="pbd_rule_text view_as_text" style="display: inline;"></span>' +
        '<span class="pbd_rule_field" style="display: none;">' +
        '<input type="text" class="" placeholder="" value="" maxlength="60"></span>' +
        '<span class="pbd_rule_action parent_id_'+this.uid+'">' +
        '<span class="btn btn-info btn-mini" id="pbd_rule_action_edit"><i class="icon-edit icon-white"></i></span>' +
        '<span class="btn btn-info btn-mini" id="pbd_rule_action_save" style="display: none;">' +
        '<i class="icon-ok icon-white"></i></span><span class="btn btn-info btn-mini" id="pbd_rule_action_cancel" style="display: none;">' +
        '<i class="icon-remove icon-white"></i></span></span></td></tr>' +


        '<tr class="pbd_rule_param state_text parent_id_'+this.uid+'">' +
        '<td class="pbd_rule_label field_type_number sort_0 name_quantity hide">Quantity<a rel="tooltip" data-original-title="amount of point to award">' +
        '<i class="icon-question-sign icon-white help"></i></a></td>' +
        '<td class="pbd_rule_data hide"><span class="pbd_rule_text view_as_number" style="display: inline;"></span>' +
        '<span class="pbd_rule_field" style="display: none;"><input type="text" class="input_number number" placeholder="How many ..." value="" maxlength="20"></span>' +
        '<span class="pbd_rule_action parent_id_'+this.uid+'">' +
        '<span class="btn btn-info btn-mini" id="pbd_rule_action_edit"><i class="icon-edit icon-white"></i></span>' +
        '<span class="btn btn-info btn-mini" id="pbd_rule_action_save" style="display: none;"><i class="icon-ok icon-white"></i></span>' +
        '<span class="btn btn-info btn-mini" id="pbd_rule_action_cancel" style="display: none;"><i class="icon-remove icon-white"></i></span></span></td></tr></tbody></table>';
    }
    else{
            htmlElement = this.currentDataSet.getHTML()
    }

    if(DEBUG)console.log('styling to >> '+boxStyle);
    this.mRuleHTML = '';
//    console.log(this.uid);
    //node enclosure
    this.mRuleHTML += '<li class="pbd_ul_child cate_'+this.category+'" id="'+this.uid+'">';
    //node stying defined
    this.mRuleHTML += '<div class="box span12 pbd_box '+boxStyle+'">';
    //node header content -> top bar of node
    this.mRuleHTML += '<div class="box-header"><h2><span class="icon-holder">' +
        '<i class="'+boxIcon+' icon-white"></i></span><span class="break"></span>' +
        '<span class="pbd_rulebox_name"><span class="name_only">' +
        '<span class="name_type">'+this.category+'</span>&nbsp;:&nbsp;'+this.jigsawName.toUpperCase()+'</span>  </span></h2>' +
        '<div class="rule-mini"></div><div class="box-icon"><a href="#" class="btn-minimize"><i class="icon-chevron-up"></i></a>' +
        '<a href="#" class="pbd_btn_close"><i class="icon-remove"></i></a></div></div>';
    //node body content
    this.mRuleHTML += '<div class="box-content"><span class="pbd_boxcontent_description">'+this.jigsawDescription+'</span>' +
        '<span class="pbd_boxcontent_action">';
    //add dataset Table
    this.mRuleHTML += htmlElement+'</span></div>';

    if( !this.isGroupItem ){
        //Add connection link
        this.mRuleHTML += '<div class="row connection"><div class="line_connect line_top offset6"></div><div class="offset6 new_node_connect"><div class="new_node_connect_btn circle" style="margin-top: -20px;"><div class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#" id="1"><i class="icon-plus icon-white"></i> Add : condition & reward</a><ul class="dropdown-menu pbd_dropdown-menu" role="menu" aria-labelledby="dLabel"><li><a tabindex="-1" id="new_condition_btn" href="#"><i class="fa-icon-cogs"></i> Condition</a></li><li><a tabindex="-1" id="new_condition_group_btn" href="#"><i class="fa-icon-cogs"></i> Condition Group</a></li><li><a tabindex="-1" id="new_reward_btn" href="#"><i class="fa-icon-trophy"></i> Reward</a></li><li><a tabindex="-1" id="new_group_btn" href="#"><i class="fa-icon-trophy"></i> Reward Group</a></li></ul></div></div></div></div></div>';
    }

    //End node enclosure
    this.mRuleHTML += '</li>';


    // methods from rule_e.js
    pb_sweapNodeDataRow();

    //Mock
    // this.mRuleHTML = '<li class="pbd_ul_child id_1 name_visit cate_ACTION specific_id_1 sort_0"><div class="box span12 pbd_box pbd_boxstyle_action"><div class="box-header"><h2><span class="icon-holder"><i class="icon-ok icon-white"></i></span><span class="break"></span><span class="pbd_rulebox_name">visit</span></h2><div class="box-icon"><!--a href="#" class="btn-setting"><i class="icon-wrench"></i></a--><a href="#" class="btn-minimize"><i class="icon-chevron-up"></i></a><a href="#" class="pbd_btn_close"><i class="icon-remove"></i></a></div></div><div class="box-content"><span class="pbd_boxcontent_description">basic visit action</span><span class="pbd_boxcontent_action"><table class="table table-bordered"><tbody><tr class="pbd_rule_param parent_id_1 state_field"><td class="pbd_rule_label field_type_text sort_0 name_url">URL</td><td class="pbd_rule_data"><span class="pbd_rule_text view_as_text" style="display: none;">www.playbasis.com/*</span><span class="pbd_rule_field" style="display: inline;"><input type="text" placeholder="Page URL here." value="www.playbasis.com/*"></span><span class="pbd_rule_action"><span class="btn btn-info btn-mini" id="pbd_rule_action_edit" style="display: none;"><i class="icon-edit icon-white"></i></span><span class="btn btn-info btn-mini" id="pbd_rule_action_save" style="display: inline-block;"><i class="icon-ok icon-white"></i></span></span></td></tr><tr class="pbd_rule_param parent_id_1 state_text"><td class="pbd_rule_label field_type_text sort_1 name_object_target">Target (Product)</td><td class="pbd_rule_data"><span class="pbd_rule_text view_as_text" style="display: inline;"></span><span class="pbd_rule_field" style="display: none;"><input type="text" placeholder="Item(Product) id" value=""></span><span class="pbd_rule_action"><span class="btn btn-info btn-mini" id="pbd_rule_action_edit" style=""><i class="icon-edit icon-white"></i></span><span class="btn btn-info btn-mini" id="pbd_rule_action_save" style="display: none;"><i class="icon-ok icon-white"></i></span></span></td></tr><tr class="pbd_rule_param parent_id_1 state_text"><td class="pbd_rule_label field_type_text sort_2 name_action_target">Element (ID/Class)</td><td class="pbd_rule_data"><span class="pbd_rule_text view_as_text" style="display: inline;"></span><span class="pbd_rule_field" style="display: none;"><input type="text" placeholder="Specific id or class" value=""></span><span class="pbd_rule_action"><span class="btn btn-info btn-mini" id="pbd_rule_action_edit" style=""><i class="icon-edit icon-white"></i></span><span class="btn btn-info btn-mini" id="pbd_rule_action_save" style="display: none;"><i class="icon-ok icon-white"></i></span></span></td></tr><tr class="pbd_rule_param parent_id_1 state_text"><td class="pbd_rule_label field_type_boolean sort_3 name_regex">Regex</td><td class="pbd_rule_data"><span class="pbd_rule_text view_as_boolean" style="display: inline;">true</span><span class="pbd_rule_field" style="display: none;"><input type="checkbox" class="input_boolean" id="bool254" name="true" value="true" checked="checked"></span><span class="pbd_rule_action"><span class="btn btn-info btn-mini boolean_btn" id="pbd_rule_action_edit" style=""><i class="icon-edit icon-white"></i></span><span class="btn btn-info btn-mini boolean_btn" id="pbd_rule_action_save" style="display: none;"><i class="icon-ok icon-white"></i></span></span></td></tr></tbody></table></span><span class="pbd_boxcontent_condition hide"></span><span class="pbd_boxcontent_reward hide"></span></div><div class="row connection"><div class="line_connect line_top offset6"></div><div class="offset6 new_node_connect"><div class="new_node_connect_btn circle" style="margin-top: -20px;"><div class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#" id="1"><i class="icon-plus icon-white"></i> Add</a><ul class="dropdown-menu pbd_dropdown-menu" role="menu" aria-labelledby="dLabel"><li><a tabindex="-1" id="new_condition_btn" href="#"><i class="fa-icon-cogs"></i> Condition</a></li><li><a tabindex="-1" id="new_reward_btn" href="#"><i class="fa-icon-trophy"></i> Reward</a></li></ul></div></div></div></div></div></li>';
    return this.mRuleHTML;
}

/*Sweap all table row to text mode */
function pb_sweapNodeDataRow() {

    var tableRow = $('.pbd_rule_param');

    if( dataMan.isObjClassExist(tableRow,'state_text') ){

        //State text show 'text' hide 'field'
        tableRow.find('.pbd_rule_action span#pbd_rule_action_edit').show();
        tableRow.find('.pbd_rule_action span#pbd_rule_action_save').hide();
        tableRow.find('.pbd_rule_action span#pbd_rule_action_cancel').hide();
        tableRow.find('.pbd_rule_data .pbd_rule_text').show();
        tableRow.find('.pbd_rule_data .pbd_rule_field').hide();
        pb_trace('pb_sweapNodeDataRow > display Text');
    }
    else {
        tableRow.find('.pbd_rule_action span#pbd_rule_action_edit').hide();
        tableRow.find('.pbd_rule_action span#pbd_rule_action_save').show();
        tableRow.find('.pbd_rule_action span#pbd_rule_action_cancel').show();
        tableRow.find('.pbd_rule_data .pbd_rule_text').hide();
        tableRow.find('.pbd_rule_data .pbd_rule_field').show();
        pb_trace('pb_sweapNodeDataRow > display Field');
    }

}


Node.prototype.updateTitle = function() {
    var titleString = '';
    titleString += '<span class="name_only"><span class="name_type">'+this.category+
        '</span>&nbsp;:&nbsp;'+this.jigsawName.toUpperCase()+'</span>';
    //Customize output
    if(this.category!='ACTION'){
        var currentData = this.currentDataSet.getJSON();
        for(var index in currentData){
            var v = currentData[index].value;
            if(DEBUG)console.log('on update rule title > '+v)
            if(v!='' && v!=undefined && v!='undefined'){
                titleString+="/";
                titleString+=v;
            }
        }
    }else{//In case it action
        // titleString = '<span class="name_only">'+this.jigsawName.toUpperCase()+'</span>';
    }

    // if(DEBUG)console.log('Title before change : '+ $(this.mRuleHTML).find('.pbd_rulebox_name').html() );
    // TODO :: update vulue of titile instead of return it out.
    return titleString;
}




Node.prototype.getJSON = function() {

    //Get attribute of this object to be json output
    var output = {};
    output.id = this.jigsawId;
    output.name = $('<textarea/>').html(this.jigsawName).val();
    output.description = $('<textarea/>').html(this.jigsawDescription).val();
    output.specific_id = this.specificId;
    output.category = this.category;
    output.sort_order = this.sortOrder;
    output.jigsaw_index = this.uid;

    //get dataset output from dataset object
    output.dataSet = this.currentDataSet.getJSON();
    //build config from dataset output
    output.config = extract_configFromJSON(output.dataSet,this);


    function extract_configFromJSON(dataSet,parentContext){

        //Custom parameter for different type of node
        var output = transformDataSetToConfig(dataSet, parentContext);

        switch(parentContext.category){
            case "ACTION" :
                output.action_id = parentContext.specificId;
                output.action_name = escape(parentContext.jigsawName);
                break;

            case "CONDITION" :
                output.condition_id = parentContext.specificId;
                break;

            case "REWARD" :
                output.reward_id = escape(parentContext.specificId);
                break;

            case "GROUP" :
                output.group_id = escape(parentContext.specificId);
                break;

        }//-->


        function transformDataSetToConfig(dataSet, parentContext){
            /*
             Building this
             "config": {
             "reward_id": "2",
             "reward_name": "justExp",
             "item_id": "",
             "quantity": "100"
             }

             */
            
            var stringOutput = '{'; //start : collecting value into string
            var len = dataSet.length;

            for(var i=0;i<len;i++){
                if(i > 0 && i < len)stringOutput += ',';
                var item = dataSet[i];

                if(item.value == undefined || item.value == 'undefined')
                    item.value = "";

                /*TODO :: in case that 's an action
                 -> if no url-> regex  flag force : false
                 -> if url existing and regex flag is true , but no / enclose to url yet fill it
                 */

                if(item.value =="" && parentContext.category == "REWARD"){
                    stringOutput+= '"'+item.param_name+'": null';
                }else if( (item.value =="true"||item.value =="false" ) && parentContext.category == "ACTION"){
                    // convert String to boolean
                    if(item.value =="true")item.value = true;
                    else item.value = false;
                    stringOutput+= '"'+item.param_name+'":'+item.value;

                }else if( item.field_type == "group_container"  || item.field_type == "condition_group_container"){
                    var group_item = [];
                    for( var key in item.value ){
                        var nodeGroupContainer =  groupMan.findNodeGroupItemInNodeList(item.value[key].jigsaw_index, parentContext.uid );
                        group_item.push( extract_configFromJSON(item.value[key].dataSet, nodeGroupContainer ) );
                    }
                    stringOutput+= '"'+item.param_name+'": '+ JSON.stringify(group_item);
                }else{
                    stringOutput+= '"'+item.param_name+'":"'+item.value+'"';
                }


            }//end for

            stringOutput += '}';//end : collecting value into string
            return $.parseJSON(stringOutput);
        }//End function : transformDataSet

        return output;
    }//End function : extract_configFromJSON

    //console.log(output);
    return output;
};


//var sample_rule = {"rule_id":"14","client_id":"3","site_id":"15","name":"Burufly_Rule_test","description":"Description for this rules","tags":"new,basic,custom3,promotion","active_status":"active","date_added":"12/12/2012","date_modified":"12/12/2012","jigsaw_set":[{"id":1,"name":"review","description":"review action jigsaw","specific_id":"7","category":"ACTION","sort_order":0,"dataSet":[{"param_name":"url","label":"URL","placeholder":"Page URL here.","sortOrder":"0","field_type":"text","value":"www.playbasis.com/*"},{"param_name":"object_target","label":"Target (Product)","placeholder":"Item(Product) id","sortOrder":"1","field_type":"text","value":""},{"param_name":"action_target","label":"Element (ID/Class)","placeholder":"Specific id or class","sortOrder":"2","field_type":"text","value":""},{"param_name":"regex","label":"Regex","placeholder":"","sortOrder":"3","field_type":"boolean","value":"true"}],"config":{"action_id":"7","action_name":"review","url":"www.playbasis.com/*","regex":"true","action_target":"eg_custom_target_class_or_target_id","object_target":"eg_shoe_bags_whatever"}},{"id":"20002","name":"every_n_day","description":"Do action every n days","category":"CONDITION","specific_id":"","sort_order":1,"dataSet":[{"param_name":"num_of_days","label":"n Days","placeholder":"How many days ?","sortOrder":"0","field_type":"number","value":"10"},{"param_name":"time_of_day","label":"start time (of Day)","placeholder":"Start day at time ...","sortOrder":"1","field_type":"time","value":"00:00"}],"config":{"time_of_day":"00:00","num_of_days":"10"}},{"id":"30001","name":"justExp","description":"User earn exp","category":"REWARD","specific_id":"44","sort_order":2,"dataSet":[{"param_name":"reward_name","label":"Exp","placeholder":"","sortOrder":"0","field_type":"read_only","value":"justExp"},{"param_name":"item_id","label":"ItemID","placeholder":"","sortOrder":"0","field_type":"hidden","value":""},{"param_name":"quantity","label":"Exp","placeholder":"How many ...","sortOrder":"0","field_type":"number","value":"100"}],"config":{"reward_id":"44","reward_name":"justExp","item_id":"","quantity":"100"}},{"id":"19672","name":"counter","description":"Do the same action n times","category":"CONDITION","specific_id":"","sort_order":3,"dataSet":[{"param_name":"counter_value","label":"Times","sortOrder":"0","field_type":"number","value":"10"},{"param_name":"interval","label":"Time interval","sortOrder":"1","field_type":"number","value":"10000"},{"param_name":"interval_unit","label":"","sortOrder":"","field_type":"hidden","value":"second"},{"param_name":"reset_timeout","label":"in a Rows","sortOrder":"2","field_type":"boolean","value":"true"}],"config":{"counter_value":"10","interval":"180","interval_unit":"second","reset_timeout":"true"}},{"id":"30002","name":"badge","description":"User earn Badge","category":"REWARD","specific_id":"55","sort_order":4,"dataSet":[{"param_name":"reward_name","label":"Name","placeholder":"","sortOrder":"0","field_type":"read_only","value":"justBadge"},{"param_name":"item_id","label":"Item id","placeholder":"Item id","sortOrder":"0","field_type":"collection","value":"14322"},{"param_name":"quantity","label":"Quantity","placeholder":"How many ...","sortOrder":"0","field_type":"number","value":"1"}],"config":{"reward_id":"55","reward_name":"justBadge","item_id":"14322","quantity":"1"}},{"id":"20003","name":"before_date","description":"Do the action before date-time","category":"CONDITION","specific_id":"","sort_order":5,"dataSet":[{"param_name":"timestamp","label":"Date-Time","sortOrder":"0","field_type":"date_time","value":"1359091200"}],"config":{"timestamp":"1359091200"}},{"id":"20004","name":"after_date","description":"Do the action after date-time","category":"CONDITION","specific_id":"","sort_order":6,"dataSet":[{"param_name":"timestamp","label":"Date-Time","placeholder":"Select date time","sortOrder":"0","field_type":"date_time","value":"1359091200"}],"config":{"timestamp":"1359091200"}},{"id":"19521","name":"coin","description":"User earn coin","category":"REWARD","specific_id":"66","sort_order":7,"dataSet":[{"param_name":"reward_name","label":"Name","placeholder":"","sortOrder":"0","field_type":"read_only","value":"justCoin"},{"param_name":"item_id","label":"Item id","placeholder":"Item id","sortOrder":"0","field_type":"hidden","value":""},{"param_name":"quantity","label":"Quantity","placeholder":"How many ...","sortOrder":"0","field_type":"number","value":"3"}],"config":{"reward_id":"66","reward_name":"justCoin","item_id":"","quantity":"3"}},{"id":"19521","name":"point","description":"User earn point","category":"REWARD","specific_id":"661","sort_order":7,"dataSet":[{"param_name":"reward_name","label":"Name","placeholder":"","sortOrder":"0","field_type":"read_only","value":"justPoint"},{"param_name":"item_id","label":"Item id","placeholder":"Item id","sortOrder":"0","field_type":"hidden","value":""},{"param_name":"quantity","label":"Quantity","placeholder":"How many ...","sortOrder":"0","field_type":"number","value":"3"}],"config":{"reward_id":"661","reward_name":"justPoint","item_id":"","quantity":"3"}},{"id":"30004","name":"coupon","description":"User earn coupon","category":"REWARD","specific_id":"77","sort_order":8,"dataSet":[{"param_name":"reward_name","label":"Name","placeholder":"","sortOrder":"0","field_type":"read_only","value":"justCoupon"},{"param_name":"item_id","label":"Item id","placeholder":"Item id","sortOrder":"0","field_type":"collection","value":"12334"},{"param_name":"quantity","label":"Quantity","placeholder":"How many ...","sortOrder":"0","field_type":"number","value":"3"}],"config":{"reward_id":"77","reward_name":"justCoupon","item_id":"12334","quantity":"1"}},{"id":"30005","name":"virtual_good","description":"User earn virtual_good","category":"REWARD","specific_id":"88","sort_order":9,"dataSet":[{"param_name":"reward_name","label":"Name","placeholder":"","sortOrder":"0","field_type":"read_only","value":"just_virtual_good"},{"param_name":"item_id","label":"Item id","placeholder":"Item id","sortOrder":"0","field_type":"collection","value":"102"},{"param_name":"quantity","label":"Quantity","placeholder":"How many ...","sortOrder":"0","field_type":"number","value":"1"}],"config":{"reward_id":"88","reward_name":"just_virtual_good","item_id":"102","quantity":"1"}},{"id":"20005","name":"between","description":"Do the action between time","category":"CONDITION","specific_id":"","sort_order":10,"dataSet":[{"param_name":"start_time","label":"Start time","placeholder":"Begin at .. ","sortOrder":"0","field_type":"time","value":"00:00"},{"param_name":"end_time","label":"End time","placeholder":"End at .. ","sortOrder":"1","field_type":"time","value":"00:00"}],"config":{"start_time":"00:00","end_time":"00:00"}},{"id":"30006","name":"level","description":"User earn level","category":"REWARD","specific_id":"99","sort_order":11,"dataSet":[{"param_name":"reward_name","label":"Name","placeholder":"","sortOrder":"0","field_type":"read_only","value":"justLevel"},{"param_name":"item_id","label":"","placeholder":"","sortOrder":"0","field_type":"hidden","value":""},{"param_name":"quantity","label":"Level","placeholder":"How many ...","sortOrder":"0","field_type":"number","value":"1"}],"config":{"reward_id":"30006","reward_name":"justLevel","item_id":"","quantity":"1"}},{"id":"20006","name":"cooldown","description":"set time waitting time to do next action again","category":"CONDITION","specific_id":"","sort_order":12,"dataSet":[{"param_name":"cooldown","placeholder":"Cooldown in....","label":"Times","sortOrder":"0","field_type":"cooldown","value":"3600"}],"config":{"cooldown":"3600"}},{"id":"30007","name":"discount","description":"User earn discount","category":"REWARD","specific_id":"11","sort_order":13,"dataSet":[{"param_name":"reward_name","label":"Name","placeholder":"","sortOrder":"0","field_type":"read_only","value":"justDiscount"},{"param_name":"item_id","label":"Item","placeholder":"item_id","sortOrder":"0","field_type":"collection","value":"104"},{"param_name":"quantity","label":"Quantity","placeholder":"How many ...","sortOrder":"0","field_type":"number","value":"1"}],"config":{"reward_id":"11","reward_name":"justDiscount","item_id":"104","quantity":"1"}},{"id":"20007","name":"daily","description":"Do the same action every day","category":"CONDITION","specific_id":"","sort_order":14,"dataSet":[{"param_name":"time_of_day","label":"start time (of Day)","placeholder":"Start day at time ...","sortOrder":"0","field_type":"time","value":"00:00"}],"config":{"time_of_day":"00:00"}},{"id":"30021","name":"exp","description":"User earn exp","category":"REWARD","specific_id":"22","sort_order":2,"dataSet":[{"param_name":"reward_name","label":"","placeholder":"","sortOrder":"0","field_type":"read_only","value":"justExp"},{"param_name":"item_id","label":"","placeholder":"","sortOrder":"0","field_type":"hidden","value":""},{"param_name":"quantity","label":"Exp","placeholder":"How many ...","sortOrder":"0","field_type":"number","value":"10"}],"config":{"reward_id":"22","reward_name":"justExp","item_id":"","quantity":"10"}},{"id":"20008","name":"weekly","description":"Do the same action every week","category":"CONDITION","specific_id":"","sort_order":16,"dataSet":[{"param_name":"time_of_day","label":"start time (of Day)","placeholder":"Start day at time ...","sortOrder":"0","field_type":"time","value":"00:00"},{"param_name":"day_of_week","label":"start day (of week)","placeholder":"Day abbreviation (eg. mon,fri,sun)","sortOrder":"1","field_type":"text","value":"2"}],"config":{"time_of_day":"00:00","day_of_week":"mon"}},{"id":"30008","name":"prize","description":"User earn prize","category":"REWARD","specific_id":"33","sort_order":17,"dataSet":[{"param_name":"reward_name","label":"name","placeholder":"","sortOrder":"0","field_type":"read_only","value":"justPrize"},{"param_name":"item_id","label":"Item","placeholder":"Item id","sortOrder":"0","field_type":"collection","value":"105"},{"param_name":"quantity","label":"Exp","placeholder":"How many ...","sortOrder":"0","field_type":"number","value":"1"}],"config":{"reward_id":"33","reward_name":"justPrize","item_id":"105","quantity":"1"}},{"id":"20081","name":"monthly","description":"Do the same action every month","category":"CONDITION","specific_id":"","sort_order":16,"dataSet":[{"param_name":"time_of_day","label":"start time (of Day)","placeholder":"Start day at time ...","sortOrder":"0","field_type":"time","value":"00:00"},{"param_name":"day_of_month","label":"start day (of month)","placeholder":"Date (eg. 1,15,31)","sortOrder":"1","field_type":"text","value":"1"}],"config":{"time_of_day":"00:00","day_of_month":"1"}},{"id":2,"name":"customPointReward","description":"customPointReward","specific_id":"","category":"REWARD","sort_order":18,"dataSet":[{"param_name":"reward_name","label":"Name","placeholder":"","sortOrder":"0","field_type":"text","value":"customPointReward"},{"param_name":"quantity","label":"Exp","placeholder":"How many ...","sortOrder":"0","field_type":"number","value":"10"}],"config":{"reward_name":"customPointReward","quantity":10}},{"id":2,"name":"Burufly Point","description":"reward","specific_id":"44","category":"REWARD","sort_order":19,"dataSet":[{"param_name":"reward_name","label":"name","placeholder":"","sortOrder":"0","field_type":"read_only","value":"Burufly Point"},{"param_name":"item_id","label":"Item","placeholder":"Item id","sortOrder":"0","field_type":"hidden","value":""},{"param_name":"quantity","label":"Quantity","placeholder":"How many ...","sortOrder":"0","field_type":"number","value":"15"}],"config":{"reward_id":"44","reward_name":"Burufly Point","item_id":"","quantity":"15"}}]};
// var oneNode = new Node(sample_rule.jigsaw_set[1]);
// console.log(oneNode.getHTML())
// console.log(oneNode.getJSON())


