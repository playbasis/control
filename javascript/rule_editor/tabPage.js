

(function( $ ){

    $.fn.tabPage = function(options) {
        var defaults = {
            collection_id: 1,
            url: urlConfig.URL_getBadges(),
            main_panel: "#reward_collection",
            panel_badge: "#rule-reward-modal",
            output_data: ".reward_type",
            html_load: '<center><img src="./image/white_loading.gif" height="100" width="100" /></center>',
            type: 'badge'
        };

        var opts = $.extend(defaults, options);

        var html = '';
        var is_goods = opts.type && opts.type == 'goods';
        if(!$(is_goods ? ".collection_nav_goods" : ".collection_nav_badge").length){

            $.ajax({
                url: opts.url ,
                dataType:"json",
                beforeSend :function(){
                    $(opts.panel_badge).html(opts.html_load);
                },
                success: function(json) {
                    // console.log('rendering badges');
                    // console.log(json)

                    if (is_goods) {
                        html = '<div id="" class="collection_nav collection_nav_goods span3"><ul class="nav nav-tabs nav-stacked">';
                    } else {
                        html = '<div id="" class="collection_nav collection_nav_badge span3"><ul class="nav nav-tabs nav-stacked">';
                    }
                    var c = 0;
                    for (i in json) {
                        if(i == ''){
                            html += '<li id="tab-' + c + '">&nbsp</li>';
                        }else {
                            html += '<li id="tab-' + c + '">' + i + '</li>';
                        }
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

                            var title = is_goods && json[i][j].group ? json[i][j].group : json[i][j].name;
                            var description = json[i][j].description;

                            if(title==undefined || title =='')
                                title = 'Unnamed ' + (is_goods ? 'goods' : 'badge');
                            if(description==undefined || description =='')
                                description = 'Undefined ' + (is_goods ? 'goods' : 'badge') + ' description';

                            html += '<li id="'+(is_goods ? json[i][j].goods_id : json[i][j].badge_id)+'" title="" class="badge-reward"><img src="'+image+'" />'
                            +'<div><h1>'+title+'</h1><span>'+strip_tags(description)+'</span></div></li>';
                        }
                        html += '</ul>';
                        c++;
                    }
                    html += '</div>';

                },
                complete: function() {
                    $(opts.panel_badge).html(html);
                    $(opts.main_panel).modal('show');
                    $(opts.main_panel+" .tab-page").hide();
                    $(opts.main_panel+" .tab-page").first().show();
                    $(opts.main_panel+" .collection_nav ul.nav-tabs li").first().addClass("active");
                }
            });

        }

        $(".nav-tabs li").live('click', function(){
            var c = $(this).attr('id');
            var cursor = c.split('-');
            $(".tab-page").hide();
            $("#page-"+cursor[1]).show();
            $(opts.main_panel+" .collection_nav ul.nav-tabs li").removeClass("active");
            $("#"+c).addClass("active");
        });

        $(".badge-reward").live('click', function(){
            $(".badge-reward").removeClass('tab-item-hightlight');
            $(this).addClass('tab-item-hightlight');
        });

        $("#badge_selection_btn").unbind('click');
        $("#goods_selection_btn").unbind('click');

        if(is_goods){
            $("#goods_selection_btn").unbind('click').on('click', function(){
                var send_data = $(opts.main_panel+' .tab-item-hightlight').attr('id');
                var img = $(opts.main_panel+' .tab-item-hightlight').find('img').attr('src')

                if( typeof opts.targetId != 'undefined' ){
                    $targetObj = $('#'+opts.targetId).find(opts.output_data+':first');
                    $targetObj.val(send_data);

                    var tableRow = $targetObj.parent().parent();
                    tableRow.find('img').remove();
                    tableRow.prepend( GoodsSet.getGoodsImage(send_data));
                    tableRow.find('.pbd_rule_text').addClass('view_as_collection-goods');

                    tableRow = $targetObj.parent().parent().parent().parent();
                    tableRow.find('.view_as_read_only').html(GoodsSet.getGoodsName(send_data));
                }else{
                    $(opts.output_data).val(send_data);
                    //Append Goods Images here
                    var tableRow = $(opts.output_data).parent().parent();
                    tableRow.find('img').remove();
                    tableRow.prepend( GoodsSet.getGoodsImage(send_data));
                    tableRow.find('.pbd_rule_text').addClass('view_as_collection-goods');
                }
            });
        }else{
            $("#badge_selection_btn").unbind('click').on('click', function(){
                var send_data = $(opts.main_panel+' .tab-item-hightlight').attr('id');
                var img = $(opts.main_panel+' .tab-item-hightlight').find('img').attr('src')

                if( typeof opts.targetId != 'undefined' ){
                    $targetObj = $('#'+opts.targetId).find(opts.output_data+':first');
                    $targetObj.val(send_data);

                    var tableRow = $targetObj.parent().parent();
                    tableRow.find('img').remove();
                    tableRow.prepend( BadgeSet.getBadgeImage(send_data));
                    tableRow.find('.pbd_rule_text').addClass('view_as_collection');

                    tableRow = $targetObj.parent().parent().parent().parent();
                    tableRow.find('.view_as_read_only').html(BadgeSet.getBadgeName(send_data));
                }else{
                    $(opts.output_data).val(send_data);
                    //Append Badges Images here
                    var tableRow = $(opts.output_data).parent().parent();
                    tableRow.find('img').remove();
                    tableRow.prepend( BadgeSet.getBadgeImage(send_data));
                    tableRow.find('.pbd_rule_text').addClass('view_as_collection');
                }
            });
        }
    };

})( jQuery );

