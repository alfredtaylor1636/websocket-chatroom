<?php

// Custom PHP WebSocket Server

$host = "0.0.0.0";
$port = 8080;


//Store connected clients

$clients = [];


//Store client information

$clientInfo = [];



//Create socket

$server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);


if(!$server){

    die("Could not create socket\n");

}



//Allow reuse

socket_set_option(
    $server,
    SOL_SOCKET,
    SO_REUSEADDR,
    1
);



//Bind socket

socket_bind(
    $server,
    $host,
    $port
);



//Listen

socket_listen($server);



echo "WebSocket server started on port $port\n";



//Add server socket

$clients[] = $server;



while(true){



    $read = $clients;

    $write = null;

    $except = null;



    socket_select(
        $read,
        $write,
        $except,
        0,
        10
    );



    foreach($read as $socket){



        //New connection

        if($socket == $server){



            $newClient = socket_accept($server);



            $clients[] = $newClient;



            $clientInfo[(int)$newClient] = [

                "room" => null,

                "screenName" => null,

                "handshake" => false

            ];



            echo "New connection\n";



        }



        else {



            $data = socket_read(
                $socket,
                8192
            );



            if(!$data){



                $index = array_search(
                    $socket,
                    $clients
                );


                if($index !== false){

                    unset($clients[$index]);

                }



                unset(
                    $clientInfo[(int)$socket]
                );



                socket_close($socket);



                echo "Client disconnected\n";


                continue;

            }



            //WebSocket handshake

            if(
                !$clientInfo[(int)$socket]["handshake"]
            ){


                performHandshake(
                    $socket,
                    $data
                );


                $clientInfo[(int)$socket]["handshake"] = true;


                continue;

            }



            //Decode message

            $message = decodeMessage($data);



            if(!$message){

                continue;

            }



            //Decode JSON

            $json = json_decode(
                $message,
                true
            );



            if(!$json){

                continue;

            }



            //User joins room

            if(
                isset($json["type"])
                &&
                $json["type"] == "join"
            ){



                $clientInfo[(int)$socket]["room"] =
                    $json["room"];



                $clientInfo[(int)$socket]["screenName"] =
                    $json["screenName"];



                echo
                $json["screenName"] .
                " joined " .
                $json["room"] .
                "\n";



                continue;

            }



            //Chat message

            if(
                isset($json["type"])
                &&
                $json["type"] == "message"
            ){



                $room =
                    $clientInfo[(int)$socket]["room"];



                if($room === null){

                    continue;

                }



                $json["screenName"] =
                    $clientInfo[(int)$socket]["screenName"];



                $messageToSend =
                    json_encode($json);



                //Send message to everyone
                //in the same room

                foreach($clients as $client){



                    if(
                        $client == $server
                    ){

                        continue;

                    }



                    if(
                        isset(
                            $clientInfo[(int)$client]
                        )
                        &&
                        $clientInfo[(int)$client]["handshake"]
                        &&
                        $clientInfo[(int)$client]["room"]
                        ==
                        $room
                    ){



                        socket_write(
                            $client,
                            encodeMessage(
                                $messageToSend
                            )
                        );



                    }



                }



                continue;

            }



            //Room was created

            if(
                isset($json["type"])
                &&
                $json["type"] == "roomCreated"
            ){



                echo "Room created - sending update to all clients\n";



                $roomUpdate = json_encode([

                    "type" => "roomsUpdated"

                ]);



                //Send room update to every
                //connected client

                foreach($clients as $client){



                    if(
                        $client == $server
                    ){

                        continue;

                    }



                    if(
                        isset(
                            $clientInfo[(int)$client]
                        )
                        &&
                        $clientInfo[(int)$client]["handshake"]
                    ){



                        socket_write(
                            $client,
                            encodeMessage(
                                $roomUpdate
                            )
                        );



                        echo "Sent room update\n";



                    }



                }



                continue;

            }



        }



    }



}





//WebSocket handshake

function performHandshake($socket, $headers){



    preg_match(
        "/Sec-WebSocket-Key:\s*(.*)\r\n/i",
        $headers,
        $matches
    );



    if(!isset($matches[1])){

        return;

    }



    $key = trim($matches[1]);



    $accept = base64_encode(
        sha1(
            $key .
            "258EAFA5-E914-47DA-95CA-C5AB0DC85B11",
            true
        )
    );



    $upgrade =
        "HTTP/1.1 101 Switching Protocols\r\n" .
        "Upgrade: websocket\r\n" .
        "Connection: Upgrade\r\n" .
        "Sec-WebSocket-Accept: $accept\r\n\r\n";



    socket_write(
        $socket,
        $upgrade
    );



}





//Decode WebSocket frame

function decodeMessage($data){



    if(strlen($data) < 2){

        return "";

    }



    $secondByte =
        ord($data[1]);



    $length =
        $secondByte & 127;



    $offset = 2;



    if($length == 126){



        if(strlen($data) < 8){

            return "";

        }



        $length =
            unpack(
                "n",
                substr(
                    $data,
                    2,
                    2
                )
            )[1];



        $offset = 4;



    }



    elseif($length == 127){



        if(strlen($data) < 14){

            return "";

        }



        $length =
            unpack(
                "J",
                substr(
                    $data,
                    2,
                    8
                )
            )[1];



        $offset = 10;



    }



    $masked =
        ($secondByte & 128) != 0;



    if($masked){



        $mask =
            substr(
                $data,
                $offset,
                4
            );



        $offset += 4;



    }



    else {

        $mask = "";

    }



    $payload =
        substr(
            $data,
            $offset,
            $length
        );



    if($masked){



        $text = "";



        for(
            $i = 0;
            $i < strlen($payload);
            $i++
        ){



            $text .=
                $payload[$i]
                ^
                $mask[$i % 4];



        }



        return $text;



    }



    return $payload;

}





//Encode WebSocket frame

function encodeMessage($text){



    $length =
        strlen($text);



    if($length <= 125){



        return chr(129)
            .
            chr($length)
            .
            $text;



    }



    elseif($length <= 65535){



        return chr(129)
            .
            chr(126)
            .
            pack(
                "n",
                $length
            )
            .
            $text;



    }



    return "";

}

?>
