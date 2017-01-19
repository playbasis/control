  'use strict';

// http://stackoverflow.com/questions/439463/how-to-get-get-and-post-variables-with-jquery
function getQueryParams(qs) {
    qs = qs.split("+").join(" ");
    var params = {},
        tokens,
        re = /[?&]?([^=]+)=([^&]*)/g;

    while (tokens = re.exec(qs)) {
        params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
    }

    return params;
}

var before_debug =true;

jQuery(document).ready( function($) {

    if(window.location.hash) {
        var hash = window.location.hash.substring(1);
        switch(hash)
        {
        case 'register':
            $('#tab-page-nav a[href=#register]').tab('show')
          break;
        case 'forgotpassword':
            $('#tab-page-nav a[href=#forgotpassword]').tab('show')
          break;
        default:
        }
    } else {

    }

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
      
      window.location.hash = e.target.hash;
    })

  // Assign form validation errors
  $.validator.messages.required = 'Required';
  $.validator.messages.email = 'Invalid Email';

  var message_error_default = 'Something\'s wrong, please try again';

  //==== Validate Register form=======//
  $('#register_form').validate({
    submitHandler: function(form) {

      if( $(form).find('input[name=mailing-list]:checked').length > 0 ){
          $.ajax({
            type:'get',
            url: 'http://playbasis.us6.list-manage2.com/subscribe/post-json?u=671d09f1f9e3b028388b39e50&amp;id=3df5213bd0&amp;c=?',
            data: {
                'EMAIL' : $(form).find('input[name=email]').val(),
                'FNAME' : $(form).find('input[name=firstname]').val(),
                'LNAME' : $(form).find('input[name=lastname]').val(),
                'MMERGE3' : $(form).find('input[name=company_name]').val()
            }
          })
          .done(function(data) {
            console.log(data);
          });

      }

      var $_GET = getQueryParams(document.location.search);
      $.ajax({
        type:'post',
        url: baseUrlPath+"user/regis"+('plan' in $_GET ? '?plan='+$_GET['plan'] : ''),
        data: $(form).serialize()+'&format=json',
          dataType: 'json'
      })
      .done(function(data) {
            if(before_debug)console.log(data);
            if(data.message){
                var messageAlert = data.message;
            }else{
                var messageAlert = message_error_default;
            }
            
            if(data.response == 'fail'){
               $('.registration-register').pbAlert({
                content: data.message
              });
            }else{
                 $('.registration-register').pbAlert({
                  content: 'Register success!'
                });
                setTimeout( function() {
                    window.location = baseUrlPath+'user/signup_finish?i='+data.data;
                }, 5000);
            }
      })
      .fail(function(data) {
          $('.registration-register').pbAlert({
              content: message_error_default
          });
      })
      .always(function(data){
          $(form).find('input').attr('disabled', false);
      })

      $(form).find('input').attr('disabled', true);
      return false;
    }
  });


