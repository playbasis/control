(function( $ ){

    $.fn.slidePanel = function(options) {

        var defaults = {
            main_panel: ".rulelist_container",
            second_panel: ".rule_jigsaws_container",
            full_width: 96,
            main_show: 80,
            motion: 'fast'
        };

        var opts = $.extend(defaults, options);

        var second_show = opts.full_width - opts.main_show;

        if($(window).width() < 768 && $(window).width() > 0){
            opts.main_show = 95;
            second_show = 95;
        }

        $(opts.main_panel).animate({
            width: opts.main_show+"%"
        }, opts.motion );

        $(opts.second_panel).animate({
            width: second_show+"%"
        }, opts.motion );


    };

})( jQuery );

$(document).ready(function() {
    $().slidePanel();

    $('.one_rule_new_btn').click( function(){
        $().slidePanel({main_show:47.5});
    });

    $('.gen_rule_edit_btn').live('click',function(event){
        $().slidePanel({main_show:47.5});
    });

    $('.import_rule_btn').click( function(){
        $().slidePanel({main_show:47.5});
    });

    /*$('.export_rule_btn').click( function(){
        $().slidePanel({main_show:47.5});
    });*/

    /* for screen width lower than 768*/
    $(window).resize(function() {
        if($(window).width() >= 768){
            if($('.pbd_one_rule_holder').css('display') == 'none' && $('.pbd_rule_import').css('display') == 'none'){
                $().slidePanel();
            }else{
                $().slidePanel({main_show:47.5});
            }
        }else if($(window).width() < 768 && $(window).width() > 0){
            $().slidePanel({full_width:190,main_show:95});
        }
    });
});