
var ws = require("nodejs-websocket");
var fs = require('fs');


function padStringRight(string, length, char) {

    if (string.length < length) {
        for (var i = string.length; i < length; i++) {
            string += char;
        }
    }

    return string;
}


Service = function(){

    var fileName = 'pixelString.txt';

    var pixelsUntilNextSave = 30;

    this.connections = [];

    this.pixelCount = 3500;

    // A 3500 character long string of zeros and ones that represent pixels on canvas (1 = coloured, 0 = transparent)
    this.pixelString = padStringRight( fs.readFileSync(fileName).toString(), this.pixelCount, '0');




    this.addConnection = function( conn ){
        this.connections.push( conn );
        this.logConnections();

        console.log('Sending canvas to new connection');
        var json = this.getRenderCanvasMessage();
        this.messageConnection( conn, json );
    };


    this.removeConnection = function( conn ){
        var keyToDelete;
        for( var i in this.connections ){
            if( this.connections[i].key === conn.key ){
                keyToDelete = i;
                break;
            }
        }
        if( keyToDelete ){
            this.connections.splice( keyToDelete, 1);
        }

        this.logConnections();
    };


    this.logConnections = function(){
        console.log("Connections now contains: " + this.connections.length + " items" );
    };


    this.setPixel = function (index, value) {

        console.log('Setting pixel ' + index + ' to ' + value);
        this.pixelString = this.pixelString.substr(0, index) + value + this.pixelString.substr(index + 1);

        if (pixelsUntilNextSave-- < 1) {
            this.saveCanvas();
            pixelsUntilNextSave = 30;
        }

        this.broadcastCanvasToAllConnections();
    };


    this.getRenderCanvasMessage = function() {

        var newData = {
            action: 'render-canvas',
            pixelString: this.pixelString,
            connectionCount: this.connections.length
        };

        return JSON.stringify(newData);
    };


    this.broadcastCanvasToAllConnections = function() {

        console.log("Sending canvas to all connections");
        var json = this.getRenderCanvasMessage();

        this.messageAllConnections(json);
    };


    this.messageAllConnections = function(message ){
        for( var i in this.connections ){
            this.messageConnection( this.connections[i], message );
        }
    };


    this.messageConnection = function( conn, message ){
        conn.sendText( message );
    };


    this.saveCanvas = function () {
        fs.writeFile(fileName, this.pixelString, function() {
            console.log("The file was saved!");
        });
    };

};

var service = new Service();

var server = ws.createServer(function (conn) {

    console.log("New connection");
    service.addConnection( conn );

    conn.on("text", function (str) {

        var data = JSON.parse(str);

        if (data.action === 'set-pixel') {
            service.setPixel(parseInt(data.index), data.value);

        }
    });

    conn.on("close", function (code, reason) {
        console.log("Connection closed");
        service.removeConnection( conn );
    });

    conn.on("error", function (err) {
        console.log("Caught flash policy server socket error: ");
        console.log(err.stack);
    });

}).listen(8002);
