//WEBSOCKET CONNECTION

let socket = new WebSocket("ws://3.19.55.19:8080");


socket.onopen = function(){

    console.log("WebSocket connected");

};


socket.onmessage = function(event){

    let data = JSON.parse(event.data);


    //ROOM LIST UPDATE

    if(data.type === "roomsUpdated"){

        console.log("ROOMS UPDATED RECEIVED");

        loadRooms();

        return;

    }


    //CHAT MESSAGE

    if(data.type === "message"){

        let messages =
        document.getElementById("messages");


        if(!messages){

            return;

        }


        let row =
        document.createElement("div");


        row.innerHTML =
        data.screenName + ": " + data.message;


        messages.appendChild(row);

    }

};


socket.onerror = function(error){

    console.log("WebSocket error:", error);

};


socket.onclose = function(){

    console.log("WebSocket closed");

};





//OVERLAY CONTROLS

//Help

const helpBtn = document.getElementById("helpBtn");
const helpOverlay = document.getElementById("helpOverlay");
const closeHelp = document.getElementById("closeHelp");


if (helpBtn) {

    helpBtn.onclick = function() {

        helpOverlay.style.display = "flex";

    };

}


if (closeHelp) {

    closeHelp.onclick = function() {

        helpOverlay.style.display = "none";

    };

}





//Signup

const signupBtn = document.getElementById("signupBtn");
const signupOverlay = document.getElementById("signupOverlay");
const closeSignup = document.getElementById("closeSignup");


if(signupBtn){

    signupBtn.onclick = function(){

        signupOverlay.style.display = "flex";

    };

}


if(closeSignup){

    closeSignup.onclick = function(){

        signupOverlay.style.display = "none";

    };

}





//Login

const loginBtn = document.getElementById("loginBtn");
const loginOverlay = document.getElementById("loginOverlay");
const closeLogin = document.getElementById("closeLogin");


if(loginBtn){

    loginBtn.onclick = function(){

        loginOverlay.style.display = "flex";

    };

}


if(closeLogin){

    closeLogin.onclick = function(){

        loginOverlay.style.display = "none";

    };

}





//Close overlays if clicking outside popup

window.onclick = function(event){

    if(event.target.classList.contains("overlay")){

        event.target.style.display = "none";

    }

};





//Logout function

function logout(){

    window.location.href = "logout.php";

}





//SIGNUP SUBMIT

const signupSubmit =
document.getElementById("signupSubmit");


if(signupSubmit){

    signupSubmit.onclick = function(){

        let username =
        document.getElementById("signupUsername").value;

        let password =
        document.getElementById("signupPassword").value;

        let screenName =
        document.getElementById("signupScreen").value;


        fetch("signup.php", {

            method: "POST",

            headers: {
                "Content-Type":
                "application/x-www-form-urlencoded"
            },

            body:
            "username=" +
            encodeURIComponent(username) +
            "&password=" +
            encodeURIComponent(password) +
            "&screenName=" +
            encodeURIComponent(screenName)

        })


        .then(response =>
            response.json()
        )


        .then(data => {

            alert(data.message);


            if(data.success){

                window.location.reload();

            }

        })


        .catch(error => {

            console.log(error);

            alert("Signup error");

        });

    };

}





//LOGIN SUBMIT

const loginSubmit =
document.getElementById("loginSubmit");


if(loginSubmit){

    loginSubmit.onclick = function(){

        let username =
        document.getElementById("loginUsername").value;

        let password =
        document.getElementById("loginPassword").value;


        fetch("login.php", {

            method:"POST",

            headers:{
                "Content-Type":
                "application/x-www-form-urlencoded"
            },

            body:
            "username=" +
            encodeURIComponent(username)
            +
            "&password=" +
            encodeURIComponent(password)

        })


        .then(response =>
            response.json()
        )


        .then(data => {

            alert(data.message);


            if(data.success){

                window.location.reload();

            }

        });

    };

}





//CREATE ROOM POPUP

const createRoomBtn =
document.getElementById("createRoomBtn");


const createRoomOverlay =
document.getElementById("createRoomOverlay");


