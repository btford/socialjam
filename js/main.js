// main.js
// Brian Ford, btford@umich.edu
// 03/25/11

/*jslint white: true, onevar: true, undef: true, newcap: true, nomen: true, regexp: true, plusplus: true, bitwise: true, browser: true, maxerr: 50, indent: 4 */

/*globals $, Vex, console, Audio*/

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
