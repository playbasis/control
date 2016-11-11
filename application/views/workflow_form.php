<link rel="stylesheet" media="screen" type="text/css" href="<?php echo base_url();?>stylesheet/goods/style.css" />
<link href="<?php echo base_url(); ?>javascript/pace/simple.css" rel="stylesheet" type="text/css">
<script data-pace-options='{ "elements": { "selectors": ["#content"] }, "ajax": false }'
        src="<?php echo base_url(); ?>javascript/pace/pace.min.js" type="text/javascript"></script>
<div class="cover"></div>
<div id="content" class="span10">
    <div class="box">
        <div class="heading">
            <h1><img src="<?php echo base_url();?>image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
            <div class="buttons">
                <button class="btn btn-info" onclick="$('#form').submit();" type="button"><?php echo $this->lang->line('button_save'); ?></button>
                <button class="btn btn-info" onclick="location = baseUrlPath+'workflow'" type="button"><?php echo $this->lang->line('button_cancel'); ?></button>
            </div>
        </div>
        <div class="content">
            <?php if(isset($message) && $message){ ?>
                <div class="content messages half-width">
                    <div class="warning"><?php echo $message; ?></div>
                </div>
            <?php }?>
            <?php if ($error_warning) { ?>
                <div class="warning"><?php echo $error_warning; ?></div>
            <?php } ?>

            <?php
            $attributes = array('id' => 'form');
            echo form_open($form ,$attributes);
            ?>
            <div id="tab-general">
                <table class="form">

                    <?php if(isset($action) && $action=="edit"){ ?>
                        <tr>
                            <input type="hidden" id="cl_player_id" name="cl_player_id" value="<?php echo isset($requester['cl_player_id']) ? $requester['cl_player_id'] :  set_value('cl_player_id'); ?>" />
                            <td><span class="required">*</span><?php echo $this->lang->line('form_id'); ?>:</td>
                            <td><input  type="text" name="temp"  disabled="disabled" value="<?php echo isset($requester['cl_player_id']) ? $requester['cl_player_id'] :  set_value('cl_player_id'); ?>" /></td>
                        </tr>
                    <?php }else{?>
                        <tr>
                            <td><span class="required">*</span><?php echo $this->lang->line('form_id'); ?>:</td>
                            <td><input  type="text" name="cl_player_id"  value="<?php echo isset($requester['cl_player_id']) ? $requester['cl_player_id'] :  set_value('cl_player_id'); ?>" /></td>
                        </tr>
                    <?php }?>

                    <tr>
                        <td><span class="required">*</span><?php echo $this->lang->line('form_username'); ?>:</td>
                        <td><input  type="text" name="username"  value="<?php echo isset($requester['username']) ? $requester['username'] :  set_value('username'); ?>" /></td>
                    </tr>

                    <?php if(isset($action) && $action=="create"){ ?>
                    <tr>
                        <td><?php echo $this->lang->line('form_password'); ?>:</td>
                        <td><input  type="password" name="password"  value="<?php echo isset($requester['password']) ? $requester['password'] :  set_value('password'); ?>" /></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('form_confirm_password'); ?>:</td>
                        <td><input  type="password" name="confirm_password"  value="<?php echo isset($requester['confirm_password']) ? $requester['confirm_password'] : set_value('confirm_password'); ?>" /></td>
                    </tr>
                    <?php }?>
                    <tr>
                        <td><span class="required">*</span><?php echo $this->lang->line('form_firstname'); ?>:</td>
                        <td><input type="text" name="first_name"  value="<?php echo isset($requester['first_name']) ? $requester['first_name'] :  set_value('first_name'); ?>" /></td>
                    </tr>
                    <tr>
                        <td><span class="required">*</span><?php echo $this->lang->line('form_lastname'); ?>:</td>
                        <td><input type="text" name="last_name"  value="<?php echo isset($requester['last_name']) ? $requester['last_name'] :  set_value('last_name'); ?>" /></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('form_nickname'); ?>:</td>
                        <td><input type="text" name="nickname"  value="<?php echo isset($requester['nickname']) ? $requester['nickname'] :  set_value('nickname'); ?>" /></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('form_gender'); ?>:</td>

                        <td>
                            <select name="gender" >
                                <option value="1" <?php if ($requester['gender'] == "male")  { ?>selected<?php }?>>Male</option>
                                <option value="2" <?php if ($requester['gender'] == "female")  { ?>selected<?php }?>>Female</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('form_phone_number'); ?>:</td>
                        <td><input  type="text" name="phone_number"  value="<?php echo isset($requester['phone_number']) ? $requester['phone_number'] :  set_value('phone_number'); ?>" /></td>
                    </tr>
                    <tr>
                        <td><span class="required">*</span><?php echo $this->lang->line('form_email'); ?>:</td>
                        <td><input  type="email" name="email" size="100" value="<?php echo isset($requester['email']) ? $requester['email'] :  set_value('email'); ?>" class="tooltips" data-placement="right" title="Email address is used to log into the system"/></td>
                    </tr>
                    <tr>
                        <td><?php echo $this->lang->line('form_approve'); ?>:</td>

                        <td>
                            <select name="approve_status"  >
                                <option value="approved" <?php if (isset($requester['approve_status']) && $requester['approve_status'] == "approved") { ?>selected<?php }?>>Approved</option>
                                <option value="rejected" <?php if (isset($requester['approve_status']) && $requester['approve_status'] == "rejected") { ?>selected<?php }?>>Rejected</option>
                                <option value="pending"  <?php if (!isset($requester['approve_status']) || $requester['approve_status'] == "pending")  { ?>selected<?php }?>>Pending</option>
                            </select>
                        </td>
                    </tr>
                    <?php if($org_status){?>
                    <tr>
                        <td><?php echo $this->lang->line('form_organization'); ?>:</td>

                        <td>
                            <?php for($i = 0;$i<count($organize_node);$i++){?>
                                <?php if(isset($organize_id)){?>
                                <input type='hidden' name="organize_id[]"   id="<?php echo "organize_id".$i ?>"   style="width:220px;" value="<?php echo isset($organize_id[$i]) ? $organize_id[$i] : set_value('organize_id'); ?>">
                                <?php }?>

                                <input type='hidden' name="organize_type[]" id="<?php echo "organize_type".$i ?>" style="width:220px;" value="<?php echo isset($organize_type[$i]) ? $organize_type[$i] : set_value('organize_type'); ?>">
                                <input type='hidden' name="organize_node[]" id="<?php echo "organize_node".$i ?>" style="width:220px;" value="<?php echo isset($organize_node[$i]) ? $organize_node[$i] : set_value('organize_node'); ?>">
                                <input class='tags' type="text"   name="organize_role[]" id="<?php echo "organize_role".$i ?>" style="width:220px;" placeholder="Role" value="<?php echo isset($organize_role[$i]) ? $organize_role[$i] :  set_value('organize_role'); ?>" />
                                <br>
                            <?php }?>
                        </td>
                    </tr>
                    <?php }?>

                </table>
            </div>

            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>

