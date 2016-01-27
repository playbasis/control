// rule_editor.js
// const DEBUG = true;


//########################################
//Header
//########################################




//########################################
//Body
//########################################





//Main
$(document).ready(function(){
	//Prevent all #_link from crashing opencart token
	$(document).on("click", 'a[href="#"],a[href*=#]"', function (event) {
		event.preventDefault();
	});
	//Initial Table of Rules
	dataMan.loadRulesTable();



	console.log('Try > Append Style for firefox')
	//Browser Detect -> Fixing 
	navigator.sayswho= (function(){
	  var N= navigator.appName, ua= navigator.userAgent, tem;
	  var M= ua.match(/(opera|chrome|safari|firefox|msie)\/?\s*(\.?\d+(\.\d+)*)/i);
	  if(M && (tem= ua.match(/version\/([\.\d]+)/i))!= null) M[2]= tem[1];
	  M= M? [M[1], M[2]]: [N, navigator.appVersion,'-?'];
	  return M;
	 })();
	 // Fixed ICON postion shift on firefox
	 if(navigator.sayswho[0]=='Firefox' || navigator.sayswho[0] =='firefox'){
	 	$('body').prepend("<style>.pbd_list_selection li i { min-width: 32px;min-height: 36px;padding-top: 12px;background: none repeat scroll 0% 0% rgb(221, 221, 221);float: left;transform: scale(3);margin: 28px;}</style>");
	 	console.log('Append Style for firefox')
	 }

});