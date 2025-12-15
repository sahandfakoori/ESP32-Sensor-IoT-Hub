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
        $activity_type = "Download";
        $activity_details = null; // You can modify this if you want to provide additional details

        $insert_query = "INSERT INTO user_activities (user_id, activity_type)
                         VALUES ('$user_id', '$activity_type')";
        mysqli_query($conn, $insert_query);


// Close the database connection
mysqli_close($conn);
?>


    <link rel="stylesheet" href="css/style.css" type="text/css">
    <link rel="stylesheet" href="css/nav.css" type="text/css">
    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <title>Download</title>
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
            padding: 20px;
            border: 1px solid #ccc;
            backdrop-filter: blur(20px);
            text-align: center;
            border-radius: 10px;
            width: 45%;
            margin: auto;
            margin-bottom: 10%;
            margin-top: 200px;
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
                width: 80%;
                padding: 25px;
                border-radius: 10px;
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



        .numRead {
            /* margin-left: 5%;
            margin-right: 5%; */
            margin: 2%;
            padding: 3%;
           
        }
         input[type="text"],
         input[type="number"] {
          width: 10%;
          padding: 3px;
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
            margin-top: 3%;
        }


        .range {
            margin: 2%;
            padding: 3%;
          
        }

        input[type="text"] {
            width: 10%;
            padding: 1%;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .templink{
           text-decoration: none;
           padding: 10px;
           margin-top:2%;
           color: #fff;
           background-color: #4CAF50;
           border-radius: 10px;
        }
        input[type="checkbox"] {
            margin-left: 5%; /* Adjust this value to your preference */
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
        <a href="javascript:void(0)" id="close" class="closebtn" onclick="closeNav()">×</a>
        <a href="home.php"><i class="fas fa-home"></i>&nbsp;Home</a>
        <a href="add_user.php"><i class="fa fa-user-plus"></i>&nbsp;Add User</a>
        <a href="LogManagement.php"><i class="fas fa-clipboard-list"></i>&nbsp;&nbsp;Log Management</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i>&nbsp;Logout</a>
    </div>

    <div class="tab-position">
       <h2>Download</h2>
          <form method="get" action="download-data1.php">
              <p>Choose Any Option You Want</p>
              <input type="checkbox" id = "temp" name = "temp">
              <label for="temp">Temperature</label>
              
              
              <input type="checkbox" id = "dis" name = "dis">
              <label for="dis">Distance</label>
              
              
              <input type="checkbox" id = "mq" name = "mq">
              <label for="mq">Air Quality</label>
              
               <input type="checkbox" id = "pir_m" name = "pir_m">
              <label for="pir_m">Motion</label>
              
              <input type="checkbox" id = "all" name = "all">
              <label for="all">All Sensors</label>
              
              
               <div class="numRead">
                <label for="num_read">Number Of Show:</label><br>
                <input type="number" id="num_read" name="num_read" value="200">
            </div>
            <div class="timeRead">
                <label for="check-in">choose your First Date:</label><br>
                <input type="date" id="check-in" name="check-in" value ="2023-01-01"><br>
                <label for="check-out">choose your Last Date:</label><br>
                <input type="date" id="check-out" name="check-out" value = "2024-01-01">
            </div>
            <div class="range">
                <p style="font-size:14px;">Range of Temperature:</p>
                <label for="temp_min">Min:</label>
                <input type="number" id="temp_min" name="temp_min" step="0.01" value = "-60">
                <label for="temp_max">Max:</label>
                <input type="number" id="temp_max" name="temp_max" step="0.01" value = "130">
            </div>
            
             <div class="range">
                <p style="font-size:14px;">Range of Distance:</p>
                <label for="dis_min">Min:</label>
                <input type="number" id="dis_min" name="dis_min" value="1">
                <label for="dis_max">Max:</label>
                <input type="number" id="dis_max" name="dis_max" value="800">
              </div>
              
              
               <div class="range">
                <p style="font-size:14px;">Range of Air Quality:</p>
                <label for="mq_min">Min:</label>
                <input type="number" id="mq_min" name="mq_min" value="0">
                <label for="mq_max">Max:</label>
                <input type="number" id="mq_max" name="mq_max" value = "10000">
                </div>         
                
                <p style="font-size:18px;">Wifi:</p>
                 <label for="wifi">Choose Mode</label><br>
                 <select id="wifi" name="wifi">                  
                    <option >Both</option>
                    <option >Access Point</option>
                    <option >Station</option>
                 </select><br>
                
                
                 <p style="font-size:18px;">Mobility:</p>
                 <label for="pir">Choose a State</label><br>
                 <select id="pir" name="pir">                  
                    <option >Both</option>
                    <option >Detected</option>
                    <option >No Detect</option>
                 </select><br>
                 <input type="submit" value="Download">

          </form>
    </div>

    <!--<div id="tab1" class="tab-content">-->
    <!--    <h2>Temperature</h2>-->
    <!--      <form method="get">-->
    <!--        <div class="numRead">-->
    <!--            <label for="num_read">Number Of Show:</label><br>-->
    <!--            <input type="number" id="num_read" name="num_read" value="<?php echo $readings_count; ?>">-->
    <!--        </div>-->
    <!--        <div class="timeRead">-->
    <!--            <label for="check-in">choose your First Date:</label><br>-->
    <!--            <input type="date" id="check-in" name="check-in" value="<?php echo $first; ?>"><br>-->
    <!--            <label for="check-out">choose your Last Date:</label><br>-->
    <!--            <input type="date" id="check-out" name="check-out" value="<?php echo $last; ?>">-->
    <!--        </div>-->
    <!--        <div class="range">-->
    <!--            <p style="font-size:14px;">Range of Temperature:</p>-->
    <!--            <label for="temp_min">Min:</label>-->
    <!--            <input type="number" id="temp_min" name="temp_min" step="0.01" value="<?php echo $t_min; ?>">-->
    <!--            <label for="temp_max">Max:</label>-->
    <!--            <input type="number" id="temp_max" name="temp_max" step="0.01" value="<?php echo $t_max; ?>">-->
    <!--        </div>-->
    <!--    <a class="templink" href="https://esp32-datalogger.ir/LoginPage/download-temp.php?num_read=<?php echo $readings_count; ?>&check-in=<?php echo $first; ?>&check-out=<?php echo $last; ?>&temp_min=<?php echo $t_min; ?>&temp_max=<?php echo $t_max; ?>">Download</a>-->
    <!--    </form>-->
    <!--</div>-->

    <!--<div id="tab2" class="tab-content">-->
    <!--    <h2>Distance</h2>-->
    <!--     <form method="get">-->
    <!--        <div class="numRead">-->
    <!--            <label for="num_read">Number Of Show:</label><br>-->
    <!--            <input type="number" id="num_read" name="num_read" value="<?php echo $readings_count; ?>">-->
    <!--        </div>-->
    <!--        <div class="timeRead">-->
    <!--            <label for="check-in">choose your First Date:</label><br>-->
    <!--            <input type="date" id="check-in" name="check-in" value="<?php echo $first; ?>"><br>-->
    <!--            <label for="check-out">choose your Last Date:</label><br>-->
    <!--            <input type="date" id="check-out" name="check-out" value="<?php echo $last; ?>">-->
    <!--        </div>-->
    <!--          <div class="range">-->
    <!--            <p style="font-size:14px;">Range of Distance:</p>-->
    <!--            <label for="dis_min">Min:</label>-->
    <!--            <input type="number" id="dis_min" name="dis_min" value="<?php echo $d_min; ?>">-->
    <!--            <label for="dis_max">Max:</label>-->
    <!--            <input type="number" id="dis_max" name="dis_max" value="<?php echo $d_max; ?>">-->
                
    <!--          </div>-->
    <!--          <input type="submit" value="Download">-->
    <!--    </form>-->
    <!--</div>-->

    <!--<div id="tab3" class="tab-content">-->
    <!--    <h2>MQ 135</h2>-->
    <!--     <form method="get">-->
    <!--        <div class="numRead">-->
    <!--            <label for="num_read">Number Of Show:</label><br>-->
    <!--            <input type="number" id="num_read" name="num_read" value="<?php echo $readings_count; ?>">-->
    <!--        </div>-->
    <!--        <div class="timeRead">-->
    <!--            <label for="check-in">choose your First Date:</label><br>-->
    <!--            <input type="date" id="check-in" name="check-in" value="<?php echo $first; ?>"><br>-->
    <!--            <label for="check-out">choose your Last Date:</label><br>-->
    <!--            <input type="date" id="check-out" name="check-out" value="<?php echo $last; ?>">-->
    <!--        </div>-->
    <!--            <div class="range">-->
    <!--            <p style="font-size:14px;">Range of Air Quality:</p>-->
    <!--            <label for="mq_min">Min:</label>-->
    <!--            <input type="number" id="mq_min" name="mq_min" value="<?php echo $mqmin; ?>">-->
    <!--            <label for="mq_max">Max:</label>-->
    <!--            <input type="number" id="mq_max" name="mq_max" value="<?php echo $mqmax; ?>">-->
    <!--            </div>         -->
    <!--            <input type="submit" value="Download">-->

    <!--    </form>-->
    <!--</div>-->

    <!--<div id="tab4" class="tab-content">-->
    <!--    <h2>PIR</h2>-->
    <!--     <form method="get">-->
    <!--        <div class="numRead">-->
    <!--            <label for="num_read">Number Of Show:</label><br>-->
    <!--            <input type="number" id="num_read" name="num_read" value="<?php echo $readings_count; ?>">-->
    <!--        </div>-->
    <!--        <div class="timeRead">-->
    <!--            <label for="check-in">choose your First Date:</label><br>-->
    <!--            <input type="date" id="check-in" name="check-in" value="<?php echo $first; ?>"><br>-->
    <!--            <label for="check-out">choose your Last Date:</label><br>-->
    <!--            <input type="date" id="check-out" name="check-out" value="<?php echo $last; ?>">-->
    <!--        </div>-->
    <!--            <div class="range">-->
    <!--             <p style="font-size:18px;">Mobility:</p>-->
    <!--             <label for="pir">Choose a State</label><br>-->
    <!--             <select id="" name="pir">                  -->
    <!--                <option value="2">Both</option>-->
    <!--                <option value="1">Detected</option>-->
    <!--                <option value="0">No Detect</option>-->
    <!--             </select>-->
    <!--            </div>-->
    <!--            <input type="submit" value="Download">-->
    <!--    </form>-->
    <!--</div>-->

    <!--<div id="tab5" class="tab-content">-->
    <!--    <h2>All</h2>-->
    <!--     <form method="get">-->
    <!--        <div class="numRead">-->
    <!--            <label for="num_read">Number Of Show:</label><br>-->
    <!--            <input type="number" id="num_read" name="num_read" value="<?php echo $readings_count; ?>">-->
    <!--        </div>-->
    <!--        <div class="timeRead">-->
    <!--            <label for="check-in">choose your First Date:</label><br>-->
    <!--            <input type="date" id="check-in" name="check-in" value="<?php echo $first; ?>"><br>-->
    <!--            <label for="check-out">choose your Last Date:</label><br>-->
    <!--            <input type="date" id="check-out" name="check-out" value="<?php echo $last; ?>">-->
    <!--        </div>-->
    <!--        <div class="range">-->
    <!--            <p style="font-size:14px;">Range of Temperature:</p>-->
    <!--            <label for="temp_min">Min:</label>-->
    <!--            <input type="number" id="temp_min" name="temp_min" step="0.01" value="<?php echo $t_min; ?>">-->
    <!--            <label for="temp_max">Max:</label>-->
    <!--            <input type="number" id="temp_max" name="temp_max" step="0.01" value="<?php echo $t_max; ?>">-->
    <!--            <hr>-->
    <!--            <p style="font-size:14px;">Range of Distance:</p>-->
    <!--            <label for="dis_min">Min:</label>-->
    <!--            <input type="number" id="dis_min" name="dis_min" value="<?php echo $d_min; ?>">-->
    <!--            <label for="dis_max">Max:</label>-->
    <!--            <input type="number" id="dis_max" name="dis_max" value="<?php echo $d_max; ?>">-->
    <!--            <hr>-->
    <!--            <p style="font-size:14px;">Range of Air Quality:</p>-->
    <!--            <label for="mq_min">Min:</label>-->
    <!--            <input type="number" id="mq_min" name="mq_min" value="<?php echo $mqmin; ?>">-->
    <!--            <label for="mq_max">Max:</label>-->
    <!--            <input type="number" id="mq_max" name="mq_max" value="<?php echo $mqmax; ?>">-->
    <!--            <hr>-->
    <!--            <p style="font-size:18px;">Mobility:</p>-->
    <!--             <label for="pir">Choose a State</label><br>-->
    <!--             <select id="" name="pir">                  -->
    <!--                <option value="2">Both</option>-->
    <!--                <option value="1">Detected</option>-->
    <!--                <option value="0">No Detect</option>-->
    <!--             </select>-->
                

    <!--        </div>-->
    <!--            <input type="submit" value="Download">-->
    <!--    </form>-->
    <!--</div>-->








    <script>

        function openNav() {
            document.getElementById("mySidebar").style.width = "300px";
            document.getElementById("main").style.marginLeft = "300px";

        }

        function closeNav() {
            document.getElementById("mySidebar").style.width = "0";
            document.getElementById("main").style.marginLeft = "0";
        }

    </script>

</body>

</html>