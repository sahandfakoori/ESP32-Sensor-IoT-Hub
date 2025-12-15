<?php
session_start();
if (!isset($_SESSION["username"])) {
    // Redirect to the login page if the user is not logged in
    header("Location: login.php");
    exit();
}
?>


<?php
$host = "localhost";
$username = "espdata1_sahand";
$password = "sahand.1377";
$database = "espdata1_esp_datalogger"; // The name of the database you created

// Establish a connection to the database
$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


    // Perform a query to check if the user exists in the database
    $query = "SELECT * FROM users WHERE username='". $_SESSION["username"] ."' ";
    $result = mysqli_query($conn, $query);

        // Insert a login activity into the user_activities table
        $user_id = mysqli_fetch_assoc($result)["id"];
        $activity_type = "Home";
        $activity_details = null; // You can modify this if you want to provide additional details

        $insert_query = "INSERT INTO user_activities (user_id, activity_type)
                         VALUES ('$user_id', '$activity_type')";
        mysqli_query($conn, $insert_query);


// Close the database connection
mysqli_close($conn);
?>





<!DOCTYPE html>
<html>

<head>
    <title>Home</title>
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <link rel="stylesheet" href="css/nav.css" type="text/css">
    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
    
    
    .sidebar {
            height: 100%;
            width: 0;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background-color: #08174c;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(5px);
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 60px;
        }

        .sidebar a {
            padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 25px;
            color: #ffffff;
            /*display: block;*/
            transition: 0.3s;
        }

        .sidebar a:hover {
            color: #ff0000;
        }

        .sidebar .closebtn {
            position: absolute;
            top: 0;
            right: 25px;
            font-size: 36px;
            margin-left: 50px;
        }

        .openbtn {
            font-size: 20px;
            cursor: pointer;
            background-color: rgba(0, 0, 50, 0.8);
            color: white;
            padding: 10px 10px;
            border: none;
            border-radius: 20px;
        }

        .openbtn:hover {
            background-color: #79b6d7;
            color: #0d153c;
        }
         .sidebar button:hover{
            color: #ff0000;
         }
        
        #close{
            color: #ff0000;
        }
        #close:hover{
            color: #ff0000;

        }
    
    
    
    
        #alert {
            padding: 15px;
            background-color: #04AA6D;
            color: white;
            opacity: 1;
            transition: opacity 0.6s;
        }
        
        
        #alert_temp{
            padding: 2px;
            background-color: red;
            color: white;
            opacity: 1;
            transition: opacity 0.6s;
        }

        .display {
            display: flex;
            justify-content: space-between;
            padding: 1px 20px;
            margin-bottom: 3%;
        }

        .sensor_display {
            text-decoration: none;
            color: #fff;
            padding: 12px;
            border-radius: 30px;
            border: 2px solid #fff;
            margin-bottom: 3%;
        }

        .sensor_display:hover {
            background-color: #79b6d7;
            color: #0d153c;
        }
        
                
        .navbar-right {
            display: flex;
            align-items: center;
        }
        
        .navbar-logo {
            font-size: 30px;
        }
        .sidebar button{
            background-color: #08174c;
            padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 25px;
            color: #ffffff;
            /*display: block;*/
            transition: 0.3s;
            display: block;
            border: none;
              cursor: pointer;

        }
        .dropdown-container {
          display: none;
          background-color: #08174c;
          padding-left: 8px;
        }
        /*#connect , #disconnect{*/
        /*    display: none;*/
        /*}*/
    </style>
</head>

<body>

    <?php
        // Display the success message if the "success" query parameter is present in the URL
        if (isset($_GET["success"]) && $_GET["success"] == "1") {
            echo '
            <div id="alert">
                <strong>Success!</strong> User added successfully.
            </div>';
        }
        
        if (isset($_GET["delete_success"]) && $_GET["delete_success"] == "1") {
            echo '
            <div id="alert">
                <strong>Success!</strong> User deleted successfully.
            </div>';
        }
        
         if (isset($_GET["sensors"]) && $_GET["sensors"] == "1") {
            echo '
            <div id="alert_temp">
                <strong>Check the sensors
!</strong>Temperature or Distance or Air Quality Are High.
            </div>';
        }
        ?>



    <div class="background-circle circle1"></div>
    <div class="background-circle circle2"></div>
    <div class="background-circle circle3"></div>


    <div class="navbar">
    <button class="openbtn" onclick="openNav()">☰ Menu</button>
    <div class="navbar-right">
        <div class="navbar-logo">
            <?php echo $_SESSION["username"]; ?>
        </div>
        <img src="personlogo.png" alt="Avatar" style="height: 30px; width: 30px;"> 
    </div>
