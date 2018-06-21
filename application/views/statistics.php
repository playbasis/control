<div id="content" class="span10">
    <div class="row-fluid">
        <div class="box span4 noMargin" onTablet="span12" onDesktop="span4">
            <div class="box-header">
                <h2><i class="icon-magic"></i><span class="break"></span>Action<span class="break"></span>
                <div id="action_sort" class="btn-group" data-toggle="buttons-radio" >
                    <button rel="day" type="button" class="btn options btn-mini">Daily</button>
                    <button rel="weekly" type="button" class="btn options btn-mini active">Weekly</button>
                    <button rel="month" type="button" class="btn options btn-mini">Monthly</button>
                </div><span class="break"></span><button href="#" type="button" id="download_action_graph"><i class="icon-download"></i></button>
                </h2>
            </div>

            <div id="chart_content" class="box-content">
                <canvas id="myChart" width="400" height="400"></canvas>
            </div>
        </div>

        <div class="box span4 noMargin" onTablet="span12" onDesktop="span4">
            <div class="box-header">
                <h2><i class="icon-gift"></i><span class="break"></span>Goods SuperDeal<span class="break"></span>
                <div id="goods_superdeal_sort" class="btn-group" data-toggle="buttons-radio" >
                    <button rel="day" type="button" class="btn options btn-mini">Daily</button>
                    <button rel="weekly" type="button" class="btn options btn-mini active">Weekly</button>
                    <button rel="month" type="button" class="btn options btn-mini">Monthly</button>
                </div><span class="break"></span><button type="button" id="download_goods_superdeal_graph"><i class="icon-download"></i></button>
                </h2>
            </div>

            <div id="chart_content1" class="box-content">
                <canvas id="myChart1" width="400" height="400"></canvas>
            </div>
        </div>
        <div class="box span4 noMargin" onTablet="span12" onDesktop="span4">
            <div class="box-header">
                <h2><i class="icon-certificate"></i><span class="break"></span>Goods Monthly<span class="break"></span>
                <div id="goods_monthly_sort" class="btn-group" data-toggle="buttons-radio" >
                    <button rel="day" type="button" class="btn options btn-mini">Daily</button>
                    <button rel="weekly" type="button" class="btn options btn-mini active">Weekly</button>
                    <button rel="month" type="button" class="btn options btn-mini">Monthly</button>
                </div><span class="break"></span><button type="button" id="download_goods_monthly_graph"><i class="icon-download"></i></button>
                </h2>
            </div>

            <div id="chart_content2" class="box-content">
                <canvas id="myChart2" width="400" height="400"></canvas>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="box span4 noMargin" onTablet="span12" onDesktop="span4">
            <div class="box-header">
                <h2><i class="icon-magic"></i><span class="break"></span>Badge<span class="break"></span>
                    <div id="badge_sort" class="btn-group" data-toggle="buttons-radio" >
                        <button rel="day" type="button" class="btn options btn-mini">Daily</button>
                        <button rel="weekly" type="button" class="btn options btn-mini active">Weekly</button>
                        <button rel="month" type="button" class="btn options btn-mini">Monthly</button>
                    </div><span class="break"></span><button type="button" id="download_badge_graph"><i class="icon-download"></i></button>
                </h2>
            </div>

            <div id="chart_content3" class="box-content">
                <canvas id="myChart3" width="400" height="400"></canvas>
            </div>
        </div>

        <div class="box span4 noMargin" onTablet="span12" onDesktop="span4">
            <div class="box-header">
                <h2><i class="icon-gift"></i><span class="break"></span>Player Registered<span class="break"></span>
                    <div id="register_sort" class="btn-group" data-toggle="buttons-radio" >
                        <button rel="day" type="button" class="btn options btn-mini">Daily</button>
                        <button rel="weekly" type="button" class="btn options btn-mini active">Weekly</button>
                        <button rel="month" type="button" class="btn options btn-mini">Monthly</button>
                    </div><span class="break"></span><button type="button" id="download_register_graph"><i class="icon-download"></i></button>
                </h2>
            </div>

            <div id="chart_content4" class="box-content">
                <canvas id="myChart4" width="400" height="400"></canvas>
            </div>
        </div>
        <div class="box span4 noMargin" onTablet="span12" onDesktop="span4">
            <div class="box-header">
                <h2><i class="icon-certificate"></i><span class="break"></span>MGM<span class="break"></span>
                    <div id="mgm_sort" class="btn-group" data-toggle="buttons-radio" >
                        <button rel="day" type="button" class="btn options btn-mini">Daily</button>
                        <button rel="weekly" type="button" class="btn options btn-mini active">Weekly</button>
                        <button rel="month" type="button" class="btn options btn-mini">Monthly</button>
                    </div><span class="break"></span><button type="button" id="download_mgm_graph"><i class="icon-download"></i></button>
                </h2>
            </div>

            <div id="chart_content5" class="box-content">
                <canvas id="myChart5" width="400" height="400"></canvas>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="box span4 noMargin" onTablet="span12" onDesktop="span4">
            <div class="box-header">
                <h2><i class="icon-certificate"></i><span class="break"></span>Action Value<span class="break"></span>
                    <div id="action_value_sort" class="btn-group" data-toggle="buttons-radio" >
                        <button rel="day" type="button" class="btn options btn-mini">Daily</button>
                        <button rel="weekly" type="button" class="btn options btn-mini active">Weekly</button>
                        <button rel="month" type="button" class="btn options btn-mini">Monthly</button>
                    </div><span class="break"></span><button type="button" id="download_action_value_graph"><i class="icon-download"></i></button>
                </h2>
            </div>
            <div id="chart_content6" class="box-content">
                <canvas id="myChart6" width="400" height="400"></canvas>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript" src="<?php echo base_url();?>javascript/custom/Chart.min.js"></script>
