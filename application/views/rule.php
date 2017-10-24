<!-- Start : Rule Editor -->
<link id="base-style" rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/rule_editor/rule_e.css" />
<link id="base-style" rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/rule_editor/jquery-ui-timepicker-addon.css" />
<!-- End : Rule Editor -->

<link id="base-style" rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/player/nivo-slider.css" />
<link rel="stylesheet" href="<?php echo base_url();?>stylesheet/player/nivo/themes/default/default.css" type="text/css" media="screen" />

<script type="text/javascript" src="<?php echo base_url();?>javascript/md5.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/mongoid.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/prettyprint.js"></script>

<!-- ############################################# -->
<!-- CONTENT -->
<!-- ############################################# -->

<!-- start : span10 -->
<div id="content" class="span10">

<div style='float:right; margin-top:-10px;margin-right:-30px'>
<!--span class="x-slider-frame value_current" style="margin: 0px; margin-bottom: -20px;">
  <span class="x-slider-button">Current</span>
</span-->

    <script type="text/javascript">
        $(document).ready(function(){
            $('.x-slider-frame').on('click',function(){

                var frame = $('.x-slider-frame');


                if( (frame.attr('class')).indexOf('current')>-1 ){
                    var obj = frame.find('.x-slider-button');
                    frame.removeClass('value_current')
                    frame.addClass('value_next')

                    obj.removeClass('on').html('Next');
                    frame.css('background','#BF0016');
                    $('.ob').hide('fast');
                    $('.od').show('fast', function() {
                        $('#rule-masonry').isotope('reLayout');
                    });

                }else{
                    var obj = frame.find('.x-slider-button');
                    frame.removeClass('value_next')
                    frame.addClass('value_current')

                    obj.addClass('on').html('Current');
                    frame.css('background','#3B9900');

                    $('.ob').show('fast');
                    $('.od').hide('fast');
                }
            })

        })
    </script>
    <style type="text/css">
        .x-slider-button{
            display: block;
            width: 46px;
            height: 20px;
            line-height: 20px;
            background: #edf2f7;
            -moz-border-radius: 2px;
            border-radius: 2px;
            -webkit-transition: all .25s ease-in-out;
            -moz-transition: all .25s ease-in-out;
            transition: all .25s ease-in-out;
            color: #000;
            font-family: sans-serif;
            font-size: 11px;
            font-weight: 700;
            text-align: center;
            cursor: pointer;
            -webkit-box-shadow: inset 0 0 2px 0 rgba(0,0,0,.25);
            -moz-box-shadow: inset 0 0 2px 0 rgba(0,0,0,.25);
            box-shadow: inset 0 0 2px 0 rgba(0,0,0,.25);
        }

        .x-slider-frame {
            position: relative;
            display: block;
            width: 54px;
            height: 20px;
            background-color: #3B9900;
            -moz-border-radius: 2px;
            border-radius: 2px;
            -webkit-box-shadow: inset 0 0 2px 0 rgba(0,0,0,.25);
            -moz-box-shadow: inset 0 0 2px 0 rgba(0,0,0,.25);
            box-shadow: inset 0 0 2px 0 rgba(0,0,0,.25);
            margin: 0 auto;
        }
    </style>




</div>

