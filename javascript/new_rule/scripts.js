/**
 * Rule toggle switches
 */
function toggleRule($toggle) {

    // Toggle: disabled -> enabled
    if ( $toggle.hasClass('disabled') ) {

        $toggle.removeClass('disabled').addClass('enabled')
        $toggle.find('span').html('Enabled');

    // Toggle: enabled -> disabled
    } else if ( $toggle.hasClass('enabled') ) {

        $toggle.removeClass('enabled').addClass('disabled')
        $toggle.find('span').html('Disabled');

    }

}

/**
 * TODO: Save rule here
 */
function saveRule($rule, $addRule) {

    var $container = $('#rule-masonry'),
        $tmp,
        tmp = $rule.find('.rule-title > input').val();

    $rule.removeClass('large');
    $container.isotope('reLayout');

    if ( $addRule ) {

        $rule.removeAttr('id');

        setTimeout( function() {
            $container.prepend( $addRule.clone() ).isotope( 'reloadItems' ).isotope({ sortBy: 'original-order' });
        }, 300);

        // $tmp = $('<div>', {'class': 'rule-tmp'}).prependTo($container);

        // setTimeout( function() {
        //     $tmp.width(180);

        //     setTimeout( function() {
        //         $tmp.replaceWith( $addRule.clone() );
        //     }, 400);

        // }, 400);

    }

    // FIX: Add to save/discard process
    if ( tmp ) {
        $rule.find('.rule-name h4').html(tmp).attr('title', tmp);
    }

}


/**
 * Initialize rule isotopes and register listeners
 */
function initRuleIsotopes($container) {

    // FIX: Store this externally
    var $addRule = $('#add-rule').clone();

    $container.isotope({
        itemSelector : '.rule-container',
        masonry: {
            columnWidth: 180
        }
        // sortBy: 'title',
        // getSortData: {
        //     title : function( $elem ) {
        //         return $elem.find('.rule-name > h4').text();
        //     }
        // }
    });

    /**
     * TODO: Only load rule content on .large
     * make sure to check datepicker then.
     */
    // $('.datepicker').datepicker();

    /**
     * Rule Creation
     */
    $container.on( 'click', '#add-rule .add-rule, #add-rule .rule-template', function(event) {

        // TODO: Garbage Collection!
        var template, newRule, $rule = $('#add-rule'),
            action_name, action_id = 1;

        if ( $(this).hasClass('add-rule') ) {

            $(this).hide();
            $rule.find('input').focus();
            $rule.find('.rule-content').css('opacity', '1').css('margin-left', '0');

        } else {

            template = Handlebars.templates['rule'];

            // TODO: Stop storing colors and icons in divs
            // newRule = [{
            //     'addRule' : true,
            //     'color' : $(this).parent().attr('class'),
            //     'name' : $rule.find('#add-rule-title').val(),
            //     'description' : $rule.find('#add-rule-description').val(),
            //     'action' : {
            //         'icon' : $(this).find('.rule-template-action > i').attr('class'),
            //         'title' : $(this).find('.rule-template-action > span').html()
            //     },
            //     'rewards' : [
            //         {
            //             'icon' : 'icon-heart',
            //             'title' : $(this).find('.rule-template-reward-type').html(),
            //             'quantity' : $(this).find('.rule-template-reward-quantity').html()
            //         }
            //     ]
            // }];

            action_name = $(this).find('.rule-template-action > span').html().toLowerCase();
            $.each( $.parseJSON(jsonString_Action), function(i,v) {
                if ( v.name == action_name ) {
                    action_id = v.specific_id;
                    return false;
                }
            });

            newRule = [{
                'addRule' : true,
                'color' : $(this).parent().attr('class'),
                'name' : $rule.find('#add-rule-title').val(),
                'description' : $rule.find('#add-rule-description').val(),
                'action_id' : action_id,
                'rewards' : [
                    {
                        'icon' : 'icon-heart',
                        'title' : $(this).find('.rule-template-reward-type').html(),
                        'quantity' : $(this).find('.rule-template-reward-quantity').html()
                    }
                ]
            }];

            $rule.find('.rule-content').css('opacity', '0').css('margin-left', '-10px');

            setTimeout( function() {
                $rule.html( template(newRule) );
                $rule.find('.rule-description').show().css('opacity', '1');
                $rule.find('.rule-content').css('opacity', '1').css('margin-left', '0');
            }, 100);

        }

    }).on( 'keydown', '#add-rule :input', function(event) {

        if ( event.keyCode === 13 || event.keyCode === 27 ) {
            event.preventDefault();
            $(this).blur();
        }

    }).on( 'blur', '#add-rule-title, #add-rule-description', function(event) {

        var $rule = $('#add-rule');

        // TODO: Validate inputs!
        if ( $(this).is('#add-rule-title') ) {

            if ( !$(this).val() ) $(this).val('Untitled Rule');
            $rule.find('.rule-description').show().css('opacity', '1').find('textarea').focus();

        } else {

            $rule.find('.rule-templates').show().css('opacity', '1');

        }

    });


    /**
     * Rule Interaction
     *
     * TODO: Only register rest of the listeners for
     * .large rules, split up the list a bit.
     *
     * TODO: Unregister .rule for .large rules, can get rid
     * of all those stopPropagations...
     */
    $container.on( 'click',
        '.rule, .rule-status, .rule-minimize, .rule-controls-save, .rule-controls-cancel, .rule-controls-duplicate, .rule-controls-delete, .rule-controls-toggle, .toggle-rule-action-date-restrictions, .add-rule-reward, .rule-reward, .close-rule-input-well',
        function(event) {

        var $rule = $(this).closest('.rule-container');

        if ( $(this).hasClass('rule-status') ) {

            event.stopPropagation();
            toggleRule( $(this) );

        // FIX: Unsave here / Alerts? (Maybe not, alerts are annoying)
        } else if ( $(this).hasClass('rule-minimize') || $(this).hasClass('rule-controls-cancel') ) {

            event.stopPropagation();

            if ( $rule.is('#add-rule') ) {
                $rule.find('.rule-content').css('opacity', '0').css('margin-left', '-10px');
                $rule.html( $addRule.html() );
            }

            $rule.removeClass('large');
            $container.isotope('reLayout');

        // FIX: Save here
        } else if ( $(this).hasClass('rule-controls-save') ) {

            event.stopPropagation();
            if ( $rule.is('#add-rule') ) {
                saveRule( $rule, $addRule );
            } else {
                saveRule( $rule );
            }

        // FIX: Duplicate here
        } else if ( $(this).hasClass('rule-controls-duplicate') ) {

            event.stopPropagation();
            // $rule.removeClass('large');
            $container.isotope( 'insert', $rule.clone().removeClass('large') );

        // FIX: Delete here / Alerts
        } else if ( $(this).hasClass('rule-controls-delete') ) {

            event.stopPropagation();
            $rule.removeClass('large');
            $container.isotope( 'remove', $rule );

        } else if ( $(this).hasClass('toggle-rule-action-date-restrictions') ) {

            $rule.find('.rule-action-date-restrictions').slideToggle(200);

        // Populate well, save on close
        } else if ( $(this).hasClass('rule-reward') ) {

            $rule.find('.rule-reward-editor').slideToggle(200);

        } else if ( $(this).hasClass('close-rule-input-well') ) {

            $(this).parent().slideUp(200);

        } else {

            $rule.addClass('large');

            // template = Handlebars.templates['rule-content'];
            // $(this).find('.rule-content').append( template(rules) );

            $container.isotope('reLayout');

        }

    }).on( 'mousedown', '.rule-input > label', function(event) {

        // Avoid animation flicker when label is clicked
        event.preventDefault();

    }).on( 'focus', '.rule-input', function(event) {

        // Toggle extra info when inputs are focused
        var $inputs = $(this).parent().find('.rule-input');
        $inputs.removeClass('active').find('.rule-input-help').slideUp(200);
        $(this).addClass('active').find('.rule-input-help').slideDown(200);

    });

}

