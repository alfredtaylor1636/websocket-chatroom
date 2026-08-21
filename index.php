<?php
require_once "db.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Chat Room via PHP WebSockets - Alfred Taylor</title>

<link rel="stylesheet" href="style.css">

</head>


<body>


<header>

<div class="top-bar">

<button id="helpBtn">
Help
</button>


<div>

<?php if(isset($_SESSION['username'])): ?>

<button onclick="logout()">
Logout
</button>

<?php else: ?>

<button id="signupBtn">
Signup
</button>


<button id="loginBtn">
Login
</button>

<?php endif; ?>

</div>


</div>


<h1>
Chat Room via PHP WebSockets
</h1>


<h3>
by Alfred Taylor
</h3>


</header>





<?php if(!isset($_SESSION['username'])): ?>


<div class="welcome">

<h2>
Welcome to the Chat Room
</h2>


<p>
Create an account, login, join chatrooms, and communicate
with other users in real time using PHP WebSockets.
</p>


</div>



<?php else: ?>



<div class="chat-container">


<!-- LEFT SIDE -->

<div class="rooms-panel">


<h2>
Available Rooms
<button id="createRoomBtn">+</button>
</h2>



<div class="room-scroll">


<table>


<thead>

<tr>

<th>
Room Name
</th>


<th>
Status
</th>


<th>
Join
</th>


</tr>

</thead>



<tbody id="roomList">

</tbody>


</table>


</div>


</div>






<!-- RIGHT SIDE -->


<div class="chat-panel">


<h2 id="currentRoom">

Current Room: None

</h2>



<div id="messages">

</div>




<div class="message-box">

<input 
type="hidden"
id="screenName"
value="<?php echo $_SESSION['screenName']; ?>">

<input 
id="messageInput"
placeholder="Type new messages here">


<button id="sendBtn">
Send
</button>



</div>


</div>



</div>



<?php endif; ?>







<!-- HELP OVERLAY -->


<div id="helpOverlay" class="overlay">


<div class="popup">


<button class="close" id="closeHelp">
X
</button>


<h2>
How This Chatroom Works
</h2>



<p>
This chatroom application allows users to create accounts,
login, create chatrooms, and communicate with other users in
real time. The system uses HTML, CSS, JavaScript, PHP, MySQL,
and PHP WebSockets to create an interactive communication
platform without requiring users to constantly refresh the
entire webpage.
</p>


<p>
Users can register by providing a username, password, and
screen name. After registration, users can login and access
available chatrooms. Chatrooms may be open to everyone or
protected with a key. The available rooms section displays the
room name, security status, and join option.
</p>


<p>
After joining a chatroom, users can send messages that are
delivered only to users currently inside that room. WebSockets
allow messages and new room information to be transmitted
instantly. MySQL stores users, chatrooms, and room members while
PHP sessions track whether a user is currently logged in.
</p>


<p>
This application demonstrates how frontend and backend
technologies work together to create a realistic online chat
system. Users can communicate, manage rooms, and participate in
multiple conversations while receiving updates immediately.
</p>



</div>

</div>








<!-- SIGNUP OVERLAY -->


<div id="signupOverlay" class="overlay">


<div class="popup">


<button class="close" id="closeSignup">
X
</button>


<h2>
Create Account
</h2>



<input 
id="signupUsername"
placeholder="Username">


<input 
id="signupPassword"
type="password"
placeholder="Password">


<input 
id="signupScreen"
placeholder="Screen Name">


<button id="signupSubmit">
Signup
</button>


</div>


</div>



<!-- CREATE ROOM OVERLAY -->

<div id="createRoomOverlay" class="overlay">

<div class="popup">

<button class="close" id="closeCreateRoom">
X
</button>


<h2>Create Chat Room</h2>


<input 
id="roomNameInput"
placeholder="Room Name">


<input 
id="roomKeyInput"
placeholder="Room Key (optional)">



<button id="createRoomSubmit">
Create Room
</button>


</div>

</div>





<!-- LOGIN OVERLAY -->


<div id="loginOverlay" class="overlay">


<div class="popup">


<button class="close" id="closeLogin">
X
</button>



<h2>
Login
</h2>



<input 
id="loginUsername"
placeholder="Username">


<input 
id="loginPassword"
type="password"
placeholder="Password">



<button id="loginSubmit">
Login
</button>


</div>


</div>







<script src="script.js"></script>


</body>

</html>
