<?php
  $servername = "localhost";

// REPLACE with your Database name
$dbname = "espdata1_esp_datalogger";
// REPLACE with Database user
$username = "espdata1_sahand";
// REPLACE with Database user password
$password = "sahand.1377";

//     // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

  
   global $servername, $username, $password, $dbname;
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
  
  function downloadNumber(){
          global $conn; 

  
  $sql = "SELECT download FROM user_activities";
    $result = $conn->query($sql);
    
    $sum = 0; // Initialize the sum variable
    
    // Calculate the sum of values in the "number" column
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sum += $row["download"];
        }
    }
    return $sum;
  }
  
  function fetchUserActivities($last, $first, $readings_count) {
    global $conn; 
    $query = "SELECT * FROM user_activities
              WHERE activity_timestamp BETWEEN '$first' AND '$last'
              ORDER BY activity_timestamp DESC
              LIMIT $readings_count";

    $result = mysqli_query($conn, $query);

    return $result;
}



?>