<div class="ob">
    <!-- start : sortable UI -->
    <div class="row-fluid sortable ui-sortable">

        <!-- start : rules-list container -->
        <div class="box span6 rulelist_container ">

            <div class="span12 action_panel_rules">
              <span class="dropdown sort_option_dropdown mini-round pull-left hide">
                <a class="dropdown-toggle" id="sort_option" role="button" data-toggle="dropdown" href="#">&nbsp;&nbsp;Sort by : Date &nbsp;<b class="caret"></b>&nbsp;</a>
                <ul id="menu2" class="dropdown-menu" role="menu" aria-labelledby="sort_option">
                    <li><a tabindex="-1" href="#">Sort by : Date</a></li>
                    <li><a tabindex="-2" href="#">Sort by : Name</a></li>
                </ul>
              </span>
              <span class="input-prepend pull-right search_filter">
                <span class="add-on">
                  <!-- search -> name and tag -> split/ separate by comma or white-space -->
                  <i class="icon-search"></i></span>
                  <input id="" class='rule_search_filter' size="16" type="text">
                </span>
            </div>


            <div class="box-header" data-original-title="">
                <h2><i class="icon-user"></i><span class="break"></span>Rules List</h2>
                <div class="box-icon">
                    <a href="javaScript:void()" class="btn-setting"><i class="icon-wrench"></i></a>
                    <a href="javaScript:void()" class="btn-minimize"><i class="icon-chevron-up"></i></a>
                    <!-- <a href="javaScript:void()" class="btn-close"><i class="icon-remove"></i></a> -->
                </div>
            </div>
            <div class="box-content">
                <table class="rulelist table table-bordered ruleslist_table">
                    <thead>
                    <tr>
                        <th class='tb_rule_th tb_rule_title'>Rule</th>
                        <th class='tb_rule_th tb_rule_action_name'>Action</th>
                        <th class='tb_rule_th tb_rule_date'>Create Date</th>
                        <th class='tb_rule_th tb_rule_status' >Status</th>
                        <th class='tb_rule_th tb_rule_action' ></th>
                        <th id="column_import_check" class='tb_hilight center hidden' ><input name="import_selected_head" type="checkbox" onclick="$('input[name*=\'import_selected\']').attr('checked', this.checked);" /></th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

                <div class="pagination pagination-centered">
                    <ul class='ul_rule_pagination_container'>
                        <li class='page_index_nav prev'><a href="">Prev</a></li>

                        <li class="active page_index_number"><a href="#">1</a></li>

                        <li  class='page_index_nav next'><a href="">Next</a></li>
                    </ul>
                </div>
                <!-- end : row fluid for pagination -->
            </div>
        </div>
        <!-- end : rules-list container -->

        <!-- start : jigsaw container -->
        <div class="row span6 rule_jigsaws_container" style='margin-top:20px;overflow:auto'>
            <div class="span12 action_panel_jigsaws">
                <button class='btn btn-primary  one_rule_new_btn right' style="width: 110px;"> + New Rule </button>
                <button class='btn btn-primary  export_rule_btn right' style="width: 110px;" ;> <i class="fa fa-cloud-upload"></i> Export </button>
                <button class='btn btn-primary  import_rule_btn right' style="width: 110px;"> <i class="fa fa-cloud-download"></i> Import </button>
                <!-- <div class="screen-width-768 "> -->
                <span class=' one_rule_actionbtn_holder hide pull-right'>
                    <?php if (!$isAdmin && sizeof($ruleTemplate) > 0): ?>
                    <!-- Start Rule Template -->
                    <div class="btn-group">
                        <button type="button"
                                class="btn btn-info dropdown-toggle"
                                data-toggle="dropdown">
                            <?php echo $this->lang->line("button_template"); ?>
                            <span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu">
                        <?php foreach($ruleTemplate as $key => $value): ?>
                        <li><a class="template_sel"
                               data-name="<?php echo $key; ?>"
                               data-id="<?php echo $value; ?>"
                               href="#"><?php echo $key; ?></a></li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                    <!-- End Rule Template -->
                    <?php endif; ?>
                    <button class='btn btn-success one_rule_save_btn'> Save </button>
                    <button class='btn btn-danger one_rule_discard_btn' style='margin-left:4px'> Discard </button>
                </span>

                <span class=' export_rule_actionbtn_holder hide pull-right'>
                    <button class='btn btn-success export_execute_btn'> Export </button>
                    <button class='btn btn-danger export_import_cancel_btn' > Cancel </button>
                </span>

                <span class=' import_rule_actionbtn_holder hide pull-right'>
                    <button class='btn btn-success import_execute_btn'> Import </button>
                    <button class='btn btn-danger export_import_cancel_btn' style='margin-left:4px'> Cancel </button>
                </span>
                <!-- </div> -->
            </div>

            <!-- import rule box -->
            <div class="pbd_rule_import hide">
                <div class="box pbd_rule_import_header" style='padding:32px 32px 24px 32px;margin-bottom:0px'>
                    <div class="box-header">
                        Import Rule
                    </div>
                    <div class="box-content">
                        <br>
                        &emsp;<span class="required">*</span><?php echo $this->lang->line('entry_file'); ?>&emsp;:&emsp;
                        <input id="file-import" type="file" size="100" />
                        <br>&emsp;
                    </div>
                </div>
            </div>

            <!-- add rule box -->
            <div class="pbd_one_rule_holder hide">
                <div class="box pbd_rule_header" style='padding:32px 32px 24px 32px;margin-bottom:0px'>
                    <div class="box-header">
                        <h2><i class="icon-play-circle"></i><span class="break"></span>
                            <span class="pbd_rulebox_name" id="rule_box_name">(String)</span>
                        </h2>
                        <div class="box-icon">
                            <a href="javaScript:void()" class="btn-minimize"><i class="icon-chevron-up"></i></a>
                        </div>
                    </div>
                    <div class="box-content" style=''>
                  <span class="pbd_box_content_head">
                    <table class="table table-bordered">
                        <tbody>

                        <tr class="pbd_rule_param content_head_title state_text">
                            <td class="pbd_rule_label ">Title</td>
                            <td class="pbd_rule_data">
                                <span class="pbd_rule_text" style="display: inline;">String</span>
                            <span class="pbd_rule_field" style="display: none;">
                              <input type="text" placeholder="text" value="String">
                            </span>

                            <span class="pbd_rule_action">
                              <span class="btn btn-info btn-mini" id="pbd_rule_action_edit" style=""><i class="icon-edit icon-white"></i></span>
                              <span class="btn btn-info btn-mini" id="pbd_rule_action_save" style="display: none;"><i class="icon-ok icon-white"></i></span>
                              <span class="btn btn-info btn-mini" id="pbd_rule_action_cancel" style="display: none;"><i class="icon-remove icon-white"></i></span>
                            </span>
                            </td>
                        </tr>

                        <tr class="pbd_rule_param content_head_description state_text">
                            <td class="pbd_rule_label ">Description</td>
                            <td class="pbd_rule_data">
                                <span class="pbd_rule_text" style="display: inline;">String</span>
                            <span class="pbd_rule_field" style="display: none;">
                              <input type="text" placeholder="text" value="String">
                            </span>

                            <span class="pbd_rule_action">
                              <span class="btn btn-info btn-mini" id="pbd_rule_action_edit" style=""><i class="icon-edit icon-white"></i></span>
                              <span class="btn btn-info btn-mini" id="pbd_rule_action_save" style="display: none;"><i class="icon-ok icon-white"></i></span>
                              <span class="btn btn-info btn-mini" id="pbd_rule_action_cancel" style="display: none;"><i class="icon-remove icon-white"></i></span>
                            </span>
                            </td>
                        </tr>

                        <tr class="pbd_rule_param content_head_status state_text">
                            <td class="pbd_rule_label ">Status</td>
                            <td class="pbd_rule_data">
                                <span class="pbd_rule_text" style="display: inline;">Enable</span>
                            <span class="pbd_rule_field " style="display: none;">
                              <!-- <input type="text" placeholder="text" value="String"> -->
                              <span class="slider-frame value_enable" style="margin: 0px; margin-bottom: -20px;">
                                <span class="slider-button">Enable</span>
                              </span>
                            </span>

                            <span class="pbd_rule_action">
                              <span class="btn btn-info status_btn btn-mini" id="pbd_rule_action_edit" style=""><i class="icon-edit icon-white"></i></span>
                              <span class="btn btn-info status_btn btn-mini" id="pbd_rule_action_save" style="display: none;"><i class="icon-ok icon-white"></i></span>
                              <span class="btn btn-info status_btn btn-mini" id="pbd_rule_action_cancel" style="display: none;"><i class="icon-remove icon-white"></i></span>
                            </span>
                            </td>
                        </tr>

                        <tr class="pbd_rule_param content_head_tags state_text">
                            <td class="pbd_rule_label ">Tags</td>
                            <td class="pbd_rule_data">
                                <span class="pbd_rule_text" style="display: inline;">String</span>
                            <span class="pbd_rule_field" style="display: none;">
                              <input type="text" placeholder="text" value="String">
                            </span>

                            <span class="pbd_rule_action">
                              <span class="btn btn-info btn-mini" id="pbd_rule_action_edit" style=""><i class="icon-edit icon-white"></i></span>
                              <span class="btn btn-info btn-mini" id="pbd_rule_action_save" style="display: none;"><i class="icon-ok icon-white"></i></span>
                              <span class="btn btn-info btn-mini" id="pbd_rule_action_cancel" style="display: none;"><i class="icon-remove icon-white"></i></span>
                            </span>
                            </td>
                        </tr>

                        </tbody>
                    </table>
                  </span>

                        <span class="pbd_boxcontent_condition hide"></span>
                        <span class="pbd_boxcontent_reward hide"></span>
                    </div>
                </div>




                <div class='pbd_initial_action_add hide'>
                    <span class='round init_action_btn'>Add Action</span>
                </div>

                <ul class="pbd_rule_unit_wrapper " >

                </ul>
            </div>

        </div>
        <!-- end : jigsaw container -->

    </div>
    <!-- End : sortable UI -->