const closeCreateRoom =
document.getElementById("closeCreateRoom");


if(createRoomBtn){

    createRoomBtn.onclick = function(){

        createRoomOverlay.style.display="flex";

    };

}


if(closeCreateRoom){

    closeCreateRoom.onclick=function(){

        createRoomOverlay.style.display="none";

    };

}





//CREATE ROOM SUBMIT

const createRoomSubmit =
document.getElementById("createRoomSubmit");


if(createRoomSubmit){

    createRoomSubmit.onclick = function(){

        let roomName =
        document.getElementById("roomNameInput").value;

        let key =
        document.getElementById("roomKeyInput").value;


        fetch("createRoom.php", {

            method:"POST",

            headers:{
                "Content-Type":
                "application/x-www-form-urlencoded"
            },

            body:
            "roomName=" +
            encodeURIComponent(roomName)
            +
            "&key=" +
            encodeURIComponent(key)

        })


        .then(response =>
            response.json()
        )


        .then(data => {

            alert(data.message);


            if(data.success){

                createRoomOverlay.style.display="none";


                //this clears inputs

                document.getElementById(
                    "roomNameInput"
                ).value="";


                document.getElementById(
                    "roomKeyInput"
                ).value="";



                //Update the client that
                //created the room

                loadRooms();



                //Tell the WebSocket server
                //that a room was created

                if(socket.readyState === WebSocket.OPEN){

                    console.log(
                        "Sending roomCreated to WebSocket server"
                    );


                    socket.send(
                        JSON.stringify({

                            type: "roomCreated"

                        })
                    );

                }

            }

        })


        .catch(error => {

            console.log(error);

            alert("Create room error");

        });

    };

}





//LOAD CHATROOMS

function loadRooms(){

    fetch("getRooms.php")

    .then(response =>
        response.json()
    )

    .then(rooms => {

        let roomList =
        document.getElementById("roomList");


        if(!roomList){

            return;

        }


        roomList.innerHTML="";


        rooms.forEach(room => {

            let row =
            document.createElement("tr");


            let status =
            room.roomKey ?
            "Locked" :
            "Unlocked";


            row.innerHTML = `

            <td>${room.chatroomName}</td>

            <td>${status}</td>

            <td>

            <button
            onclick="joinRoom(
                '${room.chatroomName}',
                '${room.roomKey || ""}'
            )">

            Join

            </button>

            </td>

            `;


            roomList.appendChild(row);

        });

    });

}





//this loads when the page opens

loadRooms();





//JOIN ROOM

function joinRoom(roomName, roomKey){

    let key = "";


    if(roomKey){

        key = prompt("Enter room key:");


        if(key === null){

            return;

        }

    }


    fetch("joinRoom.php", {

        method:"POST",

        headers:{
            "Content-Type":
            "application/x-www-form-urlencoded"
        },

        body:
        "roomName=" +
        encodeURIComponent(roomName)
        +
        "&key=" +
        encodeURIComponent(key)

    })


    .then(response =>
        response.json()
    )


    .then(data => {

        alert(data.message);


        if(data.success){

            document.getElementById(
                "currentRoom"
            ).innerHTML =
            "Current Room: " +
            data.room;


            document.getElementById(
                "messages"
            ).innerHTML = "";


            if(socket.readyState === WebSocket.OPEN){

                socket.send(
                    JSON.stringify({

                        type: "join",

                        room: data.room,

                        screenName:
                        document.getElementById(
                            "screenName"
                        ).value

                    })
                );

            }

        }

    });

}





//SEND CHAT MESSAGE

const sendBtn =
document.getElementById("sendBtn");


if(sendBtn){

    sendBtn.onclick = function(){

        let message =
        document.getElementById(
            "messageInput"
        ).value;


        if(message.trim() === ""){

            return;

        }


        let data = {

            type:"message",

            screenName:
            document.getElementById(
                "screenName"
            ).value,

            message:message

        };


        if(socket.readyState !== WebSocket.OPEN){

            alert("WebSocket is not connected.");

            return;

        }


        socket.send(
            JSON.stringify(data)
        );


        document.getElementById(
            "messageInput"
        ).value="";

    };

}
