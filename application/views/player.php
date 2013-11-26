<!-- start content -->
<div id="content" class="span10 " >
<div class="row-fluid">
<!-- start box -->
<div class="box span12">
<div class="box-header">
    <h2><i class="icon-user"></i> Players</h2>
</div>
<!-- start box-content -->
<div class="box-content">
<ul class="nav tab-menu nav-tabs" id="player-tabs">
    <li class="active"><a href="#summary"><i class="icon-briefcase"></i> Summary</a></li>
    <li><a href="#crm"><i class="icon-th"></i> CRM</a></li>
</ul>

<!-- start tab-content -->
<div id="player-tab-content" class="tab-content">
<div class="tab-pane active" id="summary">







    <div class="box filter-collection">

        <div class="box-header">
            <h2><i class="icon-map-marker pull-left"></i>&nbsp;Inspect section<span class="break"></span></h2>


            <div class="pull-right resetFilter">
                <h2><span class="break"></span><i class="icon-remove "></i>&nbsp;Start Over</h2>
            </div>
        </div>

        <div class="box-content filter-collection-ul-container">
              <span class='pull-left '>
                <ul>
                    <!-- start : listItem -->
                    <li class="filter-pill more-round" title="4-16" data-criteria="level:4-16">
                        <!-- title mean range  --><!-- id mean type|specific_name|specific_id -->
                        <span class="">level:4-16</span>
                        <i class="removeFilter remove-filter-btn fa-icon-remove-sign"></i>
                    </li>
                    <!-- end : listItem -->

                    <!-- start : listItem -->
                    <li class="filter-pill more-round" title="m" data-criteria="gender:m">
                        <!-- title mean range  --><!-- id mean type|specific_name|specific_id -->
                        <span class="label_lable">gender</span>
                        <span>:</span>
                        <span class="label_range">male</span>
                        <i class="removeFilter remove-filter-btn fa-icon-remove-sign"></i>
                    </li>
                    <!-- end : listItem -->

                    <!-- start : listItem -->
                    <li class="filter-pill more-round" title="4-16" data-criteria="reward:coin:3">
                        <!-- title mean range  --><!-- id mean type|specific_name|specific_id -->
                        <span class="label_lable">reward</span>
                        <span>:</span>
                        <span class="label_range">100-300</span>
                        <i class="removeFilter remove-filter-btn fa-icon-remove-sign"></i>
                    </li>
                    <!-- end : listItem -->

                    <!-- start : listItem -->
                    <li class="filter-pill more-round" title="" data-criteria="action:like:9">
                        <!-- title mean range  --><!-- id mean type|specific_name|specific_id -->
                        <span class="label_lable">action</span>
                        <span>:</span>
                        <span class="label_range"></span>
                        <i class="removeFilter remove-filter-btn fa-icon-remove-sign"></i>
                    </li>
                    <!-- end : listItem -->

                </ul>
              </span>

        </div>

    </div>






    <div class="context-menu pull-right" style='position:relative; z-index:10000'>

        <div class="box-header">
            <h2><i class="icon-list"></i>Filter Select</h2>
            <div class="box-icon">
                <!-- <a href="#" class="btn-setting"><i class="icon-wrench"></i></a>
                <a href="#" class="btn-minimize"><i class="icon-chevron-up"></i></a> -->
                <a href="#" class="btn-close"><i class="icon-remove"></i></a>
            </div>
        </div>


        <div class="box-content"></div>
    </div>

    <!-- start : chart container -->
    <div id="player-summary"  class="player-data"></div>
    <!-- end : chart container -->


</div>

<div class="tab-pane" id="crm">

<!-- start filter well -->
<div class="well">

    <!-- <h4>Click filter names to toggle filters, then click "View Users" to load your selection.</h4>

    <hr> -->

    <!-- start:filter set -->
    <!-- <div class="filter_set"> -->
    <div class="row-fluid">

        <!-- start input-set-level -->
        <div class="input-set span4" id="input-set-level">
            <div class="controls control-row">

                <div class="input-prepend">

                    <button class="btn btn-primary no-dropdown input-set-toggle" data-toggle="button">Level</button>

                    <div class="input-wrapper">
                        <input class="sliderRangeLabel ipt-primary" type="text" name="level" value="">
                    </div>

                </div>

            </div>
            <div class="slider sliderRange sliderBlue"></div>
        </div>
        <!-- end input-set-level -->

        <!-- start input-set-action -->
        <div class="input-set span4" id="input-set-action">
            <div class="controls control-row">

                <div class="input-prepend">

                    <!-- start btn-group -->
                    <div class="btn-group">
                        <button class="btn btn-danger dropdown-title input-set-toggle" data-toggle="button">Action
                        </button>
                        <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <?php
                            $flag = true;
                            foreach($actionList as $k => $v) {
                                if($flag) {
                                    $flag = false;
                                    echo "<li><a value='$k' selected='selected'>$v</a></li>";
                                } else {
                                    echo "<li><a value='$k'>$v</a></li>";
                                }
                            }
                            ?>
                        </ul>
                    </div>
                    <!-- end btn-group -->

                    <div class="input-wrapper">
                        <input class="sliderRangeLabel ipt-danger" type="text" name="action" value="">
                    </div>

                </div>

            </div>
            <div class="slider sliderRange sliderOrange"></div>
        </div>
        <!-- end input-set-action -->

        <!-- start input-set-reward -->
        <div class="input-set span4" id="input-set-reward">
            <div class="controls control-row">

                <div class="input-prepend">

                    <!-- start btn-group -->
                    <div class="btn-group">
                        <button class="btn btn-success dropdown-title input-set-toggle" data-toggle="button">Reward</button>
                        <button class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <?php
                            $flag = true;
                            foreach($rewardList as $k => $v) {
                                if($flag) {
                                    $flag = false;
                                    echo "<li><a value='$k' selected='selected'>$v</a></li>";
                                }
                                else {
                                    echo "<li><a value='$k'>$v</a></li>";
                                }
                            }
                            ?>
                        </ul>
                    </div>
                    <!-- end btn-group -->

                    <div class="input-wrapper">
                        <input class="sliderRangeLabel ipt-success" type="text" name="reward" value="">
                    </div>

                </div>

            </div>
            <div class="slider sliderRange sliderDarkGreen"></div>
        </div>
        <!-- end input-set-reward -->



    </div>
    <!-- </div> -->
    <!-- end:filter set -->

    <!-- <hr> -->
