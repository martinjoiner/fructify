
# Fructify

A browser-based collaborative drawing game played over a network.

![Screenshot](/docs/Screenshot-2018-10-30.png)

## Installation

- Clone repo down
- Run `npm install`
- Copy and rename _config.json.example_ to _config.json_


## Running the servers

For this to work there needs to be 2 services running, a normal web server listening on http:// and a WebSocket server listening on ws://


Run a normal web server over http:// with something like this...
```
php -S 192.168.2.99:8000
```

Run the WebSockets server with something like this...

```
node index.js
```

Obviously the IP address will vary dependant on your machine's network. 
Be sure to set "domain" in _config.json_
