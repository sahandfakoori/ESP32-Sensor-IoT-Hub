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
        $activity_type = "Diagrammatic";
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
    <script src="https://code.highcharts.com/highcharts.js"></script>

    <title>Diagrammatic</title>
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


        .tab-content {
            display: none;
            padding: 20px;
            border: 1px solid #ccc;
            backdrop-filter: blur(20px);
            text-align: center;
            border-radius: 10px;
            width: 80%;
            margin: auto;
            margin-bottom: 10%;
        }

       
        .tab-position {
            height: 58px;
            width: 403px;
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
                width: 550px;
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
                <hr>
                <p style="font-size:14px;">WiFi Mode</p>
                <label for="wifi">Choose Mode:</label><br>
                <select id="wifi" name="wifi">                  
                    <option value="2">Both</option>
                    <option value="Access Point">Access Point</option>
                    <option value="WiFi STATION">Station</option>
                </select><br>
                <hr>
                <p style="font-size:14px;">Range of Air Quality:</p>
                <label for="mq_min">Min:</label>
                <input type="number" id="mq_min" name="mq_min" value="<?php echo $mqmin; ?>">
                <label for="mq_max">Max:</label>
                <input type="number" id="mq_max" name="mq_max" value="<?php echo $mqmax; ?>">

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
        <a href="#" class="tab" onclick="openTab(event, 'tab5')">All</a>
    </div>

    <div id="tab1" class="tab-content">
        <h2>Temperature</h2>
       <?php
      include_once('esp-database1.php');
      $servername = "localhost";
      $dbname = "espdata1_esp_datalogger";
      $username = "espdata1_sahand";
      $password = "sahand.1377";
    
      // Create connection
      $conn = new mysqli($servername, $username, $password, $dbname);
      // Check connection
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      } 
      if(!($wifi == "2")){
      $sql = "SELECT Number, DS18B20, WiFi_Mode,Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' AND DS18B20 >= '$t_min' AND DS18B20 <= '$t_max' And WiFi_Mode = '$wifi' ORDER BY Time DESC LIMIT " . $readings_count;
      }else{
      $sql = "SELECT Number, DS18B20,Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' AND DS18B20 >= '$t_min' AND DS18B20 <= '$t_max' ORDER BY Time DESC LIMIT " . $readings_count;
      }
      $result = $conn->query($sql);
    
      while ($data = $result->fetch_assoc()){
        $sensor_data[] = $data;
      }
    
      $Time = array_column($sensor_data, 'Time');
    
    // $dates_only = array();
    
    // foreach ($Time as $timestamp) {
    //     $date_only = date("Y-m-d", strtotime($timestamp));
    //     $dates_only[] = $date_only;
    // }
    
      $DS18B20 = json_encode(array_reverse(array_column($sensor_data, 'DS18B20')), JSON_NUMERIC_CHECK);
      $Time = json_encode(array_reverse($Time), JSON_NUMERIC_CHECK);
      // $dates_only = json_encode(array_reverse($dates_only), JSON_NUMERIC_CHECK);
      $result->free();
      $conn->close();
      ?>
    <div id="chart-temperature" class="container"></div>
    </div>


    <div id="tab2" class="tab-content">
        <h2>Distance</h2>

 <?php

 include_once('esp-database1.php');
  $servername = "localhost";
  $dbname = "espdata1_esp_datalogger";
  $username = "espdata1_sahand";
  $password = "sahand.1377";
  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  } 
  
  if(!($wifi == "2")){
      $sql = "SELECT Number, HC_SR04, WiFi_Mode,Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' AND HC_SR04 >= '$d_min' AND HC_SR04 <= '$d_max' And WiFi_Mode = '$wifi' ORDER BY Time DESC LIMIT " . $readings_count;
      }else{
      $sql = "SELECT Number,HC_SR04,Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' AND HC_SR04 >= '$d_min' AND HC_SR04 <= '$d_max' order by Time desc limit " . $readings_count;
      }
  
  
//   $sql = "SELECT Number,HC_SR04,Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' AND HC_SR04 >= '$d_min' AND HC_SR04 <= '$d_max' order by Time desc limit " . $readings_count;

  $result = $conn->query($sql);

  while ($data = $result->fetch_assoc()){
    $sensor_data[] = $data;
  }

  $Time = array_column($sensor_data, 'Time');

// $dates_only = array();

