(function($) {
  var body = document.getElementsByTagName('body')[0];

  var s = document.createElement('script');
      s.setAttribute('type','text/javascript');
      s.setAttribute('src', 'view/javascript/player/jquery.nivo.slider.pack.js');
      body.appendChild(s);

  var cs = document.createElement('script');
      cs.setAttribute('type','text/javascript');
      cs.setAttribute('src', 'view/javascript/player/jquery.cookies.2.2.0.js');
      body.appendChild(cs);

  // $(document).ready(function() {
  //   setTimeout(function() {
  //     if(!$.cookies.get('rule_guide')) {
  //       $('#rule_guide').modal('show');
  //       $('#rule_guide #slider').nivoSlider().css('height','300px');
  //     }
  //   }
  //   ,1000);
  // });

})(jQuery)