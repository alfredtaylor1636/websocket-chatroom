<?php

require_once "db.php";


$username = $_POST["username"];
$password = $_POST["password"];
$screenName = $_POST["screenName"];


//Check username or screen name already exists

$sql = "SELECT * FROM users 
        WHERE username=? OR screenName=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ss",
    $username,
    $screenName
);

$stmt->execute();

$result = $stmt->get_result();


if($result->num_rows > 0){

    echo json_encode([
        "success"=>false,
        "message"=>"Username or screen name already exists"
    ]);

    exit();

}




//Insert new user

$sql = "INSERT INTO users
        (username,password,screenName)
        VALUES(?,?,?)";


$stmt = $conn->prepare($sql);


$stmt->bind_param(
    "sss",
    $username,
    $password,
    $screenName
);



if($stmt->execute()){


    //automatically login

    $_SESSION["username"] = $username;
    $_SESSION["screenName"] = $screenName;



    echo json_encode([
        "success"=>true,
        "message"=>"Signup successful"
    ]);


}
else{


    echo json_encode([
        "success"=>false,
        "message"=>"Signup failed"
    ]);


}


?>
