
var ws = require("nodejs-websocket")

Service = function(){

    this.connections = [];
    this.message = '';

    this.addConnection = function( conn ){
        this.connections.push( conn );
        this.logConnections();
        this.messageConnection( conn );
    }

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
    }

    this.logConnections = function(){
        console.log("Connections now contains: " + this.connections.length + " items" );
    }

    this.messageAll = function( message ){
        this.message = message;
        for( var i in this.connections ){
            this.messageConnection( this.connections[i] );
        }
    }

    this.messageConnection = function( conn ){
        conn.sendText( this.message );
    }
}

var service = new Service();

var server = ws.createServer(function (conn) {

    console.log("New connection")
    service.addConnection( conn );

    conn.on("text", function (str) {
        console.log("Received: \"" + str + "\"")
        //conn.sendText(str.toUpperCase()+"!!!")
        service.messageAll(str);
    });

    conn.on("close", function (code, reason) {
        console.log("Connection closed");
        service.removeConnection( conn );
    });

}).listen(8001);