</div>
<!-- end ob -->
<div class ="od hide">

    <?php #include 'new_rule_prototype.php';?>
</div>

</div>
<!-- end : span10 -->


<!-- start : prevent saving  -->
<div id="pbd_prevent_savnig_question" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close pbd_psq_no" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel" class='pbd_psq_title'>Message</h3>
    </div>
    <div class="modal-body pbd_psq_msg">
        <p>Message here</p>
    </div>
    <div class="modal-footer">
        <!-- <button class="btn pbd_psq_no" data-dismiss="modal">Cancel</button> -->
        <button class="btn pbd_psq_yes btn-primary" data-dismiss="modal">Ok</button>
    </div>
</div>
<!-- end : prevent saving  -->


<!-- start : general purpose question  -->
<div id="pbd_general_purpose_question" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close pbd_gpq_no" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel" class='pbd_gpq_title'>Message</h3>
    </div>
    <div class="modal-body pbd_gpq_msg">
        <p>Question here</p>
    </div>
    <div class="modal-footer">
        <button class="btn pbd_gpq_no" data-dismiss="modal">Cancel</button>
        <button class="btn pbd_gpq_yes btn-primary" data-dismiss="modal">Ok</button>
    </div>
</div>
<!-- end : general purpose question  -->

<!-- start : new rules options -->
<div id='pbd_create_newrule_options' class='pbd_create_newrule_options modal hide fade' >
    <div class="modal-header">
        <button type="button" class="close pbd_newrule_no" data-dismiss="modal" aria-hidden="true">×</button>
        <ul class='pull-right modal-body-view-switcher'>
            <li class='listview view_active'><button>List</button></li>
            <li class='gridview'><button>Grid</button></li>
        </ul>
        <h3 id="myModalLabel" class='pbd_newrule_title '>
            <button>
                <i class="icon-chevron-left"></i>
            </button>
            New Rules</h3>
    </div>

    <div class="modal-body pbd_newrule_body ">
        <ul class='newrule_item_holder' style='margin-left:12px'>
            <li class='newrule_mode_item' id='mode_import'><img src='./image/import_template.png' width='128' height='128'><br/><span>New Rule From Template</span></li>
            <li class='newrule_mode_item' id='mode_new'><img src='./image/add_newrule.png' width='128' height='128'><br/><span>New Rule From Scrath</span></li>
        </ul>
    </div>
    <div class="modal-footer">
        <button class="btn pbd_newrule_no" data-dismiss="modal">Cancel</button>

    </div>
