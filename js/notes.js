// notes.js
// Brian Ford, btford@umich.edu
// 03/26/11

/*jslint white: true, onevar: true, undef: true, newcap: true, nomen: true, regexp: true, plusplus: true, bitwise: true, browser: true, maxerr: 50, indent: 4 */

var Notes = function (canvas) {
    "use strict";

    var canvasWidth = canvas.width - 10;

    // Private
    var durationIntToStr = function (num) {
        "use strict";
        if (num === 1) {
            return "w";
        } else if (num === 2) {
            return "h";
        } else if (num === 3) {
            return "hd";
        } else if (num === 4) {
            return "q";
        } else {
            return String(num);
        }
    };
    
    var instrument = "piano";

    var makeRenderableNotes = function (notes) {
        "use strict";
        var ren = [], someKey, someDuration, restDuration, totalDuration = 0, i;

        for (i = 0; i < notes.length; i += 1) {

            someKey = notes[i].key;
            someDuration = notes[i].duration;

            //console.log(someDuration);

            totalDuration += 4 / someDuration;

            if (someKey === "rest") {
                someKey = "b/4";
                if(someDuration === 1) {
                    someKey = "d/5";
                }
                someDuration = durationIntToStr(someDuration) + "r";
            } else {
                someKey = someKey + "/4";
                someDuration = durationIntToStr(someDuration);
            }

            ren.push({key: someKey, duration: someDuration});
        }

        //console.log(totalDuration);

        if (totalDuration < 4) {
            restDuration = 4 - totalDuration;
            //console.log(restDuration);
            if (restDuration === 4) {
                ren.push({key: "d/5", duration: "wr"});
            } else if (restDuration === 3) {
                ren.push({key: "b/4", duration: "qr"});
                ren.push({key: "b/4", duration: "hr"});
            } else if (restDuration === 2) {
                ren.push({key: "b/4", duration: "hr"});
            } else {
                ren.push({key: "b/4", duration: (durationIntToStr(4 / restDuration) + "r")});
            }
        }

        return ren;
    };

    var render = function (param) {
        "use strict";

        var renderer, ctx, stave, notes = [], i, voice, formatter;

        renderer = new Vex.Flow.Renderer(canvas, Vex.Flow.Renderer.Backends.CANVAS);

        ctx = renderer.getContext();
        stave = new Vex.Flow.Stave(10, 0, canvasWidth);

        ctx.clear();
        stave.addClef("treble").setContext(ctx).draw();
        stave.addTimeSignature("4/4").setContext(ctx).draw();

        // Create a voice in 4/4
        voice = new Vex.Flow.Voice({
            num_beats: 4,
            beat_value: 4,
            resolution: Vex.Flow.RESOLUTION
        });
        
        voice.strict = false;

        // Add notes to voice
        voice.clearTickables(notes);

        for (i = 0; i < param.length; i += 1) {
            notes.push(new Vex.Flow.StaveNote({ keys: [param[i].key], duration: param[i].duration }));
            
            if(((i + 1) % 4) === 0) {
                voice.addTickables(notes);
                voice.addTickable( new Vex.Flow.BarNote() );
                notes = [];
            }
        }
        
        if(notes.length > 0) {
            voice.addTickables(notes);
        }

        // Format and justify the notes to 500 pixels
        formatter = new Vex.Flow.Formatter();
        
        formatter.joinVoices([voice]).format([voice], canvasWidth);
        // Render voice
        voice.draw(ctx, stave);
        
    };

    var renderNice = function (param) {
        "use strict";
        return render(makeRenderableNotes(param));
    };

    var playing = false;

    var playNice = function (n) {
        "use strict";

        playing = true;
        var currentNote = 0, notes, playNote, letters;

        notes = n;

        playNote = function () {
            if (currentNote >= notes.length) {
                playing = false;
                return;
            } else {
                if (notes[currentNote].key !== "rest") {
                    
                    if (instrument === undefined) {
                        instrument = "piano";
                    }
                    
                    var src = "res/" + instrument + "/" + notes[currentNote].key.toUpperCase() + "1.wav";
                    
                    if(instrument === "snare") {
                        src = "res/snare/snare.wav";
                    }
                    
                    var sound = new Audio(src);
                    sound.play();
                }

                setTimeout(playNote, 300 * 4 / notes[currentNote].duration);
                currentNote += 1;
            }
        };

        setTimeout(playNote, 200);
    };

    var niceNotes = [];

    //Public access
   
    var that = {};
    
    that.addNote = function(obj) {
        niceNotes.push(obj);
        that.render();
    };
    
    that.clear = function () {
        niceNotes = [];
        that.render();
    };
    
    that.render = function () {
        renderNice(niceNotes);
    }
    
    that.getNotes = function () {
        return niceNotes;
    };
    
    that.setNotes = function (newNotes) {
        niceNotes = newNotes;
        that.render();
    };
    
    that.play = function (newNotes) {
        playNice(niceNotes);
    };
    
    that.setInstrument = function (ins) {
        instrument = ins;
    };
    
    that.getInstrument = function () {
        return instrument;
    };
    
    that.render();
    
    return that;
};