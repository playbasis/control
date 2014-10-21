<link rel="stylesheet" href="<?php echo base_url();?>stylesheet/quiz/style.css">
<script type="text/javascript" src="<?php echo base_url();?>javascript/md5.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/mongoid.js"></script>

<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form_quiz').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'quiz'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
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
            if(isset($quiz) && empty($quiz)){
                unset($quiz);
            }

            $qndata = array('name' => 'name', 'id' => 'quiz_name','value' => isset($quiz)?$quiz['name']:'', "placeholder" => $this->lang->line('quiz_name'), "class"=>"form-control");
            $qddata = array('name' => 'description', 'id' => 'quiz_desc','value' => isset($quiz)?$quiz['description']:'', "placeholder" => $this->lang->line('quiz_description'), "class"=>"form-control", "rows" => 3);
            $qwdata = array('name' => 'weight', 'id' => 'quiz_weight','value' => isset($quiz)?$quiz['weight']:'', "placeholder" => $this->lang->line('weight'), "class"=>"form-control");

            $attributes = array('id' => 'form_quiz');
            if(isset($quiz['_id'])){
                echo form_open_multipart('quiz/edit/'.$quiz['_id']."",$attributes);
            }else{
                echo form_open_multipart('quiz/insert',$attributes);
            }

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
                                    <img width="100" height="100" src="<?php echo isset($quiz)? S3_IMAGE.$quiz['image'] : S3_IMAGE."cache/no_image-100x100.jpg"; ?>" alt="" id="quiz_thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                                    <input type="hidden" name="image" value="<?php echo isset($quiz)? $quiz['image'] : "no_image.jpg"; ?>" id="quiz_image" />
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
                            <td><?php echo $this->lang->line('entry_start_date'); ?>:</td>
                            <td>
                                <input type="text" class="date" name="date_start" value="<?php echo isset($quiz)&&isset($quiz['date_start'])&&$quiz['date_start']?date('Y-m-d', strtotime(datetimeMongotoReadable($quiz['date_start']))):''; ?>" size="50" />
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo $this->lang->line('entry_expire_date'); ?>:</td>
                            <td>
                                <input type="text" class="date" name="date_expire" value="<?php echo isset($quiz)&&isset($quiz['date_expire'])&&$quiz['date_expire']?date('Y-m-d', strtotime(datetimeMongotoReadable($quiz['date_expire']))):''; ?>" size="50" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo $this->lang->line('status'); ?> :
                            </td>
                            <td>
                                <div class="quiz-status <?php echo isset($quiz)&&$quiz['status']==true?'enabled':'disabled'; ?>">
                                    <div class="quiz-status-toggle">
                                        <span><?php echo isset($quiz)&&$quiz['status']==true?'enabled':'disabled'; ?></span>
                                    </div>
                                    <?php
                                    echo form_hidden('status', isset($quiz)&&$quiz['status']==true?'true':'false');
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
                                    <img width="100" height="100" src="<?php echo isset($quiz)? S3_IMAGE.$quiz['description_image'] : S3_IMAGE."cache/no_image-100x100.jpg"; ?>" alt="" id="quiz_description_thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                                    <input type="hidden" name="description_image" value="<?php echo isset($quiz)? $quiz['description_image'] : "no_image.jpg"; ?>" id="quiz_description_image" />
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
                    <a href="javascript:void(0)" class="btn btn-primary add-grade-btn btn-lg">Add Grade</a>
                </div>
                <div class="grade-wrapper">
                    <?php
                    if(isset($quiz) && $quiz['grades']){
                        foreach($quiz['grades'] as $grade){

                            $grade['grade_id'] = $grade['grade_id']."";

                            $custom_user_set = array();
                            $badge_user_set = array();
                            $point_user_set = array();
                            $exp_user_set = array();

                            if(isset($grade["rewards"])){
                                foreach($grade["rewards"] as $rk=>$rv){
                                    if($rk == "custom"){
                                        $custom_user_set = $rv;
                                    }
                                    if($rk == "badge"){
                                        $badge_user_set = $rv;
                                    }
                                    if($rk == "exp"){
                                        $exp_user_set = $rv;
                                    }
                                    if($rk == "point"){
                                        $point_user_set = $rv;
                                    }
                                }
                            }
                    ?>
                            <div class="box-content clearfix">
                                <div class="span6">
                                    <a href="javascript:void(0)" class="btn btn-danger right remove-grade-btn dropdown-toggle" data-toggle="dropdown">Delete </a>
                                    <table class="form">
                                        <tbody>
                                        <tr>
                                            <td colspan="2">
                                                If user's final grade is between <input type="text" name="quiz[grades][<?php echo $grade['grade_id']; ?>][start]" value = "<?php echo $grade['start']; ?>" class="grades-range" />% and <input type="text" name="quiz[grades][<?php echo $grade['grade_id']; ?>][end]" value = "<?php echo $grade['end']; ?>" class="grades-range" />%,
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                give letter grade  <input type="text" name="quiz[grades][<?php echo $grade['grade_id']; ?>][grade]" value = "<?php echo $grade['grade']; ?>"> and assign the rank : <input type="text" name="quiz[grades][<?php echo $grade['grade_id']; ?>][rank]" value = "<?php echo $grade['rank']; ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?php echo $this->lang->line('rank_image'); ?> :
                                            </td>
                                            <td>
                                                <img width="100" height="100" src="<?php echo isset($grade['rank_image'])? S3_IMAGE.$grade['rank_image'] : S3_IMAGE."cache/no_image-100x100.jpg"; ?>" alt="" id="quiz_grades_<?php echo $grade['grade_id']; ?>_thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                                                <input type="hidden" name="quiz[grades][<?php echo $grade['grade_id']; ?>][rank_image]" value="<?php echo isset($grade['rank_image'])? $grade['rank_image'] : "no_image.jpg"; ?>" id="quiz_grades_<?php echo $grade['grade_id']; ?>_image" />
                                                <br />
                                                <a onclick="image_upload('quiz_grades_<?php echo $grade['grade_id']; ?>_image', 'quiz_grades_<?php echo $grade['grade_id']; ?>_thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                                <a onclick="$('quiz_grades_<?php echo $grade['grade_id']; ?>_thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('quiz_grades_<?php echo $grade['grade_id']; ?>_image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a>
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
                                                <?php echo $this->lang->line('entry_rewards'); ?> :
                                            </td>
                                            <td>
                                                <div class="reward">
                                                    <button id="exp-entry" type="button" class="btn btn-info btn-large btn-block"><?php echo $this->lang->line('entry_exp'); ?></button>
                                                    <div class="exp">
                                                        <div class="reward-panel">
                                                            <span class="label label-primary"><?php echo $this->lang->line('entry_exp'); ?></span>
                                                            <input type="text" name="quiz[grades][<?php echo $grade['grade_id']; ?>][rewards][exp][exp_value]" size="100" class="orange tooltips" value="<?php echo $exp_user_set? $exp_user_set["exp_value"] : ''; ?>" />
                                                        </div>
                                                    </div>
                                                    <button id="point-entry" type="button" class="btn btn-info btn-large btn-block"><?php echo $this->lang->line('entry_point'); ?></button>
                                                    <div class="point">
                                                        <div class="reward-panel">
                                                            <span class="label label-primary"><?php echo $this->lang->line('entry_point'); ?></span>
                                                            <input type="text" name="quiz[grades][<?php echo $grade['grade_id']; ?>][rewards][point][point_value]" size="100" class="orange tooltips" value="<?php echo $point_user_set ? $point_user_set["point_value"] : ''; ?>" />
                                                        </div>
                                                    </div>
                                                    <div id="badge-panel">
                                                        <?php
                                                        if($badge_list){
                                                            ?>
                                                            <br>
                                                            <button id="badge-entry" type="button" class="btn btn-primary btn-large btn-block"><?php echo $this->lang->line('entry_badge'); ?></button>
                                                            <div class="badges">
                                                                <div class="reward-panel">
                                                                    <?php
                                                                    foreach($badge_list as $badge){
                                                                        ?>
                                                                        <img height="50" width="50" src="<?php echo S3_IMAGE.$badge['image']; ?>" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                                                                        <?php
                                                                        $user_b = "";
                                                                        foreach($badge_user_set as $b){
                                                                            if($b["badge_id"] == $badge['badge_id']){
                                                                                $user_b = $b["badge_value"];
                                                                                break;
                                                                            }
                                                                        }
                                                                        ?>
                                                                        <input type="text" name="quiz[grades][<?php echo $grade['grade_id']; ?>][rewards][badge][<?php echo $badge['badge_id']; ?>]" class="<?php echo alternator('green','yellow','blue');?> tooltips" size="100" value="<?php echo $user_b; ?>" /><br/>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        <?php
                                                        }
                                                        ?>
                                                    </div>
                                                    <div id="reward-panel">
                                                        <?php
                                                        if($point_list){
                                                            ?>
                                                            <br>
                                                            <button id="reward-entry" type="button" class="btn btn-warning btn-large btn-block"><?php echo $this->lang->line('entry_custom_point'); ?></button>
                                                            <div class="rewards">
                                                                <div class="reward-panel">
                                                                    <?php
                                                                    foreach($point_list as $point){
                                                                        ?>
                                                                        <?php echo $point['name']; ?>
                                                                        <?php
                                                                        $user_c = "";
                                                                        foreach($custom_user_set as $c){
                                                                            if($c["custom_id"] == $point['reward_id']){
                                                                                $user_c = $c["custom_value"];
                                                                                break;
                                                                            }
                                                                        }
                                                                        ?>
                                                                        <input type="text" name="quiz[grades][<?php echo $grade['grade_id']; ?>][rewards][custom][<?php echo $point['reward_id']; ?>]" class="<?php echo alternator('green','yellow','blue');?>" size="100" value="<?php echo $user_c; ?>" /><br/>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                    <?php
                        }
                    }else{
                    ?>

                        <?php
                        $countgrade = new MongoId();
                        $countgrade = $countgrade."";
                        ?>
                        <div class="box-content clearfix">
                            <div class="span6">
                                <a href="javascript:void(0)" class="btn btn-danger right remove-grade-btn dropdown-toggle" data-toggle="dropdown">Delete </a>
                                <table class="form">
                                    <tbody>
                                    <tr>
                                        <td colspan="2">
                                            If user's final grade is between <input type="text" name="quiz[grades][<?php echo $countgrade; ?>][start]" value = "" class="grades-range" />% and <input type="text" name="quiz[grades][<?php echo $countgrade; ?>][end]" value = "" class="grades-range" />%,
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            give letter grade  <input type="text" name="quiz[grades][<?php echo $countgrade; ?>][grade]" value = ""> and assign the rank : <input type="text" name="quiz[grades][<?php echo $countgrade; ?>][rank]" value = "">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php echo $this->lang->line('rank_image'); ?> :
                                        </td>
                                        <td>
                                            <img width="100" height="100" src="<?php echo S3_IMAGE."cache/no_image-100x100.jpg"; ?>" alt="" id="quiz_grades_<?php echo $countgrade; ?>_thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                                            <input type="hidden" name="quiz[grades][<?php echo $countgrade; ?>][rank_image]" value="<?php echo 'no_image.jpg'; ?>" id="quiz_grades_<?php echo $countgrade; ?>_image" />
                                            <br />
                                            <a onclick="image_upload('quiz_grades_<?php echo $countgrade; ?>_image', 'quiz_grades_<?php echo $countgrade; ?>_thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                            <a onclick="$('quiz_grades_<?php echo $countgrade; ?>_thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('quiz_grades_<?php echo $countgrade; ?>_image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a>
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
                                            <?php echo $this->lang->line('entry_rewards'); ?> :
                                        </td>
                                        <td>
                                            <div class="reward">
                                                <button id="point-entry" type="button" class="btn btn-info btn-large btn-block"><?php echo $this->lang->line('entry_point'); ?></button>
                                                <div class="point">
                                                    <div class="reward-panel">
                                                        <span class="label label-primary"><?php echo $this->lang->line('entry_point'); ?></span>
                                                        <input type="text" name="quiz[grades][<?php echo $countgrade; ?>][rewards][point][point_value]" size="100" class="orange tooltips" value="" />
                                                    </div>
                                                </div>
                                                <div id="badge-panel">
                                                    <?php
                                                    if($badge_list){
                                                        ?>
                                                        <br>
                                                        <button id="badge-entry" type="button" class="btn btn-primary btn-large btn-block"><?php echo $this->lang->line('entry_badge'); ?></button>
                                                        <div class="badges">
                                                            <div class="reward-panel">
                                                                <?php
                                                                foreach($badge_list as $badge){
                                                                    ?>
                                                                    <img height="50" width="50" src="<?php echo S3_IMAGE.$badge['image']; ?>" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                                                                    <input type="text" name="quiz[grades][<?php echo $countgrade; ?>][rewards][badge][<?php echo $badge['badge_id']; ?>]" class="<?php echo alternator('green','yellow','blue');?> tooltips" size="100" value="" /><br/>
                                                                <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                                <div id="reward-panel">
                                                    <?php
                                                    if($point_list){
                                                        ?>
                                                        <br>
                                                        <button id="reward-entry" type="button" class="btn btn-warning btn-large btn-block"><?php echo $this->lang->line('entry_custom_point'); ?></button>
                                                        <div class="rewards">
                                                            <div class="reward-panel">
                                                                <?php
                                                                foreach($point_list as $point){
                                                                    ?>
                                                                    <?php echo $point['name']; ?>
                                                                    <input type="text" name="quiz[grades][<?php echo $countgrade; ?>][rewards][custom][<?php echo $point['reward_id']; ?>]" class="<?php echo alternator('green','yellow','blue');?>" size="100" value="" /><br/>
                                                                <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                    <?php
                    }
                    ?>


                </div>
            </div>
            <div id="tab-quiz-data">
                <div class="question-head-wrapper">
                    <a href="javascript:void(0)" class="btn btn-primary add-question-btn btn-lg">Add Question</a>
                </div>
                <div class="question-wrapper">
                    <?php
                    if(isset($quiz) && $quiz['questions']){
                        foreach($quiz['questions'] as $questions){

                            $questions['question_id'] = $questions['question_id']."";
                    ?>
                        <div class="box-content clearfix">
                            <div class="span6">
                                <table class="form">
                                    <tbody>
                                    <tr>
                                        <td>
                                            Question :
                                        </td>
                                        <td>
                                            <textarea rows="3" name="quiz[questions][<?php echo $questions['question_id']; ?>][question]"><?php echo $questions['question']; ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Question image :
                                        </td>
                                        <td>
                                            <img width="100" height="100" src="<?php echo isset($questions['question_image'])? S3_IMAGE.$questions['question_image'] : S3_IMAGE."cache/no_image-100x100.jpg"; ?>" alt="" id="quiz_questions_<?php echo $questions['question_id']; ?>_thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                                            <input type="hidden" name="quiz[questions][<?php echo $questions['question_id']; ?>][question_image]" value="<?php echo isset($questions['question_image'])? $questions['question_image'] : "no_image.jpg"; ?>" id="quiz_questions_<?php echo $questions['question_id']; ?>_image" />
                                            <br />
                                            <a onclick="image_upload('quiz_questions_<?php echo $questions['question_id']; ?>_image', 'quiz_questions_<?php echo $questions['question_id']; ?>_thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                            <a onclick="$('#quiz_questions_<?php echo $questions['question_id']; ?>_thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#quiz_questions_<?php echo $questions['question_id']; ?>_image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="span6">
                                <div class="box box-add-item completion-wrapper">
                                    <div class="box-header overflow-visible">
                                        <a href="javascript:void(0)" class="btn btn-primary right add-option-btn dropdown-toggle" data-question-id="<?php echo $questions['question_id']; ?>" data-toggle="dropdown"> Add option</a>
                                    </div>
                                    <div class="option-wrapper">

                                        <div class="box-content clearfix">
                                            <?php
                                            if(isset($questions['options'])){
                                                foreach($questions['options'] as $option){

                                                    $option['option_id'] = $option['option_id']."";
                                            ?>
                                                    <div class="option-container">
                                                        option  <input type="text" name="quiz[questions][<?php echo $questions['question_id']; ?>][options][<?php echo $option['option_id']; ?>][option]" value = "<?php echo $option["option"]; ?>"><br>
                                                        image  <img width="100" height="100" src="<?php echo isset($option["option_image"])? S3_IMAGE.$option["option_image"] : S3_IMAGE."cache/no_image-100x100.jpg" ; ?>" alt="" id="quiz_questions_<?php echo $questions['question_id']; ?>_options_<?php echo $option['option_id']; ?>_thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                                                        <input type="hidden" name="quiz[questions][<?php echo $questions['question_id']; ?>][options][<?php echo $option['option_id']; ?>][option_image]" value="<?php echo isset($option["option_image"])? $option["option_image"] : "no_image.jpg" ; ?>" id="quiz_questions_<?php echo $questions['question_id']; ?>_options_<?php echo $option['option_id']; ?>_image" />
                                                        <br />
                                                        <a onclick="image_upload('quiz_questions_<?php echo $questions['question_id']; ?>_options_<?php echo $option['option_id']; ?>_image', 'quiz_questions_<?php echo $questions['question_id']; ?>_options_<?php echo $option['option_id']; ?>_thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                                        <a onclick="$('#quiz_questions_<?php echo $questions['question_id']; ?>_options_<?php echo $option['option_id']; ?>_thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#quiz_questions_<?php echo $questions['question_id']; ?>_options_<?php echo $option['option_id']; ?>_image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a><br>
                                                        explanation  <textarea name="quiz[questions][<?php echo $questions['question_id']; ?>][options][<?php echo $option['option_id']; ?>][explanation]"><?php echo $option["explanation"]; ?></textarea><br>
                                                        score  <input type="text" name="quiz[questions][<?php echo $questions['question_id']; ?>][options][<?php echo $option['option_id']; ?>][score]" value = "<?php echo $option["score"]; ?>">
                                                        <a href="javascript:void(0)" class="btn btn-danger right remove-option-btn dropdown-toggle" data-toggle="dropdown">Delete </a>
                                                    </div>
                                            <?php
                                                }
                                            }else{
                                                $count_option = new MongoId();
                                                $count_option = $count_option."";
                                            ?>
                                                <div class="option-container">
                                                    option  <input type="text" name="quiz[questions][<?php echo $questions['question_id']; ?>][options][<?php echo $count_option; ?>][option]" value = ""><br>
                                                    image  <img width="100" height="100" src="<?php echo S3_IMAGE."cache/no_image-100x100.jpg"; ?>" alt="" id="quiz_questions_<?php echo $questions['question_id']; ?>_options_<?php echo $count_option; ?>_thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                                                    <input type="hidden" name="quiz[questions][<?php echo $questions['question_id']; ?>][options][<?php echo $count_option; ?>][option_image]" value="<?php echo 'no_image.jpg'; ?>" id="quiz_questions_<?php echo $questions['question_id']; ?>_options_<?php echo $count_option; ?>_image" />
                                                    <br />
                                                    <a onclick="image_upload('quiz_questions_<?php echo $questions['question_id']; ?>_options_<?php echo $count_option; ?>_image', 'quiz_questions_<?php echo $questions['question_id']; ?>_options_<?php echo $count_option; ?>_thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                                    <a onclick="$('#quiz_questions_<?php echo $questions['question_id']; ?>_options_<?php echo $count_option; ?>_thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#quiz_questions_<?php echo $questions['question_id']; ?>_options_<?php echo $count_option; ?>_image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a><br>
                                                    explanation  <textarea name="quiz[questions][<?php echo $questions['question_id']; ?>][options][<?php echo $count_option; ?>][explanation]"></textarea><br>
                                                    score  <input type="text" name="quiz[questions][<?php echo $questions['question_id']; ?>][options][<?php echo $count_option; ?>][score]" value = "">
                                                    <a href="javascript:void(0)" class="btn btn-danger right remove-option-btn dropdown-toggle" data-toggle="dropdown">Delete </a>
                                                </div>
                                            <?php
                                            }
                                            ?>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <a href="javascript:void(0)" class="btn btn-danger right remove-question-btn dropdown-toggle" data-toggle="dropdown">Delete </a>

                        </div>
                    <?php
                        }
                    }else{
                    ?>
                    <?php
                        $count_question = new MongoId();
                        $count_question = $count_question."";

                        $count_option = new MongoId();
                        $count_option = $count_option."";
                    ?>
                    <div class="box-content clearfix">
                        <div class="span6">
                            <table class="form">
                                <tbody>
                                <tr>
                                    <td>
                                        Question :
                                    </td>
                                    <td>
                                        <textarea rows="3" name="quiz[questions][<?php echo $count_question; ?>][question]"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Question image :
                                    </td>
                                    <td>
                                        <img width="100" height="100" src="<?php echo S3_IMAGE."cache/no_image-100x100.jpg"; ?>" alt="" id="quiz_questions_<?php echo $count_question; ?>_thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                                        <input type="hidden" name="quiz[questions][<?php echo $count_question; ?>][question_image]" value="<?php echo 'no_image.jpg'; ?>" id="quiz_questions_<?php echo $count_question; ?>_image" />
                                        <br />
                                        <a onclick="image_upload('quiz_questions_<?php echo $count_question; ?>_image', 'quiz_questions_<?php echo $count_question; ?>_thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                        <a onclick="$('#quiz_questions_<?php echo $count_question; ?>_thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#quiz_questions_<?php echo $count_question; ?>_image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="span6">
                            <div class="box box-add-item completion-wrapper">
                                <div class="box-header overflow-visible">
                                    <a href="javascript:void(0)" class="btn btn-primary right add-option-btn dropdown-toggle" data-question-id="<?php echo $count_question; ?>" data-toggle="dropdown"> Add option</a>
                                </div>
                                <div class="option-wrapper">

                                    <div class="box-content clearfix">
                                        <div class="option-container">
                                            option  <input type="text" name="quiz[questions][<?php echo $count_question; ?>][options][<?php echo $count_option; ?>][option]" value = ""><br>
                                            image  <img width="100" height="100" src="<?php echo S3_IMAGE."cache/no_image-100x100.jpg"; ?>" alt="" id="quiz_questions_<?php echo $count_question; ?>_options_<?php echo $count_option; ?>_thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                                            <input type="hidden" name="quiz[questions][<?php echo $count_question; ?>][options][<?php echo $count_option; ?>][option_image]" value="<?php echo 'no_image.jpg'; ?>" id="quiz_questions_<?php echo $count_question; ?>_options_<?php echo $count_option; ?>_image" />
                                            <br />
                                            <a onclick="image_upload('quiz_questions_<?php echo $count_question; ?>_options_<?php echo $count_option; ?>_image', 'quiz_questions_<?php echo $count_question; ?>_options_<?php echo $count_option; ?>_thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                            <a onclick="$('#quiz_questions_<?php echo $count_question; ?>_options_<?php echo $count_option; ?>_thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#quiz_questions_<?php echo $count_question; ?>_options_<?php echo $count_option; ?>_image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a><br>
                                            explanation  <textarea name="quiz[questions][<?php echo $count_question; ?>][options][<?php echo $count_option; ?>][explanation]"></textarea><br>
                                            score  <input type="text" name="quiz[questions][<?php echo $count_question; ?>][options][<?php echo $count_option; ?>][score]" value = "">
                                            <a href="javascript:void(0)" class="btn btn-danger right remove-option-btn dropdown-toggle" data-toggle="dropdown">Delete </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <a href="javascript:void(0)" class="btn btn-danger right remove-question-btn dropdown-toggle" data-toggle="dropdown">Delete </a>

                    </div>
                    <?php
                    }
                    ?>
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

    $(document).ready(function(){
        $(".exp").hide();
        $(".point").hide();
        $(".badges").hide();
        $(".rewards").hide();
        $("#exp-entry").live('click', function() {$(this).parent().find(".exp").toggle()});
        $("#point-entry").live('click', function() {$(this).parent().find(".point").toggle()});
        $("#badge-entry").live('click', function() {$(this).parent().find(".badges").toggle()});
        $("#reward-entry").live('click', function() {$(this).parent().find(".rewards").toggle()});

        $('#tabs a').tabs();

        init_sub_remove_event('.remove-grade-btn', 2);
        init_sub_remove_event('.remove-option-btn', 2);
        init_sub_remove_event('.remove-question-btn', 1);
    });

    var countGrades = 0;
    var countQuestions = 0;
    var countOptions = 0;

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

    function init_sub_remove_event(obj_click, num_of_parent){
        $(obj_click).unbind().bind('click',function(data){

            var $target = $(this);

            for ( var i = 0; i < num_of_parent; i++ ) {
                $target = $target.parent();
            }

            var r = confirm("Are you sure to remove!");
            if (r == true) {
                $target.remove();
            }
        });
    }

    $('.add-grade-btn').click(function(){
        countGrades = mongoIDjs();

        var gradeHtml = '<div class="box-content clearfix">\
            <div class="span6">\
            <a href="javascript:void(0)" class="btn btn-danger right remove-grade-btn dropdown-toggle" data-toggle="dropdown">Delete </a>\
        <table class="form">\
            <tbody>\
            <tr>\
            <td colspan="2">\
            If user\'s final grade is between <input type="text" name="quiz[grades]['+countGrades+'][start]" value = "" class="grades-range" />% and <input type="text" name="quiz[grades]['+countGrades+'][end]" value = "" class="grades-range" />%,\
        </td>\
        </tr>\
            <tr>\
        <td colspan="2">\
            give letter grade  <input type="text" name="quiz[grades]['+countGrades+'][grade]" value = ""> and assign the rank : <input type="text" name="quiz[grades]['+countGrades+'][rank]" value = "">\
            </td>\
        </tr>\
            <tr>\
            <td>\
        <?php echo $this->lang->line('rank_image'); ?> :\
        </td>\
            <td>\
            <img width="100" height="100" src="<?php echo S3_IMAGE."cache/no_image-100x100.jpg"; ?>" alt="" id="quiz_grades_'+countGrades+'_thumb" onerror="$(this).attr(\'src\',\'<?php echo base_url();?>image/default-image.png\');" />\
            <input type="hidden" name="quiz[grades]['+countGrades+'][rank_image]" value="<?php echo 'no_image.jpg'; ?>" id="quiz_grades_'+countGrades+'_image" />\
            <br />\
            <a onclick="image_upload(\'quiz_grades_'+countGrades+'_image\', \'quiz_grades_'+countGrades+'_thumb\');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;\
        <a onclick="$(\'quiz_grades_'+countGrades+'_thumb\').attr(\'src\', \'<?php echo $this->lang->line('no_image'); ?>\'); $(\'quiz_grades_'+countGrades+'_image\').attr(\'value\', \'\');"><?php echo $this->lang->line('text_clear'); ?></a>\
        </td>\
        </tr>\
        </tbody>\
        </table>\
        </div>\
        <div class="span6">\
            <table class="form">\
            <tbody>\
            <tr>\
            <td>\
        <?php echo $this->lang->line('entry_rewards'); ?> :\
        </td>\
            <td>\
        <div class="reward">\
            <button id="point-entry" type="button" class="btn btn-info btn-large btn-block"><?php echo $this->lang->line('entry_point'); ?></button>\
        <div class="point hide">\
            <div class="reward-panel">\
            <span class="label label-primary"><?php echo $this->lang->line('entry_point'); ?></span>\
        <input type="text" name="quiz[grades]['+countGrades+'][rewards][point][point_value]" size="100" class="orange tooltips" value="" />\
            </div>\
        </div>\
        <div id="badge-panel">\
            <?php
            if($badge_list){
            ?>
            <br>\
            <button id="badge-entry" type="button" class="btn btn-primary btn-large btn-block"><?php echo $this->lang->line('entry_badge'); ?></button>\
        <div class="badges hide">\
            <div class="reward-panel">\
            <?php
            foreach($badge_list as $badge){
            ?>
            <img height="50" width="50" src="<?php echo S3_IMAGE.$badge['image']; ?>" onerror="$(this).attr(\'src\',\'<?php echo base_url();?>image/default-image.png\');" />\
            <input type="text" name="quiz[grades]['+countGrades+'][rewards][badge][<?php echo $badge['badge_id']; ?>]" class="<?php echo alternator('green','yellow','blue');?> tooltips" size="100" value="" /><br/>\
            <?php
            }
            ?>
            </div>\
        </div>\
        <?php
        }
        ?>
        </div>\
        <div id="reward-panel">\
            <?php
            if($point_list){
            ?>
            <br>\
            <button id="reward-entry" type="button" class="btn btn-warning btn-large btn-block"><?php echo $this->lang->line('entry_custom_point'); ?></button>\
        <div class="rewards hide">\
            <div class="reward-panel">\
            <?php
            foreach($point_list as $point){
            ?>
            <?php echo $point['name']; ?>\
            <input type="text" name="quiz[grades]['+countGrades+'][rewards][custom][<?php echo $point['reward_id']; ?>]" class="<?php echo alternator('green','yellow','blue');?>" size="100" value="" /><br/>\
            <?php
            }
            ?>
            </div>\
        </div>\
        <?php
        }
        ?>
        </div>\
        </div>\
        </td>\
        </tr>\
        </tbody>\
        </table>\
        </div>\
        </div>';

        $('.grade-wrapper').append(gradeHtml);

        init_sub_remove_event('.remove-grade-btn', 2);
    });

    init_option_event();

    function init_option_event(){

        $('.add-option-btn').unbind().bind('click',function(){

            var currentQuestion = $(this).attr("data-question-id");
            countOptions = mongoIDjs();

            var optionHtml = '<div class="box-content clearfix">\
            <div class="option-container">\
            option  <input type="text" name="quiz[questions]['+currentQuestion+'][options]['+countOptions+'][option]" value = ""><br>\
            image  <img width="100" height="100" src="<?php echo S3_IMAGE."cache/no_image-100x100.jpg"; ?>" alt="" id="quiz_questions_'+currentQuestion+'_options_'+countOptions+'_thumb" onerror="$(this).attr(\'src\',\'<?php echo base_url();?>image/default-image.png\');" />\
            <input type="hidden" name="quiz[questions]['+currentQuestion+'][options]['+countOptions+'][option_image]" value="<?php echo 'no_image.jpg'; ?>" id="quiz_questions_'+currentQuestion+'_options_'+countOptions+'_image" />\
            <br />\
            <a onclick="image_upload(\'quiz_questions_'+currentQuestion+'_options_'+countOptions+'_image\', \'quiz_questions_'+currentQuestion+'_options_'+countOptions+'_thumb\');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;\
            <a onclick="$(\'#quiz_questions_'+currentQuestion+'_options_'+countOptions+'_thumb\').attr(\'src\', \'<?php echo $this->lang->line('no_image'); ?>\'); $(\'#quiz_questions_'+currentQuestion+'_options_'+countOptions+'_image\').attr(\'value\', \'\');"><?php echo $this->lang->line('text_clear'); ?></a><br>\
            explanation  <textarea name="quiz[questions]['+currentQuestion+'][options]['+countOptions+'][explanation]"></textarea><br>\
            score  <input type="text" name="quiz[questions]['+currentQuestion+'][options]['+countOptions+'][score]" value = "">\
            <a href="javascript:void(0)" class="btn btn-danger right remove-option-btn dropdown-toggle" data-toggle="dropdown">Delete </a>\
            </div>\
            </div>';

            $(this).parent().parent().find('.option-wrapper').append(optionHtml);

            init_sub_remove_event('.remove-option-btn', 2);
        });
    }

    $('.add-question-btn').click(function(){
        countQuestions = mongoIDjs();
        countOptions = mongoIDjs();

        var questionHtml = '<div class="box-content clearfix">\
            <div class="span6">\
            <table class="form">\
            <tbody>\
            <tr>\
            <td>\
            Question :\
        </td>\
            <td>\
        <textarea rows="3" name="quiz[questions]['+countQuestions+'][question]"></textarea>\
            </td>\
        </tr>\
            <tr>\
            <td>\
        Question image :\
            </td>\
            <td>\
        <img width="100" height="100" src="<?php echo S3_IMAGE."cache/no_image-100x100.jpg"; ?>" alt="" id="quiz_questions_'+countQuestions+'_thumb" onerror="$(this).attr(\'src\',\'<?php echo base_url();?>image/default-image.png\');" />\
            <input type="hidden" name="quiz[questions]['+countQuestions+'][question_image]" value="<?php echo 'no_image.jpg'; ?>" id="quiz_questions_'+countQuestions+'_image" />\
            <br />\
            <a onclick="image_upload(\'quiz_questions_'+countQuestions+'_image\', \'quiz_questions_'+countQuestions+'_thumb\');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;\
        <a onclick="$(\'#quiz_questions_'+countQuestions+'_thumb\').attr(\'src\', \'<?php echo $this->lang->line('no_image'); ?>\'); $(\'#quiz_questions_'+countQuestions+'_image\').attr(\'value\', \'\');"><?php echo $this->lang->line('text_clear'); ?></a>\
        </td>\
        </tr>\
        </tbody>\
        </table>\
        </div>\
        <div class="span6">\
            <div class="box box-add-item completion-wrapper">\
            <div class="box-header overflow-visible">\
            <a href="javascript:void(0)" class="btn btn-primary right add-option-btn dropdown-toggle" data-question-id="'+countQuestions+'" data-toggle="dropdown"> Add option</a>\
        </div>\
        <div class="option-wrapper">\
            <div class="box-content clearfix">\
            <div class="option-container">\
            option  <input type="text" name="quiz[questions]['+countQuestions+'][options]['+countOptions+'][option]" value = ""><br>\
            image  <img width="100" height="100" src="<?php echo S3_IMAGE."cache/no_image-100x100.jpg"; ?>" alt="" id="quiz_questions_'+countQuestions+'_options_'+countOptions+'_thumb" onerror="$(this).attr(\'src\',\'<?php echo base_url();?>image/default-image.png\');" />\
            <input type="hidden" name="quiz[questions]['+countQuestions+'][options]['+countOptions+'][option_image]" value="<?php echo 'no_image.jpg'; ?>" id="quiz_questions_'+countQuestions+'_options_'+countOptions+'_image" />\
            <br />\
            <a onclick="image_upload(\'quiz_questions_'+countQuestions+'_options_'+countOptions+'_image\', \'quiz_questions_'+countQuestions+'_options_'+countOptions+'_thumb\');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;\
        <a onclick="$(\'#quiz_questions_'+countQuestions+'_options_'+countOptions+'_thumb\').attr(\'src\', \'<?php echo $this->lang->line('no_image'); ?>\'); $(\'#quiz_questions_'+countQuestions+'_options_'+countOptions+'_image\').attr(\'value\', \'\');"><?php echo $this->lang->line('text_clear'); ?></a><br>\
            explanation  <textarea name="quiz[questions]['+countQuestions+'][options]['+countOptions+'][explanation]"></textarea><br>\
            score  <input type="text" name="quiz[questions]['+countQuestions+'][options]['+countOptions+'][score]" value = "">\
            <a href="javascript:void(0)" class="btn btn-danger right remove-option-btn dropdown-toggle" data-toggle="dropdown">Delete </a>\
        </div>\
        </div>\
        </div>\
        </div>\
        </div>\
            <a href="javascript:void(0)" class="btn btn-danger right remove-question-btn dropdown-toggle" data-toggle="dropdown">Delete </a>\
        </div>';

        $('.question-wrapper').append(questionHtml);

        init_option_event();
        init_sub_remove_event('.remove-option-btn', 2);
        init_sub_remove_event('.remove-question-btn', 1);
    })

</script>
<script type="text/javascript">
    $(function(){

        $('.date').datepicker({dateFormat: 'yy-mm-dd'});

    })
</script>