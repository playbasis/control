

(function( $ ){

    $.fn.tabPage = function(options) {
        var defaults = {
            collection_id: 1,
            url: urlConfig.URL_getBadges(),
            main_panel: "#reward_collection",
            panel_badge: "#rule-reward-modal",
            output_data: ".reward_type",
            html_load: '<center><img src="./image/white_loading.gif" height="100" width="100" /></center>'
        };

        var opts = $.extend(defaults, options);

        var html = '';
        if(!$(".collection_nav").length){

            $.ajax({
                url: opts.url ,
                dataType:"json",
                beforeSend :function(){
                    $(opts.panel_badge).html(opts.html_load);
                },
                success: function(json) {
                    // console.log('rendering badges');
                    // console.log(json)

                    html = '<div id="" class="collection_nav span3"><ul class="nav nav-tabs nav-stacked">';
                    var c = 0;
                    for (i in json) {
                        html += '<li id="tab-'+c+'">'+i+'</li>';
                        c++;
                    }
                    html += '</ul></div><div class="collection_basket span6">';
                    c = 0;
                    for (i in json) {
                        html += '<ul id="page-'+c+'" class="tab-page">';
                        for (j in json[i]) {
                            var image = '';
                            if(json[i][j].image){
                                var ims = json[i][j].image.split(".");
                                image = imageUrlPath+"cache/"+ims[0]+"-100x100."+ims[1];
                            }else{
                                image = imageUrlPath+"cache/data/no_image-100x100.jpg";
                            }

                            var badgeTitle = json[i][j].name;
                            var badgeDescription = json[i][j].description;

                            //set badgeTitle
                                if(badgeTitle==undefined || badgeTitle =='')
                                    badgeTitle = 'Unnamed badge';
                            //set badgeDes
                                if(badgeDescription==undefined || badgeDescription =='')
                                    badgeDescription = 'Undefined badge description';

                                // console.log(badgeTitle+'::'+badgeDescription);

                            html += '<li id="'+json[i][j].badge_id+'" title="" class="badge-reward"><img src="'+image+'" />'
                            +'<div><h1>'+badgeTitle+'</h1><span>'+strip_tags(badgeDescription)+'</span></div></li>';


                        }
                        html += '</ul>';
                        c++;
                    }
                    html += '</div>';

                },
                complete: function() {
                    $(opts.panel_badge).html(html);
                    $(opts.main_panel).modal('show');
                    $(".tab-page").hide();
                    $(".tab-page").first().show();
                    $(".collection_nav ul.nav-tabs li").first().addClass("active");
                }
            });

        }

        $(".nav-tabs li").live('click', function(){
            var c = $(this).attr('id');
            var cursor = c.split('-');
            $(".tab-page").hide();
            $("#page-"+cursor[1]).show();
        });

        $(".badge-reward").live('click', function(){
            $(".badge-reward").removeClass('tab-item-hightlight');
            $(this).addClass('tab-item-hightlight');
        });

        $("#badge_selection_btn").unbind('click').on('click', function(){
            var send_data = $('.tab-item-hightlight').attr('id');
            var img = $('.tab-item-hightlight').find('img').attr('src')

            if( typeof opts.targetId != 'undefined' ){
                $targetObj = $('#'+opts.targetId).find(opts.output_data+':first');
                $targetObj.val(send_data);

                var tableRow = $targetObj.parent().parent();
                tableRow.find('img').remove();
                tableRow.prepend( BadgeSet.getBadgeImage(send_data));

            }else{
                $(opts.output_data).val(send_data);
                //Append Badges Images here
                var tableRow = $(opts.output_data).parent().parent();
                tableRow.find('img').remove();
                // tableRow.find('input').hide();
                tableRow.prepend( BadgeSet.getBadgeImage(send_data));
            }
            

            

        });

    };

})( jQuery );

$(document).ready(function() {
    $('.edit_reward_type').live('click',function(){
        if($.isFunction($.fn.tabPage)){
            $().tabPage({
                url :urlConfig.URL_getBadges(),
                targetId: $(this).closest('.pbd_ul_child').attr('id')
            });
        }
        $('#reward_collection').modal('show');
    })



    //Fetch badge collection item
    $.ajax({
        url: urlConfig.URL_getBadges(),
        dataType:"json",
        success: function(json) {
            BadgeSet.list = json.badges;
        }
    });

});


BadgeSet = {

    list:[],
    getBadgeImage:function(id){
        var output  = '';
        if(this.list && this.list.length > 0){
            for(var index in this.list){
                var b = this.list[index];
                if(b.badge_id == id){
                        var image = '';
                                var ims = b.image.split(".");
                                image = imageUrlPath+"cache/"+ims[0]+"-100x100."+ims[1];
                            output =  '<img src="'+image+'" />';
                    break;
                }else{
                    output =  '<img src="'+imageUrlPath+'cache/data/no_image-100x100.jpg"/>';
                }
            }//end for
        }//end if
        return output;
    }//end function


};
