// main.js
// Brian Ford, btford@umich.edu
// 03/25/11

/*jslint white: true, onevar: true, undef: true, newcap: true, nomen: true, regexp: true, plusplus: true, bitwise: true, browser: true, maxerr: 50, indent: 4 */

/*globals $, Vex*/

// Social jam config
var SJM = {
	width: 400
};

//TODO: put this in a closure
var canvas = $("#can")[0];
var renderer = new Vex.Flow.Renderer(canvas, Vex.Flow.Renderer.Backends.CANVAS);

var ctx = renderer.getContext();
var stave = new Vex.Flow.Stave(10, 0, SJM.width);

// Add a treble clef
stave.addClef("treble").setContext(ctx).draw();

var notes = [
  // A quarter-note C.
  new Vex.Flow.StaveNote({ keys: ["c/4"], duration: "q" }),

  // A quarter-note D.
  new Vex.Flow.StaveNote({ keys: ["d/4"], duration: "q" }),

  // A quarter-note rest. Note that the key (b/4) specifies the vertical
  // position of the rest.
  new Vex.Flow.StaveNote({ keys: ["b/4"], duration: "qr" }),

  // A C-Major chord.
  new Vex.Flow.StaveNote({ keys: ["c/4"], duration: "q" })
];

// Create a voice in 4/4
var voice = new Vex.Flow.Voice({
  num_beats: 4,
  beat_value: 4,
  resolution: Vex.Flow.RESOLUTION
});

// Add notes to voice
voice.addTickables(notes);

// Format and justify the notes to 500 pixels
var formatter = new Vex.Flow.Formatter().joinVoices([voice]).format([voice], SJM.width);

// Render voice
voice.draw(ctx, stave);