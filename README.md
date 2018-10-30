
# Fructify

A browser-based collaborative drawing game played over a network.

![Screenshot](/docs/Screenshot-2018-10-30.png)

## Installation

- Clone repo down
- Run `npm install`


## Running the servers

For this to work there needs to be 2 services running, a normal web server listening on http:// and a websocket server listening on ws://


Run a normal web server over http:// with something like this...
```
php -S 192.168.2.99:8000
```

Run the WebSockets server with something like this...

```
node index.js
```

Obviously those IP addresses above will change dependant on your machine. 
You may need to tweak the JavaScript that the browser uses to connect to the WebSocket.
