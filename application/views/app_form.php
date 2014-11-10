

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
                <td><span class="required">*</span> <?php echo $this->lang->line('entry_app_name'); ?>:</td>
                <td><input type="text" name="app_name" value="<?php echo isset($domain_domain_name) ? $domain_domain_name : set_value('app_name'); ?>" size="50" class="tooltips" data-placement="right" title="App Name (Unique)"/></td>
            </tr>
            <tr>
                <td><span class="required">*</span> <?php echo $this->lang->line('entry_platform'); ?>:</td>
                <td>
                        <div class="select-platform-wrapper">
                            
                                <?php
                                $data1 = array(
                                    'id'        => 'web',
                                    'name'        => 'platform',
                                    'value'       => 'web',
                                    'checked'     => TRUE
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
                                    'checked'     => FALSE
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
                                    'checked'     => FALSE
                                );
                                echo form_radio($data3); ?>
                                <label class="android" for="android">
                                    <i class="fa fa-android"></i><br>
                                    <?php echo $this->lang->line('entry_andriod'); ?>
                                </label>
                        </div>
                </td>
            </tr>
            <tr class="app-tab web">
                <td><span class="required">*</span> Site Url:</td>
                <td><input type="text" name="site_url" value="" size="50" /> <span class="muted">ex. http://www.example.com</span></td>
            </tr>
            <tr class="app-tab ios">
                <td><span class="required">*</span> Bundle ID:</td>
                <td><input type="text" name="ios_bundle_id" value="" size="50" /> <span class="muted">ex. com.companyname.appname</span></td>
            </tr>
            <tr class="app-tab ios">
                <td>iPhone Store ID</td>
                <td><input type="text" name="ios_iphone_store_id" value="" size="50" /> <span class="muted">ex. 544007664</span></td>
            </tr>
            <tr class="app-tab ios">
                <td>iPad Store ID</td>
                <td><input type="text" name="ios_ipad_store_id" value="" size="50" /> <span class="muted">ex. 544007664</span></td>
            </tr>
            <tr class="app-tab android">
                <td><span class="required">*</span> Package Name:</td>
                <td><input type="text" name="android_package_name" value="" size="50" /> <span class="muted">ex. com.companyname.appname</span></td>
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
        $('.app-tab.'+$('input[name=platform]').val() ).fadeIn('fast');
        $('input[name=platform]').change(function(){
            $('.app-tab').hide();
            $('.app-tab.'+$(this).val() ).fadeIn('fast');
        });
    })
</script>
