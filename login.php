<?php

require_once "db.php";


$username = $_POST["username"];
$password = $_POST["password"];



$sql = "SELECT * FROM users WHERE username=? AND password=?";


$stmt = $conn->prepare($sql);


$stmt->bind_param(
    "ss",
    $username,
    $password
);


$stmt->execute();


$result = $stmt->get_result();



if($result->num_rows == 1){


    $user = $result->fetch_assoc();


    $_SESSION["username"] = $user["username"];

    $_SESSION["screenName"] = $user["screenName"];



    echo json_encode([
        "success"=>true,
        "message"=>"Login successful"
    ]);


}
else{


    echo json_encode([
        "success"=>false,
        "message"=>"Invalid username or password"
    ]);


}


?>