</div>
<!-- end : new rules options -->

<!-- nivo slider -->
<div id='rule_guide' class="modal hide fade">
    <div class="modal-header">
        <h3>User Guide</h3>
    </div>
    <div class="modal-body">
        <div id="slider" class="nivoSlider" style='height:300px'>
            <img src="<?php echo base_url();?>image/walk_through/step1.png" data-thumb="<?php echo base_url();?>image/walk_through/step1.png" alt="" />
            <img src="<?php echo base_url();?>image/walk_through/step2.png" data-thumb="<?php echo base_url();?>image/walk_through/step2.png" alt=""/>
            <img src="<?php echo base_url();?>image/walk_through/step3.png" data-thumb="<?php echo base_url();?>image/walk_through/step3.png" alt=""/>
            <img src="<?php echo base_url();?>image/walk_through/step4.png" data-thumb="<?php echo base_url();?>image/walk_through/step4.png" alt="" />
        </div>
        <!-- <p>Show <u>walk through</u> images as slide-show for user here.</p> -->
    </div>
    <div class="modal-footer">
    <span class='pull-left'>
      <input id="never" type='checkbox' value='1' >
      <span  style='margin-top:14px;font-size:.8em'>Never show this again</span>
    </span>
        <a data-dismiss="modal" class="btn ok_user_guid btn-primary"
           onclick="javascript:if($('#never').is(':checked')) { $.cookies.set('rule_guide', true); }" >Ok</a>
    </div>
