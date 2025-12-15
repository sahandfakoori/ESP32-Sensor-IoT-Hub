<?php

session_start();
if (!isset($_SESSION["username"])) {
    // Redirect to the login page if the user is not logged in
    header("Location: login.php");
    exit();
}
?>

<?php
// Replace these credentials with your actual MySQL database credentials
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
        $activity_type = "LogManagement";
        $activity_details = null; // You can modify this if you want to provide additional details

        $insert_query = "INSERT INTO user_activities (user_id, activity_type)
                         VALUES ('$user_id', '$activity_type')";
        mysqli_query($conn, $insert_query);


// Close the database connection
mysqli_close($conn);
?>


<?php
    include('logmanagement-proccess.php');
    if (isset($_GET["num_read"])){
      $data = $_GET["num_read"];
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      $readings_count = $_GET["num_read"];
    }
    else {
      $readings_count = 200;
    }
    ?>
    <?php

    if (isset($_GET["check-in"]) && isset($_GET["check-out"])) {
    $first = $_GET["check-in"];
    $last = $_GET["check-out"];
    }
    // Store the selected dates in session variables
    else {
      $last = date("Y-m-d", strtotime("+1 days")); // Current date
      $first = date("Y-m-d", strtotime("-7 days")); // 7 days ago
    }

   
    
    ?>
    
   
    
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <link rel="stylesheet" href="css/nav.css" type="text/css">
    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <title>Log Management</title>
    <style>
        body {
            background-color: #151d45;
            color: #fff;
            font-family: Arial, sans-serif;
            overflow: scroll;
            margin: 0;
            padding: 0;
        }

        .background-circle {
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            animation-duration: 6s;
            animation-iteration-count: infinite;
            z-index: -1;
        }

        .circle1 {
            height: 250px;
            width: 250px;
            top: 50px;
            left: 20%;
            background: linear-gradient(to right, #8E54E9, #4776E6);
            animation: bounce 5s linear infinite;
        }

        .circle2 {
            height: 300px;
            width: 300px;
            top: 50%;
            left: 45%;
            background: linear-gradient(to right, #f80759, #bc4e9c);
            animation: bounce 9s linear infinite 1s;
        }

        .circle3 {
            top: 20%;
            right: 22%;
            height: 150px;
            width: 150px;
            background: linear-gradient(to right, #ff5e62, #ff9966);
            animation: bounce 6.5s linear infinite 1.5s;
        }

        @keyframes bounce {
            0% {
                transform: translateY(0px);
            }

            25% {
                transform: translateY(55px);
            }

            50% {
                transform: translateY(0px);
            }

            75% {
                transform: translateY(-55px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .card {
            background-color: rgba(0, 0, 50, 0.8);
            width: 560px;
            padding: 12px;
            margin: auto;
            margin-top: 1%;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(5px);
            text-align: center;
            border-radius: 30px;
        }

        .tab {
            display: inline-block;
            padding: 20px 20px;
            text-decoration: none;
            border-radius: 17px;
            cursor: pointer;
            color: #fff;
        }

        .tab.active {
            background-color: #ff402b;
        }

        .tab:hover {
            background-color: #779ae0;
            color: #151d45;
        }

        tr {
            text-align: center;
        }

        .tab-content {
            padding: 20px;
            border: 1px solid #ccc;
            backdrop-filter: blur(20px);
            text-align: center;
            border-radius: 10px;
            width: 45%;
            margin: auto;
            margin-top: 200px;
            margin-bottom: 10%;
        }

        table {
            width: 100%;
        }

        th,
        td {
            border: 2px solid rgb(115, 162, 251);
            padding: 5px;
        }

        tr {
            text-align: center;
        }

        tr:hover {
            background-color: #e0e0e0;
            color: black;
        }

        
        .tab-content h2 {
            text-align: center;
        }



        @media only screen and (max-width: 1080px) {
            .card {
                margin: 500px auto;
            }

            .circle1 {
                top: 15%;
                left: 6%;
            }

            .circle2 {
                top: 50%;
                left: 60%;
            }

            .circle3 {
                top: 20%;
                right: 6%;
            }



            .tab {
                padding: 30px 33px;
                border-radius: 16px;
                font-weight: 30px;
                font-size: larger;
            }


            .tab-content {
                padding: 25px;
                border-radius: 10px;
                width: 80%;
                margin: auto;
                margin-top: 40%;
                /* margin-left: 25%; */
            }

            th,
            td {
                border: 2px solid rgb(115, 162, 251);
                padding: 15px;
            }
          
          #main {
            margin-top: 12%;
            z-index:10;
         }
         
          .sidebarr {
              margin-top:1%;
          }
        }


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
            display: block;
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
            margin: 0;
        }

        .openbtn:hover {
            background-color: #79b6d7;
            color: #0d153c;

        }

        #close {
            color: #ff0000;
        }

        #close:hover {
            color: #ff0000;
        }

        .navbar-right {
            display: flex;
            align-items: center;
        }

        .navbar-logo {
            font-size: 30px;
        }

        .display {
            display: flex;
            justify-content: space-between;
            padding: 1px 20px;
            margin-bottom: 3%;
        }


        .sidebarr {
            height: 500px;
            width: 0;
            position: fixed;
            z-index: 1;
            top: 15%;
            right: 0;
            background-color: rgb(17, 4, 133);
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 60px;
            border-bottom-left-radius: 30px;
            border-top-left-radius: 30px;
        }

        .sidebarr a {
            padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 25px;
            color: #ffffff;
            display: block;
            transition: 0.3s;
            text-align: right;
        }

        .sidebarr a:hover {
            color: #000000;
        }

        .sidebarr .closebtnn {
            position: absolute;
            top: 0;
            font-size: 36px;
        }

        .openbtnn {
            font-size: 30px;
            cursor: pointer;
            background-color: rgb(17, 4, 133);
            /* color: rgb(0, 0, 0); */
            padding: 15px 15px;
            border: none;
            margin-top: 10%;
            border-top-left-radius: 30px;
            border-bottom-left-radius: 30px;
            color: #fff;
        }

        .openbtnn:hover {
            background-color: #fffefe;
            color: #000000;
        }

        #main {
            transition: margin-right .5s, margin-top .5s;
            /* Add transition for both right and top margins */
            text-align: right;
            position: fixed;
            /* Make sure it stays fixed on the screen */
            top: 200px;
            /* Initial position from the top of the screen */
            right: 0;
            /* Initial position from the right of the screen */
            z-index:10;

        }

        
         input[type="text"],
         input[type="number"] {
          width: 30%;
          padding: 3px;
          margin: px;
          border: 1px solid #ccc;
          border-radius: 4px;
        }
        input[type="submit"] {
            font-size: 25px;
            background-color: #4CAF50;
            color: white;
            padding: 10px 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 34% ;
        }

       

        .range {
            margin: 5%;
            padding: 3%;
            border: 1px solid #fff;
            border-radius: 5px;
        }

        /*input[type="text"] {*/
        /*    width: 20%;*/
        /*    padding: 1%;*/
        /*    margin-bottom: 15px;*/
        /*    border: 1px solid #ccc;*/
        /*    border-radius: 4px;*/
        /*}*/
        
        
        
         .sidebar button:hover{
            color: #ff0000;
         }
        .sidebar button{
            background-color: #08174c;
            padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 25px;
            color: #ffffff;
            display: block;
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
    </style>

</head>

<body>

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
    

    <div id="mySidebarr" class="sidebarr">
        <a href="javascript:void(0)" class="closebtnn" onclick="closeNavv()">×</a>
       
        <form method="get">
            <div class="range">
                <label for="num_read">Number Of Show:</label><br>
                <input type="number" id="num_read" name="num_read" value="<?php echo $readings_count; ?>">
            </div>
            <div class="range">
                <label for="check-in">choose your First Date:</label><br>
                <input type="date" id="check-in" name="check-in" value="<?php echo $first; ?>"><br>
                <label for="check-out">choose your Last Date:</label><br>
                <input type="date" id="check-out" name="check-out" value="<?php echo $last; ?>">
            </div>
            <div class="range">
                <h3>Download: <?php echo downloadNumber(); ?></h3>
            </div>
                <input type="submit" value="Submit">

        </form>
    </div>



    <div id="main">
        <button class="openbtnn" onclick="openNavv()">Filter</button>
    </div>



    

    <div id="tab5" class="tab-content">
        <h2>All</h2>
        <table>
            <tr>
                <th>Number</th>
                <th>User ID</th>
                <th>Activity</th>
                <th>Download</th>
                <th>Time</th>
               
            </tr>
            <tr>
               <?php
        $result = fetchUserActivities($last, $first, $readings_count);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>
                      <td>' . $row["activity_id"] . '</td>
                      <td>' . $row["user_id"] . '</td>
                      <td>' . $row["activity_type"] . '</td>
                      <td>' . $row["download"] . '</td>
                      <td>' . $row["activity_timestamp"] . '</td>
                      </tr>';
            }
            mysqli_free_result($result);
        }
        ?>
    </table>
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



        function openNavv() {
            document.getElementById("mySidebarr").style.width = "350px";
            document.getElementById("main").style.marginRight = "350px";
        }

        function closeNavv() {
            document.getElementById("mySidebarr").style.width = "0";
            document.getElementById("main").style.marginRight = "0";
        }
        
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