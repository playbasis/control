/// <reference path="jquery.d.ts" />

class Playbasis {
	
	private BASE_URL = "https://api.pbapp.net/";
	constructor (private apiKey: string) {
		this.apiKey = "?api_key=" + apiKey;
	}

	player(playerId: string, callback) {
		return this.call("Player/" + playerId, callback);
	}

	points(playerId: string, callback) {
		return this.call("Player/" + playerId + "/points", callback);
	}

	point(playerId: string, pointName: string, callback) {
		return this.call("Player/" + playerId + "/point/" + pointName, callback);
	}

	actionLastPerformed(playerId: string, callback) {
		return this.call("Player/" + playerId + "/action/time", callback);
	}

	actionLastPerformedTime(playerId: string, actionName: string, callback) {
		return this.call("Player/" + playerId + "/action/" + actionName + "/time", callback);
	}

	actionPerformedCount(playerId: string, actionName: string, callback) {
		return this.call("Player/" + playerId + "/action/" + actionName + "/count", callback);
	}

	badgeOwned(playerId: string, callback) {
		return this.call("Player/" + playerId + "/badge", callback);
	}

	rank(rankedBy: string, limit: number, callback) {
		return this.call("Player/rank/" + rankedBy + "/" + limit, callback);
	}

	badges(callback) {
		return this.call("Badge", callback);
	}

	badge(badgeId: string, callback) {
		return this.call("Badge/" + badgeId, callback);
	}

	badgeCollections(callback) {
		return this.call("Badge/collection", callback);
	}

	badgeCollection(collectionId: string, callback) {
		return this.call("Badge/collection/" + collectionId, callback);
	}

	actionConfig(callback) {
		return this.call("Engine/actionConfig", callback);
	}

	call(method: string, callback) {
		var url = this.BASE_URL + method + this.apiKey;
		$.ajax(url, {
			type: "GET",
			url: url,
			success: callback
		});
		return this;
	}
}