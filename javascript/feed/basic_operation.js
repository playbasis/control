// SLIDE 3.php
//====================
$(document).ready(function(){

    $('#pbd_context_visit').scroll(function(){
      if(isScrollBottom()){
        //scroll is at the bottom
        //do something...
        // console.log(' >>  >>  >>  >>  >>  >> ');
         // console.log('visit');
        pb.showDialog('reward', {'type':'point', 'value': '10'});
      }
    });

    function isScrollBottom() {
      var elementHeight = $('#pbd_context_visit img').height();
      // console.log('elementHeight >> '+elementHeight);
      var scrollPosition = $('#pbd_context_visit').height() + $('#pbd_context_visit').scrollTop();
      // console.log('scrollPosition >> '+scrollPosition);
      return (elementHeight == scrollPosition-2);
    }

})
// END SLIDE 6.php

// SLIDE 6 .PHP
//====================
var lock_path = '../image/data/feed/img_badges_blank.png';
var adspush_selectedIndex = 2;
var badges_collection_filled = false;
// $('.pbd_context_demo_badges_shelf').children().each(function(){
// 	var classes = $(this).attr('class');
// 	if(classes.indexOf('pbd_locked') > -1){
// 		$(this).attr('src',lock_path);
// 	}else{
// 		$(this).attr('src','image/data/feed/img_badges'+cnt+'.png')
// 	}

// 	cnt++;
// 	cnt%=3;
// })

$('.pbd_context_demo_badges_shelf').children().each(function(){
    $(this).attr('src',lock_path);
})

function oprt_fill_badges(){
    var cnt=0;
    var cntx = 0;
    $('.pbd_context_demo_badges_shelf').children().each(function(){
        var object = $(this);

        var time = ++cnt*150;
        setTimeout(function(){
            // console.log('item at >> '+time)
            var classes = object.attr('class');
            if(classes.indexOf('pbd_locked') > -1){
                //$(this).attr('src',lock_path);
            }else{
                object.hide();
                object.attr('src','../image/data/feed/img_badges'+cntx+'.png')
                object.show('fast');
            }
            cntx++;
            cntx%=3;
        }, time);

    })
}

function oprt_reload_exp(target){
    //
}

$(document).ready(function(){

    $('.pbd_context_demo_level').on('hover', function(){
        setTimeout(function() {
            $('.pbd_context_demo_level_bar_inner.bar').css('width', '30%');
        }, 2000);

        setTimeout(function() {
            $('.pbd_context_demo_level_bar_inner.bar').css('width', '60%');
        }, 5000);

        setTimeout(function() {
            $('.pbd_context_demo_level_bar_inner.bar').css('width', '80%');
        }, 7000);
    });

    $('.pbd_context_demo_badges_shelf').hover(function(){
        // console.log('hever me');

        //hover to filled badges
        if(!badges_collection_filled){
            badges_collection_filled = true;
            oprt_fill_badges();
        }
    })

    //Dropdown plugin data
    var ddData = [
        {
            text: "Ensogo",
            value: 1,
            selected: false,
            description: "Ensogo Ads. description here.",
            imageSrc: "../image/data/feed/logo_ensogo.png"
        },
        {
            text: "What's new",
            value: 2,
            selected: false,
            description: "What's new Ads. description here.",
            imageSrc: "../image/data/feed/logo_whatsnew.png"
        },
        {
            text: "Oishi",
            value: 3,
            selected: true,
            description: "Oishi Ads. description here.",
            imageSrc: "../image/data/feed/logo_oishi.png"
        }
    ];

    $('#demoBasic').ddslick({
        data: ddData,
        width: 'auto',
        imagePosition: "left",
        selectText: "Select your favorite social network",
        onSelected: function (data) {
            // console.log(data.selectedIndex);
            adspush_selectedIndex = data.selectedIndex;
        }
    });
})
// END SLIDE 6 .PHP

// SLIDE 7 .PHP
//====================
var pbd_name = new Array();
pbd_name[0] = 'https://dev.pbapp.net/image/data/feed/friends_01.jpg|Siritra';
//pbd_name[1] = 'friends_02.jpg|PM_Master';
//pbd_name[2] = 'friends_03.jpg|Rob';
pbd_name[1] = 'http://farm9.staticflickr.com/8074/8297480044_6cdbc329d8_q.jpg|nadene pellegrin';
pbd_name[2] = 'http://farm9.staticflickr.com/8201/8224001073_e3b7cb6964_q.jpg|lizeth glines ';
pbd_name[3] = 'https://dev.pbapp.net/image/data/feed/friends_04.jpg|Gus';
pbd_name[4] = 'https://dev.pbapp.net/image/data/feed/friends_05.jpg|Nupang';
pbd_name[5] = 'http://farm9.staticflickr.com/8338/8203654554_7d9d210f19_q.jpg|leigha rabago';
pbd_name[6] = 'http://farm9.staticflickr.com/8074/8319177394_ac6ae5fb9c_q.jpg|melody neumann';


var pbd_action_image = new Array();
pbd_action_image[0] = 'user.png|visit';
pbd_action_image[1] = 'chat.png|comment';
pbd_action_image[2] = 'like.png|like';
pbd_action_image[3] = 'share.png|share';
pbd_action_image[4] = 'heart.png|want';

