<?php

session_start();

function logUserActivity($connection) {
    $query = "SELECT id FROM users WHERE username='". $_SESSION["username"] ."'";
    $result = mysqli_query($connection, $query);
    $activity_type = "Downloading";
    if ($result) {
        $user_id = mysqli_fetch_assoc($result)["id"];
        $insert_query = "INSERT INTO user_activities (user_id, activity_type,download)
                         VALUES ('$user_id', '$activity_type',1)";
        mysqli_query($connection, $insert_query);
    } else {
        echo "Error: " . mysqli_error($connection);
    }
}

   $servername = "localhost";
    // REPLACE with your Database name
    $dbname = "espdata1_esp_datalogger";
    // REPLACE with Database user
    $username = "espdata1_sahand";
    // REPLACE with Database user password
    $password = "sahand.1377";
    
    // Create a connection
    $connection = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }
    
    

    
    if (isset($_GET["num_read"])) {
        $readings_count = $_GET["num_read"];
        // Process and display other data in a similar way
    }
    if (isset($_GET["temp"])) {
        $temp = $_GET["temp"];
        // Process and display other data in a similar way
    }else{
        $temp = "off";
    }
    if (isset($_GET["dis"])) {
        $dis = $_GET["dis"];
        // Process and display other data in a similar way
    }else{
        $dis = "off";
    }
    if (isset($_GET["mq"])) {
        $mq = $_GET["mq"];
    }else{
        $mq = "off";
    }
    
    if (isset($_GET["all"])) {
        $all = $_GET["all"];
    }else{
        $all = "off";
    }
     if (isset($_GET["pir_m"])) {
        $pir_m = $_GET["pir_m"];
    }else{
        $pir_m = "off";
    }

    if (isset($_GET["check-in"]) && isset($_GET["check-out"])) {
    $first = $_GET["check-in"];
    $last = $_GET["check-out"];
    }

    
    if(isset($_GET["temp_min"]) && isset($_GET["temp_max"])){
    $t_min = $_GET["temp_min"];
    $t_max = $_GET["temp_max"];
    }
    
    if(isset($_GET["dis_min"]) && isset($_GET["dis_max"])){
    $d_min = $_GET["dis_min"];
    $d_max = $_GET["dis_max"];
    }
    
    if(isset($_GET["mq_min"]) && isset($_GET["mq_max"])){
    $mqmin = $_GET["mq_min"];
    $mqmax = $_GET["mq_max"];
    }
   
    
    if(isset($_GET["pir"])){
        $pir = $_GET["pir"];
    }
    
    
    if(isset($_GET["wifi"])){
        $wifi = $_GET["wifi"];
    }
    
   if(($temp == "on") && ($dis == "off") && ($all == "off") &&  ($mq == "off") && ($pir_m == "off")){
    
    $query = "SELECT Number, DS18B20, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
    AND DS18B20 >= '$t_min' AND DS18B20 <= '$t_max' order by Time desc limit " . $readings_count;
    
    $result = $connection->query($query);
    // Set HTTP headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="Temperature.csv"');
    
    // Create a file pointer to output CSV data
    $output = fopen('php://output', 'w');
    
    // Write column headers to the CSV
    fputcsv($output, array('Number', 'Temperature', 'Time'));
    
    // Loop through the query results and write to CSV
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
    
    // Close file pointer
    fclose($output);
    
    logUserActivity($connection);

        // Close the database connection
    $connection->close();
    }
    else if(($temp == "on") &&($dis == "on") && ($all == "off") &&  ($mq == "off") && ($pir_m == "off")){
        
    $query = "SELECT Number, DS18B20,HC_SR04 ,Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
    AND DS18B20 >= '$t_min' AND DS18B20 <= '$t_max' AND HC_SR04 >= '$d_min' AND HC_SR04 <= '$d_max' order by Time desc limit " . $readings_count;
    
    $result = $connection->query($query);
    // Set HTTP headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="Temperature-Distance.csv"');
    
    // Create a file pointer to output CSV data
    $output = fopen('php://output', 'w');
    
    // Write column headers to the CSV
    fputcsv($output, array('Number', 'Temperature', 'Distance', 'Time'));
    
    // Loop through the query results and write to CSV
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
    
    // Close file pointer
    fclose($output);
    
    logUserActivity($connection);

    // Close the database connection
    $connection->close();
    }
    
    else if(($temp == "on") &&($dis == "on") && ($all == "off") &&  ($mq == "on") && ($pir_m == "off")){
            
    $query = "SELECT Number, DS18B20,HC_SR04, MQ_135, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
    AND DS18B20 >= '$t_min' AND DS18B20 <= '$t_max' AND HC_SR04 >= '$d_min' AND HC_SR04 <= '$d_max' AND MQ_135 >= '$mqmin' AND MQ_135 <= '$mqmax' order by Time desc limit " . $readings_count;
    
    $result = $connection->query($query);
    // Set HTTP headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="Temperature-Distance-AirQuality.csv"');
    
    // Create a file pointer to output CSV data
    $output = fopen('php://output', 'w');
    
    // Write column headers to the CSV
    fputcsv($output, array('Number', 'Temperature', 'Distance','Air Quality', 'Time'));
    
    // Loop through the query results and write to CSV
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
    
    // Close file pointer
    fclose($output);
    logUserActivity($connection);
   
    // Close the database connection
    $connection->close();
    }
    
    else if((($temp == "on") && ($dis == "on") && ($all == "off") &&  ($mq == "on") && ($pir_m == "on")) || ($all == "on") || (($temp == "on") && ($dis == "on") && ($all == "on") &&  ($mq == "on") && ($pir_m == "on") && ($all == "on"))){
        
         if($pir == "Detected"){
            $query = "SELECT Number, DS18B20,HC_SR04, MQ_135, PIR_Motion,WiFi_Mode,  Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
            AND DS18B20 >= '$t_min' AND DS18B20 <= '$t_max' AND HC_SR04 >= '$d_min' AND HC_SR04 <= '$d_max'
            AND MQ_135 >= '$mqmin' AND MQ_135 <= '$mqmax' AND PIR_Motion = 'Motion Detected' AND WiFi_Mode = '$wifi' order by Time desc limit " . $readings_count; 
         }
         else if($pir == "No Detect"){
              $query = "SELECT Number, DS18B20,HC_SR04, MQ_135 ,PIR_Motion,WiFi_Mode, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
                AND DS18B20 >= '$t_min' AND DS18B20 <= '$t_max' AND HC_SR04 >= '$d_min' AND HC_SR04 <= '$d_max'
                AND MQ_135 >= '$mqmin' AND MQ_135 <= '$mqmax' AND PIR_Motion = 'No Motion' AND WiFi_Mode = '$wifi' order by Time desc limit " . $readings_count; 
              }
         else{
              $query = "SELECT Number, DS18B20,HC_SR04 , MQ_135 ,PIR_Motion, WiFi_Mode, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
            AND DS18B20 >= '$t_min' AND DS18B20 <= '$t_max' AND HC_SR04 >= '$d_min' AND HC_SR04 <= '$d_max' AND MQ_135 >= '$mqmin' AND MQ_135 <= '$mqmax' AND WiFi_Mode = '$wifi' order by Time desc limit " . $readings_count;
         }    
            
        $result = $connection->query($query);
        // Set HTTP headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Temperature-Distance-AirQuality-Motion.csv"');
        
        // Create a file pointer to output CSV data
        $output = fopen('php://output', 'w');
        
        // Write column headers to the CSV
        fputcsv($output, array('Number', 'Temperature', 'Distance','Air Quality','Motion', 'Time'));
        
        // Loop through the query results and write to CSV
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
        
        // Close file pointer
        fclose($output);
        logUserActivity($connection);
       
        // Close the database connection
        $connection->close();
    }
    
    else if(($temp == "on") && ($dis == "off") && ($all == "off") &&  ($mq == "on") && ($pir_m == "off")){
        
        $query = "SELECT Number, DS18B20, MQ_135, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
        AND DS18B20 >= '$t_min' AND DS18B20 <= '$t_max' AND MQ_135 >= '$mqmin' AND MQ_135 <= '$mqmax' order by Time desc limit " . $readings_count;
        
        $result = $connection->query($query);
        // Set HTTP headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Temperature-AirQuality.csv"');
        
        // Create a file pointer to output CSV data
        $output = fopen('php://output', 'w');
        
        // Write column headers to the CSV
        fputcsv($output, array('Number', 'Temperature', 'Air Quality', 'Time'));
        
        // Loop through the query results and write to CSV
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
        
        // Close file pointer
        fclose($output);
       logUserActivity($connection);
     
        // Close the database connection
        $connection->close();
    }
     else if(($temp == "on") && ($dis == "off") && ($all == "off") &&  ($mq == "on") && ($pir_m == "on")){
        
         if($pir == "Detected"){
            $query = "SELECT Number, DS18B20, MQ_135, PIR_Motion, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
            AND DS18B20 >= '$t_min' AND DS18B20 <= '$t_max' AND MQ_135 >= '$mqmin' AND MQ_135 <= '$mqmax' AND PIR_Motion = 'Motion Detected' order by Time desc limit " . $readings_count; 
         }
         else if($pir == "No Detect"){
              $query = "SELECT Number, DS18B20, MQ_135 ,PIR_Motion, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
                AND DS18B20 >= '$t_min' AND DS18B20 <= '$t_max' AND MQ_135 >= '$mqmin' AND MQ_135 <= '$mqmax' AND PIR_Motion = 'No Motion' order by Time desc limit " . $readings_count; 
              }
         else{
              $query = "SELECT Number, DS18B20 , MQ_135 ,PIR_Motion, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
            AND DS18B20 >= '$t_min' AND DS18B20 <= '$t_max' AND MQ_135 >= '$mqmin' AND MQ_135 <= '$mqmax' order by Time desc limit " . $readings_count;
         }    
            
        $result = $connection->query($query);
        // Set HTTP headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Temperature-AirQuality-Motion.csv"');
        
        // Create a file pointer to output CSV data
        $output = fopen('php://output', 'w');
        
        // Write column headers to the CSV
        fputcsv($output, array('Number', 'Temperature','Air Quality','Motion', 'Time'));
        
        // Loop through the query results and write to CSV
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
        
        // Close file pointer
        fclose($output);
         logUserActivity($connection);
   
        // Close the database connection
        $connection->close();
        
    }
    else if(($temp == "on") && ($dis == "on") && ($all == "off") &&  ($mq == "off") && ($pir_m == "on")){
        
         if($pir == "Detected"){
            $query = "SELECT Number, DS18B20,HC_SR04, PIR_Motion, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
            AND DS18B20 >= '$t_min' AND DS18B20 <= '$t_max' AND HC_SR04 >= '$d_min' AND HC_SR04 <= '$d_max' AND PIR_Motion = 'Motion Detected' order by Time desc limit " . $readings_count; 
         }
         else if($pir == "No Detect"){
              $query = "SELECT Number, DS18B20,HC_SR04 ,PIR_Motion, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
                AND DS18B20 >= '$t_min' AND DS18B20 <= '$t_max' AND HC_SR04 >= '$d_min' AND HC_SR04 <= '$d_max' AND PIR_Motion = 'No Motion' order by Time desc limit " . $readings_count; 
              }
         else{
              $query = "SELECT Number, DS18B20,HC_SR04 ,PIR_Motion, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
            AND DS18B20 >= '$t_min' AND DS18B20 <= '$t_max' AND HC_SR04 >= '$d_min' AND HC_SR04 <= '$d_max' order by Time desc limit " . $readings_count;
         }    
            
        $result = $connection->query($query);
        // Set HTTP headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Temperature-Distance-Motion.csv"');
        
        // Create a file pointer to output CSV data
        $output = fopen('php://output', 'w');
        
        // Write column headers to the CSV
        fputcsv($output, array('Number', 'Temperature', 'Distance','Motion', 'Time'));
        
        // Loop through the query results and write to CSV
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
        
        // Close file pointer
        fclose($output);
         logUserActivity($connection);
   
        // Close the database connection
        $connection->close();
        
    }
    else if(($temp == "on") && ($dis == "off") && ($all == "off") &&  ($mq == "off") && ($pir_m == "on")){
         if($pir == "Detected"){
            $query = "SELECT Number, DS18B20, PIR_Motion, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
            AND DS18B20 >= '$t_min' AND DS18B20 <= '$t_max'AND PIR_Motion = 'Motion Detected' order by Time desc limit " . $readings_count; 
         }
         else if($pir == "No Detect"){
              $query = "SELECT Number, DS18B20,PIR_Motion, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
                AND DS18B20 >= '$t_min' AND DS18B20 <= '$t_max' AND PIR_Motion = 'No Motion' order by Time desc limit " . $readings_count; 
              }
         else{
              $query = "SELECT Number, DS18B20,PIR_Motion, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
            AND DS18B20 >= '$t_min' AND DS18B20 <= '$t_max' order by Time desc limit " . $readings_count;
         }    
            
        $result = $connection->query($query);
        // Set HTTP headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Temperature-Motion.csv"');
        
        // Create a file pointer to output CSV data
        $output = fopen('php://output', 'w');
        
        // Write column headers to the CSV
        fputcsv($output, array('Number', 'Temperature', 'Motion', 'Time'));
        
        // Loop through the query results and write to CSV
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
        
        // Close file pointer
        fclose($output);
        logUserActivity($connection);
    
        // Close the database connection
        $connection->close();
    }
     else if(($temp == "off") && ($dis == "on") && ($all == "off") &&  ($mq == "off") && ($pir_m == "off")){
         
        $query = "SELECT Number, HC_SR04, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' AND HC_SR04 >= '$d_min' AND HC_SR04 <= '$d_max' order by Time desc limit " . $readings_count;
        
        $result = $connection->query($query);
        // Set HTTP headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Distance.csv"');
        
        // Create a file pointer to output CSV data
        $output = fopen('php://output', 'w');
        
        // Write column headers to the CSV
        fputcsv($output, array('Number','Distance', 'Time'));
        
        // Loop through the query results and write to CSV
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
        
        // Close file pointer
        fclose($output);
        logUserActivity($connection);
    
        // Close the database connection
        $connection->close();
    }
     else if(($temp == "off") && ($dis == "on") && ($all == "off") &&  ($mq == "on") && ($pir_m == "off")){
        $query = "SELECT Number,HC_SR04, MQ_135, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
        AND HC_SR04 >= '$d_min' AND HC_SR04 <= '$d_max' AND MQ_135 >= '$mqmin' AND MQ_135 <= '$mqmax' order by Time desc limit " . $readings_count;
        
        $result = $connection->query($query);
        // Set HTTP headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Distance-AirQuality.csv"');
        
        // Create a file pointer to output CSV data
        $output = fopen('php://output', 'w');
        
        // Write column headers to the CSV
        fputcsv($output, array('Number','Distance','Air Quality', 'Time'));
        
        // Loop through the query results and write to CSV
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
        
        // Close file pointer
        fclose($output);
        logUserActivity($connection);
    
        // Close the database connection
        $connection->close();
    }
     else if(($temp == "off") && ($dis == "on") && ($all == "off") &&  ($mq == "off") && ($pir_m == "on")){
        
        
         if($pir == "Detected"){
            $query = "SELECT Number,HC_SR04, PIR_Motion, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
            AND HC_SR04 >= '$d_min' AND HC_SR04 <= '$d_max' AND PIR_Motion = 'Motion Detected' order by Time desc limit " . $readings_count; 
         }
         else if($pir == "No Detect"){
              $query = "SELECT Number,HC_SR04 ,PIR_Motion, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
                AND HC_SR04 >= '$d_min' AND HC_SR04 <= '$d_max' AND PIR_Motion = 'No Motion' order by Time desc limit " . $readings_count; 
              }
         else{
              $query = "SELECT Number,HC_SR04 ,PIR_Motion, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
             AND HC_SR04 >= '$d_min' AND HC_SR04 <= '$d_max' order by Time desc limit " . $readings_count;
         }    
            
        $result = $connection->query($query);
        // Set HTTP headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Distance-Motion.csv"');
        
        // Create a file pointer to output CSV data
        $output = fopen('php://output', 'w');
        
        // Write column headers to the CSV
        fputcsv($output, array('Number', 'Distance','Motion', 'Time'));
        
        // Loop through the query results and write to CSV
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
        
        // Close file pointer
        fclose($output);
       logUserActivity($connection);
     
        // Close the database connection
        $connection->close();
        
    }
     else if(($temp == "off") && ($dis == "on") && ($all == "off") &&  ($mq == "on") && ($pir_m == "on")){
        
         if($pir == "Detected"){
            $query = "SELECT Number,HC_SR04, MQ_135, PIR_Motion, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
            AND HC_SR04 >= '$d_min' AND HC_SR04 <= '$d_max' AND MQ_135 >= '$mqmin' AND MQ_135 <= '$mqmax' AND PIR_Motion = 'Motion Detected' order by Time desc limit " . $readings_count; 
         }
         else if($pir == "No Detect"){
              $query = "SELECT Number,HC_SR04, MQ_135 ,PIR_Motion, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
                AND HC_SR04 >= '$d_min' AND HC_SR04 <= '$d_max' AND MQ_135 >= '$mqmin' AND MQ_135 <= '$mqmax' AND PIR_Motion = 'No Motion' order by Time desc limit " . $readings_count; 
              }
         else{
              $query = "SELECT Number,HC_SR04 , MQ_135 ,PIR_Motion, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
              AND HC_SR04 >= '$d_min' AND HC_SR04 <= '$d_max' AND MQ_135 >= '$mqmin' AND MQ_135 <= '$mqmax' order by Time desc limit " . $readings_count;
         }    
            
        $result = $connection->query($query);
        // Set HTTP headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Distance-AirQuality-Motion.csv"');
        
        // Create a file pointer to output CSV data
        $output = fopen('php://output', 'w');
        
        // Write column headers to the CSV
        fputcsv($output, array('Number', 'Distance','Air Quality','Motion', 'Time'));
        
        // Loop through the query results and write to CSV
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
        
        // Close file pointer
        fclose($output);
        logUserActivity($connection);
  
        // Close the database connection
        $connection->close();
        
    }
     else if(($temp == "off") && ($dis == "off") && ($all == "off") &&  ($mq == "off") && ($pir_m == "on")){
         if($pir == "Detected"){
            $query = "SELECT Number, PIR_Motion, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
            AND PIR_Motion = 'Motion Detected' order by Time desc limit " . $readings_count; 
         }
         else if($pir == "No Detect"){
              $query = "SELECT Number, PIR_Motion, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
               AND PIR_Motion = 'No Motion' order by Time desc limit " . $readings_count; 
              }
         else{
              $query = "SELECT Number,PIR_Motion, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
              order by Time desc limit " . $readings_count;
         }    
            
        $result = $connection->query($query);
        // Set HTTP headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Motion.csv"');
        
        // Create a file pointer to output CSV data
        $output = fopen('php://output', 'w');
        
        // Write column headers to the CSV
        fputcsv($output, array('Number', 'Motion', 'Time'));
        
        // Loop through the query results and write to CSV
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
        
        // Close file pointer
        fclose($output);
        logUserActivity($connection);
    
        // Close the database connection
        $connection->close();
    }
    else if(($temp == "off") && ($dis == "off") && ($all == "off") &&  ($mq == "on") && ($pir_m == "off")){
        
         $query = "SELECT Number, MQ_135, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
        AND MQ_135 >= '$mqmin' AND MQ_135 <= '$mqmax' order by Time desc limit " . $readings_count;
        
        $result = $connection->query($query);
        // Set HTTP headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="AirQuality.csv"');
        
        // Create a file pointer to output CSV data
        $output = fopen('php://output', 'w');
        
        // Write column headers to the CSV
        fputcsv($output, array('Number','Air Quality', 'Time'));
        
        // Loop through the query results and write to CSV
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
        
        // Close file pointer
        fclose($output);
       logUserActivity($connection);
     
        // Close the database connection
        $connection->close();
        
    }
   else if(($temp == "off") && ($dis == "off") && ($all == "off") &&  ($mq == "on") && ($pir_m == "on")){
       
         if($pir == "Detected"){
            $query = "SELECT Number, MQ_135, PIR_Motion, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
            AND MQ_135 >= '$mqmin' AND MQ_135 <= '$mqmax' AND PIR_Motion = 'Motion Detected' order by Time desc limit " . $readings_count; 
         }
         else if($pir == "No Detect"){
              $query = "SELECT Number,MQ_135 ,PIR_Motion, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
                AND MQ_135 >= '$mqmin' AND MQ_135 <= '$mqmax' AND PIR_Motion = 'No Motion' order by Time desc limit " . $readings_count; 
              }
         else{
              $query = "SELECT Number, MQ_135 ,PIR_Motion, Time FROM sensors WHERE Time BETWEEN '$first' AND '$last' 
             AND MQ_135 >= '$mqmin' AND MQ_135 <= '$mqmax' order by Time desc limit " . $readings_count;
         }    
            
        $result = $connection->query($query);
        // Set HTTP headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="AirQuality-Motion.csv"');
        
        // Create a file pointer to output CSV data
        $output = fopen('php://output', 'w');
        
        // Write column headers to the CSV
        fputcsv($output, array('Number', 'Air Quality','Motion', 'Time'));
        
        // Loop through the query results and write to CSV
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
        
        // Close file pointer
        fclose($output);
        logUserActivity($connection);
    
        // Close the database connection
        $connection->close();
    }else{
        header("Location: download.php");
    }
    
       ?>





