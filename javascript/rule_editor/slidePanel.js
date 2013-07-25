(function( $ ){

    $.fn.slidePanel = function(options) {
        var defaults = {
            main_panel: ".rulelist_container",
            second_panel: ".rule_jigsaws_container",
            full_width: 95,
            main_show: 80,
            motion: 'fast'
        };

        var opts = $.extend(defaults, options);

        var second_show = opts.full_width - opts.main_show;

        $(opts.main_panel).animate({
            width: opts.main_show+"%"
        }, opts.motion );

        $(opts.second_panel).animate({
            width: second_show+"%"
        }, opts.motion );

        /* for screen width lower than 768*/
        $(window).resize(function() {
            if($(window).width() >= 768){
                if($('.pbd_one_rule_holder').css('display') == 'none'){
                    $().slidePanel();
                }else{
                    $().slidePanel({main_show:47.5});
                }
            }else{
                $(opts.main_panel).animate({
                    width: "95%"
                }, opts.motion );

                $(opts.second_panel).animate({
                    width: "95%"
                }, opts.motion );
            }
        });
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

});