$(document).ready(function(){

    //Randomly Add Content
    var n = 0;
//    var i = setInterval( function() {
//        if(n++<1000) {
//            pushFeed(setUpFeedObject());
//        } else {
//            clearInterval(i);
//        }
//    },8000);

    //Set Time ago to feed
    var m = 0;
    var p = setInterval(function(){

         $('#noti-stream').children().each(function(){

            //read Time
            var readTime = $(this).find('.noti-stream-item-time-stamp').attr('title');
            //Apply TimeAgo
            $(this).find('.noti-stream-item-time-stamp').html(jQuery.timeago(parseInt(readTime)));

         })

    },30000);

})

function setUpFeedObject(){

    var rand_index = Math.floor((Math.random()*5)+1)-1;
    // console.log(' rand index >>>' +rand_index);

    var text = pbd_name[rand_index].split('|');
//        var img = '../image/data/feed/'+text[0];
        var img = text[0];
        var name = text[1];
    // console.log(name + ' >> '+ img);

    var rand_index_inv = Math.floor((Math.random()*5)+1)-1;
    // console.log(' rand_inv index >>>' +rand_index_inv);
    var actz = pbd_action_image[rand_index_inv].split('|');
        var act_img = 'https://dev.pbapp.net/image/data/default/'+actz[0];
        var act_text = actz[1];
    // console.log(act_text + ' >> '+ act_img);


    return forgeStreamListItem(img,name,act_text,act_img);
}

function pushFeed(object){
    // console.log('Push Feed');
    // console.log(object);

    object.hide().prependTo('#noti-stream').slideDown("fast");
}

function forgeStreamAdsItem(img,name,message,link){
    // console.log(content_side);

    var object = $('#noti_stream_ads_item_modal').clone();

    object.attr('id','');
        var timestamp = new Date().getTime();
        object.find('.noti_stream_item_time_ago').attr('title',timestamp);
        object.find('.noti_stream_item_time_ago').html('just now');

    object.find('.noti_stream_item_name h4').html(name);
    // object.find('.noti_stream_item_info').html(message);
    object.find('.noti_stream_item_time_ago').html(message);
    object.find('.topic_img').attr('src',img);

    object.find('#noti_uname_act').html(name);
    object.find('.noti_stream_item_action_info a').html(link);
    object.find('.noti_stream_item_badge').css('visibility','invisible');

    // console.log(object);

    return object;
}

function forgeStreamListItem(img,name,act,badge){

        var object = $('#noti-stream-item-modal').clone();

        object.attr('id','');
        var timestamp = new Date().getTime();
        object.find('.noti-stream-item-time-stamp').attr('title',timestamp);
        object.find('.noti-stream-item-time-stamp').html('just now');

        object.find('.noti-stream-item-name h4').html(name);
        object.find('.noti-stream-item-portrait').attr('src',img);

        object.find('.noti-uname-act').html(name);
        object.find('.noti-action-act').html(act);
        object.find('.noti-stream-item-badge').attr('src',badge);

        return object;
}

$(function(){
    $('#pbd_stream_ads_push_btn').click(function(){
        // console.log('pbd_stream_ads_push_btn');

        var img = '';
        var name = '';
        var link = '';
        var message = $('#pbd_stream_ads_message').val();//'test';

        // console.log(adspush_selectedIndex);
        switch(adspush_selectedIndex){
            case 0 : img = '../image/data/feed/logo_ensogo.png';
             name = 'Ensogo';
             link = 'playth.is/asdf';
             break;
            case 1 : img = '../image/data/feed/logo_whatsnew.png';
             name = "What's new";
             link = 'playth.is/hjkl';
             break;
            case 2 : img = '../image/data/feed/logo_oishi.png';
             name = 'Oishi';
             link = 'playth.is/qwert';
             break;
            default: return;
        }

        pushFeed(forgeStreamAdsItem(img,name,message,link));

    });

})
            // $('#pbd_stream_ads_push_btn').click(function(){
            //     console.log('pbd_stream_ads_push_btn');
                        //if message and link != blank




            //         // pushFeed(forgeStreamAdsItem(img,name,message,link));

            // });

// END SLIDE 7 .PHP

// SLIDE 8 .PHP
//====================
$(document).ready(function(){

    $(document).on('click', '.tab_indicator_fit li', function () {

        // console.log('>> click');

        $('.tab_indicator_fit li').removeClass('pbd_r_active');
        $(this).addClass('pbd_r_active');

        var id = $(this).attr('id');
        if(id=='buddy'){
            $('.pbd_leader-list').html($('#pbd_buddy_modal_mock').clone());
        }else if(id=='weekly'){
            $('.pbd_leader-list').html($('#pbd_weekly_modal_mock').clone());
        }else if(id=='global'){
            $('.pbd_leader-list').html($('#pbd_global_modal_mock').clone());
        }

    });

    // first time load
    // $('.tab_indicator_fit li#buddy').trigger('click');
    $('.tab_indicator_fit li').removeClass('pbd_r_active');
    $('.tab_indicator_fit li#buddy').addClass('pbd_r_active');
    $('.pbd_leader-list').html($('#pbd_buddy_modal_mock').clone());

});
// END SLIDE 8 .PHP