// foreach ($Time as $timestamp) {
//     $date_only = date("Y-m-d", strtotime($timestamp));
//     $dates_only[] = $date_only;
// }

  $HC_SR04 = json_encode(array_reverse(array_column($sensor_data, 'HC_SR04')), JSON_NUMERIC_CHECK);
  $Time = json_encode(array_reverse($Time), JSON_NUMERIC_CHECK);
  // $dates_only = json_encode(array_reverse($dates_only), JSON_NUMERIC_CHECK);

  $result->free();
  $conn->close();
  ?>


            <div id="chart-HC-SR04" class="container"></div>
    </div>

    <div id="tab3" class="tab-content">
        <h2>MQ 135</h2>
      <?php
      include_once('esp-database1.php');
      $servername = "localhost";
      $dbname = "espdata1_esp_datalogger";
      $username = "espdata1_sahand";
      $password = "sahand.1377";
    
      // Create connection
      $conn = new mysqli($servername, $username, $password, $dbname);
      // Check connection
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      } 
      
      if(!($wifi == "2")){
      $sql = "SELECT Number, MQ_135, WiFi_Mode,Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' AND MQ_135 >= '$mqmin' AND MQ_135 <= '$mqmax' And WiFi_Mode = '$wifi' ORDER BY Time DESC LIMIT " . $readings_count;
      }else{
      $sql = "SELECT Number, MQ_135,Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' AND MQ_135 >= '$mqmin' AND MQ_135 <= '$mqmax' order by Time desc limit " . $readings_count;
      }
      
    //   $sql = "SELECT Number, MQ_135,Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' AND MQ_135 >= '$mqmin' AND MQ_135 <= '$mqmax' order by Time desc limit " . $readings_count;
    
      $result = $conn->query($sql);
    
      while ($data = $result->fetch_assoc()){
        $sensor_data[] = $data;
      }
    
      $Time = array_column($sensor_data, 'Time');
    
    // $dates_only = array();
    
    // foreach ($Time as $timestamp) {
    //     $date_only = date("Y-m-d", strtotime($timestamp));
    //     $dates_only[] = $date_only;
    // }
    
      $MQ_135 = json_encode(array_reverse(array_column($sensor_data, 'MQ_135')), JSON_NUMERIC_CHECK);
      $Time = json_encode(array_reverse($Time), JSON_NUMERIC_CHECK);
      // $dates_only = json_encode(array_reverse($dates_only), JSON_NUMERIC_CHECK);
      $result->free();
      $conn->close();
      ?>
            
            
            
            
            <div id="chart-MQ-135" class="container"></div>
    </div>

    <div id="tab5" class="tab-content">
        <h2>All</h2>
        
         <?php
      include_once('esp-database1.php');
      $servername = "localhost";
      $dbname = "espdata1_esp_datalogger";
      $username = "espdata1_sahand";
      $password = "sahand.1377";
    
      // Create connection
      $conn = new mysqli($servername, $username, $password, $dbname);
      // Check connection
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      } 
      if(!($wifi == "2")){
      $sql = "SELECT Number, DS18B20, HC_SR04, MQ_135,WiFi_Mode , Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' AND DS18B20 >= '$t_min' AND DS18B20 <= '$t_max' AND HC_SR04 >= '$d_min' AND HC_SR04 <= '$d_max' AND MQ_135 >= '$mqmin' AND MQ_135 <= '$mqmax' And WiFi_Mode = '$wifi' order by Time desc limit " . $readings_count;
      }
      else{
      $sql = "SELECT Number, DS18B20, HC_SR04, MQ_135 , Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' AND DS18B20 >= '$t_min' AND DS18B20 <= '$t_max' AND HC_SR04 >= '$d_min' AND HC_SR04 <= '$d_max' AND MQ_135 >= '$mqmin' AND MQ_135 <= '$mqmax' order by Time desc limit " . $readings_count;
      }
      $result = $conn->query($sql);
    
      while ($data = $result->fetch_assoc()){
        $sensor_data[] = $data;
      }
    
      $Time = array_column($sensor_data, 'Time');
    
    // $dates_only = array();
    
    // foreach ($Time as $timestamp) {
    //     $date_only = date("Y-m-d", strtotime($timestamp));
    //     $dates_only[] = $date_only;
    // }
        
      $DS18B20 = json_encode(array_reverse(array_column($sensor_data, 'DS18B20')), JSON_NUMERIC_CHECK);
      $HC_SR04 = json_encode(array_reverse(array_column($sensor_data, 'HC_SR04')), JSON_NUMERIC_CHECK);
      $MQ_135 = json_encode(array_reverse(array_column($sensor_data, 'MQ_135')), JSON_NUMERIC_CHECK);
      $Time = json_encode(array_reverse($Time), JSON_NUMERIC_CHECK);
      // $dates_only = json_encode(array_reverse($dates_only), JSON_NUMERIC_CHECK);
      $result->free();
      $conn->close();
      ?>
            <div id="combined-chart-container" class="container"></div>
    </div>






