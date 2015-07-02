var DEBUG = false;


groupMan = {
       group:$('.pbd_group_container'),
       itemList:[],
        empty:function(){
            this.group.empty();
        },

        getLen:function(){
            return this.group.children().length;
        },

        addItem:function(jsonString, groupObj){
            
            var jigsaw = $.parseJSON(jsonString);
            jigsaw.group_obj = groupObj;
            jigsaw.dataSet.push({
                field_type: "number",
                label: "Chance",
                param_name: "chance",
                placeholder: "Chance ...",
                sortOrder: "0",
                tooltips: "Chance",
                value: "50"
            })

            // console.log( jigsaw );

            // groupMan.itemList.push(new Node(jigsaw));
            
            // var html = groupMan.itemList[(groupMan.itemList.length -1)].getHTML();
            var html = new Node(jigsaw).getHTML();
            $('.pbd_group_container[id='+groupObj.group_id+'] ul').append(html);

            pb_sweapNodeDataRow();


            console.log( $.parseJSON(oneRuleMan.saveRule()) );
            // groupMan.group.append(groupMan.itemList[0].getHTML());

            // if(this.getLen() > 0){

            //     var temp = [],
            //         cnt = 0,
            //         nextLen = oneRuleMan.nodeList.length+1;

            //     for(var i = 0;i<nextLen;i++){
            //         if(i == nodePosition ){
            //             temp[i]=(new Node(jigsaw));
            //         }else{
            //             temp[i] = oneRuleMan.nodeList[cnt++];
            //         }
            //     }
            //     oneRuleMan.nodeList = temp;
               
            //     if(nodePosition == 0){
            //         groupMan.group.prepend(oneRuleMan.nodeList[nodePosition].getHTML());
            //     }else{
            //         var target = groupMan.group.find('li.pbd_ul_child:nth-child('+nodePosition+')')
            //         $(target).after(oneRuleMan.nodeList[nodePosition].getHTML());
            //     }
            // }else{ 
            //     oneRuleMan.nodeList = [];
            //     oneRuleMan.nodeList.push(new Node(jigsaw));
            //     groupMan.group.append(oneRuleMan.nodeList[0].getHTML());
            // }

            // groupMan.cleanNode();
        },

        deleteNode:function(uid){

            var r = oneRuleMan.nodeList;
            var cnt = 0;
            var temp = [];
            for(var index in r){
                if(r[index].uid != uid)
                    temp[cnt++] = r[index];
            }

            oneRuleMan.nodeList = temp;
           
        },

        showAddActionButton:function(){
            $('.pbd_initial_action_add').show('fast');
        },

        hideAddActionButton:function(){
            $('.pbd_initial_action_add').hide('fast');
        },

        cleanNode:function(){
            var length = groupMan.getLen();
            var counter = 0;

            if(DEBUG)console.log('>>> Cleaning')

            $('ul.pbd_rule_unit_wrapper').children().each(function(){

                dataMan.removeClassAttrAfterPrefix($(this),'sort_');

                $(this).addClass('sort_'+counter++);

                //modify connection button  /*capture every child in list*/
                var obj = $(this).find('.connection .new_node_connect .new_node_connect_btn');
                obj.css('margin-top','-24px');

                if(length-- < 2){
                    obj.css('margin-top','-20px');
                }

                if(DEBUG)console.log('Cleaning Node !!!')

                obj.find('.dropdown-toggle').attr('id',groupMan.getLen() - length);

                $('.datepickerx').datepicker({
                    changeMonth: true,
                    changeYear: true
                });

                $('.timepickerx').timepicker();


                $('.datetimepickerx').datetimepicker({
                    addSliderAccess: true,
                    sliderAccessArgs: { touchonly: false }
                });

            });
            pb_sweapNodeDataRow();

            $('[rel=tooltip]').tooltip();
        }
}








