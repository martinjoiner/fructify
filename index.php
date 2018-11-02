<?php

    $jsonConfig = file_get_contents('config.json');

    $config = json_decode($jsonConfig, true);

?><!DOCTYPE html>
<html>
<head>
    <title>Fructify</title>

    <link href="style.css" rel="stylesheet">

    <style type="text/css">
        #canvas {
            grid-template-columns: repeat(<?=$config['canvas']['cols']?>, 10px);
            width: <?=$config['canvas']['cols'] * 10?>px
        }
    </style>
</head>
<body>

    <p>Click on a pixel to change it's colour.</p>

    <div id="canvas" data-rows="<?=$config['canvas']['rows']?>" data-cols="<?=$config['canvas']['cols']?>">
        <!--
        <i></i>
        -->
    </div>

    <div id="color-picker">
        <i class="a"></i>
        <i class="b"></i>
        <i class="c"></i>
        <i class="d"></i>
        <i class="e"></i>
        <i class="f"></i>
        <i class="g"></i>
        <i class="h"></i>
        <i class="i"></i>
        <i class="j"></i>
        <i class="k"></i>
        <i class="l"></i>
        <i class="m"></i>
    </div>

    <div id="artists"><span id="connection-count">0</span> artists painting</div>


    <script type="text/javascript">

        webSocketAddress = "ws://" + window.location.hostname + ":<?=$config['websocket_port']?>";

        // Global variable for currently selected color
        window.currentColor = 'a';

        webSocketConnect = (function() {
            if ("WebSocket" in window) {
                console.log("WebSocket is supported by your Browser!");
           
                // Let us open a web socket
                window.ws = new WebSocket(webSocketAddress);

                window.ws.onopen = function () {
                    // Web Socket is connected, send data using send()
                    console.log("Connection established...");
                };

                window.ws.onmessage = function (evt) { 
                    var received_msg = evt.data;

                    var data = JSON.parse(received_msg);

                    if (data.action == 'render-canvas') {
                        renderWholeCanvas( data.pixelString );
                    }

                    if (typeof data.connectionCount !== 'undefined') {
                        document.getElementById('connection-count').innerHTML = data.connectionCount;
                    }


                };

                window.ws.onclose = function () { 
                    // websocket is closed.
                    console.log("Connection is closed..."); 
                }

            } else {
                // The browser doesn't support WebSocket
                alert("WebSocket NOT supported by your Browser!");
            }
        })();


        /**
         * Sets the color of a pixel
         * @param DomElement pixel
         * @param string colorClass - Must be a single char
         */
        function colorPixel( pixel, colorClass ) {
            console.log('Setting pixel ' + pixel.dataset.index + ' to ' + colorClass);
            pixel.className = colorClass;
            sendPixelColor(pixel);
        }


        /**
         * Sends the state of 1 single pixel to a server
         * @param DOM Element pixel
         */
        function sendPixelColor(pixel) {
            var data = {
                action: 'set-pixel',
                index: pixel.dataset.index,
                value: pixel.className
            };

            var json = JSON.stringify(data);

            window.ws.send(json);
        }


        function renderWholeCanvas( pixelString ){
            console.log('Rendering whole canvas');
            var pixels = canvas.getElementsByTagName('i');

            for( var i = 0, iLimit = pixelString.length; i < iLimit; i++ ){
                pixels[i].className = pixelString[i];
            }
        }

        document.onunload = function(){ window.ws.close() };

        canvas = document.getElementById('canvas');

        function populateCanvas(rowCount, colCount) {

            var pixelsCreatedCount = 0;
            var i;

            for (var r = 0; r < rowCount; r++) {
                for (var col = 0; col < colCount; col++) {
                    i = document.createElement('i');
                    i.dataset.index = pixelsCreatedCount;
                    canvas.appendChild(i);
                    i.addEventListener('click', function () {
                        colorPixel(this, window.currentColor)
                    }, false);

                    i.addEventListener('contextmenu', function (ev) {
                        ev.preventDefault();
                        colorPixel(this, 'e');
                        return false;
                    }, false);

                    pixelsCreatedCount++;
                }
            }

            console.log('Populated ' + pixelsCreatedCount + ' pixels');
        }

        var rowCount = canvas.dataset.rows;
        var colCount = canvas.dataset.cols;

        populateCanvas(rowCount, colCount);

        var colorPicker = document.getElementById('color-picker');

        var cols = colorPicker.getElementsByTagName('i');

        for (var i = 0, iLimit = cols.length; i < iLimit; i++) {
            cols[i].addEventListener('mouseup', function () {
                window.currentColor = this.className;
            });
        }

    </script>
  
</body>
</html>
