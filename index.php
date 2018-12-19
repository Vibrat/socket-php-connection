<?php

set_time_limit(0);

require_once "./functions.php";
$core = new CoreFunction();

## Server Config For Socket Connection
$host = "127.0.0.1";
$port = "88";

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

socket_bind($socket, $host, $port);
socket_listen($socket);
socket_set_nonblock($socket);

$clients = [];

while(true) {
        
    ## getting new connection
    $connection = socket_accept($socket);
    
    ## check it's new
    if (!in_array($connection, $core->arrayMapSocket($clients, 'socket')) && $connection) {

        ## Making handsake
        $response = socket_read($connection, 512);       
        
        usleep(1000);
        socket_write($connection, 
        "HTTP/1.1 101 Switching Protocols\r\n" 
        . "Upgrade: websocket\r\n" 
        . "Connection: Upgrade\r\n" 
        . "Sec-WebSocket-Accept: ". base64_encode(sha1($core->parseHTTP($response)['Sec-WebSocket-Key'] . "258EAFA5-E914-47DA-95CA-C5AB0DC85B11", true )) ."\r\n\r\n");
        
        $clients[] = [ 'socket' => $connection, 'timestamp' => strtotime('now')];
    }
   
    ## getting message
    foreach($clients as $index=>$client) {

        while ($bytes = socket_recv($client['socket'], $buf, 1024, 0) > 0) {          
                ## Start Communication
                echo " data sent : " . $client['socket'] . ": " . $core->unmask($buf) . "\n";
        }

        ## remove inconnected connection after 10 seconds
        if (strtotime('now - 10 seconds') >= $client['timestamp']) {
             unset($clients[$index]);
        }
    }
}