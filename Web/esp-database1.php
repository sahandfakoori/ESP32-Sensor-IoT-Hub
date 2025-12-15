<?php
  $servername = "localhost";

// REPLACE with your Database name
$dbname = "espdata1_esp_datalogger";
// REPLACE with Database user
$username = "espdata1_sahand";
// REPLACE with Database user password
$password = "sahand.1377";

  function insertReading($DS18B20, $HC_SR04, $MQ_135, $PIR_Motion, $WiFi_Mode, $flag, $time) {
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    
    
    $column_name = "flag";
    $query = "SELECT `flag` FROM sensors order by Time desc limit 1";
    $res = $conn->query($query);
    $row = $res->fetch_assoc();
    $last_value = $row[$column_name];
    
    if (($flag == 1) && ($last_value == 0)){
        $msg = "TEMERATURE IS VERY HIGH\nCheck that please!";
        // use wordwrap() if lines are longer than 70 characters
        $msg = wordwrap($msg,70);
        // send email
        mail("sahandfakoori99@gmail.com","High-Temp",$msg);
        }
    
    if(($flag == 0) && ($last_value == 1)){
             $msg = "TEMERATURE IS Good\nDont worry!";
            $msg = wordwrap($msg,70);
            // send email
            mail("sahandfakoori99@gmail.com","Good-Temp",$msg);
        }
        

    $sql = "INSERT INTO sensors (DS18B20, HC_SR04, MQ_135, PIR_Motion, WiFi_Mode, flag, Time)
    VALUES ('" . $DS18B20 . "', '" . $HC_SR04 . "', '" . $MQ_135 . "', '" . $PIR_Motion . "', '" . $WiFi_Mode . "', '" . $flag . "', '" . $time . "')";

    if ($conn->query($sql) === TRUE) {
      return "New record created successfully";
    }
    else {
      return "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
  }
  
  
  
  
  
  
  
  
  function AP_insertReading($DS18B20, $HC_SR04, $MQ_135, $PIR_Motion, $WiFi_Mode, $time) {
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    

    $sql = "INSERT INTO dataAP (DS18B20, HC_SR04, MQ_135, PIR_Motion, WiFi_Mode, Time)
    VALUES ('" . $DS18B20 . "', '" . $HC_SR04 . "', '" . $MQ_135 . "', '" . $PIR_Motion . "', '" . $WiFi_Mode . "', '" . $time . "')";

    if ($conn->query($sql) === TRUE) {
      return "New record created successfully";
    }
    else {
      return "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
  }
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  //just for temp and order with number and date
  function getAllReadings_temp($limit,$first_day,$last_day,$min_temp,$max_temp){
       global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
     $sql = "SELECT Number, DS18B20, Time FROM sensors WHERE Time BETWEEN '$first_day' AND '$last_day' 
    AND DS18B20 >= '$min_temp' AND DS18B20 <= '$max_temp' order by Time desc limit " . $limit;
   
    if ($result = $conn->query($sql)) {
      return $result;
    }
    else {
      return false;
    }
    $conn->close();
  }
  
  //distance
  function getAllReadings_distance($limit,$first_day,$last_day,$min_dis,$max_dis){
       global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
     $sql = "SELECT Number, HC_SR04, Time FROM sensors WHERE Time BETWEEN '$first_day' AND '$last_day' 
     AND HC_SR04 >= '$min_dis' AND HC_SR04 <= '$max_dis' order by Time desc limit " . $limit;
   
    if ($result = $conn->query($sql)) {
      return $result;
    }
    else {
      return false;
    }
    $conn->close();
  }
  
  
  
   //air quality
  function getAllReadings_mq($limit,$first_day,$last_day,$minmq,$maxmq){
       global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
     $sql = "SELECT Number, MQ_135, Time FROM sensors WHERE Time BETWEEN '$first_day' AND '$last_day' 
     AND MQ_135 >= '$minmq' AND MQ_135 <= '$maxmq' order by Time desc limit " . $limit;
   
    if ($result = $conn->query($sql)) {
      return $result;
    }
    else {
      return false;
    }
    $conn->close();
  }
  
   //air quality
  function getAllReadings_pir($limit,$first_day,$last_day,$motion){
       global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    if($motion == 1){
     $sql = "SELECT Number, PIR_Motion, Time FROM sensors WHERE Time BETWEEN '$first_day' AND '$last_day' 
     AND PIR_Motion = 'Motion Detected' order by Time desc limit " . $limit;
    }
    else if($motion == 0){
     $sql = "SELECT Number, PIR_Motion, Time FROM sensors WHERE Time BETWEEN '$first_day' AND '$last_day' 
     AND PIR_Motion = 'No Motion' order by Time desc limit " . $limit;
    }
    else{
         $sql = "SELECT Number,PIR_Motion , Time FROM sensors WHERE Time BETWEEN '$first_day' AND '$last_day' order by Time desc limit " . $limit;
    }
    if ($result = $conn->query($sql)) {
      return $result;
    }
    else {
      return false;
    }
    $conn->close();
  }
  
  
  //ALL Sensors Filter
  function getAllReadings($limit,$first_day,$last_day,$min_temp,$max_temp,$min_dis,$max_dis,$minmq,$maxmq,$motion,$wifi) {
    global $servername, $username, $password, $dbname;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    if(!($wifi == "WiFi STATION")){
    $wifi = "Access Point";
    }
      
 if($motion == 1){
    $sql = "SELECT Number, DS18B20, HC_SR04, MQ_135, PIR_Motion, WiFi_Mode, Time FROM sensors WHERE Time BETWEEN '$first_day' AND '$last_day' 
    AND DS18B20 >= '$min_temp' AND DS18B20 <= '$max_temp'
    AND HC_SR04 >= '$min_dis' AND HC_SR04 <= '$max_dis' 
    AND MQ_135 >= '$minmq' AND MQ_135 <= '$maxmq'
    AND WiFi_Mode = '$wifi'
    AND PIR_Motion = 'Motion Detected'
    order by Time desc limit " . $limit;
 }
 else if($motion == 0){
      $sql = "SELECT Number, DS18B20, HC_SR04, MQ_135, PIR_Motion, WiFi_Mode, Time FROM sensors WHERE Time BETWEEN '$first_day' AND '$last_day' 
    AND DS18B20 >= '$min_temp' AND DS18B20 <= '$max_temp'
    AND HC_SR04 >= '$min_dis' AND HC_SR04 <= '$max_dis' 
    AND MQ_135 >= '$minmq' AND MQ_135 <= '$maxmq' 
    AND WiFi_Mode = '$wifi'
    AND PIR_Motion = 'No Motion'
    order by Time desc limit " . $limit;
 }
 else{
      $sql = "SELECT Number, DS18B20, HC_SR04, MQ_135, PIR_Motion, WiFi_Mode, Time FROM sensors WHERE Time BETWEEN '$first_day' AND '$last_day' 
    AND DS18B20 >= '$min_temp' AND DS18B20 <= '$max_temp'
    AND HC_SR04 >= '$min_dis' AND HC_SR04 <= '$max_dis' 
    AND MQ_135 >= '$minmq' AND MQ_135 <= '$maxmq' 
    AND WiFi_Mode = '$wifi'
    order by Time desc limit " . $limit;
 }
 
    if ($result = $conn->query($sql)) {
      return $result;
    }
    else {
      return false;
    }
    $conn->close();
  }

?>