// preload.js
// Brian Ford, btford@umich.edu
// 03/26/11

(function () {
    var notes = "ABCDEFG";
    var instruments = ["piano", "violin", "snare", "guitar"];
    
    for (var i = 0; i < instruments.length; i += 1) {
        var instrument = instruments[i];
        
        for (var j = 0; j < notes.length; j += 1) {
            var src = "res/" + instrument + "/" + notes.charAt(j) + "1.wav";
            $(new Audio(src)).appendTo("body");
        }
    }
}());