// main.js
// Brian Ford, btford@umich.edu
// 03/25/11

/*jslint white: true, onevar: true, undef: true, newcap: true, nomen: true, regexp: true, plusplus: true, bitwise: true, browser: true, maxerr: 50, indent: 4 */

/*globals $, Vex, console, Audio, Notes, window*/

// Social jam config

//TODO: put this in a closure
var canvas = $("#can")[0];
var broCanvas = $("#broCan")[0];

var myNotes = new Notes(canvas);
var broNotes = new Notes(broCanvas);

//playNice(niceNotes);

$("#play").click(function () {
    "use strict";
    myNotes.play();
});

$("#clear").click(function () {
    "use strict";
    myNotes.clear();
});

$("#editing > span").click(function () {
    "use strict";
    myNotes.addNote({key: this.id, duration: 4});
});

// get user id from query string
var userId = parseInt(window.location.search.slice(1, 2), 10);
var sessionId = 1;
var timestamp = "2011-03-26 00:57:14";

var sendMyMusic = function () {
    "use strict";
    
    var json = {
        "session": 1,
        "user": 1,
        "timestamp": timestamp,
        "content": myNotes.getNotes()
    };

    $.ajax({
        url: "http://andrewbrobinson.com/sj/server.php?action=push",
        type: "POST",
        data: json,
        success: function (data) {
            $('#result').html("<pre>" + data + "</pre>");
            //alert('Load was performed.');
        }
    });
};

var getMusic = function () {
    "use strict";
    
    var json = {
        "session": 1,
        "user": 1,
        "timestamp": timestamp
    };

    $.ajax({
        url: "http://andrewbrobinson.com/sj/server.php?action=pull",
        type: "POST",
        data: json,
        success: function (data) {
            console.log(data);
            
            timestamp = data.timestamp;
            broNotes.setNotes(data.content);
        }
    });
};

