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
        $activity_type = "UserAdd";
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
<link rel="stylesheet" href="css/style.css" type="text/css">
<link rel="stylesheet" href="css/form.css" type="text/css">
<link rel="stylesheet" href="css/nav.css" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


  <title>Add User</title>
  
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
        }

        .openbtn:hover {
            background-color: #79b6d7;
            color: #0d153c;

        }
        #close{
            color: #ff0000;
        }
        #close:hover{
            color: #ff0000;

        }



        #alert {
            padding: 20px;
            background-color: #f44336;
            color: white;
            opacity: 1;
            transition: opacity 0.6s;
            margin-bottom: 0;
            z-index: 10;
        }

                
        .navbar-right {
            display: flex;
            align-items: center;
        }
         .navbar-logo {
            font-size: 30px;
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
   <?php
    // Display the success message if the "success" query parameter is present in the URL
    if (isset($_GET["success"]) && $_GET["success"] == "0") {
        echo '
        <div id="alert">
            <strong>Unsuccessful!</strong> Username is available, try another Username.</div>';
    }
    ?>
    
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
 
    <form action="add_process.php" method="post">
      <img src="add-user.png" alt="Avatar" class="avatar" style="height: 150px; width: 150px;">
      <h1>Add User</h1>
      
      <label for="uname">User Name</label>
      <input type="text" name="username" id="uname" placeholder="User Name" required>

      <label for="password">Password</label>
      <input type="password" name="password" id="password" placeholder="Password" required><br>

      <button type="submit" style="display: block; margin: 0 auto;">Add</button>

    </form>
 
  </div>
  <script>
      const myDiv = document.getElementById("alert");
        function makeDivDisappear() {
            myDiv.style.display = "none";
        }

        setTimeout(makeDivDisappear, 10000);
        
        
        
          function openNav() {
            document.getElementById("mySidebar").style.width = "300px";
            document.getElementById("main").style.marginLeft = "300px";
        }

        function closeNav() {
            document.getElementById("mySidebar").style.width = "0";
            document.getElementById("main").style.marginLeft = "0";
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