$(document).ready(function() {
    $('.edit_reward_type').live('click',function(){
        if($.isFunction($.fn.tabPage)){
            $().tabPage({
                url: urlConfig.URL_getBadges(),
                targetId: $(this).closest('.pbd_ul_child').attr('id'),
                collection_id: 1,
                main_panel: "#reward_collection",
                panel_badge: "#rule-reward-modal",
                output_data: ".reward_type",
                html_load: '<center><img src="./image/white_loading.gif" height="100" width="100" /></center>',
                type: 'badge'
            });
        }
        $('#reward_collection').modal('show');
    })

    $('.edit_goods_type').live('click',function(){

        if($.isFunction($.fn.tabPage)){
            $().tabPage({
                url: urlConfig.URL_getGoods(),
                targetId: $(this).closest('.pbd_ul_child').attr('id'),
                collection_id: 1,
                main_panel: "#goods_collection",
                panel_badge: "#rule-goods-modal",
                output_data: ".goods_type",
                html_load: '<center><img src="./image/white_loading.gif" height="100" width="100" /></center>',
                type: 'goods'
            });
        }
        $('#goods_collection').modal('show');
    })

    //Fetch badge collection item
    $.ajax({
        url: urlConfig.URL_getBadges(),
        dataType:"json",
        success: function(json) {

            for (var category in json) {
                if (json.hasOwnProperty(category)) {
                    BadgeSet.list = BadgeSet.list.concat( json[category]);
                }
            }
        }
    });

    //Fetch goods collection item
    $.ajax({
        url: urlConfig.URL_getGoods(),
        dataType:"json",
        success: function(json) {
            GoodsSet.list = json.goods;
        }
    });
});

BadgeSet = {
    list:[],
    getBadgeImage:function(id){
        var output =  '<img src="'+imageUrlPath+'cache/data/no_image-100x100.jpg"/>';
        if(this.list && this.list.length > 0){
            for(var index in this.list){
                var b = this.list[index];
                if(b.badge_id == id){
                        var image = '';
                                var ims = b.image.split(".");
                                image = imageUrlPath+"cache/"+ims[0]+"-100x100."+ims[1];
                            output =  '<img src="'+image+'" />';
                    break;
                }
            }//end for
        }//end if
        return output;
    },//end function

    getBadgeName:function(id){
        var output  = 'item';
        if(this.list && this.list.length > 0){
            for(var index in this.list){
                var b = this.list[index];
                if(b.badge_id == id){
                    output =  b.name;
                    break;
                }
            }//end for
        }//end if
        return output;
    }//end function
};

