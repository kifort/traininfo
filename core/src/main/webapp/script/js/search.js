var MAX_NUMBER_OF_AUTOCOMPLETE_RESULT = 5;

var accentMap = {
    "á" : "a",
    "Á" : "A",
    "ä" : "a",
    "Ä" : "A",
    "â" : "a",
    "Â" : "A",
    "ă" : "a",
    "Ă" : "A",
    "č" : "c",
    "Č" : "C",
    "đ" : "d",
    "Đ" : "D",
    "é" : "e",
    "É" : "E",
    "í" : "i",
    "Í" : "I",
    "ł" : "l",
    "Ł" : "L",
    "ž" : "l",
    "ź" : "L",
    "ň" : "n",
    "Ň" : "N",
    "ó" : "o",
    "Ó" : "o",
    "ö" : "o",
    "Ö" : "O",
    "ő" : "o",
    "Ő" : "O",
    "ş" : "s",
    "" : "s",
    "" : "S",
    "" : "t",
    "ü" : "u",
    "Ü" : "U",
    "ű" : "u",
    "Ű" : "U",
    "" : "z",
    "" : "Z",
    "" : "z",
    "" : "Z"
};

var normalize = function(term) {
    var ret = "";
    for ( var i = 0; i < term.length; i++) {
        ret += accentMap[term.charAt(i)] || term.charAt(i);
    }
    return ret;
};

var whenList = ["Ma", "Holnap", "Holnapután", "Adott napon"];
var whatTimeList = ["Mostanában", "Mostantól", "Egész nap", "Délelőtt", "Délután"];

$(function() {
    $('#fromStation,#toStation,#viaStation').autocomplete({
        source : function(request, response) {
            var matcher = new RegExp('^' + $.ui.autocomplete.escapeRegex(request.term), "i");
            var results = $.grep(mav, function(value) {
                value = value.label || value.value || value;
                return matcher.test(value) || matcher.test(normalize(value));
            });
            response(results.slice(0, MAX_NUMBER_OF_AUTOCOMPLETE_RESULT));
        },
        minLength : 2
    });
});

$(function() {
    $('#when').autocomplete({
        source : function(request, response) {
            var matcher = new RegExp('^' + $.ui.autocomplete.escapeRegex(request.term), "i");
            var results = $.grep(whenList, function(value) {
                value = value.label || value.value || value;
                return matcher.test(value) || matcher.test(normalize(value));
            });
            response(results.slice(0, MAX_NUMBER_OF_AUTOCOMPLETE_RESULT));
        },
        minLength : 0
    });
});

$(function() {
    $('#whatTime').autocomplete({
        source : function(request, response) {
            var matcher = new RegExp('^' + $.ui.autocomplete.escapeRegex(request.term), "i");
            var results = $.grep(whatTimeList, function(value) {
                value = value.label || value.value || value;
                return matcher.test(value) || matcher.test(normalize(value));
            });
            response(results.slice(0, MAX_NUMBER_OF_AUTOCOMPLETE_RESULT));
        },
        minLength : 0
    });
});
