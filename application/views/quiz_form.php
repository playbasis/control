<?php
function find_template($data, $type, $template_id) {
    if (isset($data['feedbacks']) && array_key_exists($type, $data['feedbacks'])) foreach ($data['feedbacks'][$type] as $_template_id => $val) {
        if ($_template_id == $template_id) {
            return $val;
        }
    }
    return false;
}
?>
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
                <a href="#tab-quiz-data"><?php echo $this->lang->line('tab_data'); ?></a>
                <a href="#tab-quiz-grade"><?php echo $this->lang->line('tab_grade'); ?></a>
            </div>

            <?php if(validation_errors() || isset($message)){?>
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
                            <td><?php echo $this->lang->line('entry_type'); ?>:</td>
                            <td>
                                <input type="radio" name="type" value="quiz" <?php echo isset($quiz['type']) && $quiz['type'] == 'quiz' ? 'checked' : ''; ?> /> Quiz
                                <input type="radio" name="type" value="poll" <?php echo isset($quiz['type']) && $quiz['type'] == 'poll' ? 'checked' : ''; ?> /> Poll
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
            
            <div id="tab-quiz-data">
                <div class="question-head-wrapper">
                    <a href="javascript:void(0)" class="btn  open-question-btn btn-lg">Open All</a>
                    <a href="javascript:void(0)" class="btn close-question-btn btn-lg">Close All</a>
                    <a href="javascript:void(0)" class="btn btn-primary add-question-btn btn-lg">Add Question</a>
                </div>
                <div class="question-wrapper" >
                    <?php
                    if(isset($quiz['questions']) && $quiz['questions']){
                        foreach($quiz['questions'] as $questions){

                            $questions['question_id'] = $questions['question_id']."";
                    ?>
                        <div class="question-item-wrapper" data-question-id="<?php echo $questions['question_id']; ?>">
                            <div class="box-header box-question-header overflow-visible">
                                <h2><img src="<?php echo base_url();?>image/default-image.png" width="50"><?php echo $questions['question']; ?></h2>
                                <div class="box-icon">
                                    <a href="javascript:void(0)" class="btn btn-danger right remove-question-btn dropdown-toggle" data-toggle="dropdown">Delete </a>
                                    <span class="break"></span>
                                    <a href="javaScript:void()" ><i class="icon-chevron-up"></i></a>
                                </div>
                        </div>
                        <div class="box-content clearfix" style="display: none;">
                            <div class="span12">
                                <table class="form">
                                    <tbody>
                                    <tr>
                                        <td>
                                            Question : 
                                        </td>
                                        <td>
                                            <input type="text" class="question-input" name="quiz[questions][<?php echo $questions['question_id']; ?>][question]" value="<?php echo $questions['question']; ?>" />
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
                            <div class="span11">
                                <div class="box box-add-item option-box-wrapper">
                                    <div class="box-header overflow-visible">
                                        <h2><i class="icon-question-sign"></i><span class="break"></span>Choice</h2>
                                        <div class="box-icon box-icon-action"> 
                                            <a href="javascript:void(0)" class="btn btn-primary right add-option-btn dropdown-toggle" data-question-id="<?php echo $questions['question_id']; ?>"> Add option</a>
                                        </div>
                                    </div>
                                    <div class="option-wrapper">

                                        <div class="box-content clearfix">
                                            <?php
                                            if(isset($questions['options'])){
                                                foreach($questions['options'] as $option){

                                                    $option['option_id'] = $option['option_id']."";
                                            ?>
                                                    <div class="option-container well clearfix">
                                                    <div class="span7">
                                                            <table class="form">
                                                                <tbody>
                                                                <tr>
                                                                    <td>
                                                                        Option
                                                                    </td>
                                                                     <td>
                                                                        <input type="text" name="quiz[questions][<?php echo $questions['question_id']; ?>][options][<?php echo $option['option_id']; ?>][option]" value = "<?php echo $option["option"]; ?>">
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                   <td>Score</td>
                                                                   <td><input type="text" name="quiz[questions][<?php echo $questions['question_id']; ?>][options][<?php echo $option['option_id']; ?>][score]" value = "<?php echo $option["score"]; ?>"></td>
                                                                </tr>
                                                                <tr>
                                                                   <td>Explanation</td>
                                                                   <td>
                                                                        <textarea name="quiz[questions][<?php echo $questions['question_id']; ?>][options][<?php echo $option['option_id']; ?>][explanation]"><?php echo $option["explanation"]; ?></textarea></td>
                                                                </tr>
                                                                </tbody>
                                                            </table>

                                                        </div>
                                                        <div class="span1">
                                                        </div>
                                                        <div class="span4">
                                                        Image<br><br><img width="100" height="100" src="<?php echo isset($option["option_image"])? S3_IMAGE.$option["option_image"] : S3_IMAGE."cache/no_image-100x100.jpg" ; ?>" alt="" id="quiz_questions_<?php echo $questions['question_id']; ?>_options_<?php echo $option['option_id']; ?>_thumb" onerror="$(this).attr('src','<?php echo base_url();?>image/default-image.png');" />
                                                        <input type="hidden" name="quiz[questions][<?php echo $questions['question_id']; ?>][options][<?php echo $option['option_id']; ?>][option_image]" value="<?php echo isset($option["option_image"])? $option["option_image"] : "no_image.jpg" ; ?>" id="quiz_questions_<?php echo $questions['question_id']; ?>_options_<?php echo $option['option_id']; ?>_image" />
                                                        <br />
                                                        <a onclick="image_upload('quiz_questions_<?php echo $questions['question_id']; ?>_options_<?php echo $option['option_id']; ?>_image', 'quiz_questions_<?php echo $questions['question_id']; ?>_options_<?php echo $option['option_id']; ?>_thumb');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                                        
                                                        <a onclick="$('#quiz_questions_<?php echo $questions['question_id']; ?>_options_<?php echo $option['option_id']; ?>_thumb').attr('src', '<?php echo $this->lang->line('no_image'); ?>'); $('#quiz_questions_<?php echo $questions['question_id']; ?>_options_<?php echo $option['option_id']; ?>_image').attr('value', '');"><?php echo $this->lang->line('text_clear'); ?></a>

                                                            <br>
                                                            <br>

                                                            <a href="javascript:void(0)" class="btn btn-danger right remove-option-btn dropdown-toggle" data-toggle="dropdown">Delete Option</a>
                                                        </div>
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
                                                </div>
                                            <?php
                                            }
                                            ?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    <?php
                        }
                    }else{

                        //================= [ Empty Question ] ===================//
                    ?>
                    <!--
                    <?php
                        $count_question = new MongoId();
                        $count_question = $count_question."";

                        $count_option = new MongoId();
                        $count_option = $count_option."";
                    ?>
                    <a href="javascript:void(0)" class="btn btn-danger right remove-question-btn dropdown-toggle" data-toggle="dropdown">Delete </a>
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

                        

                    </div>
                     -->
                    <?php
                    }
                    ?>

                </div>
            </div>

            <div id="tab-quiz-grade">
                <div class="grade-head-wrapper">
                    <a href="javascript:void(0)" class="btn  open-grade-btn btn-lg">Open All</a>
                    <a href="javascript:void(0)" class="btn close-grade-btn btn-lg">Close All</a>
                    <a href="javascript:void(0)" class="btn btn-primary add-grade-btn btn-lg">Add Grade</a>
                </div>

                <div class="grade-wrapper">
                    <?php
                    if(isset($quiz['grades']) && $quiz['grades']){
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
                        <div class="grade-item-wrapper" data-grade-id="<?php echo $grade['grade_id']; ?>">
                            <div class="box-header box-grade-header overflow-visible">
                                    <h2><img src="<?php echo base_url();?>image/default-image.png" width="50"> Grade between <?php echo $grade['start']; ?>% - <?php echo $grade['end']; ?>%</h2>
                                    <div class="box-icon">
                                        <a href="javascript:void(0)" class="btn btn-danger right remove-grade-btn" >Delete </a>
                                        <span class="break"></span>
                                        <a href="javaScript:void()" ><i class="icon-chevron-up"></i></a>
                                    </div>
                            </div>
                            <div class="box-content clearfix" style="display: none;">
                                <div class="span6">
                                    <table class="form">
                                        <tbody>
                                        <tr>
                                            <td>
                                                If user's final grade is between
                                            </td>
                                            <td>
                                                Start : <input type="text" name="quiz[grades][<?php echo $grade['grade_id']; ?>][start]" value = "<?php echo $grade['start']; ?>" class="grades-range" />% 
                                                <br>
                                                End : <input type="text" name="quiz[grades][<?php echo $grade['grade_id']; ?>][end]"  value = "<?php echo $grade['end']; ?>" class="grades-range" />%,
                                            </td>
                                        </tr>
                                        <tr>
                                            <td >
                                                Give grade :
                                            </td>
                                            <td>
                                                <input type="text" name="quiz[grades][<?php echo $grade['grade_id']; ?>][grade]" value = "<?php echo $grade['grade']; ?>"> <small>* ex. A, B+, B</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Rank title : 
                                            </td>
                                            <td><input type="text" name="quiz[grades][<?php echo $grade['grade_id']; ?>][rank]" value = "<?php echo $grade['rank']; ?>"> <small>* ex. Super good, normal</small></td>
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
                                                <p class="text-center">
                                                    <?php echo $this->lang->line('entry_rewards'); ?> 
                                                </p>
                                                
                                                <div class="reward">

                                                    <button id="exp-entry" type="button" class="btn btn-info btn-large btn-block"><?php echo $this->lang->line('entry_exp'); ?></button>
                                                    <div class="exp">
                                                        <div class="reward-panel">
                                                            <span class="label label-primary"><?php echo $this->lang->line('entry_exp'); ?></span>
                                                            <input type="text" name="quiz[grades][<?php echo $grade['grade_id']; ?>][rewards][exp][exp_value]" size="100" class="orange tooltips" value="<?php echo $exp_user_set? $exp_user_set["exp_value"] : ''; ?>" />
                                                        </div>
                                                    </div>

                                                    <br>

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
                                                    <div id="email-panel">
                                                        <?php
                                                        if($emails){
                                                            ?>
                                                            <br>
                                                            <button id="email-entry" type="button" class="btn btn-success btn-large btn-block"><?php echo $this->lang->line('entry_email'); ?></button>
                                                            <div class="emails">
                                                                <div class="reward-panel">
                                                                    <?php
                                                                    foreach($emails as $email){
                                                                        ?>

                                                                        <?php
                                                                        $user_b = "";
                                                                        foreach($badge_user_set as $b){
                                                                            if($b["badge_id"] == $badge['badge_id']){
                                                                                $user_b = $b["badge_value"];
                                                                                break;
                                                                            }
                                                                        }
                                                                        ?>
                                                                        <div class="each-email-template">
                                                                        <label>
                                                                            <?php $template = find_template($grade, 'email', $email['_id']); ?>
                                                                            <?php $checked = $template && isset($template['checked']) && $template['checked']; ?>
                                                                            <h3><input type="checkbox" name="quiz[grades][<?php echo $grade['grade_id']; ?>][feedbacks][email][<?php echo $email['_id']; ?>][checked]" <?php echo $checked ? 'checked' : ''; ?>> <?php echo $email['name']; ?></h3>
                                                                        </label>
                                                                             <span class="label label-primary"><?php echo $this->lang->line('entry_subject'); ?></span>
                                                                              <input type="text" name="quiz[grades][<?php echo $grade['grade_id']; ?>][feedbacks][email][<?php echo $email['_id']; ?>][subject]" class="tooltips" size="100" value="<?php echo $checked && isset($template['subject']) ? $template['subject'] : ''; ?>" /><br/>
                                                                        </div>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        <?php
                                                        }
                                                        ?>
                                                    </div>

                                                    <div id="sms-panel">
                                                        <?php
                                                        if($smses){
                                                            ?>
                                                            <br>
                                                            <button id="sms-entry" type="button" class="btn btn-success btn-large btn-block"><?php echo $this->lang->line('entry_sms'); ?></button>
                                                            <div class="smses">
                                                                <div class="reward-panel">
                                                                    <?php
                                                                    foreach($smses as $sms){
                                                                        ?>

                                                                        <?php
                                                                        $user_b = "";
                                                                        foreach($badge_user_set as $b){
                                                                            if($b["badge_id"] == $badge['badge_id']){
                                                                                $user_b = $b["badge_value"];
                                                                                break;
                                                                            }
                                                                        }
                                                                        ?>
                                                                        <div class="each-sms-template">
                                                                        <label>
                                                                            <?php $template = find_template($grade, 'sms', $sms['_id']); ?>
                                                                            <?php $checked = $template && isset($template['checked']) && $template['checked']; ?>
                                                                            <h3><input type="checkbox" name="quiz[grades][<?php echo $grade['grade_id']; ?>][feedbacks][sms][<?php echo $sms['_id']; ?>][checked]" <?php echo $checked ? 'checked' : ''; ?>> <?php echo $sms['name']; ?></h3>
                                                                          </label>
                                                                        </div>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        <?php
                                                        }
                                                        ?>
                                                    </div>
                                                    <div id="push-panel">
                                                        <?php
                                                        if($pushes){
                                                            ?>
                                                            <br>
                                                            <button id="push-entry" type="button" class="btn btn-success btn-large btn-block"><?php echo $this->lang->line('entry_push'); ?></button>
                                                            <div class="pushes">
                                                                <div class="reward-panel">
                                                                    <?php
                                                                    foreach($pushes as $push){
                                                                        ?>

                                                                        <?php
                                                                        $user_b = "";
                                                                        foreach($badge_user_set as $b){
                                                                            if($b["badge_id"] == $badge['badge_id']){
                                                                                $user_b = $b["badge_value"];
                                                                                break;
                                                                            }
                                                                        }
                                                                        ?>
                                                                        <div class="each-push-template">
                                                                            <label>
                                                                                <?php $template = find_template($grade, 'push', $push['_id']); ?>
                                                                                <?php $checked = $template && isset($template['checked']) && $template['checked']; ?>
                                                                                <h3><input type="checkbox" name="quiz[grades][<?php echo $grade['grade_id']; ?>][feedbacks][push][<?php echo $push['_id']; ?>][checked]" <?php echo $checked ? 'checked' : ''; ?>> <?php echo $push['name']; ?></h3>
                                                                            </label>
                                                                        </div>
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
                        </div>
                    <?php
                        }
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
        $(".emails").hide();
        $(".smses").hide();
        $(".pushes").hide();
        $(document)
            .on('click', "#exp-entry", function () {
                $(this).parent().find(".exp").toggle()
            })
            .on('click', "#point-entry", function () {
                $(this).parent().find(".point").toggle()
            })
            .on('click', "#badge-entry", function () {
                $(this).parent().find(".badges").toggle()
            })
            .on('click', "#reward-entry", function () {
                $(this).parent().find(".rewards").toggle()
            })
            .on('click', "#email-entry", function () {
                $(this).parent().find(".emails").toggle()
            })
            .on('click', "#sms-entry", function () {
                $(this).parent().find(".smses").toggle()
            })
            .on('click', "#push-entry", function () {
                $(this).parent().find(".pushes").toggle()
            });

        $('#tabs a').tabs();

        init_sub_remove_event('.remove-grade-btn', 2);
        init_sub_remove_event('.remove-option-btn', 2);
        
        init_question_event();
        init_grade_event();

        $('.open-question-btn').click(function(){
            $('.question-item-wrapper>.box-content').show();
        });
        $('.close-question-btn').click(function(){
            $('.question-item-wrapper>.box-content').hide();
        });

        $('.open-grade-btn').click(function(){
            $('.grade-item-wrapper>.box-content').show();
        });
        $('.close-grade-btn').click(function(){
            $('.grade-item-wrapper>.box-content').hide();
        });

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

        var gradeHtml = '<div class="grade-item-wrapper" data-grade-id="'+countGrades+'">\
        <div class="box-header box-grade-header overflow-visible">\
                <h2><img src="<?php echo base_url();?>image/default-image.png" width="50"> Grading</h2>\
                <div class="box-icon">\
                    <a href="javascript:void(0)" class="btn btn-danger right remove-grade-btn" >Delete </a>\
                    <span class="break"></span>\
                    <a href="javaScript:void()" ><i class="icon-chevron-up"></i></a>\
                </div>\
        </div>\
        <div class="box-content clearfix">\
            <div class="span6">\
        <table class="form">\
            <tbody>\
            <tr>\
                <td>\
                    If user\'s final grade is between\
                </td>\
            <td>\
            Start :  <input type="text" name="quiz[grades]['+countGrades+'][start]" value = "" class="grades-range" />%\
            <br>\
            End : <input type="text" name="quiz[grades]['+countGrades+'][end]" value = "" class="grades-range" />%\
        </td>\
        </tr>\
            <tr>\
                <td >\
                    Give grade :\
                </td>\
                <td>\
                    <input type="text" name="quiz[grades]['+countGrades+'][grade]" value = ""> <small>* ex. A, B+, B</small>\
                </td>\
            </tr>\
            <tr>\
                <td >\
                    Rank title : \
                </td>\
                <td>\
                    <input type="text" name="quiz[grades]['+countGrades+'][rank]" value = ""> <small>* ex. Super good, normal</small>\
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
                <p class="text-center">\
                    <?php echo $this->lang->line('entry_rewards'); ?>\
                </p>\
            <div class="reward">\
            <button id="exp-entry" type="button" class="btn btn-info btn-large btn-block"><?php echo $this->lang->line('entry_exp'); ?></button>\
            <div class="exp" style="display: none;">\
                <div class="reward-panel">\
                    <span class="label label-primary"><?php echo $this->lang->line('entry_exp'); ?></span>\
                    <input type="text" name="quiz[grades]['+countGrades+'][rewards][exp][exp_value]" size="100" class="orange tooltips" value="" />\
                </div>\
            </div>\
            <br>\
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
        <div id="email-panel">\
            <?php
            if($emails){
                ?>
                <br>\
                <button id="email-entry" type="button" class="btn btn-success btn-large btn-block"><?php echo $this->lang->line('entry_email'); ?></button>\
                <div class="emails hide">\
                    <div class="reward-panel">\
                        <?php
                        foreach($emails as $email){
                            ?>
                            <div class="each-email-template">\
                            <label>\
                            <h3><input type="checkbox" name="quiz[grades]['+countGrades+'][feedbacks][email][<?php echo $email['_id']; ?>]" > <?php echo $email['name']; ?></h3>\
                            </label>\
                             <span class="label label-primary"><?php echo $this->lang->line('entry_subject'); ?></span>\
                              <input type="text" name="quiz[grades]['+countGrades+'][feedbacks][email][<?php echo $email['_id']; ?>][subject]" class="tooltips" size="100" value="" /><br/>\
                            </div>\
                        <?php
                        }
                        ?>
                    </div>\
                </div>\
            <?php
            }
            ?>
        </div>\
        <div id="smses-panel">\
            <?php
            if($smses){
                ?>
                <br>\
                <button id="sms-entry" type="button" class="btn btn-success btn-large btn-block"><?php echo $this->lang->line('entry_sms'); ?></button>\
                <div class="smses hide">\
                    <div class="reward-panel">\
                        <?php
                        foreach($smses as $sms){
                            ?>
                            <div class="each-sms-template">\
                            <label>\
                            <h3><input type="checkbox" name="quiz[grades]['+countGrades+'][feedbacks][sms][<?php echo $sms['_id']; ?>]" > <?php echo $sms['name']; ?></h3>\
                              </label>\
                            </div>\
                        <?php
                        }
                        ?>
                    </div>\
                </div>\
            <?php
            }
            ?>
        </div>\
        <div id="pushes-panel">\
            <?php
            if($pushes){
                ?>
                <br>\
                <button id="push-entry" type="button" class="btn btn-success btn-large btn-block"><?php echo $this->lang->line('entry_push'); ?></button>\
                <div class="pushes hide">\
                    <div class="reward-panel">\
                    <?php
                    foreach($pushes as $push){
                        ?>
                        <div class="each-push-template">\
                        <label>\
                        <h3><input type="checkbox" name="quiz[grades]['+countGrades+'][feedbacks][push][<?php echo $push['_id']; ?>]" > <?php echo $push['name']; ?></h3>\
                        </label>\
                        </div>\
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
        </div>\
        </div>';

        $('.grade-wrapper').append(gradeHtml);

        // init_sub_remove_event('.remove-grade-btn', 2);
        var element_position = $('.grade-item-wrapper[data-grade-id="'+countGrades+'"] ').offset();
        $("html, body").animate({scrollTop:(element_position.top-20)}, 600);

        init_grade_event();
    });

    init_option_event();

    function init_option_event(){

        $('.add-option-btn').unbind().bind('click',function(){

            var currentQuestion = $(this).attr("data-question-id");
            countOptions = mongoIDjs();
            
            var optionHtml = '<div class="option-container well clearfix">\
            <div class="span7">\
                    <table class="form">\
                        <tbody>\
                        <tr>\
                            <td>Option</td>\
                             <td>\
                             <input type="text" name="quiz[questions]['+currentQuestion+'][options]['+countOptions+'][option]" value = "">\
                                </td>\
                            </tr>\
                            <tr>\
                               <td>Score</td>\
                               <td>\
                                    <input type="text" name="quiz[questions]['+currentQuestion+'][options]['+countOptions+'][score]" value = "">\
                                </td>\
                            </tr>\
                            <tr>\
                               <td>Explanation</td>\
                               <td>\
                                    <textarea name="quiz[questions]['+currentQuestion+'][options]['+countOptions+'][explanation]"></textarea>\
                                </td>\
                            </tr>\
                            </tbody>\
                        </table>\
                        </div>\
                        <div class="span1">\
                        </div>\
                        <div class="span4">\
                        Image<br><br>\
                        <img width="100" height="100" src="<?php echo S3_IMAGE."cache/no_image-100x100.jpg"; ?>" alt="" id="quiz_questions_'+currentQuestion+'_options_'+countOptions+'_thumb" onerror="$(this).attr(\'src\',\'<?php echo base_url();?>image/default-image.png\');" />\
                                    <input type="hidden" name="quiz[questions]['+currentQuestion+'][options]['+countOptions+'][option_image]" value="<?php echo 'no_image.jpg'; ?>" id="quiz_questions_'+currentQuestion+'_options_'+countOptions+'_image" />\
                                    <br />\
                                    <a onclick="image_upload(\'quiz_questions_'+currentQuestion+'_options_'+countOptions+'_image\', \'quiz_questions_'+currentQuestion+'_options_'+countOptions+'_thumb\');"><?php echo $this->lang->line('text_browse'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;\
                                    <a onclick="$(\'#quiz_questions_'+currentQuestion+'_options_'+countOptions+'_thumb\').attr(\'src\', \'<?php echo $this->lang->line('no_image'); ?>\'); $(\'#quiz_questions_'+currentQuestion+'_options_'+countOptions+'_image\').attr(\'value\', \'\');"><?php echo $this->lang->line('text_clear'); ?></a><br><br>\
                                    <a href="javascript:void(0)" class="btn btn-danger right remove-option-btn dropdown-toggle" data-toggle="dropdown">Delete Option</a>\
                                </div>\
                        </div>';

            $(this).parent().parent().parent().find('.option-wrapper > .box-content').append(optionHtml);
            var element_position = $('.question-item-wrapper[data-question-id="'+currentQuestion+'"] .option-container:last-child').offset();

            $("html, body").animate({scrollTop:(element_position.top-20)}, 600);

            init_sub_remove_event('.remove-option-btn', 2);
        });
    }

    $('.add-question-btn').click(function(){
        countQuestions = mongoIDjs();
        countOptions = mongoIDjs();

        var questionHtml = '<div class="question-item-wrapper" data-question-id="'+countQuestions+'">\
                        <div class="box-header box-question-header overflow-visible">\
                            <h2><img src="<?php echo base_url();?>image/default-image.png" width="50"> Question Name</h2>\
                            <div class="box-icon">\
                                <a href="javascript:void(0)" class="btn btn-danger right remove-question-btn dropdown-toggle" data-toggle="dropdown">Delete </a>\
                                <span class="break"></span>\
                                <a href="javaScript:void()" ><i class="icon-chevron-up"></i></a>\
                            </div>\
                        </div>\
        <div class="box-content clearfix">\
            <div class="span12">\
                                <table class="form">\
                                    <tbody>\
                                    <tr>\
                                        <td>\
                                            Question : \
                                        </td>\
                                        <td>\
                                            <input type="text" class="question-input" name="quiz[questions]['+countQuestions+'][question]" value="" ></input>\
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
        <div class="span11">\
            <div class="box box-add-item option-box-wrapper">\
            <div class="box-header overflow-visible">\
                <h2><i class="icon-question-sign"></i><span class="break"></span>Choice</h2>\
                <div class="box-icon box-icon-action"> \
                    <a href="javascript:void(0)" class="btn btn-primary right add-option-btn dropdown-toggle" data-question-id="'+countQuestions+'"> Add option</a>\
                </div>\
            </div>\
        <div class="option-wrapper">\
            <div class="box-content clearfix">\
            </div>\
        </div>\
        </div>\
        </div>\
        </div>\
        </div>';

        $('.question-wrapper').append(questionHtml);

        var element_position = $('.question-item-wrapper[data-question-id="'+countQuestions+'"]').offset();
        $("html, body").animate({scrollTop:(element_position.top-20)}, 600);

        init_option_event();
        init_sub_remove_event('.remove-option-btn', 2);

        init_question_event();
    });
    
    function init_question_event(){
       
        $('.question-item-wrapper .box-question-header').unbind().bind('click',function(data){
            var $target = $(this).next('.box-content');

            if($target.is(':visible')) $('i',$(this)).removeClass('icon-chevron-up').addClass('icon-chevron-down');
            else                       $('i',$(this)).removeClass('icon-chevron-down').addClass('icon-chevron-up');
            $target.slideToggle();
        });

        $('.remove-question-btn').unbind().bind('click',function(data){
            var $target = $(this).parent().parent().parent();
            
            var r = confirm("Are you sure to remove!");
            if (r == true) {
                $target.remove();
                init_question_event()
            }
        });
    }

    function init_grade_event(){
       
        $('.grade-item-wrapper .box-grade-header').unbind().bind('click',function(data){
            var $target = $(this).next('.box-content');

            if($target.is(':visible')) $('i',$(this)).removeClass('icon-chevron-up').addClass('icon-chevron-down');
            else                       $('i',$(this)).removeClass('icon-chevron-down').addClass('icon-chevron-up');
            $target.slideToggle();
        });

        $('.remove-grade-btn').unbind().bind('click',function(data){
            var $target = $(this).parent().parent().parent();
            
            var r = confirm("Are you sure to remove!");
            if (r == true) {
                $target.remove();
                init_grade_event()
            }
        });
    }

    $(function(){

        $('.date').datepicker({dateFormat: 'yy-mm-dd'});

    })
</script>