</div>
<style>

    .nivo-controlNav{display: none}
    .nivoSlider {
        position: relative;
        width: 100%;
        height: 300px;
        overflow: hidden;
    }
    .nivoSlider img {
        position:absolute;
        top:0px;
        left:0px;
        display:none;
    }
    .nivoSlider a {
        border:0;
        display:block;
    }
</style>
<!-- /nivo slider -->



<!-- TRY ADD CHZN PLUGIN - BUT NOT WORK  -->
<link id="base-style" rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/rule_editor/jquery.gritter.css" />
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/jquery.gritter.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/jquery-ui-sliderAccess.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/rulenode_tablerow_description.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-csv/0.71/jquery.csv-0.71.min.js"></script>
<!-- TRY ADD CHZN PLUGIN - BUT NOT WORK  -->


<!-- Start : Rule Editor -->
<script type="text/javascript">
    //########### Start : Global variable for rule_e.js ##################/
    var jsonConfig_siteId   = '<?php echo $jsonConfig_siteId;?>';
    var jsonConfig_clientId = '<?php echo $jsonConfig_clientId;?>';
    var jsonConfig_ActionId = undefined;

    var jsonString_Action    = <?php echo json_encode($actionList); ?>;
    var jsonString_Condition = <?php echo json_encode($conditionList); ?>;
    var jsonString_ConditionGroup = <?php echo json_encode($conditionGroupList); ?>;
    var jsonString_Reward    = <?php echo json_encode($rewardList); ?>;
    var jsonString_RewardSequence = <?php echo json_encode($rewardSequenceList); ?>;
    var jsonString_Feedback  = <?php echo json_encode($feedbackList); ?>;
    var jsonString_Group     = <?php echo json_encode($groupList); ?>;
    var jsonString_CustomReward    = <?php echo json_encode($customRewardFileList); ?>;
    var jsonString_Email     = <?php echo json_encode($emailList); ?>;
    var jsonString_Sms       = <?php echo json_encode($smsList); ?>;
    var jsonString_Push      = <?php echo json_encode($pushList); ?>;
    var jsonString_levelCondition = <?php echo json_encode($levelConditionList); ?>;
    var jsonString_Webhook   = <?php echo json_encode($webhookList); ?>;
    var jsonString_Game   = <?php echo json_encode($gameList); ?>;
    var jsonString_Location   = <?php echo json_encode($locationList); ?>;
    var jsonString_SequenceFile   = <?php echo json_encode($sequenceFile); ?>;
    var jsonString_CustomParamFile   = <?php echo json_encode($customParamFile); ?>;
    var jsonString_Point   = <?php echo json_encode($pointList); ?>;

    //var jsonString_RulesList= '<?php //print_r($ruleList);?>';
    var requestedSet         = '<?php echo $requestParams; ?>';
    var jsonConfig_icons     = <?php echo json_encode($jsonIcons); ?>;

    var csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrf_token_hash = '<?php echo $this->security->get_csrf_hash(); ?>';
    //########### End : Global variable for rule_e.js ##################/
