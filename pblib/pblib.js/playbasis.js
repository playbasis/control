var Playbasis = (function () {
    function Playbasis(apiKey) {
        this.apiKey = apiKey;
        this.BASE_URL = "https://api.pbapp.net/";
        this.apiKey = "?api_key=" + apiKey;
    }
    Playbasis.prototype.player = function (playerId, callback) {
        return this.call("Player/" + playerId, callback);
    };
    Playbasis.prototype.points = function (playerId, callback) {
        return this.call("Player/" + playerId + "/points", callback);
    };
    Playbasis.prototype.point = function (playerId, pointName, callback) {
        return this.call("Player/" + playerId + "/point/" + pointName, callback);
    };
    Playbasis.prototype.actionLastPerformed = function (playerId, callback) {
        return this.call("Player/" + playerId + "/action/time", callback);
    };
    Playbasis.prototype.actionLastPerformedTime = function (playerId, actionName, callback) {
        return this.call("Player/" + playerId + "/action/" + actionName + "/time", callback);
    };
    Playbasis.prototype.actionPerformedCount = function (playerId, actionName, callback) {
        return this.call("Player/" + playerId + "/action/" + actionName + "/count", callback);
    };
    Playbasis.prototype.badgeOwned = function (playerId, callback) {
        return this.call("Player/" + playerId + "/badge", callback);
    };
    Playbasis.prototype.rank = function (rankedBy, limit, callback) {
        return this.call("Player/rank/" + rankedBy + "/" + limit, callback);
    };
    Playbasis.prototype.badges = function (callback) {
        return this.call("Badge", callback);
    };
    Playbasis.prototype.badge = function (badgeId, callback) {
        return this.call("Badge/" + badgeId, callback);
    };
    Playbasis.prototype.badgeCollections = function (callback) {
        return this.call("Badge/collection", callback);
    };
    Playbasis.prototype.badgeCollection = function (collectionId, callback) {
        return this.call("Badge/collection/" + collectionId, callback);
    };
    Playbasis.prototype.actionConfig = function (callback) {
        return this.call("Engine/actionConfig", callback);
    };
    Playbasis.prototype.call = function (method, callback) {
        var url = this.BASE_URL + method + this.apiKey;
        $.ajax(url, {
            type: "GET",
            url: url,
            success: callback
        });
        return this;
    };
    return Playbasis;
})();
