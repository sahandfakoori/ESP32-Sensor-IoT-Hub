<?php
  
    header('Content-Type: application/json');
  
    $servername = "localhost";

    // REPLACE with your Database name
    $dbname = "espdata1_esp_datalogger";
    // REPLACE with Database user
    $username = "espdata1_sahand";
    // REPLACE with Database user password
    $password = "sahand.1377";
    
    function insertReading($DS18B20, $HC_SR04, $MQ_135, $PIR_Motion, $WiFi_Mode) {
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO sensors (DS18B20, HC_SR04, MQ_135, PIR_Motion, WiFi_Mode)
    VALUES ('" . $DS18B20 . "', '" . $HC_SR04 . "', '" . $MQ_135 . "', '" . $PIR_Motion . "', '" . $WiFi_Mode . "')";

    if ($conn->query($sql) === TRUE) {
      return "New record created successfully";
    }
    else {
      return "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
  }
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    
    $sql = sprintf("SELECT Number, DS18B20, Time FROM sensors order by Time desc");
    
    $result = $mysqli->query($sql);
    $data = array();
    foreach ($result as $row){
        $data[] = $row;
    }
    $result->close();
    $mysqli->close();
    print json_encode($data);
    ?>