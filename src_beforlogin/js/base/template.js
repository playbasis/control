this["template"] = this["template"] || {};

this["template"]["src_beforlogin/handlebars/login"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression;


  buffer += "<div class=\"jumbotron text-center\">\n    <h1>Login</h1>\n\n    <p>";
  if (stack1 = helpers.message) { stack1 = stack1.call(depth0, {hash:{},data:data}); }
  else { stack1 = (depth0 && depth0.message); stack1 = typeof stack1 === functionType ? stack1.call(depth0, {hash:{},data:data}) : stack1; }
  buffer += escapeExpression(stack1)
    + "</p>\n</div>";
  return buffer;
  });

this["template"]["src_beforlogin/handlebars/register"] = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  


  return "<header class=\"pbr-header\" role=\"banner\">\n  <div class=\"row\" role=\"navigation\">\n\n              <a href=\"http://www.playbasis.com\">\n                <svg title=\"Playbasis\" class=\"pbr-header-logo\">\n                  <use xlink:href=\"<?php echo base_url();?>image/beforelogin/logo.svg#logo\"></use>\n                </svg>\n              </a>\n\n  </div>\n\n  <div class=\"row\">\n\n    <h1>\n      <small>Get started with Gamification today!</small>\n      Sign up for Playbasis\n    </h1>\n\n    <!-- <div class=\"plan\">\n    <div class=\"plan-type\">Professional Plan</div>\n    <div class=\"plan-price\">$99 / month</div>\n    </div> -->\n\n  </div>\n</header><!-- /header -->\n\n<main class=\"pbr-main\" role=\"main\">\n  <div class=\"row\">\n    <div id=\"registration\">\n      <div class=\"registration-form-bg\"></div>\n      <div class=\"registration-form\">\n        <form class=\"pbf-form\">\n          <fieldset>\n\n            <legend>Basic User Information</legend>\n\n            <div class=\"pbf-field-group pbf-half\">\n              <label for=\"first-name\">First Name <span>*</span></label>\n              <input type=\"text\" name=\"firstname\" placeholder=\"First Name\" id=\"first-name\" required>\n            </div>\n\n           <div class=\"pbf-field-group pbf-half\">\n              <label for=\"last-name\">Last Name <span>*</span></label>\n              <input type=\"text\" name=\"lastname\" placeholder=\"Last Name\" id=\"last-name\" required>\n            </div>\n\n            <div class=\"pbf-field-group\">\n              <label for=\"email-address\">Email Address <span>*</span></label>\n              <input type=\"email\" name=\"email\" placeholder=\"Email Address\" id=\"email-address\" required>\n            </div>\n\n           <div class=\"pbf-field-group\">\n              <label for=\"company-name\">Company Name</label>\n              <input type=\"text\" name=\"company_name\" placeholder=\"Company Name\" id=\"company-name\">\n            </div>\n\n          </fieldset>\n\n          <hr>\n\n          <fieldset>\n\n            <div class=\"pbf-field-group pbf-half\">\n              <input type=\"submit\" value=\"Take me to my dashboard →\">\n            </div>\n\n            <div class=\"pbf-field-group pbf-half pbf-checked\">\n              <input type=\"checkbox\" name=\"mailing-list\" id=\"mailing-list\" checked>\n              <label for=\"mailing-list\">Signup for our mailing list.</label>\n            </div>\n\n          </fieldset>\n\n        </form>\n\n      </div><!-- /form -->\n\n      <div class=\"registration-benefits\">\n\n        <div class=\"benefits-block\">\n          <h4>First 30 days free with every plan</h4>\n          <p>We're so confident you'll love our service that all our plans come with the first 30 days free. Come have a look around.</p>\n        </div>\n\n        <div class=\"benefits-block\">\n          <h4>Quick and easy to setup</h4>\n          <p>Get up and running quickly with preset rules, or tailor your rules to create a custom framework that aligns with your key objectives.</p>\n        </div>\n\n      </div><!-- /benefits -->\n\n    </div><!-- /registration -->\n\n    <div class=\"registration-policy\">By clicking on \"Take me to my dashboard\", you agree to the <a href=\"http://app-dev.pbapp.net/playbasis/playbasis/app/terms-of-service.html\">Terms of Service</a> and the <a href=\"http://app-dev.pbapp.net/playbasis/playbasis/app/privacy.html\">Privacy Policy</a></div>\n\n  </div>\n</main><!-- /main -->";
  });