//rule_dataset_group.js
DataSetGroup = function(jsonArray, parent_id, groupObj) {
    if(!jsonArray || !$.isArray(jsonArray)) {
        try {
            $.parseJSON(jsonArray);
        }
        catch(e) {
            console.log(e);
        }
        //console.log('Please initial data (json array)');
        if(DEBUG)console.log(jsonArray);

        return;
    }

    return {
        dataset: jsonArray,
        parent_id: parent_id,

        getHTML: function() {

            // diery hack
            window.isValid = true;


            var jigsaw = $('<table class="table table-bordered">');
            jigsaw.append('<div class="pbd_group_container" id="'+groupObj.group_id+'"><ul></ul><div style="clear:both"><a herf="#" class="new_group_item_btn"><i class="icon-plus icon-white"></i> Add Reward</a></div></div>');
            

            $.each(groupObj.itemList, function(key, item) {
                  var jigsaw = $.parseJSON(item);
                  var html = new Node(jigsaw).getHTML();
                  jigsaw.find('ul').append(html);
            });


            //Event : Insert new Reward
            $('.new_group_item_btn').live('click',function(event){
                event.preventDefault();
                var theModal = $('#newrule_reward_modal');

                oneRuleMan.openNodeSelectionDialog(theModal.find('.modal-body .selection_wrapper'),jsonString_Feedback,'feedback');
                theModal.modal('show');
                oneRuleMan.openNodeSelectionDialogType = 'GROUP_ITEM';
                oneRuleMan.openNodeSelectionDialogTargetObj = groupObj;
            })

            return jigsaw[0].outerHTML;
        },

        getJSON: function() {
//            console.log('getJson : ' + this.parent_id);
            var _this = this;
            var result = [];
            $('#'+groupObj.group_id+' ul li').each(function() {
                var item_id = $(this).attr('id');
                var itemObj = _this.getJSONItem(item_id);
                result.push(itemObj);
            });
            
            return result;
        },
        getJSONItem: function(item_id){
            var resultItem = [];

            $('#'+item_id+' tbody tr').each(function() {
                var obj = {},
                    $this = $(this),
                    class_data = $($(this).find('td')[0]).attr('class');

                data = class_data.split(' ');
                $.each(data, function(k, v){
                    if(v.match('name_')) {
                        obj.param_name = v.split('name_')[1];
                    }
                    if(v.match('field_type_')) {
                        obj.field_type = v.split('field_type_')[1];
                    }
                    if(v.match('sort_')) {
                        obj.sortOrder = v.split('sort_')[1];
                    }
                });

                obj.label = $this.find('td')[0].innerHTML;
                var elm = $this.find('input'); // try to find "input" first
                if (elm.length <= 0) elm = $this.find('select'); // if not found, then try to find "select"
                obj.placeholder = elm.attr('placeholder');
                if (elm.attr('datatype')) obj.type = elm.attr('datatype');

                /*
                 * track to grand parent node to get node header
                 * for formatting result to correct format for backend
                 */
                var value = elm.val(),
                    $parent = $this.parent().parent().parent().parent().parent().parent(),
                    anotherType = $parent.find('.name_only').html();

                switch(anotherType) {
                    case 'BEFORE_DATE':
                    // Before: date time in unix timestamp
                    case 'AFTER_DATE':
                        // After: date time in unix timestamp
                        try {
                            value = Date.parse(value).getTime()/1000;
                        }
                        catch(e) {
                            // value is unix time format so correct
                            // skip the error
                        }
                        break;

                    case 'BETWEEN':
                    // Between :time in 24hr format {00:00 - 23:59}
                    // do nothing
                    case 'COOLDOWN':
                    // Cooldown :cooldown time in second
                    // do nothing
                    case 'DAILY':
                    // Daily->time_of_day : time in 24hr format {00:00 - 23:59}
                    // donothing
                    case 'WEEKLY':
                    // Weekly->time_of_day : time in 24hr format {00:00 - 23:59}
                    // Weekly->date_of_month{1-7}
                    case 'MONTHLY':
                    // Monthly->time_of_day : time in 24hr format {00:00 - 23:59}
                    // Monthly->date_of_month{1-31}
                    case 'EVERY_N_DAY':
                        // EveryDay->time_of_day : time in 24hr format {00:00 - 23:59}
                        // EveryDay->num_of_day : num
                        break;
                }

                obj.value = value;
                resultItem.push(obj);
            });

            return resultItem;
        }
    }
};
