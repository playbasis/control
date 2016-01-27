(function( $ ){

    $.fn.goToTop = function(options) {
        var defaults = {
            area_motion: "html, body",/* area to scroll up */
            motion: "slow"
        };

        var opts = $.extend(defaults, options);

        $(this).click(function() {
            $(opts.area_motion).animate({ scrollTop: 0 }, opts.motion);
            return false;
        });
    };

    $.fn.goToDown = function(options) {
        var defaults = {
            area_motion: "html, body",/* area to scroll down */
            motion: "slow"
        };

        var opts = $.extend(defaults, options);

        $(this).click(function() {
            var n = $(document).height();
            $(opts.area_motion).animate({ scrollTop: n }, opts.motion);
            return false;
        });
    };

    $.fn.fixMenuVertical = function(options) {
        var defaults = {
            position: "right", /* left , right*/
            space_of_top: "180px",
            z_index: 999999
        };

        var this_ele = $(this);
        var opts = $.extend(defaults, options);

        $(this).css("position","fixed");

        switch(opts.position) {
            case ("right"):
                $(this).css("right",0);
                break;
            case ("left"):
                $(this).css("left",0);
                break;
            default:
                $(this).css("right",0);
        }

        if(opts.space_of_top){
            $(this).css("top",opts.space_of_top);
        }

        if(opts.z_index){
            $(this).css("z-index",opts.z_index);
        }

        /* for screen width lower than 768*/
        $(window).resize(function() {
            if($(window).width() >= 768){
                $(".screen-width-768").hide();
                $(this_ele).show();
            }else{
                $(".screen-width-768").show();
                $(this_ele).hide();
            }
        });

    };

    $.fn.disableFixMenu = function() {
        if(!$(this).hasClass("disabled")){
            $(this).addClass("disabled");
            $("<div></div>").attr('id','block').attr('class','fixMenuDisable').appendTo($(this));
        }
    };

    $.fn.activeFixMenu = function() {
        if($(this).hasClass("disabled")){
            $(this).removeClass("disabled");
            $(this).find('#block').remove();
        }
    };

    $.fn.expandAndCollapse = function(options) {

        var defaults = {
            parent_element: ".pbd_one_rule_holder",
            pointer_up_element: ".icon-chevron-up",
            pointer_down_element: ".icon-chevron-down",
            class_collapse: "collapse-fixmenu",
            class_expand: "expand-fixmenu"
        };

        var opts = $.extend(defaults, options);

        if($(this).hasClass(opts.class_collapse)){
            $(this).removeClass(opts.class_collapse).addClass(opts.class_expand);
            $.each($(opts.parent_element+" "+opts.pointer_up_element), function(){
                
                
                //Every game rules node has its own Unique id if undefined it's not game rules node
                //So ignore to collapse it
                var uid = $(this).parent().parent().parent().parent().parent().attr('id');
                if(uid!=undefined)$(this).parent().click();
            
                var title = oneRuleMan.updateRuleHeaderById(uid);
                if(title)
                    $('#'+uid).find('#rule_box_name').html(title);

            });

            //On Collapse

        }else{
            $(this).removeClass(opts.class_expand).addClass(opts.class_collapse);
            $.each($(opts.parent_element+" "+opts.pointer_down_element), function(){

                
                var uid = $(this).parent().parent().parent().parent().parent().attr('id');
                if(uid!=undefined)$(this).parent().click();

                var title = oneRuleMan.updateRuleHeaderById(uid);
                if(title)
                    $('#'+uid).find('#rule_box_name').html(title);
            });

            //On Expand
        }
    }

})( jQuery );

$(document).ready(function() {
    $(".fixMenu").disableFixMenu();

    $("a[href='#top']").goToTop({motion:"fast"});
    $("a[href='#down']").goToDown({motion:"fast"});
    $(".fixMenu").fixMenuVertical({position:"right", z_index:10000});

    $("#fixMenuActionRule").click( function(){
        $("#fixMenuActionRule").expandAndCollapse();
    });

    $('.one_rule_new_btn').click( function(){
        $(".fixMenu").activeFixMenu();
    });

    $(document).on("click", '.gen_rule_edit_btn', function (event) {
        $(".fixMenu").activeFixMenu();
    });

});