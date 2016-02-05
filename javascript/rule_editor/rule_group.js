var DEBUG = false;


groupMan = {
       group:$('.pbd_group_container'),
       // itemList:[],
        empty:function(){
            this.group.empty();
        },

        findGroupContainerInNodeList: function( group_id ){

            var groupContainer;

            for(var index in oneRuleMan.nodeList)
            {
                if( oneRuleMan.nodeList[index].uid ==  group_id )
                {
                    for(var keyDataset in oneRuleMan.nodeList[index].currentDataSet.dataset )
                    {
                        if( oneRuleMan.nodeList[index].currentDataSet.dataset[keyDataset].field_type == "group_container" ||
                            oneRuleMan.nodeList[index].currentDataSet.dataset[keyDataset].field_type == "condition_group_container"
                        )
                        {
                           groupContainer = oneRuleMan.nodeList[index].currentDataSet.dataset[keyDataset];
                           break;
                        }
                    }
                }
            }

            return groupContainer;
        },
        findNodeGroupItemInNodeList: function(item_id, group_id ){
            var groupContainer;
            var nodeGroupItem;

            for(var index in oneRuleMan.nodeList)
            {
                if( oneRuleMan.nodeList[index].uid ==  group_id )
                {
                    for(var keyDataset in oneRuleMan.nodeList[index].currentDataSet.dataset )
                    {
                        if( oneRuleMan.nodeList[index].currentDataSet.dataset[keyDataset].field_type == "group_container" ||
                            oneRuleMan.nodeList[index].currentDataSet.dataset[keyDataset].field_type == "condition_group_container"
                        )
                        {
                           groupContainer = oneRuleMan.nodeList[index].currentDataSet.dataset[keyDataset];

                           for( var keyGroupItem in groupContainer.value ){
                                if( groupContainer.value[keyGroupItem].uid == item_id ){
                                    nodeGroupItem = groupContainer.value[keyGroupItem];
                                }
                           }
                           break;
                        }
                    }
                }
            }

            return nodeGroupItem;
        },
        addItem:function(jsonString, group_id, group_type){
            
            var jigsaw = $.parseJSON(jsonString);

            jigsaw.is_group_item = group_id;

            if( group_type == 'random' ){
                //Inject Chance in group item
                jigsaw.dataSet.push({
                    field_type: "number",
                    label: "Weight",
                    param_name: "weight",
                    placeholder: "Weight ...",
                    sortOrder: "0",
                    tooltips: "Weight",
                    value: "50"
                })
            }
            

            var groupItem = new Node(jigsaw);
            var groupContainer = groupMan.findGroupContainerInNodeList( group_id );
            groupContainer.value.push( groupItem );
            
            $('.pbd_group_container[id='+group_id+'] .pbd_ul_group').append( groupItem.getHTML() );

            groupMan.initEvent();

            pb_sweapNodeDataRow();

            // console.log( oneRuleMan.nodeList );
            // console.log( $.parseJSON(oneRuleMan.saveRule()) );

            // groupMan.cleanNode();
        },
        initEvent: function(){

             $('.pbd_group_container').each(function(){
                
                var group_id = $(this).attr('id');

                // *****************************//
                // binding event for SEQUENCE Group      //
                // *****************************//
                $('.pbd_group_container[id='+group_id+'] .pbd_group_sequence').sortable({
                    placeholder: "ui-state-highlight",
                    deactivate: function( event, ui ) {
                        var groupContainer = groupMan.findGroupContainerInNodeList( group_id );
                        var temp = [];
                        $ulGroup = ui.item.parent();
                        $ulGroup.find('>li').each(function(){
                            var itemId = $(this).attr('id');
                            for(var key in groupContainer.value){
                                if(groupContainer.value[key].uid == itemId){
                                    temp.push(groupContainer.value[key]);
                                }
                            }
                        });
                        groupContainer.value = temp;
                    }
                });

             });
            

        },
        deleteNode:function(uid, group_id){

            var groupContainer = this.findGroupContainerInNodeList( group_id );

            if( typeof groupContainer.value != 'undefined' ){
                var groupNodeList =  groupContainer.value;
                var cnt = 0;
                var temp = [];
                for(var index in groupNodeList){
                      if(groupNodeList[index].uid != uid)
                          temp[cnt++] = groupNodeList[index];
                }

                groupContainer.value = temp;

            }
           
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