</div>

    
    
    

    <div id="mySidebar" class="sidebar">
        <a href="javascript:void(0)" id ="close" class="closebtn" onclick="closeNav()">×</a>
        <a href="home.php"><i class="fas fa-home"></i>&nbsp;Home</a>
        <button class="dropdown-btn"><i class="fa fa-user-plus"></i>&nbsp;Manage Users 
        <i class="fa fa-caret-down"></i>
        </button>
  <div class="dropdown-container">
        <a href="add_user.php">&nbsp;Add User</a>
        <a href="delete_user.php">Delete User</a>
  </div>
        <a href="LogManagement.php"><i class="fas fa-clipboard-list"></i>&nbsp;&nbsp;Log Management</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i>&nbsp;Logout</a>
    </div>


    <div class="card">
        <h2>Display Of Sensors</h2>
        <div class="display">
            <a class="sensor_display" href="https://esp32-datalogger.ir/LoginPage/table.php">Tabular</a>
            <a class="sensor_display" href="https://esp32-datalogger.ir/esp-chart.php">Diagrammatic</a>
            <a class="sensor_display" href="https://esp32-datalogger.ir/LoginPage/download.php">Download</a>
        </div>
        <hr>
        <!--<h3 id="connect" style="color:lightgreen; margin-top: 10px; margin-bottom:10px;">This Link Is Reachable!</h3>-->
        <!--<h3 id="disconnect" style="color:red; margin-top: 10px; margin-bottom:10px;">This Link Is Not Reachable!</h3>-->
        <h2><a class="sensor_display" href="http://192.168.112.111/check"  id="myLink">Communication with sensors</a></h2>

        <!--<div class="display">-->
        <!--    <a class="sensor_display" href="http://192.168.1.103/">Temperature</a>-->
        <!--    <a class="sensor_display" href="#">Distance</a>-->
        <!--    <a class="sensor_display" href="#">MQ 135</a>-->
        <!--    <a class="sensor_display" href="#">PIR</a>-->
        <!--</div>-->

    </div>

    <script>
    
      function openNav() {
            document.getElementById("mySidebar").style.width = "300px";
            // document.getElementById("main").style.marginLeft = "300px";
        }

        function closeNav() {
            document.getElementById("mySidebar").style.width = "0";
            // document.getElementById("main").style.marginLeft = "0";
        }
        
        
        
        const myDiv = document.getElementById("alert");
        function makeDivDisappear() {
            myDiv.style.display = "none";
        }
        setTimeout(makeDivDisappear, 3000);
        
        
        
        
        
    document.addEventListener("DOMContentLoaded", function() {
    var link = document.getElementById("myLink");

    link.addEventListener("click", function(event) {
        var confirmation = confirm("This link may not be available, continue?");

        // If user clicked OK
        if (!confirmation) {
            event.preventDefault(); // open link
        }
    });
});
        
    // // Function to check if a URL is reachable using AJAX
    // function isLinkReachable(url, callback) {
    //     var xhr = new XMLHttpRequest();
    //     xhr.open('HEAD', url, true);
    //     xhr.onreadystatechange = function () {
    //         if (xhr.readyState === 4) {
    //             callback(xhr.status === 200);
    //         }
    //     };
    //     xhr.send();
    // }

    // // Event listener for the link
    // document.getElementById('myLink').addEventListener('click', function (event) {
    //     var linkURL = this.href;
    //     isLinkReachable(linkURL, function (reachable) {
    //         if (!reachable) {
    //             var confirmMsg = "The link seems to be disconnected. Do you still want to proceed?";
    //             if (!confirm(confirmMsg)) {
    //                 event.preventDefault(); // Prevent following the link
    //             }
    //         }
    //     });
    // });



var dropdown = document.getElementsByClassName("dropdown-btn");
var i;

for (i = 0; i < dropdown.length; i++) {
  dropdown[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var dropdownContent = this.nextElementSibling;
    if (dropdownContent.style.display === "block") {
      dropdownContent.style.display = "none";
    } else {
      dropdownContent.style.display = "block";
    }
  });
}


    </script>
</body>

</html>