GoodsSet = {
    list:[],
    new_list:[],
    getGoodsImageDirect:function(id){
        var output  = '<img src="'+imageUrlPath+'cache/data/no_image-100x100.jpg"/>';
        var found = false;
        if(this.list && this.list.length > 0){
            for(var index in this.list){
                var b = this.list[index];

                if(b.goods_id == id){
                    var image = '';
                    var ims = b.image.split(".");
                    image = imageUrlPath+"cache/"+ims[0]+"-100x100."+ims[1];
                    output =  '<img src="'+image+'" />';
                    found = true;
                    break;
                }
            }//end for
            if(!found){
                if(this.new_list && this.new_list[0] != undefined && this.new_list.length > 0){
                    // check already in new list ?
                    for(var index1 in this.new_list){
                        var a = this.new_list[index1];
                        if(a.goods_id == id){
                            var image = '';
                            var ims = a.image.split(".");
                            image = imageUrlPath+"cache/"+ims[0]+"-100x100."+ims[1];
                            output =  '<img src="'+image+'" />';
                            found = true;
                            break;
                        }
                    }
                }
                if(!found && id){
                    var goods_info = [];
                    //todo: set time out of jquery
                    $.ajax({
                        url: urlConfig.URL_getGoodsById(),
                        data: 'goodsID='+id ,
                        dataType:"json",
                        async: false,
                        success: function(json) {
                            goods_info = json.goods_info;
                            if(typeof goods_info != "undefined"){
                                GoodsSet.new_list.push(goods_info);
                                var image = '';
                                var ims = goods_info['image'].split(".");
                                image = imageUrlPath+"cache/"+ims[0]+"-100x100."+ims[1];
                                output =  '<img src="'+image+'" />';
                            }
                        }
                    });
                }
            }
        }//end if
        return output;
    },//end function

    getGoodsImage:function(id){
        var output  = '<img src="'+imageUrlPath+'cache/data/no_image-100x100.jpg"/>';
        if(this.list && this.list.length > 0){
            for(var index in this.list){
                var b = this.list[index];
                if(b['goods_id'] == id){
                    var image = '';
                    var ims = b.image.split(".");
                    image = imageUrlPath+"cache/"+ims[0]+"-100x100."+ims[1];
                    output =  '<img src="'+image+'" />';
                    break;
                }
            }//end for
        }//end if

        return output;
    },//end function

    getGoodsNameDirect:function(id){
        var output =  'goods';
        var found = false;
        if(this.list && this.list.length > 0){
            for(var index in this.list){
                var b = this.list[index];
                if(b.goods_id == id){
                    if (typeof b.group != "undefined"){
                        output =  b.group;
                    }else{
                        output =  b.name;
                    }
                    found = true;
                    break;
                }
            }//end for
            if(!found){
                if(this.new_list && this.new_list[0] != undefined && this.new_list.length > 0){
                    // check already in new list ?
                    for(var index1 in this.new_list){
                        var c = this.new_list[index1];
                        if(c.goods_id == id){
                            if (typeof c.group != "undefined"){
                                output =  c.group;
                            }else{
                                output =  c.name;
                            }
                            found = true;
                            break;
                        }
                    }
                }
                if(!found && id){
                    var goods_info = [];
                    $.ajax({
                        url: urlConfig.URL_getGoodsById(),
                        data: 'goodsID='+id ,
                        dataType:"json",
                        success: function(json) {
                            goods_info = json.goods_info;
                            if(typeof goods_info != "undefined"){
                                GoodsSet.new_list.push(goods_info);
                                if (typeof goods_info['group'] != "undefined"){
                                    output =  goods_info['group'];
                                }else{
                                    output =  goods_info['name'];
                                }
                            }
                        },
                        async: false
                    });
                }
            }
        }//end if
        return output;
    },//end function

    getGoodsName:function(id){
        var output  = 'goods';
        if(this.list && this.list.length > 0){
            for(var index in this.list){
                var b = this.list[index];
                if(b.goods_id == id){
                    if (typeof b.group != "undefined"){
                        output =  b.group;
                    }else{
                        output =  b.name;
                    }
                    break;
                }
            }//end for
        }//end if
        return output;
    }//end function
};