<?php

require_once "db.php";


$sql = "SELECT * FROM list_of_chatrooms";


$result = $conn->query($sql);


$rooms = [];


while($row = $result->fetch_assoc()){

    $rooms[] = $row;

}


echo json_encode($rooms);


?>
