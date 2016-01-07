<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt=""/> <?php echo $heading_title; ?></h1>

            <div class="buttons">
                <button class="btn btn-info" onclick="location =  baseUrlPath+'content/insert'"
                        type="button"><?php echo $this->lang->line('button_insert'); ?></button>
                <button class="btn btn-info" onclick="$('#form').submit();"
                        type="button"><?php echo $this->lang->line('button_delete'); ?></button>
            </div>
        </div>
        <!-- .heading -->
        <div class="content">
            <?php if ($this->session->flashdata('success')) { ?>
                <div class="content messages half-width">
                    <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php } ?>
            <div id="contents">
                <?php $attributes = array('id' => 'form'); ?>
                <?php echo form_open('content/delete', $attributes); ?>
                <table class="list">
                    <thead>
                    <tr>
                        <td width="1" style="text-align: center;">
                            <input type="checkbox"
                                   onclick="$('input[name*=\'selected\']').attr('checked', this.checked);"/>
                        </td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_name'); ?></td>
                        <td class="right" style="width:80px;"><?php echo $this->lang->line('column_category'); ?></td>
                        <td class="right"
                            style="width:100px;"><?php echo $this->lang->line('column_date_range'); ?></td>
                        <td class="right" style="width:60px;"><?php echo $this->lang->line('column_status'); ?></td>
                        <td class="right" style="width:140px;"><?php echo $this->lang->line('column_action'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="filter">
                        <td></td>
                        <td><input title="name" type="text" name="filter_name" value="" style="width:50%;"/></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="right">
                            <a onclick="clear_filter();" class="button"
                               id="clear_filter"><?php echo $this->lang->line('button_clear_filter'); ?></a>
                            <a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a>
                        </td>
                    </tr>

                    <?php if (isset($contents) && $contents) { ?>
                        <?php foreach ($contents as $content) { ?>
                            <tr>
                                <td style="text-align: center;"><?php if (isset($content['selected'])) { ?>
                                        <input type="checkbox" name="selected[]"
                                               value="<?php echo $content['_id']; ?>" checked="checked"/>
                                    <?php } else { ?>
                                        <input type="checkbox" name="selected[]"
                                               value="<?php echo $content['_id']; ?>"/>
                                    <?php } ?></td>
                                <td class="right"><?php echo $content['name']; ?> <?php if (!empty($content['error'])) { ?>
                                        <span class="red"><a herf="javascript:void(0)" class="error-icon"
                                                             title="<?php echo $content['error']; ?>"
                                                             data-toggle="tooltip"><i class="icon-warning-sign"></i></a>
                                        </span><?php } ?></td>
                                <td class="right"><?php echo isset($content['category']['name']) ? $content['category']['name'] : ""; ?></td>
                                <td class="right"><?php echo dateMongotoReadable($content['date_start']); ?>&nbsp;-&nbsp;<?php echo dateMongotoReadable($content['date_end']); ?></td>
                                <td class="right"><?php echo isset($content['status']) ? "Enabled" : "Disabled"; ?></td>
                                <td class="right">
                                    <?php if ($push_feature_existed) { ?>
                                        <span>[ <?php echo anchor('#confirmModal', 'Send push',
                                                array(
                                                    'class' => 'open-confirmModal',
                                                    'title' => 'Send push notification to all players',
                                                    'data-toggle' => 'modal',
                                                    'data-target' => '#confirmModal',
                                                    'data-id' => $content['_id'],
                                                )); ?> ]</span>
                                    <?php } ?>
                                    <span>[ <?php echo anchor('content/update/' . $content['_id'], 'Edit'); ?> ]</span>
                                </td>
                            </tr>
                        <?php }
                    } else {
                        ?>
                        <tr>
                            <td colspan="6" class="center">
                                <?php echo $this->lang->line('text_empty_content'); ?>
                            </td>
                        </tr>
                    <?php } ?>

                    </tbody>
                </table>
                <?php echo form_close(); ?>
            </div>
            <div class="pagination">
                <ul class='ul_rule_pagination_container'>
                    <li class="page_index_number active"><a>Total Records:</a></li>
                    <li class="page_index_number"><a><?php echo number_format($pagination_total_rows); ?></a></li>
                    <li class="page_index_number active"><a>(<?php echo number_format($pagination_total_pages); ?>
                            Pages)</a></li>
                    <?php echo $pagination_links; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php if ($push_feature_existed) { ?>
    <div class="modal hide fade" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true"
         aria-labelledby="confirmModalLabel">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3 id="confirmModalLabel">Send push to players</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to send push notification for this content to all players?</p>
            <input type="hidden" name="contentId" id="contentId" value=""/>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">Cancel</a>
            <a href="#" class="btn btn-primary" id="confirmPush">Confirm</a>
        </div>
    </div>

    <div class="modal hide" id="pleaseWaitDialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-header">
            <h1>Please Wait</h1>
        </div>
        <div class="modal-body">
            <div class="offset5 ">
                <i class="fa fa-spinner fa-spin fa-5x"></i>
            </div>
        </div>
    </div>

    <div class="modal hide" id="sentDialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3>Push notification sent</h3>
        </div>
        <div class="modal-body">
            <div>
                <span><i class="fa fa-send"></i>&nbsp;Push notification has been sent!</span>&nbsp;<span id="devices_sent"></span>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        </div>
    </div>
<?php } ?>
<script type="text/javascript"><!--
    function filter() {
        url = baseUrlPath + 'content';

        var filter_name = $('input[name=\'filter_name\']').attr('value');

        if (filter_name) {
            url += '?name=' + encodeURIComponent(filter_name);
        }

        location = url;
    }
    //-->
</script>

<script type="text/javascript">
    <?php if (!isset($_GET['name'])){?>
    $("#clear_filter").hide();
    <?php }else{?>
    $("#clear_filter").show();
    <?php }?>

    function clear_filter() {
        window.location.replace(baseUrlPath + 'content');
    }

    var $waitDialog = $('#pleaseWaitDialog'),
        $confirmModalDialog = $('#confirmModal'),
        $sentDialog = $('#sentDialog');

    $(document)
        .on("click", ".open-confirmModal", function () {
            var contentId = $(this).data('id');
            $(".modal-body #contentId").val(contentId);
        })
        .on("click", "#confirmPush", function(){
            var contentId = $(".modal-body #contentId").val();
            var request = $.ajax({
                url: baseUrlPath + "content/push/" + contentId,
                type: "POST",
                dataType: "json",
                beforeSend: function (xhr) {
                    $confirmModalDialog.modal('hide');
                    $waitDialog.modal();
                }
            });

            request.done(function (data, textStatus, jqXHR ) {
                $waitDialog.modal('hide');
                var resp = JSON.parse(jqXHR.responseText);
                if (typeof resp !== "undefined")
                    if (resp.hasOwnProperty("devices"))
                        $sentDialog.find("#devices_sent").text("(" + resp.devices + " Devices)");
                $sentDialog.modal();
            });

            request.fail(function( jqXHR, textStatus ) {
                alert(JSON.parse(jqXHR.responseText).message + ' \n\nPlease contact Playbasis!');
            });

            request.always(function(){
                $waitDialog.modal('hide');
            });
        });
</script>