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
        $activity_type = "Tabular";
        $activity_details = null; // You can modify this if you want to provide additional details

        $insert_query = "INSERT INTO user_activities (user_id, activity_type)
                         VALUES ('$user_id', '$activity_type')";
        mysqli_query($conn, $insert_query);


// Close the database connection
mysqli_close($conn);
?>


<?php
    include('esp-database1.php');
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
    
    if(isset($_GET["temp_min"]) && isset($_GET["temp_max"])){
    $t_min = $_GET["temp_min"];
    $t_max = $_GET["temp_max"];
    }
    else{
        $t_min = -60;
        $t_max= 130;
    }
    
    if(isset($_GET["dis_min"]) && isset($_GET["dis_max"])){
    $d_min = $_GET["dis_min"];
    $d_max = $_GET["dis_max"];
    }
    else{
        $d_min = 0;
        $d_max= 800;
    }
    
     if(isset($_GET["mq_min"]) && isset($_GET["mq_max"])){
    $mqmin = $_GET["mq_min"];
    $mqmax = $_GET["mq_max"];
    }
    else{
        $mqmin = 0;
        $mqmax= 5000;
    }
    
    
    if(isset($_GET["pir"])){
        $pir = $_GET["pir"];
    }
   else{
        $pir = 2;
    }
      
    if(isset($_GET["wifi"])){
        $wifi = $_GET["wifi"];
    }
   else{
        $wifi = 2;
    }
    ?>
    
   
    
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <link rel="stylesheet" href="css/nav.css" type="text/css">
    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <title>Tabular</title>
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
            display: none;
            padding: 20px;
            border: 1px solid #ccc;
            backdrop-filter: blur(20px);
            text-align: center;
            border-radius: 10px;
            width: 45%;
            margin: auto;
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

        .tab-position {
            height: 58px;
            width: 472px;
            border-radius: 20px;
            border: 3px solid rgb(255, 255, 255);
            background: rgba(39, 39, 39, 0.1);
            backdrop-filter: blur(60px);
            /* margin-left: 550px;*/
            margin: auto;
            margin-top: 200px;
            text-align: center;
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


            .tab-position {
                height: 83px;
                width: 650px;
                /* margin-left: 21%; */
                margin: auto;
                margin-top: 40%;
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

        .numRead {
            /* margin-left: 5%;
            margin-right: 5%; */
            margin: 5%;
            padding: 3%;
            border: 1px solid #fff;
            border-radius: 5px;
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

        .timeRead {
            margin: 5%;
            /* margin-left: 5%;
            margin-right: 5%; */
            padding: 3%;
            border: 1px solid #fff;
            border-radius: 5px;
        }

        .range {
            margin: 5%;
            padding: 3%;
            border: 1px solid #fff;
            border-radius: 5px;
        }

        input[type="text"] {
            width: 20%;
            padding: 1%;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
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
        <!-- <a href="#">About</a>
        <a href="#">Services</a>
        <a href="#">Clients</a>
        <a href="#">Contact</a> -->
        <form method="get">
            <div class="numRead">
                <label for="num_read">Number Of Show:</label><br>
                <input type="number" id="num_read" name="num_read" value="<?php echo $readings_count; ?>">
            </div>
            <div class="timeRead">
                <label for="check-in">choose your First Date:</label><br>
                <input type="date" id="check-in" name="check-in" value="<?php echo $first; ?>"><br>
                <label for="check-out">choose your Last Date:</label><br>
                <input type="date" id="check-out" name="check-out" value="<?php echo $last; ?>">
            </div>
            <div class="range">
                <p style="font-size:14px;">Range of Temperature:</p>
                <label for="temp_min">Min:</label>
                <input type="number" id="temp_min" name="temp_min" step="0.01" value="<?php echo $t_min; ?>">
                <label for="temp_max">Max:</label>
                <input type="number" id="temp_max" name="temp_max" step="0.01" value="<?php echo $t_max; ?>">
                <hr>
                <p style="font-size:14px;">Range of Distance:</p>
                <label for="dis_min">Min:</label>
                <input type="number" id="dis_min" name="dis_min" value="<?php echo $d_min; ?>">
                <label for="dis_max">Max:</label>
                <input type="number" id="dis_max" name="dis_max" value="<?php echo $d_max; ?>">
                <hr>
                <p style="font-size:14px;">Range of Air Quality:</p>
                <label for="mq_min">Min:</label>
                <input type="number" id="mq_min" name="mq_min" value="<?php echo $mqmin; ?>">
                <label for="mq_max">Max:</label>
                <input type="number" id="mq_max" name="mq_max" value="<?php echo $mqmax; ?>">
                <hr>
                <p style="font-size:14px;">WiFi Mode</p>
                <label for="wifi">Choose Mode:</label><br>
                <select id="" name="wifi">                  
                    <option value="2">Both</option>
                    <option value="1">Access Point</option>
                    <option value="0">Station</option>
                </select>
                <hr>
                <p style="font-size:14px;">Mobility:</p>
                <!--<input type="radio" id="pir_max" name="pir_max" value="<?php echo "motion"; ?>">-->
                <!--<label for="pir_max">Detected</label><br>-->
                <!--<input type="radio" id="pir_min" name="pir_min" value="<?php echo "no motion"; ?>">-->
                <!--<label for="pir_min">No Detect</label>-->
                
               <label for="pir">Choose a State</label><br>
                 <select id="" name="pir">                  
                    <option value="2">Both</option>
                    <option value="1">Detected</option>
                    <option value="0">No Detect</option>
                 </select>
                

            </div>
                <input type="submit" value="Submit">

        </form>
    </div>



    <div id="main">
        <button class="openbtnn" onclick="openNavv()">Filter</button>
    </div>



    <div class="tab-position">
        <a href="#" class="tab" onclick="openTab(event, 'tab1')">Temperature</a>
        <a href="#" class="tab" onclick="openTab(event, 'tab2')">Distance</a>
        <a href="#" class="tab" onclick="openTab(event, 'tab3')">MQ 135</a>
        <a href="#" class="tab" onclick="openTab(event, 'tab4')">PIR</a>
        <a href="#" class="tab" onclick="openTab(event, 'tab5')">All</a>
    </div>

    <div id="tab1" class="tab-content">
        <h2>Temperature</h2>
        <table>
            <tr>
                <th>Number</th>
                <th>Data</th>
                <th>Time</th>
            </tr>
            <tr>
                <?php 
                $result = getAllReadings_temp($readings_count,$first,$last,$t_min,$t_max);
                if ($result) {
                while ($row = $result->fetch_assoc()) {
                $row_number = $row["Number"];
                $row_ds = $row["DS18B20"];
                $row_time = $row["Time"];
                echo '<td>'. $row_number . '</td>
                <td>'. $row_ds . '</td>
                <td>' . $row_time . '</td>
            </tr>';
                }
        echo '</table>';
        $result->free();
                }
                ?>
    </div>

    <div id="tab2" class="tab-content">
        <h2>Distance</h2>
        <table>
            <tr>
                <th>Number</th>
                <th>Data</th>
                <th>Time</th>
            </tr>
            <tr>
                 <?php 
                $result = getAllReadings_distance($readings_count,$first,$last,$d_min,$d_max);
                if ($result) {
                while ($row = $result->fetch_assoc()) {
                $row_number = $row["Number"];
                $row_ultra = $row["HC_SR04"];
                $row_time = $row["Time"];
                echo '<td>'. $row_number . '</td>
                <td>'. $row_ultra . '</td>
                <td>' . $row_time . '</td>
            </tr>';
                }
        echo '</table>';
        $result->free();
                }
                ?>
    </div>

    <div id="tab3" class="tab-content">
        <h2>MQ 135</h2>
        <table>
            <tr>
                <th>Number</th>
                <th>Data</th>
                <th>Time</th>
            </tr>
            <tr>
                  <?php 
                $result = getAllReadings_mq($readings_count,$first,$last,$mqmin,$mqmax);
                if ($result) {
                while ($row = $result->fetch_assoc()) {
                $row_number = $row["Number"];
                $row_mq = $row["MQ_135"];
                $row_time = $row["Time"];
                echo '<td>'. $row_number . '</td>
                <td>'. $row_mq . '</td>
                <td>' . $row_time . '</td>
            </tr>';
                }
        echo '</table>';
        $result->free();
                }
                ?>
    </div>

    <div id="tab4" class="tab-content">
        <h2>PIR</h2>
        <table>
            <tr>
                <th>Number</th>
                <th>Data</th>
                <th>Time</th>
            </tr>
            <tr>
                 <?php 
                $result = getAllReadings_pir($readings_count,$first,$last,$pir);
                if ($result) {
                while ($row = $result->fetch_assoc()) {
                $row_number = $row["Number"];
                $row_pir = $row["PIR_Motion"];
                $row_time = $row["Time"];
                echo '<td>'. $row_number . '</td>
                <td>'. $row_pir . '</td>
                <td>' . $row_time . '</td>
            </tr>';
                }
        echo '</table>';
        $result->free();
                }
                ?>
    </div>

    <div id="tab5" class="tab-content">
        <h2>All</h2>
        <table>
            <tr>
                <th>Number</th>
                <th>Temperature</th>
                <th>Distance</th>
                <th>Air Quality</th>
                <th>Motion</th>
                <th>WiFi Mode</th>
                <th>Time</th>
            </tr>
            <tr>
                 <?php 
        $result = getAllReadings($readings_count,$first,$last,$t_min,$t_max,$d_min,$d_max,$mqmin,$mqmax,$pir,$wifi);
        if ($result) {
        while ($row = $result->fetch_assoc()) {
            $row_number = $row["Number"];
            $row_ds = $row["DS18B20"];
            $row_ultra = $row["HC_SR04"];
            $row_mq = $row["MQ_135"];
            $row_pir = $row["PIR_Motion"];
            $row_wifi = $row["WiFi_Mode"];
            $row_time = $row["Time"];
            echo '<tr>
                  <td>' . $row_number . '</td>
                  <td>' . $row_ds . '</td>
                  <td>' . $row_ultra . '</td>
                  <td>' . $row_mq . '</td>
                  <td>' . $row_pir . '</td>
                  <td>' . $row_wifi . '</td>
                  <td>' . $row_time . '</td>
                  </tr>';
        }
        echo '</table>';
        $result->free();
    }
?>
    </div>








    <script>
        // const myDiv = document.getElementById("alert");
        // function makeDivDisappear() {
        //     myDiv.style.display = "none";
        // }

        // setTimeout(makeDivDisappear, 3000);

        function openNav() {
            document.getElementById("mySidebar").style.width = "300px";
            document.getElementById("main").style.marginLeft = "300px";

        }

        function closeNav() {
            document.getElementById("mySidebar").style.width = "0";
            document.getElementById("main").style.marginLeft = "0";
        }






        function openTab(event, tabName) {
            var i, tabContent, tabLinks;
            tabContent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabContent.length; i++) {
                tabContent[i].style.display = "none";
            }
            tabLinks = document.getElementsByClassName("tab");
            for (i = 0; i < tabLinks.length; i++) {
                tabLinks[i].className = tabLinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            event.currentTarget.className += " active";
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