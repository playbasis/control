$.fn.getRuleText = function(options) {
    var defaults = {
        obj_focus: $(this).parents('li').find(".box-content .pbd_boxcontent_action"),
        obj_focus_row: "tr",
        obj_focus_label: ".pbd_rule_label",
        obj_focus_data: ".pbd_rule_data",
        obj_panel_show: $(this).parents('li').find(".box-header .rule-mini"),
        state : "show" //has 2 state show / hide
    };

    var opts = $.extend(defaults, options);
    var panelShow = opts.obj_panel_show;

    if(opts.state == "show"){
        var strShow = "";
        $.each( opts.obj_focus.find(opts.obj_focus_row), function() {
            if(!$(this).hasClass("hide")){
                strShow += "<span>";
                strShow += $(this).find(opts.obj_focus_label).text()+" : ";
                strShow += ($(this).find(opts.obj_focus_data).text().length > 0) ? $(this).find(opts.obj_focus_data).text() : " - ";
                strShow += "</span>";
            }
        });
        panelShow.html(strShow);
        panelShow.css("display", "inline-block");
    }else{
        panelShow.hide();
    }

}