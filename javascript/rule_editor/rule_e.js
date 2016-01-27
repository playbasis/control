var DEBUG = false;

function trace(msg){
    // console.log(msg);
}

var pbd_rule_globalvar = (function($){

    var obj = {};
    obj.modalOptionSelectedItem = undefined;
    obj.modalOptionOpenSection = undefined;

    obj.nodeInsertAfterPosition = undefined;

    obj.collectionCurrentSelectedItem  = undefined;
    obj.collectionCurrentQuantity = undefined;
    obj.collectionCurrentNode_id = undefined;

    obj.currentRuleIdToDelete = undefined;
    obj.currentRuleIdChangeState = undefined;


    // obj.nodeContainerElement = $('ul.pbd_rule_unit_wrapper');

    return obj;
}(jQuery));


var pbd_nodeString = (function(){

}());




var pbnode = (function($,gVar,nodeString){

    var obj = {};




    function pb_stringToBoolean(string){
        if(string.toLowerCase() =='enable' || string.toLowerCase() =='true' || string =='0'){
            return true;
        }else if(string.toLowerCase() =='disable' || string.toLowerCase() =='false'  || string =='1'){
            return false;
        }
        return false;
    }







    /*Sweap all table row to text mode */
    function pb_sweapNodeDataRow(){

        var tableRow = $('.pbd_ul_child .pbd_rule_param');

        if( pb_isClassExist(tableRow,'state_text') ){

            //State text show 'text' hide 'field'
            tableRow.find('.pbd_rule_action span#pbd_rule_action_edit').show();
            tableRow.find('.pbd_rule_action span#pbd_rule_action_save').hide();
            tableRow.find('.pbd_rule_action span#pbd_rule_action_cancel').hide();
            tableRow.find('.pbd_rule_data .pbd_rule_text').show();
            tableRow.find('.pbd_rule_data .pbd_rule_field').hide();
            pb_trace('pb_sweapNodeDataRow > display Text');
        }else{

            tableRow.find('.pbd_rule_action span#pbd_rule_action_edit').hide();
            tableRow.find('.pbd_rule_action span#pbd_rule_action_save').show();
            tableRow.find('.pbd_rule_action span#pbd_rule_action_cancel').show();
            tableRow.find('.pbd_rule_data .pbd_rule_text').hide();
            tableRow.find('.pbd_rule_data .pbd_rule_field').show();
            pb_trace('pb_sweapNodeDataRow > display Field');
        }

    }








    return obj;
}(jQuery,pbd_rule_globalvar,pbd_nodeString));



/*general function */
function pb_trace(msg){
    // enable-disable trace here
    trace(msg);
}


function pb_isClassExist(targetElement,className){

    var getClass = targetElement.attr('class');
    if(getClass==undefined)
        pb_trace('unable to extract class name from undefined object');

    if(getClass!=undefined && getClass.indexOf(className)>-1)
        return true;
    return false;
}

function pb_removeAllNode(){
    /*
     clear action sequence list
     */
    $('ul.pbd_rule_unit_wrapper').html('');
}


//Delete
function pb_classInspect(target){

    var classesString = target.attr('class')+'';
    var arrayOutout = classesString.split(' ');
    return arrayOutout;
}

//Delete
function pb_removeClassParamAfterPrefix(object,string){

    //get class into array
    var classArr = pb_classInspect(object);
    //remove all class
    object.attr('class','');

    // pb_tracejsArray(classArr);
    var len = classArr.length;
    var cloneArr=[];
    //using array clone instead of remove/delete
    for(var i =0; i<len; i++){
        if(classArr[i].indexOf(string) != 0){
            cloneArr.push(classArr[i]);
        }
    }
    // pb_tracejsArray(cloneArr);
    for(var idxx in cloneArr){
        object.addClass(cloneArr[idxx]);
    }
    // console.log('removed '+object.attr('class'))

    return object;
}

function pb_tracejsArray(array){
    trace('?? > trace array');
    var len = array.length;
    for(var i =0; i<len; i++){
        trace(array[i]);
    }
}


//Delete
function pb_getClassParamAfterPrefix(string,array){


    var len = array.length;
    for(var i =0; i<len; i++){
        if(array[i].indexOf(string) == 0){
            return array[i].substr(string.length);
        }
    }

}


