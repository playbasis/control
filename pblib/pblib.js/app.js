var Greeter = (function () {
    function Greeter(element) {
        this.element = element;
        this.element.innerText += "The time is: ";
        this.span = document.createElement('span');
        this.element.appendChild(this.span);
        this.span.innerText = new Date().toUTCString();
    }
    Greeter.prototype.start = function () {
        var _this = this;
        this.timerToken = setInterval(function () {
            return _this.span.innerText = new Date().toUTCString();
        }, 500);
    };
    Greeter.prototype.stop = function () {
        clearTimeout(this.timerToken);
    };
    return Greeter;
})();
window.onload = function () {
    var el = document.getElementById('content');
    var greeter = new Greeter(el);
    greeter.start();
    var pb = new Playbasis('abc');
    pb.player('1', function (result) {
        console.log("pb.player");
        console.log(result);
    });
    pb.points('1', function (result) {
        console.log("pb.points");
        console.log(result);
    });
    pb.point('1', 'exp', function (result) {
        console.log("pb.point");
        console.log(result);
    });
    pb.actionLastPerformed('1', function (result) {
        console.log("pb.actionLastPerformed");
        console.log(result);
    });
    pb.actionLastPerformedTime('1', 'like', function (result) {
        console.log("pb.actionLastPerformedTime");
        console.log(result);
    });
    pb.actionPerformedCount('1', 'like', function (result) {
        console.log("pb.actionPerformedCount");
        console.log(result);
    });
    pb.badgeOwned('1', function (result) {
        console.log("pb.badgeOwned");
        console.log(result);
    });
    pb.rank('exp', 10, function (result) {
        console.log("pb.rank");
        console.log(result);
    });
    pb.badges(function (result) {
        console.log("pb.badges");
        console.log(result);
    });
    pb.badge('1', function (result) {
        console.log("pb.badge");
        console.log(result);
    });
    pb.badgeCollections(function (result) {
        console.log("pb.badgeCollections");
        console.log(result);
    });
    pb.badgeCollection('1', function (result) {
        console.log("pb.badgeCollection");
        console.log(result);
    });
    pb.actionConfig(function (result) {
        console.log("pb.actionConfig");
        console.log(result);
    });
};
