<?php
  $servername = "localhost";

// REPLACE with your Database name
$dbname = "espdata1_esp_datalogger";
// REPLACE with Database user
$username = "espdata1_sahand";
// REPLACE with Database user password
$password = "sahand.1377";



// Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    
   
    $readings_count = $_GET['num_read'];
    $first = $_GET['check-in'];
    $last = $_GET['check-out'];
    $temp_min = $_GET['temp_min'];
    $temp_max = $_GET['temp_max'];
        
    
    
    
    $sql = "SELECT Number, DS18B20, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
    AND DS18B20 >= '$temp_min' AND DS18B20 <= '$temp_max' order by Time desc limit " . $readings_count;
    echo $sql;
    $result = $conn->query($sql);

// Generate CSV content
$csvContent = "Number, Data, Time\n";
while ($row = $result->fetch_assoc()) {
    $csvContent .= $row['Number'] . "," . $row['DS18B20'] . "," . $row['Time'] . "\n";
}

// Close the database connection
$conn->close();

// Set the appropriate headers for download
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=data.csv");

// Output the CSV content
echo $csvContent;
?>