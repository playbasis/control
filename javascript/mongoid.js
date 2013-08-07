var mongoIDjs = function() {
    var d = new Date();
    var c = d.getTime().toString();
    var m = MD5(c);
    var r = m.substring(0,18)+""+c.substring(7);

    return r;
};