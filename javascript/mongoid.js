var mongoIDjs = function() {
    var d = new Date();
    var c = d.getTime().toString();
    var m = MD5(c+Math.random());
    var r = c.substring(7)+""+m.substring(0,18);

    return r;
};