<link href="<?php echo base_url(); ?>stylesheet/select2/select2.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url(); ?>javascript/select2/select2.min.js" type="text/javascript"></script>

<script type="text/javascript">

    var $nodeOrganizeSearch = new Array();
    //var $pleaseWaitSpanHTML = $("#pleaseWaitSpanDiv").html();

    function organizeFormatResult(organize) {
        return '<div class="row-fluid">' +
            '<div>' + organize.name;

    }
    function organizeFormatSelection(organize) {
        return organize.name;
    }

    function nodeFormatResult(node) {
        return '<div class="row-fluid">' +
            '<div>' + node.name;
    }

    function nodeFormatSelection(node) {
        return node.name;
    }

    $(document).ready(function() {
        <?php for($i = 0;$i<count($organize_node);$i++){?>

        $nodeOrganizeSearch[<?php echo $i ?>] = "";

        $("#<?php echo "organize_type".$i ?>").select2({
            placeholder: "Select Organization type",
            allowClear: false,
            minimumInputLength: 0,
            id: function (data) {
                return data._id;
            },
            ajax: {
                url: baseUrlPath + "store_org/organize/",
                dataType: 'json',
                quietMillis: 250,
                data: function (term, page) {return {
                    search: term, // search term
                };
                },
                results: function (data, page) {
                    return {results: data.rows};
                },
                cache: true
            },
            initSelection: function (element, callback) {
                var id = $(element).val();
                if (id !== "") {
                    $.ajax(baseUrlPath + "store_org/organize/" + id, {
                        dataType: "json",
                        beforeSend: function (xhr) {
                            $("#<?php echo "organize_type".$i ?>")
                                .select2('enable', false)
                        }
                    }).done(function (data) {
                        if (typeof data != "undefined")
                            callback(data);
                        $nodeOrganizeSearch[<?php echo $i ?>] = id;
                    }).always(function () {
                        $("#<?php echo "organize_type".$i ?>")
                            .select2('enable', true)
                    });
                }else{
                    $("#<?php echo "organize_node".$i ?>")
                        .select2('enable', false);
                }
            },
            formatResult: organizeFormatResult,
            formatSelection: organizeFormatSelection,

        });

        $("#<?php echo "organize_node".$i ?>").select2({
            placeholder: "Select Node",
            //allowClear: true,
            minimumInputLength: 0,
            id: function (data) {
                return data._id;
            },
            ajax: {
                url: baseUrlPath + "store_org/node/",
                dataType: 'json',
                quietMillis: 250,
                data: function (term, page) {
                    return {
                        search: term, // search term
                        organize: $nodeOrganizeSearch[<?php echo $i ?>]
                    };
                },
                results: function (data, page) {
                    return {results: data.rows};
                },
                cache: true
            },
            initSelection: function (element, callback) {
                var id = $(element).val();
                if (id !== "") {
                    $.ajax(baseUrlPath + "store_org/node/" + id, {
                        dataType: "json",
                        beforeSend: function (xhr) {
                            $("#<?php echo "organize_node".$i ?>")
                                .select2('enable', false)
                        }
                    }).done(function (data) {
                        if (typeof data != "undefined")
                            callback(data);
                    }).always(function () {
                        $("#<?php echo "organize_node".$i ?>")
                            .select2('enable', true)
                    });
                }else{
                    $("#<?php echo "organize_node".$i ?>")
                        .select2('enable', false);
                }
            },
            formatResult: nodeFormatResult,
            formatSelection: nodeFormatSelection,
        });


        if(document.getElementById("<?php echo "organize_type".$i ?>").value==""){
            $("#<?php echo "organize_node".$i ?>")
                .select2('enable', false);
        }
        <?php }?>

        $(".tags").select2({
            width: 'resolve',
            tags: true,
            tokenSeparators: [',', ' ']
        });
    });

    <?php for($i = 0;$i<count($organize_node);$i++){?>
    $("#<?php echo "organize_type".$i ?>")
        .on("change", function (e) {
            var $nodeParent = $("#<?php echo "organize_node".$i ?>");

            if (e.val === "") {
                $nodeParent
                    .select2("val", "")
                    .select2("enable", false);
            }
            else {
                $.ajax(baseUrlPath + "store_org/organize/" + e.val, {
                        dataType: "json",
                        beforeSend: function (xhr) {
                            $nodeParent
                                .select2("enable", false)
                                .select2("val", "")
                        }
                    })
                    .done(function (data) {
                        $nodeOrganizeSearch[<?php echo $i ?>] = data._id;
                        $nodeParent.select2("enable", true);
                    })
                    .always(function () {

                    });
            }
        });

    <?php }?>

    Pace.on("done", function () {
        $(".cover").fadeOut(1000);
    });

</script>

