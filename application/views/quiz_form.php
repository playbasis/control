<link rel="stylesheet" href="<?php echo base_url();?>stylesheet/quiz/style.css">

<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'quest'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div><!-- .buttons -->
        </div><!-- .heading -->
        <div class="content">
            <div id="tabs" class="htabs">
                <a href="#tab-quiz-info"><?php echo $this->lang->line('tab_info'); ?></a>
                <a href="#tab-quiz-grade"><?php echo $this->lang->line('tab_grade'); ?></a>
                <a href="#tab-quiz-data"><?php echo $this->lang->line('tab_data'); ?></a>
            </div>

            <?php if(validation_errors() || isset($message)){?>
                <div class="content messages half-width">
                    <?php echo validation_errors('<div class="warning">', '</div>');?>
                    <?php if (isset($message) && $message){
                        foreach ($message as $m): ?>
                            <div class="warning"><?php echo $m;?></div>
                        <?php endforeach; }?>
                </div>
            <?php }?>
            <?php
            $qndata = array('name' => 'quiz_name', 'id' => 'quiz_name','value' => set_value('quiz_name'), "placeholder" => $this->lang->line('quiz_name'), "class"=>"form-control");
            $qddata = array('name' => 'quiz_description', 'id' => 'quiz_description','value' => set_value('quiz_description'), "placeholder" => $this->lang->line('quiz_description'), "class"=>"form-control", "rows" => 3);
            $qnidata = array('name' => 'quiz_image', 'id' => 'quiz_image','value' => set_value('quiz_image'), "placeholder" => $this->lang->line('quiz_image'), "class"=>"form-control");
            $qdidata = array('name' => 'quiz_description_image', 'id' => 'quiz_description_image','value' => set_value('quiz_description_image'), "placeholder" => $this->lang->line('quiz_description_image'), "class"=>"form-control");
            $qwdata = array('name' => 'weight', 'id' => 'weight','value' => set_value('weight'), "placeholder" => $this->lang->line('weight'), "class"=>"form-control");

            $attributes = array('id' => 'form_quiz');
            echo form_open_multipart('quiz',$attributes);
            ?>
            <div id="tab-quiz-info">
                <div class="span6">
                    <table class="form">
                        <tbody>
                        <tr>
                            <td>
                                <?php echo $this->lang->line('quiz_name'); ?> :
                            </td>
                            <td>
                                <?php
                                echo form_input($qndata);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo $this->lang->line('quiz_image'); ?> :
                            </td>
                            <td>
                                <div class="image">
                                    <img src="<?php echo $quiz_thumb; ?>" alt="" id="quiz_thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                                    <input type="hidden" name="quiz_image" value="<?php echo $quiz_image; ?>" id="quiz_image" />
                                    <br />
                                    <a onclick="image_upload('quiz_image', 'quiz_thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                    <a onclick="$('#quiz_thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#quiz_image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo $this->lang->line('weight'); ?> :
                            </td>
                            <td>
                                <?php
                                echo form_input($qwdata);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo $this->lang->line('status'); ?> :
                            </td>
                            <td>
                                <div class="quiz-status <?php echo $status=='true'?'enabled':'disabled'; ?>">
                                    <div class="quiz-status-toggle">
                                        <span><?php echo $status=='true'?'enabled':'disabled'; ?></span>
                                    </div>
                                    <?php
                                    echo form_hidden('status', set_value('status')=='true'? true : ($status=='true'?true:false));
                                    ?>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="span6">
                    <table class="form">
                        <tbody>
                        <tr>
                            <td>
                                <?php echo $this->lang->line('quiz_description'); ?> :
                            </td>
                            <td>
                                <?php
                                echo form_textarea($qddata);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo $this->lang->line('quiz_description_image'); ?> :
                            </td>
                            <td>
                                <div class="image">
                                    <img src="<?php echo $quiz_description_thumb; ?>" alt="" id="quiz_description_thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                                    <input type="hidden" name="quiz_description_image" value="<?php echo $quiz_description_image; ?>" id="quiz_description_image" />
                                    <br />
                                    <a onclick="image_upload('quiz_description_image', 'quiz_description_thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                    <a onclick="$('#quiz_description_thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#quiz_description_image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="tab-quiz-grade">
                <div class="grade-head-wrapper">
                    <a href="javascript:void(0)" class="btn open-grade-btn btn-lg">Open All</a>
                    <a href="javascript:void(0)" class="btn close-grade-btn btn-lg">Close All</a>
                    <a href="javascript:void(0)" class="btn btn-primary add-grade-btn btn-lg">Add Grade</a>
                </div>
                <div class="grade-wrapper">
                    <div class="box-header box-grade-header overflow-visible">
                        <h2><img src="http://localhost/control/image/default-image.png" width="50" onerror="$(this).attr('src','http://localhost/control/image/default-image.png');">grade range ...  -  ... %</h2>
                        <div class="box-icon">
                            <a href="javascript:void(0)" class="btn btn-danger right remove-grade-btn dropdown-toggle" data-toggle="dropdown">Delete </a>
                            <span class="break"></span>
                            <a href="javaScript:void()"><i class="icon-chevron-up"></i></a>
                        </div>
                    </div>
                    <div class="box-content clearfix">
                        <div class="span6">
                            <table class="form">
                            <tbody>
                                <tr>
                                    <td>
                                        If user's final grade is between <input type="text" width="20" name="quiz[grades][0][grade_start]" value = "">% and <input type="text" width="20" name="quiz[grades][0][grade_end]" value = "">%,
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        give letter grade  <input type="text" name="quiz[grades][0][grade]" value = ""> and assign the rank : <input type="text" name="quiz[grades][0][rank]" value = "">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="span6">
                            <table class="form">
                                <tbody>
                                <tr>
                                    <td>
                                        <?php echo $this->lang->line('rank_image'); ?> :
                                    </td>
                                    <td>
                                        <img src="<?php echo ''; ?>" alt="" id="quiz[grades][0][rank_thumb]" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                                        <input type="hidden" name="quiz[grades][0][rank_image]" value="<?php echo ''; ?>" id="quiz[grades][0][rank_image]" />
                                        <br />
                                        <a onclick="image_upload('quiz[grades][0][rank_image]', 'quiz[grades][0][rank_thumb]');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                        <a onclick="$('#quiz[grades][0][rank_thumb]').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#quiz[grades][0][rank_image]').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div id="tab-quiz-data">
                <div class="grade-head-wrapper">
                    <a href="javascript:void(0)" class="btn open-grade-btn btn-lg">Open All</a>
                    <a href="javascript:void(0)" class="btn close-grade-btn btn-lg">Close All</a>
                    <a href="javascript:void(0)" class="btn btn-primary add-grade-btn btn-lg">Add Question</a>
                </div>
                <div class="data-wrapper">
                    <div class="box-header box-data-header overflow-visible">
                        <h2><img src="http://localhost/control/image/default-image.png" width="50" onerror="$(this).attr('src','http://localhost/control/image/default-image.png');">Qustion ... </h2>
                        <div class="box-icon">
                            <a href="javascript:void(0)" class="btn btn-danger right remove-grade-btn dropdown-toggle" data-toggle="dropdown">Delete </a>
                            <span class="break"></span>
                            <a href="javaScript:void()"><i class="icon-chevron-up"></i></a>
                        </div>
                    </div>
                    <div class="box-content clearfix">
                        <div class="span6">
                            <table class="form">
                                <tbody>
                                <tr>
                                    <td>
                                        Question :
                                    </td>
                                    <td>
                                        <input type="text" name="quiz[grades][0][grade]" value = "">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Question image :
                                    </td>
                                    <td>
                                        <img src="<?php echo ''; ?>" alt="" id="quiz[grades][0][rank_thumb]" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                                        <input type="hidden" name="quiz[grades][0][rank_image]" value="<?php echo ''; ?>" id="quiz[grades][0][rank_image]" />
                                        <br />
                                        <a onclick="image_upload('quiz[grades][0][rank_image]', 'quiz[grades][0][rank_thumb]');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                        <a onclick="$('#quiz[grades][0][rank_thumb]').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#quiz[grades][0][rank_image]').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="span6">
                            <div class="box box-add-item completion-wrapper">
                                <div class="box-header overflow-visible">
                                    <h2><span class="break"></span>Options</h2>
                                    <div class="box-icon box-icon-action">
                                        <a href="javascript:void(0)" class="btn btn-primary right add-completion-btn dropdown-toggle" data-toggle="dropdown"> Add option</a>
                                        <span class="break"></span>
                                        <a href="javaScript:void()" class="btn-minimize"><i class="icon-chevron-up"></i></a>
                                    </div>
                                </div>
                                <div class="box-content">
                                    <div class="option-container">
                                        option  <input type="text" name="quiz[options][0][option]" value = ""><br>
                                        option_image  <img src="<?php echo ''; ?>" alt="" id="quiz[grades][0][rank_thumb]" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                                        <input type="hidden" name="quiz[grades][0][rank_image]" value="<?php echo ''; ?>" id="quiz[grades][0][rank_image]" />
                                        <br />
                                        <a onclick="image_upload('quiz[grades][0][rank_image]', 'quiz[grades][0][rank_thumb]');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                        <a onclick="$('#quiz[grades][0][rank_thumb]').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#quiz[grades][0][rank_image]').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a><br>
                                        explanation  <textarea></textarea><br>
                                        score  <input type="text" name="quiz[options][0][score]" value = "">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo base_url();?>javascript/quiz/quiz.js"></script>
<script type="text/javascript">

    $('#tabs a').tabs();

    function image_upload(field, thumb) {
        $('#dialog').remove();

        $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="'+baseUrlPath+'filemanager?field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 200px; height: 100%;" frameborder="no" scrolling="no"></iframe></div>');

        $('#dialog').dialog({
            title: '<?php echo $this->lang->line('text_image_manager'); ?>',
            close: function (event, ui) {
                if ($('#' + field).attr('value')) {
                    $.ajax({
                        url: baseUrlPath+'filemanager/image?image=' + encodeURIComponent($('#' + field).val()),
                        dataType: 'text',
                        success: function(data) {
                            $('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" onerror="$(this).attr(\'src\',\'<?php echo base_url();?>image/default-image.png\');" />');
                        }
                    });
                }
            },
            bgiframe: false,
            width: 200,
            height: 100,
            resizable: false,
            modal: false
        });
    };
</script>