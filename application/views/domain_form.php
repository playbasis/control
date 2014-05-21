<div id="content" class="span10">

<div class="box">
<div class="heading">
    <h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
    <div class="buttons">
        <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
        <button class="btn btn-info" onclick="location = baseUrlPath+'domain'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
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
                <td><span class="required">*</span> <?php echo $this->lang->line('entry_domain_name'); ?>:</td>
                <td><input type="text" name="domain_domain_name" value="<?php echo isset($domain_domain_name) ? $domain_domain_name : set_value('domain_domain_name'); ?>" size="50" class="tooltips" data-placement="right" title="Client's domain name (example: www.playbasis.com)"/></td>
            </tr>
            <tr>
                <td><span class="required">*</span> <?php echo $this->lang->line('entry_site_name'); ?>:</td>
                <td><input type="text" name="domain_site_name" value="<?php echo isset($domain_site_name) ? $domain_site_name : set_value('domain_site_name'); ?>" size="50" class="tooltips" data-placement="right" title="Client's site name (example: Playbasis)"/></td>
            </tr>
        </table>
    </div>
<?php
echo form_close();
?>
</div>
</div>
</div>