<script src="https://code.highcharts.com/highcharts.js"></script>


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
        
        
var value1 = <?php echo $DS18B20; ?>;
var value2 = <?php echo $HC_SR04; ?>;
var value3 = <?php echo $MQ_135; ?>;

var reading_time = <?php echo $Time; ?>;

var chartT = new Highcharts.Chart({
  chart:{ renderTo : 'chart-temperature' },
  title: { text: 'DS18b20' },
  series: [{
    showInLegend: false,
    name: 'Temperature', // Name of the series for legend
    data: value1,        // Temperature data array
    type: 'line',        // Series type (line chart)
    color: '#059e8a',    // Color of the data line
    marker: {
        enabled: true,    // Show markers for data points
        radius: 4        // Marker radius
    },
    tooltip: {
        valueSuffix: ' °C' // Tooltip suffix for values
    }
  }],
  plotOptions: {
    line: { animation: false,
      dataLabels: { enabled: true }
    },
    series: { color: '#059e8a' }
  },
  xAxis: { 
    type: 'datetime',
    categories: reading_time
  },
  yAxis: {
    title: { text: 'Temperature (Celsius Degree)' }
    //title: { text: 'Temperature (Fahrenheit)' }
  },
  credits: { enabled: false }
});

var chartH = new Highcharts.Chart({
  chart:{ renderTo:'chart-HC-SR04' },
  title: { text: 'HC-SR04' },
  series: [{
    showInLegend: false,
    data: value2
  }],
  plotOptions: {
    line: { animation: false,
      dataLabels: { enabled: true }
    }
  },
  xAxis: {
    type: 'datetime',
    //dateTimeLabelFormats: { second: '%H:%M:%S' },
    categories: reading_time
  },
  yAxis: {
    title: { text: 'Distance (cm)' }
  },
  credits: { enabled: false }
});


var chartP = new Highcharts.Chart({
  chart:{ renderTo:'chart-MQ-135' },
  title: { text: 'Air Quality' },
  series: [{
    showInLegend: false,
    data: value3
  }],
  plotOptions: {
    line: { animation: false,
      dataLabels: { enabled: true }
    },
    series: { color: '#18009c' }
  },
  xAxis: {
    type: 'datetime',
    categories: reading_time
  },
  yAxis: {
    title: { text: 'Quality' }
  },
  credits: { enabled: false }
});




var combinedChart = new Highcharts.Chart({
    chart: {
        renderTo: 'combined-chart-container',
        type: 'line'
    },
    title: {
        text: 'All Sensors'
    },
    xAxis: {
        type: 'datetime',
        categories: reading_time
    },
    yAxis: [
        {
            title: {
                text: 'Temperature (Celsius Degree)',
                style: {
                    color: '#059e8a' // Change the color for this y-axis title
                }
            },
            opposite: false // Place this yAxis on the left
        },
        {
            title: {
                text: 'Distance (cm)',
                style: {
                    color: '#ff0000' // Change the color for this y-axis title
                }
            },
            opposite: true // Place this yAxis on the right
        },
        {
            title: {
                text: 'Quality',
                style: {
                    color: '#18009c' // Change the color for this y-axis title
                }
            },
            opposite: true // Place this yAxis on the right
        }
    ],
    series: [
        {
            name: 'Temperature',
            data: value1,
            yAxis: 0,
            color: '#059e8a',
            marker: {
                enabled: true,
                radius: calculateMarkerRadius()
            }
        },
        {
            name: 'Distance',
            data: value2,
            yAxis: 1,
            color: '#ff0000', // Adjust the color
            marker: {
                enabled: true,
                radius: calculateMarkerRadius()
            }
        },
        {
            name: 'Air Quality',
            data: value3,
            yAxis: 2,
            color: '#18009c',
            marker: {
                enabled: true,
                radius: calculateMarkerRadius()
            }
        }
    ],
    plotOptions: {
        line: {
            animation: false,
            dataLabels: {
                enabled: true
            }
        }
    },
    credits: {
        enabled: false
    }
});

function calculateMarkerRadius() {
    var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
    if (screenWidth <= 1080) {
        return 2; // Adjust the marker radius for mobile devices
    } else {
        return 4; // Default marker radius for larger screens
    }
}
    </script>

</body>

</html>