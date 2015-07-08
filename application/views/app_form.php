

<div id="content" class="span10">

<div class="box">
<div class="heading">
    <h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
    <div class="buttons">
        <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
        <button class="btn btn-info" onclick="location = baseUrlPath+'app'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
    </div>
</div>
<div class="content">
<?php if($this->session->flashdata('success')){ ?>
    <div class="content messages half-width">
        <div id = "notification2" class="success"><?php echo $this->session->flashdata('success'); ?></div>
    </div>
<?php }?>
<div id ="notification" class="half-width"></div>
<?php
if(validation_errors() || isset($message)) {
    ?>
    <div class="content messages half-width">
        <?php
        echo validation_errors('<div class="warning">','</div>');

        if (isset($message) && $message) {
            ?>
            <div class="warning"><?php echo $message; ?></div>
        <?php
        }
        ?>
    </div>
<?php
}
$attributes = array('id' => 'form');
echo form_open($form ,$attributes);
?>
    <div id="tab-domain">
        <table class="form">
            <tr>
                <?php
                if($app_id){
                ?>
                    <td> <?php echo $this->lang->line('entry_app_name'); ?>:</td>
                    <td> <?php echo isset($app_name) ? $app_name : set_value('app_name'); ?></td>
                <?php
                }else{
                ?>
                    <td><span class="required">*</span> <?php echo $this->lang->line('entry_app_name'); ?>:</td>
                    <td><input type="text" name="app_name" value="<?php echo isset($app_name) ? $app_name : set_value('app_name'); ?>" size="50" class="tooltips" data-placement="right" title="App Name (Unique)"/></td>
                <?php
                }
                ?>
            </tr>
            <tr>
                <td><span class="required">*</span> <?php echo $this->lang->line('entry_platform'); ?>:</td>
                <td>
                        <div class="select-platform-wrapper">
                            <?php
                            if($platform_id){
                            ?>
                                <?php
                                if($platform == 'web'){
                                    $aicon = 'fa-desktop';
                                    $aname = $this->lang->line('entry_web');
                                    $avalue = 'web';
                                }elseif($platform == 'ios'){
                                    $aicon = 'fa-apple';
                                    $aname = $this->lang->line('entry_ios');
                                    $avalue = 'ios';
                                }elseif($platform == 'android'){
                                    $aicon = 'fa-android';
                                    $aname = $this->lang->line('entry_android');
                                    $avalue = 'android';
                                }
                                ?>

                                <?php
                                $data = array(
                                    'id'        => $avalue,
                                    'name'        => 'platform',
                                    'value'       => $avalue,
                                    'checked'     => TRUE
                                );

                                echo form_radio($data); ?>
                                <label class="<?php echo $avalue; ?>" for="<?php echo $avalue; ?>">
                                    <i class="fa <?php echo $aicon; ?>"></i><br>
                                    <?php echo $aname; ?>
                                </label>
                            <?php
                            }else{
                            ?>
                                    <?php
                                    $data1 = array(
                                        'id'        => 'web',
                                        'name'        => 'platform',
                                        'value'       => 'web',
                                        'checked'     => isset($platform) && $platform == "web" ? TRUE : (set_value('platform')?(set_value('platform')=='web'?TRUE:FALSE):FALSE)
                                    );

                                    echo form_radio($data1); ?>
                                    <label class="web" for="web">
                                        <i class="fa fa-desktop"></i><br>
                                        <?php echo $this->lang->line('entry_web'); ?>
                                    </label>

                                    <?php
                                    $data2 = array(
                                        'id'        => 'ios',
                                        'name'        => 'platform',
                                        'value'       => 'ios',
                                        'checked'     => isset($platform) && $platform == "ios" ? TRUE : (set_value('platform')?(set_value('platform')=='ios'?TRUE:FALSE):FALSE)
                                    );
                                    echo form_radio($data2); ?>
                                    <label class="ios" for="ios">
                                        <i class="fa fa-apple"></i><br>
                                        <?php echo $this->lang->line('entry_ios'); ?>
                                    </label>

                                    <?php
                                    $data3 = array(
                                        'id'        => 'android',
                                        'name'        => 'platform',
                                        'value'       => 'android',
                                        'checked'     => isset($platform)  && $platform == "android" ? TRUE : (set_value('platform')?(set_value('platform')=='android'?TRUE:FALSE):FALSE)
                                    );
                                    echo form_radio($data3); ?>
                                    <label class="android" for="android">
                                        <i class="fa fa-android"></i><br>
                                        <?php echo $this->lang->line('entry_android'); ?>
                                    </label>

                            <?php
                            }
                            ?>
                        </div>
                </td>
            </tr>
            <tr class="app-tab web">
                <td><span class="required">*</span> Site Url:</td>
                <td><input type="text" name="site_url" value="<?php echo $site_url; ?>" size="50" /> <span class="muted">ex. http://www.example.com</span></td>
            </tr>
            <tr class="app-tab ios">
                <td><span class="required">*</span> Bundle ID:</td>
                <td><input type="text" name="ios_bundle_id" value="<?php echo $ios_bundle_id; ?>" size="50" /> <span class="muted">ex. com.companyname.appname</span></td>
            </tr>
            <tr class="app-tab ios">
                <td>iPhone Store ID</td>
                <td><input type="text" name="ios_iphone_store_id" value="<?php echo $ios_iphone_store_id; ?>" size="50" /> <span class="muted">ex. 544007664</span></td>
            </tr>
            <tr class="app-tab ios">
                <td>iPad Store ID</td>
                <td><input type="text" name="ios_ipad_store_id" value="<?php echo $ios_ipad_store_id; ?>" size="50" /> <span class="muted">ex. 544007664</span></td>
            </tr>
            <tr class="app-tab android">
                <td><span class="required">*</span> Package Name:</td>
                <td><input type="text" name="android_package_name" value="<?php echo $android_package_name; ?>" size="50" /> <span class="muted">ex. com.companyname.appname</span></td>
            </tr>
        </table>
    </div>
<?php
echo form_close();
?>
</div>
</div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.app-tab').hide();

        $("input[name=platform]:first").click();

        var platform = $('input[name=platform]:checked').val();
        $('.app-tab.'+platform ).fadeIn('fast');

        $('input[name=platform]').change(function(){
            $('.app-tab').hide();
            platform = $(this).val();
            $('.app-tab.'+platform ).fadeIn('fast');
        });
    })
</script>