function pb_unixTimeToDateTimeFormat(unixtime){
    var dtx = new Date(parseInt(unixtime)*1000);
    var output = '';
    output += dtx.getDate()+"/";
    output += (dtx.getMonth()+1)+"/";
    output += dtx.getFullYear()+" ";
    output += dtx.getHours()+":";
    output += dtx.getMinutes();


    return output;
}

function pb_unixTimeFromDateTimeFormat(datetime){
    // return Math.round(new Date(datetime).getTime()/1000)
    // var datetime = '25/1/2013 12:20';
    var bunch = datetime.split(' ');
    var date = bunch[0].split('/');
    var time = bunch[1].split(':');

    return Math.round(new Date(date[2], date[1]-1,date[0], time[0], time[1], 0,0).getTime()/1000);

}









$(document).ready(function(){






// //		$('.edit_reward_type').live('click',function(){
// //            $('#reward_collection').modal('show');
// //		})
// 		//preparing badges collection
// 		$('#reward_collection').on('hidden',function(){

// 			if(DEBUG)console.log('collection dialog been close');


// 			// scope with current node
// 			if(DEBUG)console.log('scope > '+pbd_rule_globalvar.collectionCurrentNode_id)
// 			if(pbd_rule_globalvar.collectionCurrentNode_id!=undefined && pbd_rule_globalvar.collectionCurrentSelectedItem !=undefined){

// 				var target = $('.'+pbd_rule_globalvar.collectionCurrentNode_id);
// 	      		//set reward type(id) 
// 	      		target.find('.reward_type').val(pbd_rule_globalvar.collectionCurrentSelectedItem);
// 	      		target.find('.pbd_rule_text.view_as_collection').html(pbd_rule_globalvar.collectionCurrentSelectedItem);	
// 			}

//       	})
// //		$('#badge_selection_btn').live('click',function(){
// //			pbd_rule_globalvar.collectionCurrentSelectedItem = pbd_selected_badge;
// //			console.log('Change selected badge > '+pbd_rule_globalvar.collectionCurrentSelectedItem);
// //		})







    // $('.collection_nav .nav-tabs li').live('click',function(){
    // 	$('.collection_nav .nav-tabs li').removeClass('active');
    // 	$(this).addClass('active');
    // 	var id = $(this).attr('id');

    // });

    // $('.pbd_rule_reward_collection_modal').on('hidden',function(){
    // 	//force close modal-backdrop
    // 		$(this).modal('hide');
    // 		$('body').removeClass('modal-open');
    // 		$('.modal-backdrop').remove()

    //  });






    /*
     +------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
     rule_misc
     +------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
     */










    function pb_getNodeCount(){
        /*return node count in list*/
        return $('ul.pbd_rule_unit_wrapper').children().length;
    }



    function pb_clearSelectedItem(){
        $('.pbd_list_selection tr td').removeClass('pbd_selected_item');
        $('.pbd_list_selection tr td').find('i').removeClass('icon-white');
        pbd_rule_globalvar.modalOptionSelectedItem = undefined;
    }






    function pb_nodeDataRowSave(){
        $(document).on("click", '.pbd_rule_action span', function (event) {
            var id = $(this).attr('id');
            if(id == 'pbd_rule_action_edit'){


            }else if(id == 'pbd_rule_action_save'){


            }

        })
    }

    function sized(string){
        var output = '';

        string = string.split('.');
        output = string[0]+'-50x50.'+string[1];

        return output;
    }

    //  function pb_renderBadgesBasketForId(setIndex,jsonString){


    // var badge_li = $('.collection_basket ul');
    // badge_li.empty();

    // var jsonObj = $.parseJSON(jsonString);
    // var badges_set = jsonObj.badges_set;
    // var items = jsonObj.badges_set.items;


    // var img_location = pbd_nodeString.badges_location;
    // var itemCount = 0;

    // var prefix_set = $.parseJSON(pbd_nodeString.badges_pathname_prefix);
    // var badgesetCount = 0;
    // var max = prefix_set[badgesetCount].max;


    // for(var index in items){
    // 	// if(itemCount<max-1)itemCount++;
    // 	item = items[index];
    // 	var imgSized = sized(item.img_path);
    // 	trace('refer image path ? '+img_location+imgSized);
    // 			// trace(img_location+name+itemCount+'.png');
    // 			var badge = '<li style="background:url(\''+img_location+imgSized+'\');background-size: 100% 100%;"';
    // 			badge += "id='"+item.id+"' class=''  title='"+item.name+" :: "+item.description+"' >"+item.id+"</li>";
    // 			badge_li.append(badge);
    // }


    // // bind each li to -> pb_readBadgeBasketForSelectedItem
    // $('.collection_basket ul li').live('click',function(){
    // 	$('.collection_basket ul li').removeClass('selected_badge');
    // 	$(this).addClass('selected_badge');
    // 	pbd_selected_badge = $(this).attr('id');
    // 	pb_readBadgeBasketForSelectedItem();
    // })

    // }//end function


    // function pb_readBadgeBasketForSelectedItem(){
    // $('.collection_basket ul li').each(function(){
    // 	var currentItem = $(this);
    // 	if(pb_isClassExist( currentItem,'selected_badge')) {
    // 		pb_trace(' selected Item >> '+currentItem.attr('id')+" + "+currentItem.attr('class'));
    // 	}

    // })

    // }



    // function pb_renderBadgesCollection(jsonString){
    // 	//select element
    // 	var nav_li = $('.collection_nav .nav-tabs');

    // 	// clear element
    // 	nav_li.empty();
    // 	trace('try render badges : '+jsonString);


    // 	var jsonObj = $.parseJSON(jsonString);
    // 	trace('Rendering bages -> ');
    // 	trace(jsonObj);




    // 	//for common 1 set
    // 	nav_item = "<li class='active' id='"+jsonObj.badges_set.set_id+"'>"+jsonObj.badges_set.set_label+"<div class='arrow-right'></div></li>";
    // 	nav_li.append(nav_item);

    // 	pb_renderBadgesBasketForId(jsonObj.badges_set.set_id,jsonString);
    // 	isFirstNodeRendered = true;


    // 	//bind each li to -> pb_renderBadgesBasketForId
    // 	$('.collection_nav .nav-tabs li').live('click',function(){
    // 		pb_renderBadgesBasketForId($(this).attr('id'),jsonString);

    // 	});

    // }






})//end document ready




