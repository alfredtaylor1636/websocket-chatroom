<?php

require_once "db.php";


$roomName = $_POST["roomName"];
$key = $_POST["key"];



//Check if room already exists

$sql = "SELECT * FROM list_of_chatrooms WHERE chatroomName=?";


$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "s",
    $roomName
);


$stmt->execute();


$result = $stmt->get_result();



if($result->num_rows > 0){


    echo json_encode([
        "success"=>false,
        "message"=>"Room already exists"
    ]);

    exit();

}




//Create room

$sql = "INSERT INTO list_of_chatrooms
(chatroomName, roomKey, creatorUsername)
VALUES (?,?,?)";

$stmt = $conn->prepare($sql);


$stmt->bind_param(
    "sss",
    $roomName,
    $key,
    $_SESSION["username"]
);


if($stmt->execute()){


    echo json_encode([
        "success"=>true,
        "message"=>"Room created successfully"
    ]);


}
else{


    echo json_encode([
        "success"=>false,
        "message"=>"Room creation failed"
    ]);


}


?>