<script>
    var action_sort = $('#action_sort button').filter('.active').attr('rel');
    var goods_sort = $('#goods_sort button').filter('.active').attr('rel');
    var badge_sort = $('#badge_sort button').filter('.active').attr('rel');
    $.ajax({
        url: baseUrlPath+'statistics/getActionData',
        data: {},
        context: document.body
    }).done(function(data){
        var myArray = JSON.parse(data);
        var ctx = document.getElementById("myChart").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: myArray.label,
                datasets: myArray.data
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
        });
    });

    $('#action_sort button').live('click', function(){
        $('#myChart').remove(); // this is my <canvas> element
        $('#chart_content').append('<canvas id="myChart" width="400" height="400"></canvas>');
        var e = $(this).attr('rel');
        $.ajax({
            url: baseUrlPath+'statistics/getActionData/'+ e,
            data: {},
            context: document.body
        }).done(function(data){
            var myArray = JSON.parse(data);
            var ctx = document.getElementById("myChart").getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: myArray.label,
                    datasets: myArray.data
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    }
                }
            });
        });
    });

    $('#download_action_graph').live('click', function(){
        var canvas = document.getElementById("myChart");
        var img    = canvas.toDataURL("image/png");
        link = document.createElement('a');
        link.setAttribute('href', img);
        link.setAttribute('download', 'action_graph');
        link.click();
    });

    $.ajax({
        url: baseUrlPath+'statistics/getActionAmountData',
        data: {},
        context: document.body
    }).done(function(data){
        var myArray = JSON.parse(data);
        var ctx = document.getElementById("myChart6").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: myArray.label,
                datasets: myArray.data
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
        });
    });

    $('#action_value_sort button').live('click', function(){
        $('#myChart6').remove(); // this is my <canvas> element
        $('#chart_content6').append('<canvas id="myChart6" width="400" height="400"></canvas>');
        var e = $(this).attr('rel');
        $.ajax({
            url: baseUrlPath+'statistics/getActionAmountData/'+ e,
            data: {},
            context: document.body
        }).done(function(data){
            var myArray = JSON.parse(data);
            var ctx = document.getElementById("myChart6").getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: myArray.label,
                    datasets: myArray.data
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    }
                }
            });
        });
    });

    $('#download_action_value_graph').live('click', function(){
        var canvas = document.getElementById("myChart6");
        var img    = canvas.toDataURL("image/png");
        link = document.createElement('a');
        link.setAttribute('href', img);
        link.setAttribute('download', 'action_value_graph');
        link.click();
    });

    $.ajax({
        url: baseUrlPath+'statistics/getGoodsSuperData',
        data: {},
        context: document.body
    }).done(function(data){
        var myArray = JSON.parse(data);
        var ctx = document.getElementById("myChart1").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: myArray.label,
                datasets: myArray.data
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
        });
    });

    $('#goods_superdeal_sort button').live('click', function(){
        $('#myChart1').remove(); // this is my <canvas> element
        $('#chart_content1').append('<canvas id="myChart1" width="400" height="400"></canvas>');
        var e = $(this).attr('rel');
        $.ajax({
            url: baseUrlPath+'statistics/getGoodsSuperData/'+ e,
            data: {},
            context: document.body
        }).done(function(data){
            var myArray = JSON.parse(data);
            var ctx = document.getElementById("myChart1").getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: myArray.label,
                    datasets: myArray.data
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    }
                }
            });
        });
    });

    $('#download_goods_superdeal_graph').live('click', function(){
        var canvas = document.getElementById("myChart1");
        var img    = canvas.toDataURL("image/png");
        link = document.createElement('a');
        link.setAttribute('href', img);
        link.setAttribute('download', 'goods_superdeal_graph');
        link.click();
    });

    $.ajax({
        url: baseUrlPath+'statistics/getGoodsMonthlyData',
        data: {},
        context: document.body
    }).done(function(data){
        var myArray = JSON.parse(data);
        var ctx = document.getElementById("myChart2").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: myArray.label,
                datasets: myArray.data
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
        });
    });

    $('#goods_monthly_sort button').live('click', function(){
        $('#myChart2').remove(); // this is my <canvas> element
        $('#chart_content2').append('<canvas id="myChart2" width="400" height="400"></canvas>');
        var e = $(this).attr('rel');
        $.ajax({
            url: baseUrlPath+'statistics/getGoodsMonthlyData/'+ e,
            data: {},
            context: document.body
        }).done(function(data){
            var myArray = JSON.parse(data);
            var ctx = document.getElementById("myChart2").getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: myArray.label,
                    datasets: myArray.data
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    }
                }
            });
        });
    });

    $('#download_goods_monthly_graph').live('click', function(){
        var canvas = document.getElementById("myChart2");
        var img    = canvas.toDataURL("image/png");
        link = document.createElement('a');
        link.setAttribute('href', img);
        link.setAttribute('download', 'goods_monthly_graph');
        link.click();
    });

    $.ajax({
        url: baseUrlPath+'statistics/getBadgeData',
        data: {},
        context: document.body
    }).done(function(data){
        var myArray = JSON.parse(data);
        var ctx = document.getElementById("myChart3").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: myArray.label,
                datasets: myArray.data
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
        });
    });

    $('#badge_sort button').live('click', function(){
        $('#myChart3').remove(); // this is my <canvas> element
        $('#chart_content3').append('<canvas id="myChart3" width="400" height="400"></canvas>');
        var e = $(this).attr('rel');
        $.ajax({
            url: baseUrlPath+'statistics/getBadgeData/'+ e,
            data: {},
            context: document.body
        }).done(function(data){
            var myArray = JSON.parse(data);
            var ctx = document.getElementById("myChart3").getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: myArray.label,
                    datasets: myArray.data
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    }
                }
            });
        });
    });

    $('#download_badge_graph').live('click', function(){
        var canvas = document.getElementById("myChart3");
        var img    = canvas.toDataURL("image/png");
        link = document.createElement('a');
        link.setAttribute('href', img);
        link.setAttribute('download', 'badge_graph');
        link.click();
    });

    $.ajax({
        url: baseUrlPath+'statistics/getRegisterData',
        data: {},
        context: document.body
    }).done(function(data){
        var myArray = JSON.parse(data);
        var ctx = document.getElementById("myChart4").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: myArray.label,
                datasets: myArray.data
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
        });
    });

    $('#register_sort button').live('click', function(){
        $('#myChart4').remove(); // this is my <canvas> element
        $('#chart_content4').append('<canvas id="myChart4" width="400" height="400"></canvas>');
        var e = $(this).attr('rel');
        $.ajax({
            url: baseUrlPath+'statistics/getRegisterData/'+ e,
            data: {},
            context: document.body
        }).done(function(data){
            var myArray = JSON.parse(data);
            var ctx = document.getElementById("myChart4").getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: myArray.label,
                    datasets: myArray.data
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    }
                }
            });
        });
    });

    $('#download_register_graph').live('click', function(){
        var canvas = document.getElementById("myChart4");
        var img    = canvas.toDataURL("image/png");
        link = document.createElement('a');
        link.setAttribute('href', img);
        link.setAttribute('download', 'register_graph');
        link.click();
    });

    $.ajax({
        url: baseUrlPath+'statistics/getMGMData',
        data: {},
        context: document.body
    }).done(function(data){
        var myArray = JSON.parse(data);
        var ctx = document.getElementById("myChart5").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: myArray.label,
                datasets: myArray.data
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
        });
    });

    $('#mgm_sort button').live('click', function(){
        $('#myChart5').remove(); // this is my <canvas> element
        $('#chart_content5').append('<canvas id="myChart5" width="400" height="400"></canvas>');
        var e = $(this).attr('rel');
        $.ajax({
            url: baseUrlPath+'statistics/getMGMData/'+ e,
            data: {},
            context: document.body
        }).done(function(data){
            var myArray = JSON.parse(data);
            var ctx = document.getElementById("myChart5").getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: myArray.label,
                    datasets: myArray.data
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    }
                }
            });
        });
    });

    $('#download_mgm_graph').live('click', function(){
        var canvas = document.getElementById("myChart5");
        var img    = canvas.toDataURL("image/png");
        link = document.createElement('a');
        link.setAttribute('href', img);
        link.setAttribute('download', 'mgm_graph');
        link.click();
    });

</script>
<!-- end : override - Leader board item -css   -->