</script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/msgpack.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/rule_e.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/rule_editor.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/rule_editor_data.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/rule_editor_onerule.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/rule_editor_table.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/rule_dataset.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/rule_group.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/rule_node.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/scripts.js"></script>


<!--  Table Sort : hot fix wait for refactor code -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/rule_editor/tablecloth.css" />
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/jquery.tablecloth.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/jquery.tablesorter.min.js"></script>
<!--  /Table Sort : hot fix wait for refactor code -->

<!--  FixMenu -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/rule_editor/fix-menu.css" />
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/fixMenu.js"></script>
<!--  /FixMenu-->

<!-- tabPage -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/rule_editor/tab-page.css" />
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/tabPage.js"></script>
<!-- /tabPage -->

<!--  slidePanel -->
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/slidePanel.js"></script>
<!--  /slidePanel-->

<!-- tabPage -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/rule_editor/get-rule-text.css" />
<script type="text/javascript" src="<?php echo base_url();?>javascript/rule_editor/getRuleText.js"></script>
<!-- /tabPage -->

<!-- End : Rule Editor -->

<!-- ############################################# -->
<!-- MODAL -->
<!-- ############################################# -->
<div class="modal hide fade pbd_rule_reward_collection_modal" id='reward_collection'>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Reward Collection modal</h3>
    </div>
    <div class="modal-body " style='padding:0px'>
        <div class="row" style="margin-left:0px" id="rule-reward-modal">

        </div>
    </div>
    <div class="modal-footer">
        <span href="" class="btn" data-dismiss="modal">Cancel</span>
        <span href="" class="btn btn-primary" id='badge_selection_btn' data-dismiss="modal">Select</span>
    </div>
</div>

<div class="modal hide fade pbd_rule_goods_collection_modal" id='goods_collection'>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Goods Collection modal</h3>
    </div>
    <div class="modal-body " style='padding:0px'>
        <div class="row" style="margin-left:0px" id="rule-goods-modal">

        </div>
    </div>
    <div class="modal-footer">
        <span href="" class="btn" data-dismiss="modal">Cancel</span>
        <span href="" class="btn btn-primary" id='goods_selection_btn' data-dismiss="modal">Select</span>
    </div>
</div>

<div class="modal hide fade simple_rule_interface">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Rule Export</h3>
    </div>
    <div class="modal-body">
        <textarea id='json_io' placeholder='JSON String here'></textarea>
        <div class="view"></div>
    </div>
    <div class="modal-footer">
        <span class="btn" data-dismiss="modal" >Close</span>
        <span class="btn btn-primary">Create Rule</span>
    </div>
</div>


<div class="modal hide fade rule_modal_action pbd_rule_editor_modal" id="newrule_modal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3 rel="popover" data-trigger="hover"data-placement="bottom" data-content="Select an action to trigger this rule." data-original-title="Action" >Choose Action</h3>
    </div>
    <div class="modal-body ">
        <div class="selection_wrapper" style="margin-left:4px"></div>
    </div>
    <div class="modal-footer">
        <a href="javaScript:void()" class="btn btn-primary pbd_modal_confirm_btn">OK</a>
    </div>
</div>
<!-- end : choose action modal -->


<!-- start : choose condition modal -->
<div class="modal hide fade rule_modal_condition pbd_rule_editor_modal" id="newrule_condition_modal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3 rel="popover" data-trigger="hover"data-placement="bottom" data-content="Select a condition to add this rule." data-original-title="Condition" >Choose Condition</h3>
    </div>
    <div class="modal-body">
        <div class="selection_wrapper" style="margin-left:4px"></div>
    </div>
    <div class="modal-footer">
        <a href="javaScript:void()" class="btn btn-primary pbd_modal_confirm_btn">OK</a>
    </div>