$(function(){

    // Handlebars!
    var template = Handlebars.templates['rule'],
        icons = $.parseJSON(jsonConfig_icons),
        colors = {};

    // console.log(jsonConfig_icons);
    // console.log(jsonString_Action);

    colors[1] = "rule-purple";
    colors[2] = "rule-orange";
    colors[3] = "rule-green";
    colors[4] = "rule-yellow";
    colors[7] = "rule-blue";
    colors[9] = "rule-pink";

    Handlebars.registerHelper('actionIcon', function(action_id) {
        var name = action_list[action_id].name;
        return icons[name];
    });
    Handlebars.registerHelper('actionName', function(action_id) {
        var name = action_list[action_id].name;
        return name;
    });
    Handlebars.registerHelper('actionColor', function(action_id) {
        return colors[action_id];
    });
    Handlebars.registerHelper('enabled', function(status) {
        if (status == 1) return 'enabled';
        else return 'disabled';
    });

    $.ajax({
        url : urlConfig.URL_getRules(),
        data : '&siteId='+jsonConfig_siteId+'&clientId='+jsonConfig_clientId+'&ts='+(new Date()).getMilliseconds(),
        type : 'GET',
        dataType : 'json',
        beforeSend : function() {
            progressDialog.show('Fetching rules ...');
        },
        success : function(data) {
            $('#rule-masonry').append( template(data) );
            initRuleIsotopes( $('#rule-masonry') );
        },
        error : function() {
            dialogMsg = 'Cannot load rule from server,\n Please try again later';
            return false;
        },
        complete : function() {
            // notificationManagerJS.showAlertDialog('loadtable', dialogMsg);
            progressDialog.hide();
        }
    });

});
