<?php

require_once "db.php";


$roomName = $_POST["roomName"];
$key = $_POST["key"];

$username = $_SESSION["username"];
$screenName = $_SESSION["screenName"];


//Check room

$sql = "SELECT * FROM list_of_chatrooms 
        WHERE chatroomName=?";


$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "s",
    $roomName
);


$stmt->execute();


$result = $stmt->get_result();



if($result->num_rows == 0){

    echo json_encode([
        "success"=>false,
        "message"=>"Room does not exist"
    ]);

    exit();

}



$room = $result->fetch_assoc();



//Check key

if($room["roomKey"] != "" && $room["roomKey"] != $key){


    echo json_encode([
        "success"=>false,
        "message"=>"Incorrect room key"
    ]);

    exit();

}


//Remove user from any previous room

$sql = "DELETE FROM current_chatroom_occupants
        WHERE screenName=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $screenName);

$stmt->execute();

//this adds user to current room


$sql = "INSERT INTO current_chatroom_occupants
(chatroomName, screenName)
VALUES (?,?)";


$stmt = $conn->prepare($sql);


$stmt->bind_param(
    "ss",
    $roomName,
    $screenName
);



if($stmt->execute()){


    $_SESSION["currentRoom"] = $roomName;


    echo json_encode([
        "success"=>true,
        "message"=>"Joined room",
        "room"=>$roomName
    ]);


}
else{


    echo json_encode([
        "success"=>false,
        "message"=>"Could not join room"
    ]);


}



?>
