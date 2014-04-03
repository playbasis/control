$(function(){

    var Playbasis = window.Playbasis || {};

    Playbasis.Dashboard = {
        Init:function(){
            this.initForm(function(){
                Playbasis.Dashboard.Action.Init();
                Playbasis.Dashboard.Reward.Init();
                Playbasis.Dashboard.User.Init();
            });
        },
        initForm:function(callback){
            $('input[id$="start"],input[id$="end"]').datepicker( "option", "dateFormat", "yy-mm-dd" );
            // set start and to for timespans
            // - from is  first day of month if current day is above 7 (can show at least 1 one week)
            // otherwise it is the first day of the previous month
            // - to is yesterday
            var Yesterday, From;
            Yesterday   = new Date();
            Yesterday.setDate(Yesterday.getDate() - 1);
            From        = Yesterday.getDay() < 7 ? new Date(Yesterday.getFullYear(), Yesterday.getMonth() - 1, 1) : new Date(Yesterday.getFullYear(), Yesterday.getMonth(), 1);
            $('input[id$="start"]').val($.datepicker.formatDate('yy-mm-dd', From));
            $('input[id$="end"]').val($.datepicker.formatDate('yy-mm-dd', Yesterday));
            // initialize loading animation
            Playbasis.API.Setup();
            $('.dashboard-panel')
                .find('.api-result-container')
                .each(function(){
                    Playbasis.API.ShowLoadingAnimation($(this), 'initial-loading');
                });
            // define the current panel using data attributes
            $('.dashboard-panel').on('mouseenter', function(){
                $('.dashboard-panel').removeAttr('data-current-panel');
                $(this).attr('data-current-panel', 'true');
            });
            callback();
        }
    };

    Playbasis.Dashboard.Helpers = {
        getBarChartWidthByUnit:function(unit){
            var barChartWidth = 0;
            switch (unit){
                case 'day':
                    barChartWidth = 1000 * 60 * 60 * 24 * 1;
                    break;
                case 'week':
                    barChartWidth = 1000 * 60 * 60 * 24 * 7;
                    break;
                case 'month':
                    barChartWidth = 1000 * 60 * 60 * 24 * 30;
                    break;
                default :
                    barChartWidth = 60 * 60 * 1000
            }
            return barChartWidth;
        },
        showTooltip:function (x, y, contents, z) {
            $('#action-tool-tip')
                .show()
                .find('.tooltip-inner')
                .text(contents);
            var width = $('#action-tool-tip').width();
            $('#action-tool-tip')
                .css({
                    top: y - 40,
                    left: x - (width/2)
                });
        },
        hideTooltip:function(){
            $('#action-tool-tip').hide();
        },
        getShortDisplayUnit:function(unit){
            var displayUnit = '';
            switch (unit){
                case 'day':
                    displayUnit = 'D';
                    break;
                case 'week':
                    displayUnit = 'W';
                    break;
                case 'month':
                    displayUnit = 'M';
                    break;

            }
            return displayUnit;
        },
        capitaliseFirstLetter: function(string){
            return string.charAt(0).toUpperCase() + string.slice(1);
        },
        getMonthShort: function(month){
            var months = new Array();;
            months[0]='Jan';
            months[1]='Feb';
            months[2]='Mar';
            months[3]='Apr';
            months[4]='May';
            months[5]='Jun';
            months[6]='Jul';
            months[7]='Aug';
            months[8]='Sep';
            months[9]='Oct';
            months[10]='Nov';
            months[11]='Dec';
            return months[month];
        }
    };

    Playbasis.Dashboard.Action = {
        _actionTypes: null,
        _actionCompareDate: null,
        _actionChart: '#playbasis-action-stack-chart',
        _actionCompareChart: '#playbasis-action-compare-stack-chart',
        _actionPanel: '#action-panel',
        _actionsActionPanel: '#actions-action',
        _actionStart: '#action-start',
        _actionEnd: '#action-end',
        _actionUnitType: '#action-unit-type',
        _actionCompareType: '#action-compare-type',
        _actionDateSubmitButton: '#submitActionDate',
        _actionUnitSubmitButton: '#submitActionUnit',
        _actionCompareTypeSubmitButton: '#submitActionCompareType',
        // chart logs to show total of stacked bars
        _actionLogChart: null,
        _actionCompareLogChart: null,
        getActionTypes:function(){
            this._actionTypes = new Array();
            Playbasis.API.getActionTypes(function(data){
                if(data!=false){
                    var colors = ColorSequenceGenerator.createColorSequence(data.length).getColors();
                    $.each(data,function(index,value){
                        Playbasis.Dashboard.Action.setActionTypes({
                            name:value,
                            color:colors[index]
                        });
                    });
                    Playbasis.Dashboard.Action.generateActionList();
                    Playbasis.Dashboard.Action.getActionLog();
                }
            });
        },
        setActionTypes:function(actionType){
            this._actionTypes.push(actionType);
        },
        getActionChecked:function(){
            var checkedAction = new Array();
            $(this._actionPanel+' :checked').each(function() {
                checkedAction.push($(this).data('value'));
            });
            return checkedAction;
        },
        selectAllAction: function(){
            $(this._actionsActionPanel)
                .on('change', 'input', function(){
                    var isChecked   = $(this).prop('checked'),
                        $actions    = $(Playbasis.Dashboard.Action._actionPanel).find('input');
                    if (isChecked){
                        $actions.attr('checked', 'checked')
                    } else {
                        $actions.removeAttr('checked', 'checked');
                    }
                });
        },
        generateActionList:function(){
            // removing loading animation
            $(this._actionPanel).empty();
            // generate list of actions
            $.each(this._actionTypes,function(index,value){
                var actionSelect =
                    $('<li />', {
                        'html': '<input type="checkbox" checked="checked" id="action_'+value.name+'" data-value="'+value.name+'">'+value.name
                    })
                    .css('background',value.color);
                $(Playbasis.Dashboard.Action._actionPanel).append(actionSelect);
            });
        },
        getActionLog: function(){

            var startDate       = $(Playbasis.Dashboard.Action._actionStart).val();
            var endDate         = $(Playbasis.Dashboard.Action._actionEnd).val();
            var actionUnitType  = $(Playbasis.Dashboard.Action._actionUnitType).val();

            var params = {
                'startDate':startDate,
                'endDate':endDate,
                'actionUnitType':actionUnitType
            };

            Playbasis.API.getActionLog(params, function(data){
                var checkedAction   = Playbasis.Dashboard.Action.getActionChecked();
                var chartData       = new Array();
                var chartColor      = new Array();

                if(!data){
                    return;
                }

                Playbasis.Dashboard.Action._actionLogChart = new Array();

                $.each(Playbasis.Dashboard.Action._actionTypes, function(index, action){

                    if(checkedAction.indexOf(action.name) !== -1){

                        var currentChartData = new Array();

                        $.each(data, function(logIndex, logValue){

                            $.each(logValue, function(logValueIndex, logValueValue){

                                var currentChartValue       = 0;
                                var currentChartDataUnit    = new Array();

                                $.each(logValueValue, function(actionIndex, actionValue){
                                    if(actionIndex == action.name){
                                        currentChartValue = actionValue;
                                    }
                                });

                                var timeDate = (new Date(logValueIndex)).getTime();

                                currentChartDataUnit.push(timeDate);
                                currentChartDataUnit.push(currentChartValue);

                                // generate the total of each stacked bar

                                if (!Playbasis.Dashboard.Action._actionLogChart[timeDate]){
                                    Playbasis.Dashboard.Action._actionLogChart[timeDate] = currentChartValue;
                                } else {
                                    Playbasis.Dashboard.Action._actionLogChart[timeDate] += currentChartValue;
                                }

                                currentChartData.push(currentChartDataUnit);
                            });

                        });

                        chartData.push({
                            label: action.name,
                            data: currentChartData
                        });
                        chartColor.push(action.color);
                    }
                });

                // generate chart
                Playbasis.Dashboard.Action.generateActionLogChart(chartData, chartColor);
                Playbasis.Dashboard.Action.getActionCompareLog();
            });
        },
        generateActionLogChart:function(data, color){

            var actionUnitType = $(Playbasis.Dashboard.Action._actionUnitType).val();
            var actionBarWidth = Playbasis.Dashboard.Helpers.getBarChartWidthByUnit(actionUnitType);

            // Plot(flot) didn't know how to display week unit.
            // Put month instead but using a week for max bar chart width.
            actionUnitType = (actionUnitType == 'week') ? 'month' : actionUnitType;

            $.plot(this._actionChart, data, {
                series: {
                    stack: true,
                    bars: { show: true, fill: true, barWidth: actionBarWidth, align:'center' }
                },
                colors: color,
                xaxis: {
                    mode: "time",
                    minTickSize: [1, actionUnitType],
                    tickLength: 1
                },
                yaxis: {
                    tickDecimals: 0,
                    min: 0
                },
                grid: {
                    borderWidth: 0,
                    hoverable: true,
                    clickable: true
                },
                legend: {
                    show: false
                }
            });

            var previousPoint = null;
            $(this._actionChart)
                .unbind('plotclick')
                .unbind('plothover')
                .bind("plotclick", function (event, pos, item) {
                    if(item != null){
                        Playbasis.Dashboard.Action._actionCompareDate = item.datapoint[0];
                        Playbasis.Dashboard.Action.getActionCompareLog();
                    }
                })
                .bind('plothover', function (event, pos, item) {
                    if (item) {
                        if (previousPoint != item.datapoint) {
                            previousPoint = item.datapoint;

                            Playbasis.Dashboard.Helpers.hideTooltip();
                            var x       = item.datapoint[0],
                                y       = item.datapoint[1] - item.datapoint[2],
                                // get total from the generated array
                                total   = Playbasis.Dashboard.Action._actionLogChart[x],
                                date    = new Date(item.datapoint[0]);
                            var tooltip = 'Total: '+total+' - ';
                                tooltip += ( item.series.label ? Playbasis.Dashboard.Helpers.capitaliseFirstLetter(item.series.label) + ': ' + y : y) + ' - ';
                                tooltip += Playbasis.Dashboard.Helpers.getMonthShort(date.getMonth()) + ' '+ date.getDate();
                            if (y > 0){
                                Playbasis.Dashboard.Helpers.showTooltip(item.pageX, item.pageY, tooltip, item.series.color);
                            }
                        }
                    } else {
                        Playbasis.Dashboard.Helpers.hideTooltip();
                        previousPoint = null;
                    }
                });
        },
        getActionCompareLog:function(){
            var compareDate         = $.datepicker.formatDate('yy-mm-dd', new Date(Playbasis.Dashboard.Action._actionCompareDate)),
                actionCompareType   = $(this._actionCompareType).val();

            var params = {
                'compareDate':compareDate,
                'actionCompareType': actionCompareType
            };

            Playbasis.API.getActionCompareLog(params,function(data){
                var checkedAction   = Playbasis.Dashboard.Action.getActionChecked();
                var chartData       = new Array();
                var chartColor      = new Array();

                if(!data){
                    return;
                }

                Playbasis.Dashboard.Action._actionCompareLogChart = new Array();

                $.each(Playbasis.Dashboard.Action._actionTypes,function(index, action){

                    if(checkedAction.indexOf(action.name) !== -1){

                        var currentChartData = new Array();
                        var comparisonData   = null;

                        $.each(data,function(logIndex,logValue){
                            var currentChartValue       = 0;
                            var currentChartDataUnit    = new Array();

                            $.each(logValue,function(logValueIndex,logValueValue){
                                if(logValueIndex == action.name){
                                    // value contains the dta in format: value_change
                                    // change is teh comparison data in percentage or value
                                    if (logValueValue.toString().indexOf('_') !== -1 && logValueValue.toString().split('_')[1]){
                                        currentChartValue   = parseInt(logValueValue.toString().split('_')[0]);
                                        comparisonData      = logValueValue.toString().split('_')[1];
                                    } else {
                                        currentChartValue   = logValueValue;
                                        comparisonData      = null;
                                    }
                                }
                            });

                            var timeDate = (new Date(logIndex)).getTime();

                            currentChartDataUnit.push(timeDate);
                            currentChartDataUnit.push(currentChartValue);

                            // generate the total of each stacked bar

                            if (!Playbasis.Dashboard.Action._actionCompareLogChart[timeDate]){
                                Playbasis.Dashboard.Action._actionCompareLogChart[timeDate] = currentChartValue;
                            } else {
                                Playbasis.Dashboard.Action._actionCompareLogChart[timeDate] += currentChartValue;
                            }

                            // add extra variable to store the evolution between n-1 and n-2
                            if (comparisonData){
                                // transform data as a string to prevent issues with rendering
                                currentChartDataUnit.push('_'+comparisonData);
                            }

                            currentChartData.push(currentChartDataUnit);
                        });

                        chartData.push({
                            label: action.name,
                            data: currentChartData
                        });
                        chartColor.push(action.color);
                    }
                });

                Playbasis.Dashboard.Action.generateActionCompareLogChart(chartData,chartColor);
            });
        },
        generateActionCompareLogChart:function(data, color){

            var actionBarWidth = Playbasis.Dashboard.Helpers.getBarChartWidthByUnit('day');

            $.plot(this._actionCompareChart, data, {
                series: {
                    stack: true,
                    bars: { show: true, fill: true, barWidth: (actionBarWidth * 0.8),align:'center' }
                },
                colors: color,
                xaxis: {
                    mode: "time",
                    tickSize: [1, 'day']
                },
                yaxis: {
                    tickDecimals: 0,
                    min: 0
                },
                grid: {
                    borderWidth:0,
                    hoverable:true,
                    minBorderMargin: 10
                },
                legend: {
                    show: false
                }
            });

            var previousPoint = null;
            $(this._actionCompareChart)
                .unbind('plothover')
                .bind('plothover', function (event, pos, item) {

                    if (item) {
                        if (previousPoint != item.datapoint) {
                            previousPoint = item.datapoint;

                            Playbasis.Dashboard.Helpers.hideTooltip();

                            var x       = item.datapoint[0],
                                y       = item.datapoint[1] - item.datapoint[2],
                                change  = item.series.data[item.dataIndex][2],
                                // get total form the generated array
                                total   = Playbasis.Dashboard.Action._actionCompareLogChart[x];
                            var tooltip = 'Total: ' + total + ' - ';
                                tooltip += ( item.series.label ? Playbasis.Dashboard.Helpers.capitaliseFirstLetter(item.series.label) + ': ' + y : y);
                                tooltip += change ? ' ( ' + ( change.toString().indexOf('-') !== -1 ? '' : '+') + change.toString().replace('_','')  + ' ) ': '';

                            if (y > 0){
                                Playbasis.Dashboard.Helpers.showTooltip(item.pageX, item.pageY, tooltip, item.series.color);
                            }
                        }
                    } else {
                        Playbasis.Dashboard.Helpers.hideTooltip();
                        previousPoint = null;
                    }
                });

        },
        Init:function(){
            // initialize date
            var Yesterday = new Date();
            Yesterday.setDate(Yesterday.getDate() - 1);
            this._actionCompareDate = Yesterday.getTime();
            // actions buttons
            $(this._actionDateSubmitButton+','+this._actionUnitSubmitButton).on('click', function(){
                Playbasis.Dashboard.Action.getActionLog();
            });
            $(this._actionCompareTypeSubmitButton).on('click', function(){
                Playbasis.Dashboard.Action.getActionCompareLog();
            });
            // action list
            $(this._actionPanel).on('click', 'input[type="checkbox"]', function(){
                Playbasis.Dashboard.Action.getActionLog();
            });
            // select / un-select all actions
            $(this._actionsActionPanel).on('click', 'input[type="checkbox"]', function(){
                Playbasis.Dashboard.Action.getActionLog();
            });
            // actions list
            this.getActionTypes();
            this.selectAllAction();
        }
    };

    Playbasis.Dashboard.Reward = {
        _rewardBadges: null,
        _rewardCompareDate: null,
        _rewardChart: '#playbasis-reward-stack-chart',
        _rewardCompareChart: '#playbasis-reward-compare-stack-chart',
        _rewardBadgePanel: '#reward-badge-panel',
        _rewardBadgeAction: '#reward-badge-action',
        _rewardStart: '#reward-start',
        _rewardEnd: '#reward-end',
        _rewardType: '#reward-type',
        _rewardUnitType: '#reward-unit-type',
        _rewardCompareType: '#reward-compare-type',
        _rewardDateSubmitButton: '#submitRewardDate',
        _rewardUnitSubmitButton: '#submitRewardUnit',
        _rewardCompareTypeSubmitButton: '#submitRewardCompareType',
        // chart logs to show total of stacked bars
        _rewardLogChart: null,
        _rewardCompareLogChart: null,
        initRewardBadges:function(){
            this._rewardBadges = new Array();

            Playbasis.API.getRewardBadges(function(data){
                if(data!=false){
                    var colors = ColorSequenceGenerator.createColorSequence(data.badges.length).getColors();

                    $.each(data.badges,function(index,value){

                        Playbasis.Dashboard.Reward.setRewardBadges({
                            badge_id:value.badge_id,
                            name:value.name,
                            description:value.description,
                            hint:value.hint,
                            image:value.image,
                            color:colors[index]
                        });
                    });
                    Playbasis.Dashboard.Reward.bindRewardBadges();
                    Playbasis.Dashboard.Reward.getRewardLog();
                }
            });
        },
        checkRewardType:function(){
            if($(Playbasis.Dashboard.Reward._rewardType).val() == 'badge'){
                $(Playbasis.Dashboard.Reward._rewardBadgePanel).show();
                $(Playbasis.Dashboard.Reward._rewardBadgeAction).show();
            }else{
                $(Playbasis.Dashboard.Reward._rewardBadgePanel).hide();
                $(Playbasis.Dashboard.Reward._rewardBadgeAction).hide();
            }
            Playbasis.Dashboard.Reward.getRewardLog();
        },
        setRewardBadges:function(rewardBadge){
            this._rewardBadges.push(rewardBadge);
        },
        bindRewardBadges:function(){
            $.each(this._rewardBadges,function(index,value){
                var rewardBadgeSelect =
                    $('<li />', {
                        'html': '<input type="checkbox" checked="checked" id="action_'+value.name+'" data-value="'+value.badge_id+'">'+value.name
                    })
                    .css('background',value.color);
                $(Playbasis.Dashboard.Reward._rewardBadgePanel).append(rewardBadgeSelect);
            });
        },
        getRewardBadgeChecked:function(){
            var checkedRewardBadge = new Array();
            $(this._rewardBadgePanel+' :checked').each(function() {
                checkedRewardBadge.push($(this).data('value'));
            });
            return checkedRewardBadge;
        },
        selectAllBadges: function(){
            $(this._rewardBadgeAction)
                .on('change', 'input', function(){
                    var isChecked   = $(this).prop('checked'),
                        $badges     = $(Playbasis.Dashboard.Reward._rewardBadgePanel).find('input');
                    if (isChecked){
                        $badges.attr('checked', 'checked')
                    } else {
                        $badges.removeAttr('checked', 'checked');
                    }
                });
        },
        getRewardLog:function(){

            var startDate = $(Playbasis.Dashboard.Reward._rewardStart).val();
            var endDate = $(Playbasis.Dashboard.Reward._rewardEnd).val();
            var rewardUnitType = $(Playbasis.Dashboard.Reward._rewardUnitType).val();
            var rewardType = $(Playbasis.Dashboard.Reward._rewardType).val();

            var params = {
                'startDate':startDate,
                'endDate':endDate,
                'rewardUnitType':rewardUnitType
            };

            Playbasis.API.getRewardLog(rewardType,params,function(data){

                var checkedRewardBadge = Playbasis.Dashboard.Reward.getRewardBadgeChecked();
                var chartData = new Array();
                var chartColor = new Array();

                Playbasis.Dashboard.Reward._rewardLogChart = new Array();

                if(data!=false){
                    if(rewardType == 'badge'){
                        $.each(Playbasis.Dashboard.Reward._rewardBadges,function(index,value){

                            if(checkedRewardBadge.indexOf(value.badge_id) !== -1){

                                var currentChartData = new Array();

                                $.each(data, function(logIndex, logValue){

                                    $.each(logValue, function(logValueIndex, logValueValue){

                                        var currentChartValue       = 0;
                                        var currentChartDataUnit    = new Array();

                                        $.each(logValueValue, function(badgeId, badgeValue){
                                            if(badgeId == value.badge_id){
                                                currentChartValue = badgeValue;
                                            }
                                        });

                                        var timeDate = (new Date(logValueIndex)).getTime();

                                        currentChartDataUnit.push(timeDate);
                                        currentChartDataUnit.push(currentChartValue);

                                        // generate the total of each stacked bar

                                        if (!Playbasis.Dashboard.Reward._rewardLogChart[timeDate]){
                                            Playbasis.Dashboard.Reward._rewardLogChart[timeDate] = currentChartValue;
                                        } else {
                                            Playbasis.Dashboard.Reward._rewardLogChart[timeDate] += currentChartValue;
                                        }

                                        currentChartData.push(currentChartDataUnit);
                                    });
                                });

                                chartData.push({
                                    label:value.name,
                                    data:currentChartData
                                });
                                chartColor.push(value.color);
                            }

                        });
                    }else{
                        var currentChartData = new Array();

                        $.each(data,function(logIndex,logValue){
                            var currentChartValue = 0;
                            var currentChartDataUnit = new Array();
                            var currentChartIndex = 0;

                            $.each(logValue,function(logValueIndex,logValueValue){
                                currentChartIndex = logValueIndex;

                                for (var property in logValueValue) {
                                    if (logValueValue.hasOwnProperty(property)) {
                                        currentChartValue = logValueValue[property];
                                    }
                                }
                            });

                            var timeDate = (new Date(currentChartIndex)).getTime();

                            currentChartDataUnit.push(timeDate);
                            currentChartDataUnit.push(currentChartValue);

                            // generate the total of each stacked bar

                            if (!Playbasis.Dashboard.Reward._rewardLogChart[timeDate]){
                                Playbasis.Dashboard.Reward._rewardLogChart[timeDate] = currentChartValue;
                            } else {
                                Playbasis.Dashboard.Reward._rewardLogChart[timeDate] += currentChartValue;
                            }

                            currentChartData.push(currentChartDataUnit);
                        });

                        chartData.push(currentChartData);
                        chartColor.push(ColorSequenceGenerator.createColorSequence(1).getColors());
                    }

                    Playbasis.Dashboard.Reward.generateRewardLogChart(chartData,chartColor);
                    Playbasis.Dashboard.Reward.getRewardCompareLog();
                }
            });
        },
        generateRewardLogChart:function(data,color){
            var rewardUnitType = $(Playbasis.Dashboard.Reward._rewardUnitType).val();
            var rewardBarWidth = Playbasis.Dashboard.Helpers.getBarChartWidthByUnit(rewardUnitType);

            // Plot(flot) didn't know how to display week unit.
            // Put month instead but using a week for mat bar chart width.
            rewardUnitType = (rewardUnitType == 'week')?'month': rewardUnitType;

            var chart = $.plot(this._rewardChart, data, {
                series: {
                    stack: true,
                    bars: { show: true, fill: true, barWidth: rewardBarWidth, align:'center' }
                },
                colors: color,
                xaxis: {
                    mode: "time",
                    minTickSize: [1, rewardUnitType],
                    tickLength: 0
                },
                yaxis: {
                    tickDecimals: 0,
                    min: 0
                },
                grid: {
                    borderWidth:0,
                    hoverable:true,
                    clickable:true
                },
                legend:{
                    show:false
                }
            });

            var previousPoint = null;
            $(this._rewardChart)
                .unbind('plotclick')
                .unbind('plothover')
                .bind("plotclick", function (event, pos, item) {
                    if(item != null){
                        Playbasis.Dashboard.Reward._rewardCompareDate = item.datapoint[0];
                        Playbasis.Dashboard.Reward.getRewardCompareLog();
                    }
                })
                .bind('plothover', function (event, pos, item) {
                    if (item) {
                        if (previousPoint != item.datapoint) {
                            previousPoint = item.datapoint;

                            Playbasis.Dashboard.Helpers.hideTooltip();
                            var x       = item.datapoint[0],
                                y       = item.datapoint[1] - item.datapoint[2],
                                // get total from the generated array
                                total   = Playbasis.Dashboard.Reward._rewardLogChart[x],
                                date    = new Date(item.datapoint[0]);
                            var tooltip = 'Total: '+total+' - ';
                                tooltip += ( item.series.label ? Playbasis.Dashboard.Helpers.capitaliseFirstLetter(item.series.label) + ': ' + y : y) + ' - ';
                                tooltip += Playbasis.Dashboard.Helpers.getMonthShort(date.getMonth()) + ' '+ date.getDate();
                            if (y > 0){
                                Playbasis.Dashboard.Helpers.showTooltip(item.pageX, item.pageY, tooltip, item.series.color);
                            }
                        }
                    } else {
                        Playbasis.Dashboard.Helpers.hideTooltip();
                        previousPoint = null;
                    }
                });
        },
        getRewardCompareLog:function(){

            var compareDate         = $.datepicker.formatDate('yy-mm-dd',new Date(Playbasis.Dashboard.Reward._rewardCompareDate));
            var rewardType          = $(Playbasis.Dashboard.Reward._rewardType).val();
            var rewardCompareType   = $(Playbasis.Dashboard.Reward._rewardCompareType).val();

            var params = {
                'compareDate':compareDate,
                'rewardType':rewardType,
                'rewardCompareType': rewardCompareType
            };

            Playbasis.API.getRewardCompareLog(params,function(data){
                var checkedRewardBadge = Playbasis.Dashboard.Reward.getRewardBadgeChecked()
                var chartData = new Array();
                var chartColor = new Array();

                Playbasis.Dashboard.Reward._rewardCompareLogChart = new Array();

                if(data!=false){
                    if(rewardType == 'badge'){
                        $.each(Playbasis.Dashboard.Reward._rewardBadges,function(index,value){

                            if(checkedRewardBadge.indexOf(value.badge_id) !== -1){

                                var currentChartData = new Array();
                                var comparisonData   = null;
                                var totalData        = null;

                                $.each(data,function(logIndex,logValue){
                                    var currentChartValue       = 0;
                                    var currentChartDataUnit    = new Array();

                                    $.each(logValue,function(logValueIndex,logValueValue){
                                        if(logValueIndex == value.badge_id){
                                            // value contains the dta in format: value_change
                                            // change is teh comparison data in percentage or value
                                            if (logValueValue.toString().indexOf('_') !== -1 && logValueValue.toString().split('_')[1]){
                                                currentChartValue   = parseInt(logValueValue.toString().split('_')[0]);
                                                comparisonData      = logValueValue.toString().split('_')[1];
                                            } else {
                                                currentChartValue   = logValueValue;
                                                comparisonData      = null;
                                            }
                                        }
                                    });

                                    var timeDate = (new Date(logIndex)).getTime();

                                    currentChartDataUnit.push(timeDate);
                                    currentChartDataUnit.push(currentChartValue);

                                    // generate the total of each stacked bar

                                    if (!Playbasis.Dashboard.Reward._rewardCompareLogChart[timeDate]){
                                        Playbasis.Dashboard.Reward._rewardCompareLogChart[timeDate] = currentChartValue;
                                    } else {
                                        Playbasis.Dashboard.Reward._rewardCompareLogChart[timeDate] += currentChartValue;
                                    }

                                    // add extra variable to store the evolution between n-1 and n-2
                                    if (comparisonData){
                                        // transform data as a string to prevent issues with rendering
                                        currentChartDataUnit.push('_'+comparisonData);
                                    }

                                    currentChartData.push(currentChartDataUnit);
                                });

                                chartData.push({
                                    label:value.name,
                                    data:currentChartData
                                });
                                chartColor.push(value.color);
                            }
                        });
                    } else {
                        var currentChartData = new Array();

                        $.each(data,function(logIndex,logValue){
                            var currentChartValue       = 0;
                            var currentChartDataUnit    = new Array();
                            var comparisonData          = null;
                            $.each(logValue,function(logValueIndex,logValueValue){
                                // value contains the dta in format: value_change
                                // change is teh comparison data in percentage or value
                                if (logValueValue.toString().indexOf('_') !== -1 && logValueValue.toString().split('_')[1]){
                                    currentChartValue   = parseInt(logValueValue.toString().split('_')[0]);
                                    comparisonData      = logValueValue.toString().split('_')[1];
                                } else {
                                    currentChartValue   = logValueValue;
                                    comparisonData      = null;
                                }
                            });

                            var timeDate = (new Date(logIndex)).getTime();

                            currentChartDataUnit.push((new Date(logIndex)).getTime());
                            currentChartDataUnit.push(currentChartValue);

                            // generate the total of each stacked bar

                            if (!Playbasis.Dashboard.Reward._rewardCompareLogChart[timeDate]){
                                Playbasis.Dashboard.Reward._rewardCompareLogChart[timeDate] = currentChartValue;
                            } else {
                                Playbasis.Dashboard.Reward._rewardCompareLogChart[timeDate] += currentChartValue;
                            }

                            // add extra variable to store the evolution between n-1 and n-2
                            if (comparisonData){
                                // transform data as a string to prevent issues with rendering
                                currentChartDataUnit.push('_'+comparisonData);
                            }

                            currentChartData.push(currentChartDataUnit);
                        });

                        chartData.push(currentChartData);
                        chartColor.push(ColorSequenceGenerator.createColorSequence(1).getColors());
                    }
                    Playbasis.Dashboard.Reward.generateRewardCompareLogChart(chartData,chartColor);
                }
            });

        },
        generateRewardCompareLogChart:function(data,color){

            var rewardUnitType  = $(Playbasis.Dashboard.Reward._rewardUnitType).val();
            var actionBarWidth  = Playbasis.Dashboard.Helpers.getBarChartWidthByUnit('day');

            $.plot(this._rewardCompareChart, data, {
                series: {
                    stack: true,
                    bars: { show: true, fill: true, barWidth: (actionBarWidth * 0.8), align:'center' }
                },
                colors: color,
                xaxis: {
                    mode: 'time',
                    tickSize: [1, 'day']
                },
                yaxis: {
                    tickDecimals: 0,
                    min: 0
                },
                grid: {
                    borderWidth:0,
                    hoverable:true,
                    minBorderMargin: 10
                },
                legend:{
                    show:false
                }
            });

            var previousPoint = null;
            $(this._rewardCompareChart)
                .unbind('plothover')
                .bind('plothover', function (event, pos, item) {

                    if (item) {
                        if (previousPoint != item.datapoint) {
                            previousPoint = item.datapoint;

                            Playbasis.Dashboard.Helpers.hideTooltip();
                            var x = item.datapoint[0],
                                y = item.datapoint[1] - item.datapoint[2],
                                change  = item.series.data[item.dataIndex][2],
                                // get total form the generated array
                                total   = Playbasis.Dashboard.Reward._rewardCompareLogChart[x];
                            var tooltip = total ? 'Total: ' + total.toString().replace('_','') + ' - ' : '';
                                tooltip += ( item.series.label ? Playbasis.Dashboard.Helpers.capitaliseFirstLetter(item.series.label) + ': ' + y : y);
                                tooltip += change ? ' ( ' + ( change.toString().indexOf('-') !== -1 ? '' : '+') + change.toString().replace('_','')  + ' ) ': '';
                            if (y > 0){
                                Playbasis.Dashboard.Helpers.showTooltip(item.pageX, item.pageY, tooltip, item.series.color);
                            }
                        }
                    } else {
                        Playbasis.Dashboard.Helpers.hideTooltip();
                        previousPoint = null;
                    }
                });
        },
        Init:function(){
            // set date for two-day comparison chart
            var Yesterday = new Date();
            Yesterday.setDate(Yesterday.getDate() - 1);
            this._rewardCompareDate = Yesterday.getTime();
            // bind events
            $(this._rewardDateSubmitButton+','+this._rewardUnitSubmitButton).on('click', function(){
                Playbasis.Dashboard.Reward.getRewardLog();
            });
            $(this._rewardCompareTypeSubmitButton).on('click', function(){
                Playbasis.Dashboard.Reward.getRewardCompareLog();
            });
            // type selection
            $(this._rewardType).change(this.checkRewardType);
            // badges selection
            $(this._rewardBadgePanel).on('click', 'input[type="checkbox"]', function(){
                Playbasis.Dashboard.Reward.getRewardLog();
            });
            // select / un-select all action
            $(this._rewardBadgeAction).on('click', 'input[type="checkbox"]', function(){
                Playbasis.Dashboard.Reward.getRewardLog();
            });

            this.initRewardBadges();
            this.checkRewardType();
            this.selectAllBadges();
        }
    };

    Playbasis.Dashboard.User = {
        _userChart: '#playbasis-user-line-chart',
        _userStart: '#user-start',
        _userEnd: '#user-end',
        _userUnitType: '#user-unit-type',
        _userType: '#user-type',
        _userDateSubmitButton: '#submitUserDate',
        _userUnitSubmitButton: '#submitUserUnit',
        _userTypeSubmitButton: '#submitUserType',
        checkUserLogType: function(){
            if($(Playbasis.Dashboard.User._userType).val() == 'dau'){
                $(Playbasis.Dashboard.User._userUnitType).attr('disabled', 'disabled').val('day');
                $(Playbasis.Dashboard.User._userUnitSubmitButton).attr('disabled', 'disabled');
            }else{
                $(Playbasis.Dashboard.User._userUnitType).removeAttr('disabled');
                $(Playbasis.Dashboard.User._userUnitSubmitButton).removeAttr('disabled');
            }
        },
        getUserLog:function(){

            var startDate = $(Playbasis.Dashboard.User._userStart).val();
            var endDate = $(Playbasis.Dashboard.User._userEnd).val();
            var userUnitType = $(Playbasis.Dashboard.User._userUnitType).val();
            var userType = $(Playbasis.Dashboard.User._userType).val();

            var params = {
                'startDate':startDate,
                'endDate':endDate,
                'userUnitType':userUnitType
            };

            Playbasis.API.getUserLog(userType,params,function(data){
                var chartData = new Array();
                var chartColor = new Array();

                if(data!=false){
                    var currentChartData = new Array();

                    $.each(data,function(logIndex,logValue){
                        var currentChartValue = 0;
                        var currentChartDataUnit = new Array();

                        $.each(logValue,function(logValueIndex,logValueValue){
                            currentChartValue = logValueValue.count;

                            currentChartDataUnit.push((new Date(logValueIndex)).getTime());
                            currentChartDataUnit.push(currentChartValue);

                            currentChartData.push(currentChartDataUnit);
                        });
                    });

                    chartData.push(currentChartData);
                    chartColor.push(ColorSequenceGenerator.createColorSequence(1).getColors());

                    Playbasis.Dashboard.User.bindUserLogChart(chartData,chartColor);
                }
            });
        },
        bindUserLogChart:function(data,color){
            var userUnitType = $(Playbasis.Dashboard.User._userUnitType).val();
            var userBarWidth = Playbasis.Dashboard.Helpers.getBarChartWidthByUnit(userUnitType);

            // Plot(flot) didn't know how to display week unit.
            // Put month instead but using a week for mat bar chart width.
            userUnitType = (userUnitType == 'week')?'month':userUnitType;

            $.plot(this._userChart, data, {
                series: {
                    stack: true,
                    lines: {show:true, fill: true },
                    points: { show: true, fill: false }
                },
                colors: color,
                xaxis: {
                    mode: "time",
                    minTickSize: [1, userUnitType],
                    tickLength: 0
                },
                yaxis: {
                    min: 0
                },
                grid: {
                    borderWidth:0,
                    hoverable:true
                }
            });

            var previousPoint = null;
            $(this._userChart)
                .unbind('plothover')
                .bind('plothover', function (event, pos, item) {
                    if (item) {
                        if (previousPoint != item.datapoint) {
                            previousPoint = item.datapoint;

                            Playbasis.Dashboard.Helpers.hideTooltip();
                            var x       = item.datapoint[0],
                                y       = item.datapoint[1] - item.datapoint[2]
                                date    = new Date(item.datapoint[0]),
                                tooltip = y + ' - ' + Playbasis.Dashboard.Helpers.getMonthShort(date.getMonth()) + ' '+ date.getDate();

                            Playbasis.Dashboard.Helpers.showTooltip(item.pageX, item.pageY, tooltip, item.series.color);
                        }
                    } else {
                        Playbasis.Dashboard.Helpers.hideTooltip();
                        previousPoint = null;
                    }
                });
        },
        Init:function(){
            // bind events
            $(this._userDateSubmitButton+','+this._userUnitSubmitButton+','+this._userTypeSubmitButton).on('click', function(){
                Playbasis.Dashboard.User.getUserLog();
            });
            $(this._userType).on('change', function(){
                Playbasis.Dashboard.User.checkUserLogType();
            });
            // initialize the chart
            this.getUserLog();
        },
    };

    Playbasis.API = {
        getActionTypes:function(callback){
            this._Call(baseUrlPath + '/insight/getAction',null,callback);
        },
        getActionLog:function(params,callback){
            this._Call(baseUrlPath + '/insight/getActionLog',params,callback);
        },
        getActionCompareLog:function(params,callback){
            this._Call(baseUrlPath + '/insight/getActionCompareLog',params,callback);
        },
        getRewardBadges:function(callback){
            this._Call(baseUrlPath + '/insight/getRewardBadge',null,callback);
        },
        getRewardBadgeLog:function(params,callback){
            this._Call(baseUrlPath + '/insight/getRewardBadgeLog',params,callback);
        },
        getRewardPointLog:function(params,callback){
            this._Call(baseUrlPath + '/insight/getRewardPointLog',params,callback);
        },
        getRewardExpLog:function(params,callback){
            this._Call(baseUrlPath + '/insight/getRewardExpLog',params,callback);
        },
        getRewardLevelLog:function(params,callback){
            this._Call(baseUrlPath + '/insight/getRewardLevelLog',params,callback);
        },
        getRewardCompareLog:function(params,callback){
            this._Call(baseUrlPath + '/insight/getRewardCompareLog',params,callback);
        },
        getUserRegLog:function(params,callback){
            this._Call(baseUrlPath + '/insight/getUserRegLog',params,callback);
        },
        getUserDAULog:function(params,callback){
            this._Call(baseUrlPath + '/insight/getUserDAULog',params,callback);
        },
        getUserMAULog:function(params,callback){
            this._Call(baseUrlPath + '/insight/getUserMAULog',params,callback);
        },
        getRewardLog:function(rewardType,params,callback){

            switch (rewardType){
                case 'badge':
                    this.getRewardBadgeLog(params,callback);
                    break;
                case 'level':
                    this.getRewardLevelLog(params,callback);
                    break;
                case 'exp':
                    this.getRewardExpLog(params,callback);
                    break;
                case 'point':
                    this.getRewardPointLog(params,callback);
                    break;
            }
        },
        getUserLog:function(userType,params,callback){

            switch (userType){
                case 'new':
                    this.getUserRegLog(params,callback);
                    break;
                case 'dau':
                    this.getUserDAULog(params,callback);
                    break;
                case 'mau':
                    this.getUserMAULog(params,callback);
                    break;
            }
        },
        Setup: function(){
            $.ajaxSetup({
                cache: false,
                beforeSend: function(){
                    var $panel = $('.dashboard-panel[data-current-panel="true"]');
                    Playbasis.API.ShowLoadingAnimation($panel.find('.box-content'), null);
                }
            });
            $(document).ajaxComplete(function(event, xhr, settings){
                $('.box-content').find('.overlay-loading').not('.initial-loading').remove();
            });
        },
        ShowLoadingAnimation: function(el, Klass){
            var $overlay = $('<div />', {'class':'overlay-loading ' + Klass});
            el.append($overlay);
        },
        _Call:function(url,params,callback){
            $.getJSON(url,params,function(data){
                if(data != null && data.success == true){
                    callback(data.response);
                }else{
                    callback(false);
                }
            });
        }
    };

    Playbasis.Init = (function(){
        Playbasis.Dashboard.Init();
    })();
});