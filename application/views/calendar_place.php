<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_watch'); ?></button>
            </div>
        </div><!-- .heading -->
        <div class="content">
                <div id="tabs" class="htabs">
                    <a href="<?php echo site_url('calendar');?>" style="display: inline;"><?php echo $this->lang->line('tab_setup'); ?></a>
                    <a href="<?php echo site_url('calendar/place');?>" class="selected" style="display: inline;"><?php echo $this->lang->line('tab_place'); ?></a>
                    <a href="<?php echo site_url('calendar/webhook');?>" style="display: inline;"><?php echo $this->lang->line('tab_webhook'); ?></a>
                </div>

            <?php if($this->session->flashdata('success')){ ?>
                <div class="content messages half-width">
                    <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php }?>
            <?php if($this->session->flashdata('fail')){ ?>
                <div class="content messages half-width">
                    <div class="warning"><?php echo $this->session->flashdata('fail'); ?></div>
                </div>
            <?php }?>

            <?php if (isset($calendar)) { ?>
                <?php echo form_open('calendar/place', array('id' => 'form')); ?>
                <table class="list">
                    <thead>
                    <tr>
                        <td width="7" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                        <td class="center" style="width:10px;"><?php echo $this->lang->line('column_id'); ?></td>
                        <td class="center" style="width:20px;"><?php echo $this->lang->line('column_name'); ?></td>
                        <td class="center" style="width:100px;"><?php echo $this->lang->line('column_description'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (isset($places) && $places) { ?>
                        <?php foreach ($places as $place) { ?>
                            <tr>
                                <td style="text-align: center;"><?php if (isset($place['selected']) && $place['selected']) { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $place['placeID']; ?>" checked="checked" />
                                    <?php } else { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $place['placeID']; ?>" />
                                    <?php } ?></td>
                                <td class="left"><?php echo $place['placeID']; ?></td>
                                <td class="left"><?php echo $place['name']; ?></td>
                                <td class="left"><?php echo $place['description']; ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td class="center" colspan="4"><?php echo $this->lang->line('text_no_results'); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php echo form_close();?>
            <?php } else { ?>
                Please set up your Google account with Playbasis first.
            <?php } ?>
        </div><!-- .content -->
    </div><!-- .box -->
</div><!-- #content .span10 -->

<script type="text/javascript">
    $(document).ready(function(){
        $('[rel=popover]').popover({
            html: true
        });
    });
    $('body').on('click', function (e) {
        $('[rel="popover"]').each(function () {
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    });
</script>