var notificationManagerJS = (function($){
    var obj = {};
    obj.showAlertDialog = function(title,message){
        // trace('notificationManagerJS.showAlertDialog("'+title+'","'+message+'")');
        var img = 'image/notification_icon/inform.png';

        if(title=='loadtable'){
            title = "Table updated";
            img = 'image/notification_icon/load_table.png';

        }else if(title=='loadrule'){
            title = "Editor updated";
            img = 'image/notification_icon/load_editor.png';

        }else if(title=='save'){
            title = "Saved";
            img = 'image/notification_icon/save.png';
            title = "Message"

        }else if(title=='delete'){
            title = "Deleted";
            img = 'image/notification_icon/delete.png';
            title = "Message"

        }else{//inform
            title = "Message";

        }



        $.extend($.gritter.options, {
            position: 'bottom-right', // defaults to 'top-right' but can be 'bottom-left', 'bottom-right', 'top-left', 'top-right' (added in 1.7.1)
            fade_in_speed: 'medium', // how fast notifications fade in (string or int)
            fade_out_speed: 2000, // how fast the notices fade out
            time: 6000 // hang on the screen for...
        });


        // $('#add-sticky').click(function(){		});
        var unique_id = $.gritter.add({
            title: title,
            text: message,
            position: 'bottom-right',
            image: img,
            sticky: true,
            time: '',
            class_name: 'rule_editor_notify'
        });

        setTimeout(function(){
            $.gritter.remove(unique_id, {
                fade: true,
                speed: 'slow'
            });
        }, 8000)
        return false;
    }
    return obj;
}(jQuery))


var progressDialog = (function($){
    var obj = {};
    obj.show = function(text){
        //If dialog appear hide all notification
        // $.gritter.removeAll();// $('.gritter-close').trigger('click');

        $('body').prepend('<div class="custom_blackdrop"><img src="./image/white_loading.gif" /><br><span>'+text+'</span></div>');
    }

    obj.hide = function(){

        if(DEBUG)console.log('execute hide!');
        setTimeout(function(){
            $('.custom_blackdrop').remove();
        },1000)
    }

    return obj;
}(jQuery))



//out of scope

function undefinedReplacement(variable,targetValue){
    if(DEBUG)console.log(variable+" :: "+targetValue)
    if(variable == undefined || variable == 'undefined' || variable == '' || variable == NaN){
        variable = targetValue;
        if(DEBUG)console.log('replace with new value')
    }else{
        if(DEBUG)console.log('not! replace with new value')
    }

    return variable;
}


