var action_list = {};

$(document).ready(function() {

    $.each(jsonString_Action, function(i,v){
        action_list[v.specific_id] = v;
    });

});