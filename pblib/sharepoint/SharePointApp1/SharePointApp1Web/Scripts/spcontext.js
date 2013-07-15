// SPHostUrl parameter name
var SPHostUrlKey = "SPHostUrl";

// Gets SPHostUrl from the current URL and appends it as query string to each of the links in the page.
$(document).ready(function () {
    var spHostUrl = getSPHostUrlFromQueryString(window.location.search);

    if (spHostUrl) {
        appendSPHostUrlToAllLinks(spHostUrl);
    }
});

// Appends SPHostUrl as query string to each of the links in the page.
function appendSPHostUrlToAllLinks(spHostUrl) {
    $("a").each(function () {
        if (!getSPHostUrlFromQueryString(this.search)) {
            if (this.search.length > 0) {
                this.search += "&" + SPHostUrlKey + "=" + spHostUrl;
            }
            else {
                this.search = "?" + SPHostUrlKey + "=" + spHostUrl;
            }
        }
    });
}

// Gets SPHostUrl from the given query string.
function getSPHostUrlFromQueryString(queryString) {
    if (queryString) {
        if (queryString[0] === "?") {
            queryString = queryString.substring(1);
        }

        var keyValuePairArray = queryString.split("&");

        for (i = 0; i < keyValuePairArray.length; i++) {
            var currentKeyValuePair = keyValuePairArray[i].split("=");

            if (currentKeyValuePair.length > 1 && currentKeyValuePair[0] == SPHostUrlKey) {
                return currentKeyValuePair[1];
            }
        }
    }

    return null;
}
