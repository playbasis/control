  'use strict';

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
            url: '//playbasis.us6.list-manage2.com/subscribe/post-json?u=671d09f1f9e3b028388b39e50&amp;id=3df5213bd0&amp;c=?',
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

      $.ajax({
        type:'post',
        url: baseUrlPath+"user/regis",
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
        url: baseUrlPath+"account/update_profile",
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


  //==== Animate form=======//
  $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
    var current_id = $(e.target).attr('href');
    var $obj = $(current_id+' main.pbr-main');
    $obj.css({'opacity':0.0,'margin-top':"-155px"});
    $obj.stop().animate({marginTop: -160, opacity:1}, { queue:false, duration:300, easing: 'easeOutQuad' })
  })

});