</div>
<!-- end : choose condition modal -->

<!-- start : choose condition group modal -->
<div class="modal hide fade rule_modal_condition pbd_rule_editor_modal" id="newrule_condition_group_modal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3 rel="popover" data-trigger="hover"data-placement="bottom" data-content="Select a condition group to add this rule." data-original-title="Condition" >Choose Condition Group</h3>
    </div>
    <div class="modal-body">
        <div class="selection_wrapper" style="margin-left:4px"></div>
    </div>
    <div class="modal-footer">
        <a href="javaScript:void()" class="btn btn-primary pbd_modal_confirm_btn">OK</a>
    </div>
</div>
<!-- end : choose condition modal -->

<!-- start : choose reward modal -->
<div class="modal hide fade rule_modal_condition pbd_rule_editor_modal" id="newrule_reward_modal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3 rel="popover" data-trigger="hover"data-placement="bottom" data-content="Select a reward to give this rule." data-original-title="Reward" >Choose Reward</h3>
    </div>

    <div class="modal-body ">
        <div class="selection_wrapper" style="margin-left:4px">
        </div>
    </div>

    <div class="modal-footer">
        <a href="javaScript:void()" class="btn btn-primary pbd_modal_confirm_btn">OK</a>
    </div>
</div>
<!-- end : choose reward modal -->

<!-- start : choose reward by custom param modal -->
<div class="modal hide fade rule_modal_condition pbd_rule_editor_modal" id="newrule_reward_custom_modal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3 rel="popover" data-trigger="hover"data-placement="bottom" data-content="Select a reward to give this rule." data-original-title="Reward" >Choose a Custom Control file</h3>
    </div>

    <div class="modal-body ">
        <div class="selection_wrapper" style="margin-left:4px">
        </div>
    </div>

    <div class="modal-footer">
        <a href="javaScript:void()" class="btn btn-primary pbd_modal_confirm_btn">OK</a>
    </div>
</div>
<!-- end : choose reward by custom param modal -->

<!-- start : choose reward group modal -->
<div class="modal hide fade rule_modal_condition pbd_rule_editor_modal" id="newrule_group_modal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3 rel="popover" data-trigger="hover"data-placement="bottom" data-content="Select a reward group type" data-original-title="Reward" >Choose Reward Group Type</h3>
    </div>

    <div class="modal-body ">
        <div class="selection_wrapper" style="margin-left:4px">
        </div>
    </div>

    <div class="modal-footer">
        <a href="javaScript:void()" class="btn btn-primary pbd_modal_confirm_btn">OK</a>
    </div>
</div>
<!-- end : choose reward group modal -->


<!-- Error Modal -->
<div id="errorModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Warning !</h3>
    </div>
    <div class="modal-body red">
        <p>One fine body…</p>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Close</button>
    </div>
</div>

<div id="playModal" title="Success Response">
</div>


<!-- /Error Modal -->

<!-- ############################################# -->
<!-- END MODAL -->
<!-- ############################################# -->

<!-- Navigation -->
<!-- <nav class="fixMenu radius-small">
    <ul>
        <li>
            <a href="#top" id="fixMenugoToTop" class="radius-small">GoToTop</a>
        </li>
        <li>
            <a href="#" id="fixMenuActionRule" class="radius-small collapse-fixmenu"></a>
        </li>
        <li style='display:none'>
            <a href="#" id="fixMenuSave" class="radius-small one_rule_save_btn"></a>
        </li>
        <li style='display:none'>
            <a href="#" id="fixMenuDelete" class="radius-small one_rule_discard_btn"></a>
        </li>
        <li>
            <a href="#down" id="fixMenugoToDown" class="radius-small">GoToDown</a>
        </li>
    </ul>
</nav> -->