</div>
<!-- end filter well -->

<!-- start sort well -->
<div class="well clearfix">


    <button class='btn submit_filter_btn btn-primary'><i class='fa-icon-th'></i> &nbsp;View Users</button>

    <div class="common-filter">
        <span>Sort by: </span>

        <div class="btn-group" data-toggle="buttons-radio" data-option-key="sortBy">
            <button type="button" class="btn options btn-small" data-option-value="level">Level</button>
            <button type="button" class="btn options btn-small" data-option-value="point">Point</button>
            <button type="button" class="btn options btn-small" data-option-value="name">Name</button>
        </div>

        <div class="btn-group" data-toggle="buttons-radio" data-option-key="sortAscending">
            <button type="button" class="btn options active btn-small" data-option-value="true">Low-High</button>
            <button type="button" class="btn options btn-small" data-option-value="false">High-Low</button>
        </div>

        <div class="btn-group" data-toggle="buttons-radio" data-option-key="filter">
            <button type="button" class="btn options btn-small" data-option-value="0">Unknow</button>
            <button type="button" class="btn options btn-small" data-option-value="1">Male</button>
            <button type="button" class="btn options btn-small" data-option-value="2">Female</button>
            <button type="button" class="btn options active btn-small" data-option-value="*">All</button>
        </div>
    </div>

    <div class="pull-right paginator" style="margin-top:3px; display:none;">
        <span id="current_result">1-100</span>
        of
        <span id="max_result">6098</span>
        results
        <div class="btn-group paging paginator" style="display:none;">
            <button id="prev" class="btn">&lt;</button>
            <button id="next" class="btn">&gt;</button>
        </div>
    </div>

</div>
<!-- end sort well -->

<!--           <div class="pagination">
            <ul>
              <li><a href="#">Prev</a></li>
              <li><a href="#">1</a></li>
              <li><a href="#">2</a></li>
              <li><a href="#">3</a></li>
              <li><a href="#">4</a></li>
              <li><a href="#">5</a></li>
              <li><a href="#">Next</a></li>
            </ul>
          </div> -->

<div class="pagination-wrapper">

    <!-- pagination -->
    <div id="pagination" class="pagination" style="display:none;">
        <div class='paginator_p_wrap'>
            <div class='paginator_p_bloc' style="display:none;">
            </div>
        </div>

        <!-- slider -->
        <div class='paginator_slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all' style="width:80%;">
            <a class='ui-slider-handle ui-state-default ui-corner-all' href='#'></a>
        </div>
    </div>


</div>

<div id="player-isotopes" class="player-data">
    <blockquote>
        <p>Enter some values in the filters above, click the buttons to toggle them on or off, then click <span class="submit_filter_btn">View Users</span> when you're ready.</p>
    </blockquote>
</div>

<!-- </div> -->
<!-- end : isotope container -->

</div>

</div>
<!-- end tab-content -->

</div>
<!-- end box-content -->

</div>
<!-- end box -->
</div>

<!-- </div> -->

<!-- ######################################## -->
<!--       Popup Modal                        -->
<!-- ######################################## -->
<!-- Button to trigger modal -->
<!-- <a href="#myModal" role="button" class="btn" data-toggle="modal">Launch demo modal</a> -->

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Error ..</h3>
    </div>
    <div class="modal-body">
        <p class="error-dialog"></p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    </div>
</div>


<div class='model-hidden hide' >
    <div class="filter_popover" rel="popover">

        <ol>
            <li>View ISOTOPE</li>
        </ol>
    </div>
</div>

<hr>
</div>
<!-- end content -->

<script type="text/javascript">

    var config_stat_path = '/getSummary';
    var config_statistic_link = window.location.protocol
            +"//"+window.location.host
            +window.location.pathname
            +config_stat_path
    // console.log('Call to link : '+config_statistic_link);

</script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/isotope/jPaginator.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>javascript/player/script.js"></script>
<link id="base-style" rel="stylesheet" type="text/css" href="<?php echo base_url();?>stylesheet/player/player.css" />
<script type="text/javascript" src="<?php echo base_url();?>javascript/player/player.js"></script>