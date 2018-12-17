<?php

require_once "./functions.php";
$core = new CoreFunction();

## Server Config For Socket Connection
$host = "127.0.0.1";
$port = "88";

$clients = [];
$buf = '';

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

socket_bind($socket, $host, $port);
socket_listen($socket);

while(true) {
    if ($connection = socket_accept($socket)) {

        ## Making handsake
        $response = socket_read($connection, 512);       
        usleep(1000);
        socket_write($connection, 
        "HTTP/1.1 101 Switching Protocols\r\n" 
        . "Upgrade: websocket\r\n" 
        . "Connection: Upgrade\r\n" 
        . "Sec-WebSocket-Accept: ". base64_encode(sha1($core->parseHTTP($response)['Sec-WebSocket-Key'] . "258EAFA5-E914-47DA-95CA-C5AB0DC85B11", true )) ."\r\n\r\n");

        $clients[] = $connection;

        ## Start Communication
        $buf = socket_read($connection, 100000, PHP_BINARY_READ);
        echo " data sent : " . $buf . "\n";
        
    }
}

socket_close($socket);