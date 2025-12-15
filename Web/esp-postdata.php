<?php
  include_once('esp-database1.php');
  $api_key_value = "tPmAT5Ab3j7F9";
//   $api_key= $DS18B20 = $HC_SR04 = $MQ_135 = $PIR_Motion = $WiFi_Mode = "";
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $api_key = test_input($_POST["api_key"]);
    if($api_key == $api_key_value) {
        $DS18B20 = test_input($_POST["DS18B20"]);
        $HC_SR04 = test_input($_POST["HC_SR04"]);
        $MQ_135 = test_input($_POST["MQ_135"]);
        $PIR_Motion = test_input($_POST["PIR_Motion"]);
        $WiFi_Mode = test_input($_POST["WiFi_Mode"]);
        $flag = test_input($_POST["flag"]);
        $time = test_input($_POST["Time"]);
        
        $result = insertReading($DS18B20, $HC_SR04, $MQ_135, $PIR_Motion, $WiFi_Mode,$flag,$time);
        echo $result;
    }
    else {
      echo "Wrong API Key provided.";
    }
  }
  else {
    echo "No data posted with HTTP POST.";
  }

  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
  
  
  
  