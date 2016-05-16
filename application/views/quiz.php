<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="location =  baseUrlPath+'quiz/insert'" type="button"><?php echo $this->lang->line('button_insert'); ?></button>
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_delete'); ?></button>
            </div>
        </div><!-- .heading -->
        <div class="content">
            <?php if($this->session->flashdata('success')){ ?>
                <div class="content messages half-width">
                    <div class="success"><?php echo $this->session->flashdata('success'); ?></div>
                </div>
            <?php }?>
            <div id="quizs">
                <?php $attributes = array('id'=>'form');?>
                <?php echo form_open('quiz/delete', $attributes);?>
                <table class="list">
                    <thead>
                    <tr>
                        <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_name'); ?></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_date_added'); ?></td>
                        <td class="right" style="width:100px;"><?php echo $this->lang->line('column_status'); ?></td>
                        <td class="right" style="width:140px;"><?php echo $this->lang->line('column_tags'); ?></td>
                        <td class="right" style="width:140px;"><?php echo $this->lang->line('column_action'); ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="filter">
                        <td></td>
                        <td><input type="text" name="filter_name" value="" style="width:50%;" /></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="right">
                            <a onclick="clear_filter();" class="button" id="clear_filter"><?php echo $this->lang->line('button_clear_filter'); ?></a>
                            <a onclick="filter();" class="button"><?php echo $this->lang->line('button_filter'); ?></a>
                        </td>
                    </tr>

                    <?php if(isset($quizs) && $quizs){?>
                        <?php foreach($quizs as $quiz){?>
                            <tr>
                                <td style="text-align: center;"><?php if (isset($quiz['selected'])) { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $quiz['_id']; ?>" checked="checked" />
                                    <?php } else { ?>
                                        <input type="checkbox" name="selected[]" value="<?php echo $quiz['_id']; ?>" />
                                    <?php } ?></td>
                                <td class="right"><?php echo $quiz['name']; ?> <?php if (!empty($quiz['error'])) { ?><span class="red"><a herf="javascript:void(0)" class="error-icon" title="<?php echo $quiz['error']; ?>" data-toggle="tooltip"><i class="icon-warning-sign" ></i></a></span><?php } ?></td>
                                <td class="right"><?php echo datetimeMongotoReadable($quiz['date_added']); ?></td>
                                <td class="right"><?php echo ($quiz['status'])? "Enabled" : "Disabled"; ?></td>
                                <td class="right"><?php echo (isset($quiz['tags'])? implode($quiz['tags'],',') : null); ?></td>
                                <td class="right">
                                    [ <?php if($client_id){
                                        echo anchor('quiz/edit/'.$quiz['_id'], 'Edit');
                                    }else{
                                        echo anchor('quiz/edit/'.$quiz['_id'], 'Edit');
                                    }
                                    ?> ]
                                </td>

                            </tr>
                        <?php }
                            }else{
                        ?>
                    <tr>
                        <td class="center" colspan="5">
                        No quiz
                        </td>
                    </tr>
                    <?php }?>

                    </tbody>
                </table>
                <?php echo form_close();?>
            </div>
            <div class="pagination">
                <ul class='ul_rule_pagination_container'>
                    <li class="page_index_number active"><a>Total Records:</a></li> <li class="page_index_number"><a><?php echo number_format($pagination_total_rows); ?></a></li>
                    <li class="page_index_number active"><a>(<?php echo number_format($pagination_total_pages); ?> Pages)</a></li>
                    <?php echo $pagination_links; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript"><!--
    function filter() {
        url = baseUrlPath+'quiz';

        var filter_name = $('input[name=\'filter_name\']').attr('value');

        if (filter_name) {
            url += '?filter_name=' + encodeURIComponent(filter_name);
        }

        location = url;
    }
    //-->
</script>

<script type="text/javascript">
    <?php if (!isset($_GET['filter_name'])){?>
    $("#clear_filter").hide();
    <?php }else{?>
    $("#clear_filter").show();
    <?php }?>

    function clear_filter(){
        window.location.replace(baseUrlPath+'quiz');
    }
</script>