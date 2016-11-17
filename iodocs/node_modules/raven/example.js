var raven = require('.');
var client = new raven.Client('https://1d7c498d9af8436eb3c915959adbe901:01d269909c6449bfac8538b6dc8722f4@app.getsentry.com/53023', {
  environment: 'release'
});
client.patchGlobal();

setTimeout(2000);

function foo() {
  bar();
}

foo();

