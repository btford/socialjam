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

// get user id from query string

var query = window.location.search.slice(1, window.location.search.length);

var querySep = query.indexOf("&");

var userId = parseInt(query.slice(0, querySep), 10);
var sessionId = parseInt(query.slice(querySep + 1), 10);
var timestamp = "2011-03-26 00:57:14";

var sendMyMusic = function () {
    "use strict";
    
    var json = {
        "session": 1,
        "user": userId,
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
        "user": userId,
        "timestamp": timestamp
    };

    $.ajax({
        url: "http://andrewbrobinson.com/sj/server.php?action=pull",
        type: "POST",
        data: json,
        success: function (data) {
            
            if (data) {
                var parsedData = $.parseJSON(data), i;
                console.log(parsedData);

                for (i = 0; i < parsedData.content.length; i += 1) {
                    parsedData.content[i].duration =
                        parseInt(parsedData.content[i].duration, 10);
                }

                timestamp = parsedData.timestamp;
                broNotes.setNotes(parsedData.content);
            }
            
            setTimeout(getMusic, 100);
        }
    });
};

// Event Handlers
///////////////////////////////////////////////////////////////////////////////
$("#play").click(function () {
    "use strict";
    myNotes.play();
});

$("#clear").click(function () {
    "use strict";
    myNotes.clear();
    sendMyMusic();
});

$("#editing > span").click(function () {
    "use strict";
    myNotes.addNote({key: this.id, duration: 4});
    sendMyMusic();
});