//==== Validate Login form=======//
  $('#login_form').validate({
    submitHandler: function(form) {
      $.ajax({
        type:'post',
        url: baseUrlPath+"login",
        data: $(form).serialize()+'&format=json',
        cache: false,
        dataType: 'json'
      })
      .done(function(data) {
            if(before_debug)console.log(data);
            if(data.message){
                var messageAlert = data.message;
            }else{
                var messageAlert = message_error_default;
            }
            if(data.status == 'error'){
               $('.registration-login').pbAlert({
                content: messageAlert
              });
            }else{
               window.location = baseUrlPath;
            }
      })
      .fail(function(data) {
          $('.registration-login').pbAlert({
              content: message_error_default
          });
      })
      .always(function(data){
          $(form).find('input').attr('disabled', false);
      })

      $(form).find('input').attr('disabled', true);
      return false;

    }
  });

  //==== Validate Reset Password form=======//
  $('#resetpassword_form').validate({
    rules: {
        password: {
            minlength: 8
        },
        confirm_password: {
             equalTo : "#reset-password"
        }
    },
    submitHandler: function(form) {
      $.ajax({
        type:'post',
        url: baseUrlPath+"reset_password",
        data: $(form).serialize()+'&format=json',
        cache: false,
        dataType: 'json'
      })
      .done(function(data) {
            if(before_debug)console.log(data);
            if(data.message){
                var messageAlert = data.message;
            }else{
                var messageAlert = message_error_default;
            }
            if(data.status == 'error'){
               $('.registration-resetpassword').pbAlert({
                content: messageAlert
              });
            }else{
                $('.registration-resetpassword').pbAlert({
                    content: messageAlert
                });
                setTimeout( function() {
                    window.location = baseUrlPath;
                }, 5000);
            }
      })
      .fail(function(data) {
          $('.registration-resetpassword').pbAlert({
              content: message_error_default
          });
      })
      .always(function(data){
          $(form).find('input').attr('disabled', false);
      })

      $(form).find('input').attr('disabled', true);
      return false;

    }
  });

    //==== Validate Reset Player Password form=======//
    $('#playerresetpassword_form').validate({
        rules: {
            confirm_password: {
                equalTo: "#reset-password"
            }
        },
        submitHandler: function (form) {
            $.ajax({
                type: 'post',
                url: baseUrlPath + "player/password/reset/" + $('#password-recovery-code').val(),
                data: $(form).serialize() + '&format=json',
                cache: false,
                dataType: 'json'
            })
                .done(function (data) {
                    if (before_debug)console.log(data);
                    if (data.message) {
                        var messageAlert = data.message;
                    } else {
                        var messageAlert = message_error_default;
                    }
                    if (data.status == 'error') {
                        $('.registration-resetpassword').pbAlert({
                            content: messageAlert,
                            type: 'error'
                        });
                    } else {
                        $('.registration-resetpassword').pbAlert({
                            content: messageAlert,
                            type: 'success'
                        });
                        $('#playerresetpassword_form').find('input').attr('disabled',true);
                        setTimeout(function () {
                            window.location = baseUrlPath + "player/password/reset/completed";
                        }, 5000);
                    }
                })
                .fail(function (data) {
                    $('.registration-resetpassword').pbAlert({
                        content: message_error_default
                    });
                })
                .always(function (data) {
                    $(form).find('input').attr('disabled', false);
                });

            $(form).find('input').attr('disabled', true);
            return false;

        }
    });

  //==== Validate Forgot Password form=======//
  $('#forgotpassword_form').validate({
    submitHandler: function(form) {
      $.ajax({
        type:'post',
        url: baseUrlPath+"user/forgot_password",
        data: $(form).serialize()+'&format=json',
        cache: false,
        dataType: 'json'
      })
      .done(function(data) {
            if(before_debug)console.log(data);
            if(data.message){
                var messageAlert = data.message;
            }else{
                var messageAlert = message_error_default;
            }
            if(data.status == 'error'){
                $('.registration-forgotpassword').pbAlert({
                    content: messageAlert
                });
            }else{
                $('.registration-forgotpassword').pbAlert({
                    content: messageAlert
                });
                setTimeout( function() {
                  window.location = baseUrlPath;
                }, 5000);
            }
      })
      .fail(function(data) {
          $('.registration-forgotpassword').pbAlert({
              content: message_error_default
          });
      })
      .always(function(data){
          $(form).find('input').attr('disabled', false);
      })

      $(form).find('input').attr('disabled', true);
      return false;

    }
  });


  //==== Validate Complete Password form=======//
  $('#completeprofile_form').validate({
    rules: {
        password: {
            minlength: 8
        },
        confirm_password: {
             equalTo : "#completeprofile-password"
        }
    },
    submitHandler: function(form) {
      $.ajax({
        type:'post',
        url: baseUrlPath+"account/update_password",
        data: $(form).serialize()+'&format=json',
        cache: false,
        dataType: 'json'
      })
      .done(function(data) {
            if(before_debug)console.log(data);
            if(data.message){
                var messageAlert = data.message;
            }else{
                var messageAlert = message_error_default;
            }
            if(data.status == 'error'){
               $('.registration-completeprofile').pbAlert({
                content: messageAlert
              });
            }else{
               window.location = baseUrlPath;
            }
      })
      .fail(function(data) {
          $('.registration-completeprofile').pbAlert({
              content: message_error_default
          });
      })
      .always(function(data){
          $(form).find('input').attr('disabled', false);
      })

      $(form).find('input').attr('disabled', true);
      return false;

    }
  });

    //==== Validate Referral form=======//
    $('#referral_form').validate({
        submitHandler: function(form) {

            var $_GET = getQueryParams(document.location.search);
            $.ajax({
                type:'post',
                url: baseUrlPath+"referral/"+$('#referral-code').val(),
                data: $(form).serialize()+'&format=json',
                dataType: 'json'
            })
                .done(function(data) {
                    if(before_debug)console.log(data);
                    if(data.message){
                        var messageAlert = data.message;
                    }else{
                        var messageAlert = message_error_default;
                    }

                    if(data.status == 'fail'){
                        $('.registration-register').pbAlert({
                            content: data.message
                        });
                    }else{
                        $('.registration-register').pbAlert({
                            content: 'Referral success!'
                        });
                    }
                })
                .fail(function(data) {
                    $('.registration-register').pbAlert({
                        content: message_error_default
                    });
                })
                .always(function(data){
                    $(form).find('input').attr('disabled', false);
                })

            $(form).find('input').attr('disabled', true);
            return false;
        }
    });

    //==== Merchant Login form=======//
    $('#merchant_form').validate({
        submitHandler: function(form) {

            var $_GET = getQueryParams(document.location.search);
            $.ajax({
                type:'post',
                url: baseUrlPath+"merchant_verify",
                data: $(form).serialize()+'&format=json',
                dataType: 'json'
            })
                .done(function(data) {
                    if(before_debug)console.log(data);
                    if(data.message){
                        var messageAlert = data.message;
                    }else{
                        var messageAlert = message_error_default;
                    }

                    if(data.status == 'fail'){
                        $('.registration-login').pbAlert({
                            content: data.message
                        });
                    }else{
                        $('.registration-login').pbAlert({
                            content: 'Login success!'
                        });
                        setTimeout(function() {
                            location.reload(true);
                        }, 800);
                    }
                })
                .fail(function(data) {
                    $('.registration-login').pbAlert({
                        content: message_error_default
                    });
                })
                .always(function(data){
                    $(form).find('input').attr('disabled', false);
                })

            $(form).find('input').attr('disabled', true);
            return false;
        }
    });

    //==== Coupon Validation form=======//
    $('#coupon_form').validate({
        submitHandler: function(form) {

            $('#coupon').removeClass('success');
            var $_GET = getQueryParams(document.location.search);
            $.ajax({
                type:'post',
                url: baseUrlPath+"merchant_verify",
                data: $(form).serialize()+'&format=json',
                dataType: 'json'
            })
                .done(function(data) {
                    if(before_debug)console.log(data);
                    if(data.message){
                        var messageAlert = data.message;
                    }else{
                        var messageAlert = message_error_default;
                    }

                    if(data.status == 'fail'){
                        if (data.login) location.reload(true); // detect session has expired
                        $('.registration-login').pbAlert({
                            content: data.message
                        });
                        $('#coupon').addClass('error');
                        var m = 'Invalid Coupon' + (data.at ? ': It has been used already at ' + data.at + ' (' + data.when + ')' : '');
                        $('#coupon').after('<label for="coupon" class="error">'+m+'</label>');
                    }else{
                        $('.registration-login').pbAlert({
                            content: data.message
                        });
                        $('#coupon').addClass('success');
                    }
                })
                .fail(function(data) {
                    $('.registration-login').pbAlert({
                        content: message_error_default
                    });
                })
                .always(function(data){
                    $(form).find('input').attr('disabled', false);
                })

            $(form).find('input').attr('disabled', true);
            return false;
        }
    });

    //==== Merchant Logout link =======//
    $(document).ready(function() {
        $("#merchant-logout a").click(function() {
            var $_GET = getQueryParams(document.location.search);
            $.ajax({
                type:'post',
                url: baseUrlPath+"merchant_logout",
                data: 'format=json',
                dataType: 'json'
            })
                .done(function(data) {
                    if(before_debug)console.log(data);
                    if(data.message){
                        var messageAlert = data.message;
                    }else{
                        var messageAlert = message_error_default;
                    }

                    if(data.status == 'fail'){
                        $('.registration-login').pbAlert({
                            content: data.message
                        });
                    }else{
                        $('.registration-login').pbAlert({
                            content: 'Logout successfully!'
                        });
                        setTimeout(function() {
                            location.reload(true);
                        }, 800);
                    }
                })
                .fail(function(data) {
                    $('.registration-login').pbAlert({
                        content: message_error_default
                    });
                })

            return false;
        });
    });

  //==== Animate form=======//
  $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
    var current_id = $(e.target).attr('href');
    var $obj = $(current_id+' main.pbr-main');
    $obj.css({'opacity':0.0,'margin-top':"-155px"});
    $obj.stop().animate({marginTop: -160, opacity:1}, { queue:false, duration:300, easing: 'easeOutQuad' })
  })

});
