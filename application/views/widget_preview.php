<?php
    $typeWidget =  $_GET['type'];

    $apikey =  isset($_GET['apikey'] )  ? $_GET['apikey'] : '';
    $color =  isset($_GET['color'] ) ? $_GET['color'] : '';
    $player_id =  isset($_GET['player_id'] )  ? $_GET['player_id'] : '';
    $cssfile =  isset($_GET['cssfile'] )  ? $_GET['cssfile'] : '';

    $width =  !empty($_GET['width'] ) ? $_GET['width'] : '';
    $height =  !empty($_GET['height'] )  ? $_GET['height'] : '';
    $rankby =  isset($_GET['rankby'] )  ? $_GET['rankby'] : 'point';
    $displaypoint =  isset($_GET['displaypoint'] )  ? $_GET['displaypoint'] : 'point';

    $nologin =  isset($_GET['nologin'] )  ? $_GET['nologin'] : '';

    $rule_id =  isset($_GET['rule_id'] )  ? $_GET['rule_id'] : '';
    // echo $width.'<br>';
    // echo $height.'<br>';
    // echo $color.'<br>';
    // echo $rankby.'<br>';
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <title>Widget Preview</title>
    <style type="text/css">
    body{
        font-family: Arial, Helvetica, sans-serif;
        background: #F5F5F5;
        padding: 20px;
    }
    .noti-warning{
        color: #ccc;
        text-align: center;
        font-size: 30px;
        margin-top: 100px;
    }
    </style>
</head>
<body>

<?php if($apikey != ''){ ?>

    <script type='text/javascript'>
        window.PBAsyncInit = function(){
            PB.init({
                api_key:'<?php echo $apikey; ?>',
                <?php if($color){ ?>
                theme_color :'#<?php echo $color; ?>',
                <?php } ?>
                <?php if($player_id){ ?>
                player_id : '<?php echo $player_id; ?>',//optional
                <?php } ?>
                <?php if( !empty($cssfile) ){ ?>
                cssfile : '<?php echo $cssfile; ?>',//optional
                <?php } ?>
                // pb_host: '//localhost/widget',
                pb_host: '//<?php echo WIDGET_SERVER; ?>/v1',
                api_node: '<?php echo NODE_SERVER; ?>',
                api_url: '<?php echo API_SERVER; ?>'
            });
        };
        (!function(d,s,id){
            var js,fjs=d.getElementsByTagName(s)[0],
                    p=/^http:/.test(d.location)?'http':'https';
            if(!d.getElementById(id)){js=d.createElement(s);js.id=id;
                js.src=p+"://<?php echo WIDGET_SERVER; ?>/sdk.js?version=v1";
                // js.src=p+"://localhost/widget/playbasis/en/all.js";
                fjs.parentNode.insertBefore(js,fjs);}
        }(document,"script","playbasis-js"));
    </script>


    <?php if($typeWidget == 'profile'): ?>
        <?php if($width < 500){$width = 500;} ?>
        <?php if( empty($player_id) ){ ?>
            <div class="noti-warning">
                Please insert Player ID
            </div>
        <?php }else{ ?>
            <div style="width:<?php echo $width; ?>px;margin:40px auto;">
                <div class="pb-profile" <?php echo !empty($width)? 'data-pb-width="'.$width.'"' : ''; ?> <?php echo !empty($width)? 'data-pb-height="'.$height.'"' : ''; ?> data-pb-displayPoint="<?php echo $displaypoint; ?>" ></div>
            </div>
        <?php } ?>
    <?php endif; ?>

    <?php if($typeWidget == 'leaderboard'): ?>
        <div class="pb-leaderboard" <?php echo !empty($width)? 'data-pb-width="'.$width.'"' : ''; ?> <?php echo !empty($width)? 'data-pb-height="'.$height.'"' : ''; ?>  data-pb-rankBy="<?php echo $rankby; ?>" ></div>
    <?php endif; ?>


    <?php if($typeWidget == 'livefeed'): ?>
            <div class="pb-livefeed" <?php echo !empty($width)? 'data-pb-width="'.$width.'"' : ''; ?> <?php echo !empty($width)? 'data-pb-height="'.$height.'"' : ''; ?> ></div>        
    <?php endif; ?>


    <?php if($typeWidget == 'login'): ?>
           <div class="pb-login" <?php echo !empty($nologin) ? 'data-pb-nologout' : '' ?> <?php echo !empty($width)? 'data-pb-width="'.$width.'"' : ''; ?> <?php echo !empty($width)? 'data-pb-height="'.$height.'"' : ''; ?>></div>
    <?php endif; ?>


    <?php if($typeWidget == 'achievement'): ?>
            <div class="pb-achievement" <?php echo !empty($width)? 'data-pb-width="'.$width.'"' : ''; ?> <?php echo !empty($width)? 'data-pb-height="'.$height.'"' : ''; ?> ></div>
    <?php endif; ?>

    <?php if($typeWidget == 'quiz'): ?>
        <?php if( empty($player_id) ){ ?>
            <div class="noti-warning">
                Please insert Player ID
            </div>
        <?php }else{ ?>
                <div class="pb-quiz" <?php echo !empty($width)? 'data-pb-width="'.$width.'"' : ''; ?> <?php echo !empty($width)? 'data-pb-height="'.$height.'"' : ''; ?> ></div>
        <?php } ?>
        
    <?php endif; ?>

    <?php if($typeWidget == 'quest'): ?>
        <?php if( empty($player_id) ){ ?>
            <div class="noti-warning">
                Please insert Player ID
            </div>
        <?php }else{ ?>
                <div class="pb-quest" <?php echo !empty($width)? 'data-pb-width="'.$width.'"' : ''; ?> <?php echo !empty($width)? 'data-pb-height="'.$height.'"' : ''; ?> ></div>
        <?php } ?>
        
    <?php endif; ?>
    

    <?php if($typeWidget == 'feed'): ?>
        
            <div class="pb-feed" <?php echo !empty($width)? 'data-pb-width="'.$width.'"' : ''; ?> <?php echo !empty($width)? 'data-pb-height="'.$height.'"' : ''; ?>></div>
        
    <?php endif; ?>


    <?php if($typeWidget == 'rewardstore'): ?>
    
            <div class="pb-rewardstore" <?php echo !empty($width)? 'data-pb-width="'.$width.'"' : ''; ?> <?php echo !empty($width)? 'data-pb-height="'.$height.'"' : ''; ?> ></div>
            
        
    <?php endif; ?>

    <?php if($typeWidget == 'treasure'): ?>
    
            <div style="width:<?php echo $width; ?>px;margin:40px auto;">
                
            </div>    
            
            <?php if( empty($player_id) ){ ?>
                <div class="noti-warning">
                    Please insert Player ID
                </div>
            <?php }else if( empty($rule_id) ){ ?>
                <div class="noti-warning">
                    Please insert Rule ID
                </div>
            <?php }else{ ?>
                    <div class="pb-treasure" <?php echo !empty($width)? 'data-pb-width="'.$width.'"' : ''; ?> <?php echo !empty($width)? 'data-pb-height="'.$height.'"' : ''; ?> <?php echo !empty($rule_id)? 'data-pb-rule-id="'.$rule_id.'"' : ''; ?> ></div>
            <?php } ?>

    <?php endif; ?>

    <?php if($typeWidget == 'trackevent'): ?>
    
            <div class="pb-trackevent" ></div>
        
    <?php endif; ?>


<?php } ?>

</body>
</html>