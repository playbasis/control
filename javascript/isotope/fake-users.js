var fakeUser = {};

fakeUser.rank = '5:noob 10:amature 15:experience 20:god 25:motherofgod'.split(' ');
fakeUser.profile_image = "//fbcdn-profile-a.akamaihd.net/hprofile-ak-ash3/c12.12.156.156/s100x100/529462_10151030915344835_1070028058_a.jpg //fbcdn-profile-a.akamaihd.net/hprofile-ak-ash3/c9.9.113.113/s80x80/601239_10150975497198427_210069180_s.jpg //fbcdn-profile-a.akamaihd.net/hprofile-ak-ash3/c9.9.113.113/s80x80/182935_1792995353638_6837236_s.jpg //fbcdn-profile-a.akamaihd.net/hprofile-ak-prn1/c9.9.113.113/s80x80/564752_10152073256730215_558789502_s.jpg //fbcdn-profile-a.akamaihd.net/hprofile-ak-ash3/c9.9.108.108/s80x80/557344_109677485838088_1790511458_s.jpg //fbcdn-profile-a.akamaihd.net/hprofile-ak-prn1/c9.9.113.113/s80x80/537557_523953194291123_453543563_s.jpg https://fbcdn-profile-a.akamaihd.net/hprofile-ak-ash3/c9.9.113.113/s80x80/23911_10152229612465018_433117256_s.jpg https://fbcdn-profile-a.akamaihd.net/hprofile-ak-snc6/c7.7.82.82/s80x80/181194_10100975186939385_1614921382_s.jpg".split(' ');
fakeUser.first = "Rob John Steven Paul David Matt Jack".split(' ');
fakeUser.last = "Sparrow Doe Gerrard Jobs Copperfield Demon".split(' ');

fakeUser.getRandom = function( property ) {
    var values = fakeUser[ property ];
    return values[ Math.floor( Math.random() * values.length ) ];
};

fakeUser.getRank = function( level ) {
    if(level >= 25) return 'motherofgod';
    for(var i = 0; i < fakeUser.rank.length; i++) {
        var rank = fakeUser.rank[i].split(':');
        if(level <= parseInt(rank[0], 10)) return rank[1];
    }
};

fakeUser.create = function() {
    var username = fakeUser.getRandom('first') + ' ' + fakeUser.getRandom('last'),
        level = Math.ceil( Math.random() * 30),
        point = ~~( 21 + Math.random() * 1000 ),
        rank = fakeUser.getRank(level);

    return  '<div class="element ' + rank + '">' + 
                '<img class="user" src="' + fakeUser.getRandom('profile_image') + '" alt="profile images" />' +
                '<p class="level">' + level + '</p>' +
                '<h2 class="name">' + username + '</h2>' +
                '<p class="point">' + point + '</p>' +
                '<img src="view/image/isotope/gradient.png" class="subtle" />' +
            ' </div>';
};

fakeUser.getGroup = function() {
    var i = Math.ceil( Math.random()*3 + 1 ),
        newEls = '';
    while ( i-- ) {
        newEls += fakeUser.create();
    }
    return newEls;
};

window.fakeUser = fakeUser;
