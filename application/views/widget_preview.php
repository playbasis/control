<?php
    $typeWidget =  $_GET['type'];
    $width =  isset($_GET['width'] ) ? $_GET['width'] : '360';
    $height =  isset($_GET['height'] )  ? $_GET['height'] : '360';
    $color =  isset($_GET['color'] )  ? $_GET['color'] : '#0e9ce41';
    $rankby =  isset($_GET['rankby'] )  ? $_GET['rankby'] : 'point';
    $displaypoint =  isset($_GET['displaypoint'] )  ? $_GET['displaypoint'] : 'point';
    $player_id =  isset($_GET['playerId'] )  ? $_GET['playerId'] : '';

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
        background: #F5F5F5;
    }
    </style>
</head>
<body>

<script type='text/javascript'>
    window.PBAsyncInit = function(){
        PB.init({
            api_key:'<?php echo $site_data["api_key"]; ?>',
            theme_color :'#<?php echo $color; ?>',//'#52b398',
            <?php
            if($player_id){
            ?>
            playerId : '<?php echo $player_id; ?>'//optional
            <?php
            }
            ?>
        });
    };
    (!function(d,s,id){
        var js,fjs=d.getElementsByTagName(s)[0],
                p=/^http:/.test(d.location)?'http':'https';
        if(!d.getElementById(id)){js=d.createElement(s);js.id=id;
            js.src=p+"://widget.pbapp.net/playbasis/en/all.js";
            fjs.parentNode.insertBefore(js,fjs);}
    }(document,"script","playbasis-js"));
</script>


<?php if($typeWidget == 'profile'): ?>
    <?php if($width < 500){$width = 500;} ?>
    <div style="width:<?php echo $width; ?>px;margin:40px auto;">
        <div class="pb-profile" data-pb-width="<?php echo $width; ?>" data-pb-displayPoint="<?php echo $displaypoint; ?>" ></div>
    </div>
<?php endif; ?>

<?php if($typeWidget == 'leaderboard'): ?>
    <div style="width:<?php echo $width; ?>px;margin:40px auto;">
        <div class="pb-leaderboard"  data-pb-width="<?php echo $width; ?>"  data-pb-rankBy="<?php echo $rankby; ?>" ></div>
    </div>
<?php endif; ?>


<?php if($typeWidget == 'livefeed'): ?>
    <div style="width:<?php echo $width; ?>px;margin:40px auto;">
        <div class="pb-livefeed" data-pb-width="<?php echo $width; ?>" data-pb-height="<?php echo $height; ?>" ></div>
    </div>
<?php endif; ?>

<!--Widget Code Starts-->
<!-- <div class="pb-gaminotification" data-pb-position="top-right"></div> -->
<!--Widget Code Ends-->

<!--Widget Code Starts-->
<!-- <div class="playbasis-leaderboard" data-leaderboard-id="221015714591364" data-leaderboard-width="300" data-leaderboard-height="600" ></div> -->
<!--Widget Code Ends-->

<?php if($typeWidget == 'userbar'): ?>
<div class="pb-userbar"  data-pb-displayPoint="<?php echo $displaypoint; ?>" ></div>
<?php endif; ?>

</body>
</html>