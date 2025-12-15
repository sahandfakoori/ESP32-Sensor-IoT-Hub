#include "config.h"
#include "wifi_manager.h"
#include "sensors/temp_sensor.h"
#include "sensors/hc_sr04.h"
#include "sensors/mq135.h"
#include "sensors/pir.h"
#include "sms_manager.h"
#include "time_manager.h"

AsyncWebServer server(80);

void setup() {
    Serial.begin(115200);
    pinMode(Rled, OUTPUT);
    pinMode(Gled, OUTPUT);

    SPIFFS.begin();
    setupAPMode(server);
}

void loop() {

  recieveSMS();
  temp_file = SPIFFS.open(temp_fileName, "a");
  temp_file.close();
  hc_file = SPIFFS.open(hc_fileName, "a");
  hc_file.close();
  mq_file = SPIFFS.open(mq_fileName, "a");
  mq_file.close();
  pir_file = SPIFFS.open(pir_fileName, "a");
  pir_file.close();
  mySwitch.loop();
  //Temp
  sensors.requestTemperatures();
  temperatureC = sensors.getTempCByIndex(0);
  if (temperatureC >= 45){
    sendSMS("     ESP32\nALERT!\nHigh temperature:" + String(temperatureC));
  }
  //MQ-135 Sensor
  sensorValue = analogRead(mq);
  // Serial.println("Air Quality: ");
  // if (sensorValue < 1000) {
  //   Serial.print("Fresh air: ");
  // } else {
  //   Serial.print("poor air: ");
  // }
  // Serial.println(sensorValue);
if (sensorValue >= 3000){
    sendSMS("     ESP32\nALERT!\nBad Air Quality:" + String(sensorValue));
  }
  
  //HC-SR04 Sensor
  digitalWrite(trigPin, LOW);
  delayMicroseconds(200);
  digitalWrite(trigPin, HIGH);
  delayMicroseconds(1000);
  digitalWrite(trigPin, LOW);
  duration = pulseIn(echoPin, HIGH);
  distanceCm = duration * SOUND_SPEED / 2;
  // Serial.print("Distance (cm): ");
  // Serial.println(distanceCm);

  //PIR Sensor
  val = digitalRead(inputPin);
  if (val == HIGH) {
    digitalWrite(ledPin, HIGH);
    if (pirState == LOW) {
      pir_state = "Motion Detected";
      // Serial.println("Motion detected!");
      pirState = HIGH;
    }
  } else {
    digitalWrite(ledPin, LOW);
    if ((pirState == HIGH) || (pirState == LOW)) {
      pir_state = "No Motion";
      // Serial.println("No Motion!");
      pirState = LOW;
    }
  }

  if (mySwitch.getState() == HIGH) {
    if (WiFi.status() != WL_CONNECTED) {
      // WiFi.softAPdisconnect();
      WiFi.softAPdisconnect(false);
      WiFi.begin(ssid, password);
      Serial.println("AP Disconnected");
      delay(3000);
    }
    Serial.println("WiFi STATION");
    wifi_state = "WiFi STATION";
    Serial.print("IP Address: ");
    Serial.println(WiFi.localIP());
    digitalWrite(ledPin, HIGH);

    server.on("/check", HTTP_GET, [](AsyncWebServerRequest* request) {
      String html = "<!DOCTYPE html><html><head>";
      html += "<title>Communications</title>";
      html += "<link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css\">";
      html += "<style>";
      html += "body { background-color: #151d45; color: #fff; font-family: Arial, sans-serif; overflow: hidden; margin: 0; padding: 0; }";
      html += ".background-circle { position: absolute; width: 200px; height: 200px; border-radius: 50%; animation-duration: 6s; animation-iteration-count: infinite; z-index: -1; }";
      html += ".circle1 { height: 250px; width: 250px; top: 50px; left: 20%;background: linear-gradient(to right, #8E54E9, #4776E6);animation: bounce 5s linear infinite; }";
      html += ".circle2 { height: 300px; width: 300px;top: 50%;left: 45%; background: linear-gradient(to right, #f80759, #bc4e9c);animation: bounce 9s linear infinite 1s; }";
      html += ".circle3 {top: 20%;  right: 22%; height: 150px; width: 150px; background: linear-gradient(to right, #ff5e62, #ff9966); animation: bounce 6.5s linear infinite 1.5s; }";
      html += "@keyframes bounce { 0% { transform: translateY(0px); }25% {transform: translateY(55px); } 50% { transform: translateY(0px); } 75% {transform: translateY(-55px); } 100% { transform: translateY(0px);}}";
      html += ".card { background-color: rgba(0, 0, 50, 0.8); width: 560px; padding: 20px;margin: auto; margin-top: 8%; box-shadow: 0 0 10px rgba(255, 255, 255, 0.3); backdrop-filter: blur(5px);text-align: center; border-radius: 30px;}";
      html += "@media only screen and (max-width: 1080px) {.card {margin: 500px auto; }";
      html += " .circle1 { top: 15%; left: 6%; }";
      html += ".circle2 {top: 50%; left: 60%;}";
      html += ".circle3 {top: 20%;right: 6%;}";
      html += ".card {margin-top: 45%; width: 60%;height: 50%;}}";
      html += ".navbar { background-color: rgba(0, 0, 50, 0.8); box-shadow: 0 0 10px rgba(255, 255, 255, 0.3); backdrop-filter: blur(5px); border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;display: flex;  justify-content: space-between; align-items: center; padding: 1px 10px;}";
      html += ".navbar-logo {font-size: 30px;}";
      html += ".sidebar {  height: 100%;  width: 0; position: fixed; z-index: 1; top: 0;left: 0;background-color: #08174c;box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);backdrop-filter: blur(5px);overflow-x: hidden;transition: 0.5s;padding-top: 60px;}";
      html += ".sidebar a, button{padding: 8px 8px 8px 32px; text-decoration: none; font-size: 25px;color: #ffffff; display: block;transition: 0.3s;}";
      html += ".sidebar a,button:hover {color: #ff0000; }";
      html += ".sidebar .closebtn { position: absolute;top: 0;right: 25px;font-size: 36px; margin-left: 50px; }";
      html += ".openbtn {font-size: 20px;cursor: pointer;background-color: rgba(0, 0, 50, 0.8);color: white;padding: 10px 10px;border: none;border-radius: 20px;}";
      html += ".openbtn:hover { background-color: #79b6d7; color: #0d153c; }";
      html += "#close{color: #ff0000;}";
      html += " #close:hover{ color: #ff0000; }";
      html += " #alert {padding: 15px;background-color: #04AA6D; color: white; opacity: 1; transition: opacity 0.6s; }";
      html += ".sidebar { height: 100%; width: 0; position: fixed; z-index: 1; top: 0; left: 0; background-color: #08174c; box-shadow: 0 0 10px rgba(255, 255, 255, 0.3); backdrop-filter: blur(5px); overflow-x: hidden;";
      html += "transition: 0.5s; padding-top: 60px; }";
      html += ".sidebar a { padding: 8px 8px 8px 32px;text-decoration: none;font-size: 25px;color: #ffffff; display: block;transition: 0.3s;}";
      html += ".sidebar a:hover { color: #ff0000; }";
      html += ".sidebar .closebtn {position: absolute;top: 0;right: 25px;font-size: 36px;margin-left: 50px;}";
      html += ".openbtn {font-size: 20px; cursor: pointer;background-color: rgba(0, 0, 50, 0.8);color: white;padding: 10px 10px;border: none;border-radius: 20px;}";
      html += ".openbtn:hover { background-color: #79b6d7; color: #0d153c; }";
      html += ".sidebar button:hover{ color: #ff0000;}";
      html += "#close{color: #ff0000;}";
      html += "#close:hover{color: #ff0000;}";
      html += ".display {    display: flex;  justify-content: space-between;  padding: 1px 20px; margin-bottom: 3%;}";
      html += ".sensor_display { text-decoration: none; color: #fff;padding: 12px;border-radius: 30px;border: 2px solid #fff; margin-bottom: 3%;}";
      html += ".sensor_display:hover {background-color: #79b6d7; color: #0d153c;}";
      html += ".sidebar button{background-color: #08174c;padding: 8px 8px 8px 32px;text-decoration: none; font-size: 25px; color: #ffffff;display: block;transition: 0.3s; display: block; border: none;cursor: pointer;}";
      html += ".dropdown-container {display: none; background-color: #08174c; padding-left: 8px;}";
      html += "</style></head><body>";
      html += "<div class=\"background-circle circle1\"></div>";
      html += "<div class=\"background-circle circle2\"></div>";
      html += "<div class=\"background-circle circle3\"></div>";
      html += "<div class=\"navbar\">";
      html += "<button class=\"openbtn\" onclick=\"openNav()\">Menu</button>";
      html += "</div>";
      html += "<div id=\"mySidebar\" class=\"sidebar\">";
      html += "<a href=\"javascript:void(0)\" id =\"close\" class=\"closebtn\" onclick=\"closeNav()\">x</a>";
      html += "<a href=\"https://esp32-datalogger.ir/LoginPage/home.php\"><i class=\"fas fa-home\"></i>&nbsp;Home</a>";
      html += "<button class=\"dropdown-btn\"><i class=\"fa fa-user-plus\"></i>&nbsp;Manage Users";
      html += "<i class=\"fa fa-caret-down\"></i>";
      html += "</button>";
      html += "<div class=\"dropdown-container\">";
      html += "<a href=\"https://esp32-datalogger.ir/LoginPage/add_user.php\">&nbsp;Add User</a>";
      html += "<a href=\"https://esp32-datalogger.ir/LoginPage/delete_user.php\">Delete User</a>";
      html += "</div>";
      html += "<a href=\"https://esp32-datalogger.ir/LoginPage/LogManagement.php\"><i class=\"fas fa-clipboard-list\"></i>&nbsp;&nbsp;Log Management</a>";
      html += "<a href=\"https://esp32-datalogger.ir/LoginPage/logout.php\"><i class=\"fas fa-sign-out-alt\"></i>&nbsp;Logout</a>";
      html += "</div>";
      html += "<div class=\"card\">";
      html += "<h2>Communication with sensors</h2>";
      html += "<div class=\"display\">";
      html += "<a class=\"sensor_display\" href='/temp'>Temperature</a>";
      html += "<a class=\"sensor_display\" href='/distance'>Distance</a>";
      html += "<a class=\"sensor_display\" href='/mq'>MQ 135</a>";
      html += "</div>";
      html += "</div>";
      html += "<script>";
      html += "function openNav() { document.getElementById(\"mySidebar\").style.width = \"300px\";   document.getElementById(\"main\").style.marginLeft = \"300px\"; }";
      html += "function closeNav() {  document.getElementById(\"mySidebar\").style.width = \"0\";    document.getElementById(\"main\").style.marginLeft = \"0\";}";
      html += "var dropdown = document.getElementsByClassName(\"dropdown-btn\");";
      html += "var i;";
      html += "for (i = 0; i < dropdown.length; i++) {";
      html += "dropdown[i].addEventListener(\"click\", function() {";
      html += "this.classList.toggle(\"active\");";
      html += "var dropdownContent = this.nextElementSibling;";
      html += "if (dropdownContent.style.display === \"block\") {";
      html += "dropdownContent.style.display = \"none\";";
      html += " } else {";
      html += "dropdownContent.style.display = \"block\";";
      html += "}  });}";
      html += "</script></body></html>";
      request->send(200, "text/html", html);
    });
    server.on("/temp", HTTP_GET, [](AsyncWebServerRequest* request) {
      String html = "<!DOCTYPE html><html><head>";
      html += "<title>Communications</title>";
      html += "<link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css\">";
      html += "<style>";
      html += "body { background-color: #151d45; color: #fff; font-family: Arial, sans-serif; overflow: hidden; margin: 0; padding: 0; }";
      html += ".background-circle { position: absolute; width: 200px; height: 200px; border-radius: 50%; animation-duration: 6s; animation-iteration-count: infinite; z-index: -1; }";
      html += ".circle1 { height: 250px; width: 250px; top: 50px; left: 20%;background: linear-gradient(to right, #8E54E9, #4776E6);animation: bounce 5s linear infinite; }";
      html += ".circle2 { height: 300px; width: 300px;top: 50%;left: 45%; background: linear-gradient(to right, #f80759, #bc4e9c);animation: bounce 9s linear infinite 1s; }";
      html += ".circle3 {top: 20%;  right: 22%; height: 150px; width: 150px; background: linear-gradient(to right, #ff5e62, #ff9966); animation: bounce 6.5s linear infinite 1.5s; }";
      html += "@keyframes bounce { 0% { transform: translateY(0px); }25% {transform: translateY(55px); } 50% { transform: translateY(0px); } 75% {transform: translateY(-55px); } 100% { transform: translateY(0px);}}";
      html += ".card { background-color: rgba(0, 0, 50, 0.8); width: 560px; padding: 20px;margin: auto; margin-top: 8%; box-shadow: 0 0 10px rgba(255, 255, 255, 0.3); backdrop-filter: blur(5px);text-align: center; border-radius: 30px;}";
      html += "@media only screen and (max-width: 1080px) {.card {margin: 500px auto; }";
      html += " .circle1 { top: 15%; left: 6%; }";
      html += ".circle2 {top: 50%; left: 60%;}";
      html += ".circle3 {top: 20%;right: 6%;}";
      html += ".card {margin-top: 45%; width: 60%;height: 50%;}}";
      html += ".navbar { background-color: rgba(0, 0, 50, 0.8); box-shadow: 0 0 10px rgba(255, 255, 255, 0.3); backdrop-filter: blur(5px); border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;display: flex;  justify-content: space-between; align-items: center; padding: 1px 10px;}";
      html += ".navbar-logo {font-size: 30px;}";
      html += ".sidebar {  height: 100%;  width: 0; position: fixed; z-index: 1; top: 0;left: 0;background-color: #08174c;box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);backdrop-filter: blur(5px);overflow-x: hidden;transition: 0.5s;padding-top: 60px;}";
      html += ".sidebar a, button{padding: 8px 8px 8px 32px; text-decoration: none; font-size: 25px;color: #ffffff; display: block;transition: 0.3s;}";
      html += ".sidebar a,button:hover {color: #ff0000; }";
      html += ".sidebar .closebtn { position: absolute;top: 0;right: 25px;font-size: 36px; margin-left: 50px; }";
      html += ".openbtn {font-size: 20px;cursor: pointer;background-color: rgba(0, 0, 50, 0.8);color: white;padding: 10px 10px;border: none;border-radius: 20px;}";
      html += ".openbtn:hover { background-color: #79b6d7; color: #0d153c; }";
      html += "#close{color: #ff0000;}";
      html += " #close:hover{ color: #ff0000; }";
      html += " #alert {padding: 15px;background-color: #04AA6D; color: white; opacity: 1; transition: opacity 0.6s; }";
      html += ".sidebar { height: 100%; width: 0; position: fixed; z-index: 1; top: 0; left: 0; background-color: #08174c; box-shadow: 0 0 10px rgba(255, 255, 255, 0.3); backdrop-filter: blur(5px); overflow-x: hidden;";
      html += "transition: 0.5s; padding-top: 60px; }";
      html += ".sidebar a { padding: 8px 8px 8px 32px;text-decoration: none;font-size: 25px;color: #ffffff; display: block;transition: 0.3s;}";
      html += ".sidebar a:hover { color: #ff0000; }";
      html += ".sidebar .closebtn {position: absolute;top: 0;right: 25px;font-size: 36px;margin-left: 50px;}";
      html += ".openbtn {font-size: 20px; cursor: pointer;background-color: rgba(0, 0, 50, 0.8);color: white;padding: 10px 10px;border: none;border-radius: 20px;}";
      html += ".openbtn:hover { background-color: #79b6d7; color: #0d153c; }";
      html += ".sidebar button:hover{ color: #ff0000;}";
      html += "#close{color: #ff0000;}";
      html += "#close:hover{color: #ff0000;}";
      html += ".display {    display: flex;  justify-content: space-between;  padding: 1px 20px; margin-bottom: 3%;}";
      html += ".sensor_display { text-decoration: none; color: #fff;padding: 12px;border-radius: 30px;border: 2px solid #fff; margin-bottom: 3%;}";
      html += ".sensor_display:hover {background-color: #79b6d7; color: #0d153c;}";
      html += ".sidebar button{background-color: #08174c;padding: 8px 8px 8px 32px;text-decoration: none; font-size: 25px; color: #ffffff;display: block;transition: 0.3s; display: block; border: none;cursor: pointer;}";
      html += ".dropdown-container {display: none; background-color: #08174c; padding-left: 8px;}";
      html += ".ds{color: #fff;background-color: red;padding: 5px;border-radius: 10px;width: 14%;}";
      html += ".ds2{color: #fff;background-color: green;padding: 10px 10px;border-radius: 10px;width: 14%;}";
      html += ".space{ display: flex; justify-content: space-between;align-items: center;}";
      html += "</style></head><body>";
      html += "<div class=\"background-circle circle1\"></div>";
      html += "<div class=\"background-circle circle2\"></div>";
      html += "<div class=\"background-circle circle3\"></div>";
      html += "<div class=\"navbar\">";
      html += "<button class=\"openbtn\" onclick=\"openNav()\">Menu</button>";
      html += "</div>";
      html += "<div id=\"mySidebar\" class=\"sidebar\">";
      html += "<a href=\"javascript:void(0)\" id =\"close\" class=\"closebtn\" onclick=\"closeNav()\">x</a>";
      html += "<a href=\"https://esp32-datalogger.ir/LoginPage/home.php\"><i class=\"fas fa-home\"></i>&nbsp;Home</a>";
      html += "<button class=\"dropdown-btn\"><i class=\"fa fa-user-plus\"></i>&nbsp;Manage Users";
      html += "<i class=\"fa fa-caret-down\"></i>";
      html += "</button>";
      html += "<div class=\"dropdown-container\">";
      html += "<a href=\"https://esp32-datalogger.ir/LoginPage/add_user.php\">&nbsp;Add User</a>";
      html += "<a href=\"https://esp32-datalogger.ir/LoginPage/delete_user.php\">Delete User</a>";
      html += "</div>";
      html += "<a href=\"https://esp32-datalogger.ir/LoginPage/LogManagement.php\"><i class=\"fas fa-clipboard-list\"></i>&nbsp;&nbsp;Log Management</a>";
      html += "<a href=\"https://esp32-datalogger.ir/LoginPage/logout.php\"><i class=\"fas fa-sign-out-alt\"></i>&nbsp;Logout</a>";
      html += "</div>";
      html += "<div class=\"card\">";
      html += "<h2>Temerature</h2>";
      html += "<p style=\"text-align:left; margin-bottom:5%;\">The DS18B20 is a digital temperature sensor with a measurement range of -55&deg; to +125&deg;C, using the 1-Wire interface, known for its high accuracy, typically 0.5&deg;C, adjustable to 0.06&deg;C.</p>";
      sensors.requestTemperatures();
      temperatureC = sensors.getTempCByIndex(0);
      html += "<div class=\"space\">";
      if (temperatureC == -127) {
        html += "<div class=\"ds\">Not Connected</div>";
      } else {
        html += "<div class=\"ds2\">Connected</div>";
      }
      html += "<div>Current temperature: " + String(temperatureC);
      html += "</div>";
      html += "</div>";
      html += "</div>";
      html += "<script>";
      html += "function openNav() { document.getElementById(\"mySidebar\").style.width = \"300px\";   document.getElementById(\"main\").style.marginLeft = \"300px\"; }";
      html += "function closeNav() {  document.getElementById(\"mySidebar\").style.width = \"0\";    document.getElementById(\"main\").style.marginLeft = \"0\";}";
      html += "var dropdown = document.getElementsByClassName(\"dropdown-btn\");";
      html += "var i;";
      html += "for (i = 0; i < dropdown.length; i++) {";
      html += "dropdown[i].addEventListener(\"click\", function() {";
      html += "this.classList.toggle(\"active\");";
      html += "var dropdownContent = this.nextElementSibling;";
      html += "if (dropdownContent.style.display === \"block\") {";
      html += "dropdownContent.style.display = \"none\";";
      html += " } else {";
      html += "dropdownContent.style.display = \"block\";";
      html += "}  });}";
      html += "</script></body></html>";
      request->send(200, "text/html", html);
    });

    server.on("/distance", HTTP_GET, [](AsyncWebServerRequest* request) {
      String html = "<!DOCTYPE html><html><head>";
      html += "<title>Communications</title>";
      html += "<link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css\">";
      html += "<style>";
      html += "body { background-color: #151d45; color: #fff; font-family: Arial, sans-serif; overflow: hidden; margin: 0; padding: 0; }";
      html += ".background-circle { position: absolute; width: 200px; height: 200px; border-radius: 50%; animation-duration: 6s; animation-iteration-count: infinite; z-index: -1; }";
      html += ".circle1 { height: 250px; width: 250px; top: 50px; left: 20%;background: linear-gradient(to right, #8E54E9, #4776E6);animation: bounce 5s linear infinite; }";
      html += ".circle2 { height: 300px; width: 300px;top: 50%;left: 45%; background: linear-gradient(to right, #f80759, #bc4e9c);animation: bounce 9s linear infinite 1s; }";
      html += ".circle3 {top: 20%;  right: 22%; height: 150px; width: 150px; background: linear-gradient(to right, #ff5e62, #ff9966); animation: bounce 6.5s linear infinite 1.5s; }";
      html += "@keyframes bounce { 0% { transform: translateY(0px); }25% {transform: translateY(55px); } 50% { transform: translateY(0px); } 75% {transform: translateY(-55px); } 100% { transform: translateY(0px);}}";
      html += ".card { background-color: rgba(0, 0, 50, 0.8); width: 560px; padding: 20px;margin: auto; margin-top: 8%; box-shadow: 0 0 10px rgba(255, 255, 255, 0.3); backdrop-filter: blur(5px);text-align: center; border-radius: 30px;}";
      html += "@media only screen and (max-width: 1080px) {.card {margin: 500px auto; }";
      html += " .circle1 { top: 15%; left: 6%; }";
      html += ".circle2 {top: 50%; left: 60%;}";
      html += ".circle3 {top: 20%;right: 6%;}";
      html += ".card {margin-top: 45%; width: 60%;height: 50%;}}";
      html += ".navbar { background-color: rgba(0, 0, 50, 0.8); box-shadow: 0 0 10px rgba(255, 255, 255, 0.3); backdrop-filter: blur(5px); border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;display: flex;  justify-content: space-between; align-items: center; padding: 1px 10px;}";
      html += ".navbar-logo {font-size: 30px;}";
      html += ".sidebar {  height: 100%;  width: 0; position: fixed; z-index: 1; top: 0;left: 0;background-color: #08174c;box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);backdrop-filter: blur(5px);overflow-x: hidden;transition: 0.5s;padding-top: 60px;}";
      html += ".sidebar a, button{padding: 8px 8px 8px 32px; text-decoration: none; font-size: 25px;color: #ffffff; display: block;transition: 0.3s;}";
      html += ".sidebar a,button:hover {color: #ff0000; }";
      html += ".sidebar .closebtn { position: absolute;top: 0;right: 25px;font-size: 36px; margin-left: 50px; }";
      html += ".openbtn {font-size: 20px;cursor: pointer;background-color: rgba(0, 0, 50, 0.8);color: white;padding: 10px 10px;border: none;border-radius: 20px;}";
      html += ".openbtn:hover { background-color: #79b6d7; color: #0d153c; }";
      html += "#close{color: #ff0000;}";
      html += " #close:hover{ color: #ff0000; }";
      html += " #alert {padding: 15px;background-color: #04AA6D; color: white; opacity: 1; transition: opacity 0.6s; }";
      html += ".sidebar { height: 100%; width: 0; position: fixed; z-index: 1; top: 0; left: 0; background-color: #08174c; box-shadow: 0 0 10px rgba(255, 255, 255, 0.3); backdrop-filter: blur(5px); overflow-x: hidden;";
      html += "transition: 0.5s; padding-top: 60px; }";
      html += ".sidebar a { padding: 8px 8px 8px 32px;text-decoration: none;font-size: 25px;color: #ffffff; display: block;transition: 0.3s;}";
      html += ".sidebar a:hover { color: #ff0000; }";
      html += ".sidebar .closebtn {position: absolute;top: 0;right: 25px;font-size: 36px;margin-left: 50px;}";
      html += ".openbtn {font-size: 20px; cursor: pointer;background-color: rgba(0, 0, 50, 0.8);color: white;padding: 10px 10px;border: none;border-radius: 20px;}";
      html += ".openbtn:hover { background-color: #79b6d7; color: #0d153c; }";
      html += ".sidebar button:hover{ color: #ff0000;}";
      html += "#close{color: #ff0000;}";
      html += "#close:hover{color: #ff0000;}";
      html += ".display {    display: flex;  justify-content: space-between;  padding: 1px 20px; margin-bottom: 3%;}";
      html += ".sensor_display { text-decoration: none; color: #fff;padding: 12px;border-radius: 30px;border: 2px solid #fff; margin-bottom: 3%;}";
      html += ".sensor_display:hover {background-color: #79b6d7; color: #0d153c;}";
      html += ".sidebar button{background-color: #08174c;padding: 8px 8px 8px 32px;text-decoration: none; font-size: 25px; color: #ffffff;display: block;transition: 0.3s; display: block; border: none;cursor: pointer;}";
      html += ".dropdown-container {display: none; background-color: #08174c; padding-left: 8px;}";
      html += ".ds{color: #fff;background-color: red;padding: 5px;border-radius: 10px;width: 14%;}";
      html += ".ds2{color: #fff;background-color: green;padding: 10px 10px;border-radius: 10px;width: 14%;}";
      html += ".space{ display: flex; justify-content: space-between;align-items: center;}";
      html += "</style></head><body>";
      html += "<div class=\"background-circle circle1\"></div>";
      html += "<div class=\"background-circle circle2\"></div>";
      html += "<div class=\"background-circle circle3\"></div>";
      html += "<div class=\"navbar\">";
      html += "<button class=\"openbtn\" onclick=\"openNav()\">Menu</button>";
      html += "</div>";
      html += "<div id=\"mySidebar\" class=\"sidebar\">";
      html += "<a href=\"javascript:void(0)\" id =\"close\" class=\"closebtn\" onclick=\"closeNav()\">x</a>";
      html += "<a href=\"https://esp32-datalogger.ir/LoginPage/home.php\"><i class=\"fas fa-home\"></i>&nbsp;Home</a>";
      html += "<button class=\"dropdown-btn\"><i class=\"fa fa-user-plus\"></i>&nbsp;Manage Users";
      html += "<i class=\"fa fa-caret-down\"></i>";
      html += "</button>";
      html += "<div class=\"dropdown-container\">";
      html += "<a href=\"https://esp32-datalogger.ir/LoginPage/add_user.php\">&nbsp;Add User</a>";
      html += "<a href=\"https://esp32-datalogger.ir/LoginPage/delete_user.php\">Delete User</a>";
      html += "</div>";
      html += "<a href=\"https://esp32-datalogger.ir/LoginPage/LogManagement.php\"><i class=\"fas fa-clipboard-list\"></i>&nbsp;&nbsp;Log Management</a>";
      html += "<a href=\"https://esp32-datalogger.ir/LoginPage/logout.php\"><i class=\"fas fa-sign-out-alt\"></i>&nbsp;Logout</a>";
      html += "</div>";
      html += "<div class=\"card\">";
      html += "<h2>Distance</h2>";
      html += "<p style=\"text-align:left; margin-bottom:5%;\">The HC-SR04 is an ultrasonic distance sensor with a measurement range of about 2cm to 400cm, using pulse-echo signals. Its accuracy is generally around 3mm.</p>";
      digitalWrite(trigPin, LOW);
      delayMicroseconds(200);
      digitalWrite(trigPin, HIGH);
      delayMicroseconds(1000);
      digitalWrite(trigPin, LOW);
      duration = pulseIn(echoPin, HIGH);
      distanceCm = duration * SOUND_SPEED / 2;
      html += "<div class=\"space\">";
      if ((distanceCm < 2)) {
        html += "<div class=\"ds\">Not Connected</div>";
      } else {
        html += "<div class=\"ds2\">Connected</div>";
      }
      html += "<div>Current Distance: " + String(distanceCm);
      html += "</div>";
      html += "</div>";
      html += "</div>";
      html += "<script>";
      html += "function openNav() { document.getElementById(\"mySidebar\").style.width = \"300px\";   document.getElementById(\"main\").style.marginLeft = \"300px\"; }";
      html += "function closeNav() {  document.getElementById(\"mySidebar\").style.width = \"0\";    document.getElementById(\"main\").style.marginLeft = \"0\";}";
      html += "var dropdown = document.getElementsByClassName(\"dropdown-btn\");";
      html += "var i;";
      html += "for (i = 0; i < dropdown.length; i++) {";
      html += "dropdown[i].addEventListener(\"click\", function() {";
      html += "this.classList.toggle(\"active\");";
      html += "var dropdownContent = this.nextElementSibling;";
      html += "if (dropdownContent.style.display === \"block\") {";
      html += "dropdownContent.style.display = \"none\";";
      html += " } else {";
      html += "dropdownContent.style.display = \"block\";";
      html += "}  });}";
      html += "</script></body></html>";
      request->send(200, "text/html", html);
    });

    server.on("/mq", HTTP_GET, [](AsyncWebServerRequest* request) {
      String html = "<!DOCTYPE html><html><head>";
      html += "<title>Communications</title>";
      html += "<link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css\">";
      html += "<style>";
      html += "body { background-color: #151d45; color: #fff; font-family: Arial, sans-serif; overflow: hidden; margin: 0; padding: 0; }";
      html += ".background-circle { position: absolute; width: 200px; height: 200px; border-radius: 50%; animation-duration: 6s; animation-iteration-count: infinite; z-index: -1; }";
      html += ".circle1 { height: 250px; width: 250px; top: 50px; left: 20%;background: linear-gradient(to right, #8E54E9, #4776E6);animation: bounce 5s linear infinite; }";
      html += ".circle2 { height: 300px; width: 300px;top: 50%;left: 45%; background: linear-gradient(to right, #f80759, #bc4e9c);animation: bounce 9s linear infinite 1s; }";
      html += ".circle3 {top: 20%;  right: 22%; height: 150px; width: 150px; background: linear-gradient(to right, #ff5e62, #ff9966); animation: bounce 6.5s linear infinite 1.5s; }";
      html += "@keyframes bounce { 0% { transform: translateY(0px); }25% {transform: translateY(55px); } 50% { transform: translateY(0px); } 75% {transform: translateY(-55px); } 100% { transform: translateY(0px);}}";
      html += ".card { background-color: rgba(0, 0, 50, 0.8); width: 560px; padding: 20px;margin: auto; margin-top: 8%; box-shadow: 0 0 10px rgba(255, 255, 255, 0.3); backdrop-filter: blur(5px);text-align: center; border-radius: 30px;}";
      html += "@media only screen and (max-width: 1080px) {.card {margin: 500px auto; }";
      html += " .circle1 { top: 15%; left: 6%; }";
      html += ".circle2 {top: 50%; left: 60%;}";
      html += ".circle3 {top: 20%;right: 6%;}";
      html += ".card {margin-top: 45%; width: 60%;height: 50%;}}";
      html += ".navbar { background-color: rgba(0, 0, 50, 0.8); box-shadow: 0 0 10px rgba(255, 255, 255, 0.3); backdrop-filter: blur(5px); border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;display: flex;  justify-content: space-between; align-items: center; padding: 1px 10px;}";
      html += ".navbar-logo {font-size: 30px;}";
      html += ".sidebar {  height: 100%;  width: 0; position: fixed; z-index: 1; top: 0;left: 0;background-color: #08174c;box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);backdrop-filter: blur(5px);overflow-x: hidden;transition: 0.5s;padding-top: 60px;}";
      html += ".sidebar a, button{padding: 8px 8px 8px 32px; text-decoration: none; font-size: 25px;color: #ffffff; display: block;transition: 0.3s;}";
      html += ".sidebar a,button:hover {color: #ff0000; }";
      html += ".sidebar .closebtn { position: absolute;top: 0;right: 25px;font-size: 36px; margin-left: 50px; }";
      html += ".openbtn {font-size: 20px;cursor: pointer;background-color: rgba(0, 0, 50, 0.8);color: white;padding: 10px 10px;border: none;border-radius: 20px;}";
      html += ".openbtn:hover { background-color: #79b6d7; color: #0d153c; }";
      html += "#close{color: #ff0000;}";
      html += " #close:hover{ color: #ff0000; }";
      html += " #alert {padding: 15px;background-color: #04AA6D; color: white; opacity: 1; transition: opacity 0.6s; }";
      html += ".sidebar { height: 100%; width: 0; position: fixed; z-index: 1; top: 0; left: 0; background-color: #08174c; box-shadow: 0 0 10px rgba(255, 255, 255, 0.3); backdrop-filter: blur(5px); overflow-x: hidden;";
      html += "transition: 0.5s; padding-top: 60px; }";
      html += ".sidebar a { padding: 8px 8px 8px 32px;text-decoration: none;font-size: 25px;color: #ffffff; display: block;transition: 0.3s;}";
      html += ".sidebar a:hover { color: #ff0000; }";
      html += ".sidebar .closebtn {position: absolute;top: 0;right: 25px;font-size: 36px;margin-left: 50px;}";
      html += ".openbtn {font-size: 20px; cursor: pointer;background-color: rgba(0, 0, 50, 0.8);color: white;padding: 10px 10px;border: none;border-radius: 20px;}";
      html += ".openbtn:hover { background-color: #79b6d7; color: #0d153c; }";
      html += ".sidebar button:hover{ color: #ff0000;}";
      html += "#close{color: #ff0000;}";
      html += "#close:hover{color: #ff0000;}";
      html += ".display {    display: flex;  justify-content: space-between;  padding: 1px 20px; margin-bottom: 3%;}";
      html += ".sensor_display { text-decoration: none; color: #fff;padding: 12px;border-radius: 30px;border: 2px solid #fff; margin-bottom: 3%;}";
      html += ".sensor_display:hover {background-color: #79b6d7; color: #0d153c;}";
      html += ".sidebar button{background-color: #08174c;padding: 8px 8px 8px 32px;text-decoration: none; font-size: 25px; color: #ffffff;display: block;transition: 0.3s; display: block; border: none;cursor: pointer;}";
      html += ".dropdown-container {display: none; background-color: #08174c; padding-left: 8px;}";
      html += ".ds{color: #fff;background-color: red;padding: 5px;border-radius: 10px;width: 14%;}";
      html += ".ds2{color: #fff;background-color: green;padding: 10px 10px;border-radius: 10px;width: 14%;}";
      html += ".space{ display: flex; justify-content: space-between;align-items: center;}";
      html += "</style></head><body>";
      html += "<div class=\"background-circle circle1\"></div>";
      html += "<div class=\"background-circle circle2\"></div>";
      html += "<div class=\"background-circle circle3\"></div>";
      html += "<div class=\"navbar\">";
      html += "<button class=\"openbtn\" onclick=\"openNav()\">Menu</button>";
      html += "</div>";
      html += "<div id=\"mySidebar\" class=\"sidebar\">";
      html += "<a href=\"javascript:void(0)\" id =\"close\" class=\"closebtn\" onclick=\"closeNav()\">x</a>";
      html += "<a href=\"https://esp32-datalogger.ir/LoginPage/home.php\"><i class=\"fas fa-home\"></i>&nbsp;Home</a>";
      html += "<button class=\"dropdown-btn\"><i class=\"fa fa-user-plus\"></i>&nbsp;Manage Users";
      html += "<i class=\"fa fa-caret-down\"></i>";
      html += "</button>";
      html += "<div class=\"dropdown-container\">";
      html += "<a href=\"https://esp32-datalogger.ir/LoginPage/add_user.php\">&nbsp;Add User</a>";
      html += "<a href=\"https://esp32-datalogger.ir/LoginPage/delete_user.php\">Delete User</a>";
      html += "</div>";
      html += "<a href=\"https://esp32-datalogger.ir/LoginPage/LogManagement.php\"><i class=\"fas fa-clipboard-list\"></i>&nbsp;&nbsp;Log Management</a>";
      html += "<a href=\"https://esp32-datalogger.ir/LoginPage/logout.php\"><i class=\"fas fa-sign-out-alt\"></i>&nbsp;Logout</a>";
      html += "</div>";
      html += "<div class=\"card\">";
      html += "<h2>Air Quality</h2>";
      html += "<p style=\"text-align:left; margin-bottom:5%;\">The MQ-135 is a gas sensor used to detect air quality, measuring various gases in the range of CO2, VOCs, and pollutants. It doesn't have a specific protocol and its accuracy can vary but is generally moderate for qualitative measurements.</p>";
      sensorValue = analogRead(mq);
      html += "<div class=\"space\">";
      if (sensorValue == 0) {
        html += "<div class=\"ds\">Not Connected</div>";
      } else {
        html += "<div class=\"ds2\">Connected</div>";
      }
      html += "<div>Current Distance: " + String(sensorValue);
      html += "</div>";
      html += "</div>";
      html += "</div>";
      html += "<script>";
      html += "function openNav() { document.getElementById(\"mySidebar\").style.width = \"300px\";   document.getElementById(\"main\").style.marginLeft = \"300px\"; }";
      html += "function closeNav() {  document.getElementById(\"mySidebar\").style.width = \"0\";    document.getElementById(\"main\").style.marginLeft = \"0\";}";
      html += "var dropdown = document.getElementsByClassName(\"dropdown-btn\");";
      html += "var i;";
      html += "for (i = 0; i < dropdown.length; i++) {";
      html += "dropdown[i].addEventListener(\"click\", function() {";
      html += "this.classList.toggle(\"active\");";
      html += "var dropdownContent = this.nextElementSibling;";
      html += "if (dropdownContent.style.display === \"block\") {";
      html += "dropdownContent.style.display = \"none\";";
      html += " } else {";
      html += "dropdownContent.style.display = \"block\";";
      html += "}  });}";
      html += "</script></body></html>";
      request->send(200, "text/html", html);
    });
    server.begin();
    sendDataToServer()


  } else {
    if (WiFi.status() == WL_CONNECTED) {
      WiFi.disconnect();
      WiFi.mode(WIFI_STA);
      WiFi.softAP(ssidAP, passwordAP);
      WiFi.softAPConfig(local_ip, gateway, subnet);
    }
    Serial.println("AP Mode");
    wifi_state = "AP Mode";

    unsigned long currentMillis = millis();
    if (currentMillis - previousMillis >= interval) {
      previousMillis = currentMillis;
      digitalWrite(Gled, !digitalRead(Gled));
    }

    server.on("/", HTTP_GET, [](AsyncWebServerRequest* request) {
      if (!request->authenticate(www_username, www_password))
        return request->requestAuthentication();
      String html = "<!DOCTYPE html>";
      html += "<html><head>";
      html += "<style>";
      html += "body {background-color: #151d45; color: #fff; font-family: Arial, sans-serif; overflow: hidden; margin: 0; padding: 0; }";
      html += ".background-circle { position: absolute; width: 200px; height: 200px; border-radius: 50%; animation-duration: 6s; animation-iteration-count: infinite; z-index: -1; }";
      html += ".circle1 {height: 250px; width: 250px; top: 50px; left: 20%; background: linear-gradient(to right, #8E54E9, #4776E6); animation: bounce 5s linear infinite; }";
      html += " .circle2 {height: 300px; width: 300px;top: 50%; left: 45%; background: linear-gradient(to right, #f80759, #bc4e9c); animation: bounce 9s linear infinite 1s; }";
      html += ".circle3 {top: 20%; right: 22%; height: 150px; width: 150px; background: linear-gradient(to right, #ff5e62, #ff9966); animation: bounce 6.5s linear infinite 1.5s;}";
      html += "@keyframes bounce {0% {transform: translateY(0px);}25% {transform: translateY(55px);}50% {transform: translateY(0px);}75% {transform: translateY(-55px);} 100% {transform: translateY(0px);}}";
      html += ".card {background-color: rgba(0, 0, 50, 0.8);width: 560px;padding: 20px;border-radius: 10px;margin: 200px auto; box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);backdrop-filter: blur(5px); text-align: center;border-radius: 10%;}";
      html += "h1 {font-size: 24px;color: #333;animation: colorChange 4s infinite;}";
      html += "@keyframes colorChange {0% {color: #fffefe;}50% {color: blue;}100% {color: #fffeff;}}";
      html += " #hotspot {animation: colorChange_hotspot 2s infinite;}";
      html += " @keyframes colorChange_hotspot {0% {color: #580101;}50% {color: rgb(246, 3, 3);} 100% {color: #5a0000;}}";
      html += "h2 {font-size: 18px;}";
      html += " h3 {font-size: 16px;}";
      html += "p {font-size: 14px;color: #ff5e5e;}";
      html += "label {display: block;margin-bottom: 10px;text-align: left;}";
      html += "input[type='submit'] { margin-top: 20px;padding: 10px 20px;background-color: #ff5e5e;color: #fff; border: none; border-radius: 5px;cursor: pointer;}";
      html += "@media only screen and (max-width: 1080px) {.card { margin: 500px auto;}.circle1 {top: 15%; left: 6%;}.circle2 { top: 50%;left: 60%;}.circle3 { top: 20%;right: 6%;}.card {margin-top:450px; width: 650px;}}";
      html += "</style>";
      html += "<script>";
      html += "function openTab(event, tabName) {";
      html += "  var i, tabContent, tabLinks;";
      html += "  tabContent = document.getElementsByClassName('tab-content');";
      html += "  for (i = 0; i < tabContent.length; i++) {";
      html += "    tabContent[i].style.display = 'none';";
      html += "  }";
      html += "  tabLinks = document.getElementsByClassName('tab');";
      html += "  for (i = 0; i < tabLinks.length; i++) {";
      html += "    tabLinks[i].className = tabLinks[i].className.replace(' active', '');";
      html += "  }";
      html += "  document.getElementById(tabName).style.display = 'block';";
      html += "  event.currentTarget.className += ' active';";
      html += "}";
      html += "</script>";
      html += "</head><body>";
      html += "<div class='background-circle circle1'></div>";
      html += "<div class='background-circle circle2'></div>";
      html += "<div class='background-circle circle3'></div>";
      html += "<div class='card'>";
      html += "<h1>You are not connected to the internet</h1>";
      html += "<h2>ACCESS POINT MODE ( <span id='hotspot'>Hotspot</span> )</h2>";
      html += "<h3>Sensor data is stored in SPIFFS</h3>";
      html += "<p>Choose your sensor(s):</p>";
      html += "<form method='POST' action='/data'>";
      html += "<label><input type='checkbox' name='temp' value='1'> Temperature</label>";
      html += "<label><input type='checkbox' name='distance' value='1'> Distance</label>";
      html += "<label><input type='checkbox' name='airquality' value='1'> Air Quality</label>";
      html += "<label><input type='checkbox' name='motion' value='1'> PIR Motion</label>";
      html += "<input type='submit' value='Submit'>";
      html += "</form>";
      html += "</div></body></html>";

      request->send(200, "text/html", html);
    });
    server.on("/data", HTTP_POST, [](AsyncWebServerRequest* request) {
      String html = "<!DOCTYPE html>";
      html += "<html><head>";
      html += "<title>Sensors Table</title>";
      html += "<style>";
      html += "body { background-color: #151d45; color: #fff; font-family: Arial, sans-serif; overflow: hidden; margin: 0; padding: 0; overflow: scroll;}";
      html += ".background-circle {position: absolute;width: 200px;height: 200px;border-radius: 50%;animation-duration: 6s; animation-iteration-count: infinite;z-index: -1;}";
      html += ".circle1 {height: 250px; width: 250px;top: 50px;left: 20%; background: linear-gradient(to right, #8E54E9, #4776E6);animation: bounce 6s linear infinite;}";
      html += ".circle2 {height: 300px;width: 300px;top: 50%;left: 45%;background: linear-gradient(to right, #f80759, #bc4e9c);animation: bounce 9s linear infinite 1s;}";
      html += ".circle3 {top: 20%;right: 22%;height: 150px;width: 150px;background: linear-gradient(to right, #ff5e62, #ff9966);animation: bounce 4.5s linear infinite 1.5s;}";
      html += "@keyframes bounce {0% {transform: translateY(0px);}25% {transform:translateY(55px);}50 % {transform: translateY(0px);} 75 % {transform: translateY(-55px); }100 % {transform:translateY(0px);}}";
      html += ".tab {display: inline-block; padding: 20px 20px; text-decoration: none;border-radius: 17px;cursor: pointer;color: #fff;}";
      html += ".tab.active {background-color: #ff402b;}";
      html += ".tab:hover { background-color: #779ae0;color: #151d45;}";
      html += "tr {text-align: center;}";
      html += ".tab-content {display: none;padding: 20px;border: 1px solid #ccc;backdrop-filter: blur(20px);text-align: center;border-radius: 10px;width: 35%;margin: auto; margin-bottom: 10%;}";
      html += "#tab5{background-color: #7a7777}";
      html += "table {width: 100%;}";
      html += "th,td {border: 2px solid rgb(115, 162, 251);padding: 10px;}";
      html += "tr {text-align: center;}";
      html += "tr:hover {background-color: #e0e0e0;color: black;}";
      html += ".tab-position {height: 58px; width: 410px;border-radius: 20px;border: 3px solid rgb(255, 255, 255);background: rgba(39, 39, 39, 0.1);backdrop-filter: blur(60px);margin: auto;margin-top: 200px;text-align: center;}";
      html += ".tab-content h2 {text-align: center;}";
      html += "@media only screen and (max-width: 900px) {.circle1 { height: 300px;width: 300px;top: 15%;left: 10%;}";
      html += ".circle2 {height: 450px;width: 450px;top: 65%;left: 30%;}";
      html += ".circle3 {height: 200px;width: 200px;top: 33%;right: 10%;}";
      html += ".tab-position {height: 83px;width: 560px;margin: auto;margin-top: 300px;}";
      html += ".tab {padding: 30px 33px;border-radius: 16px;font-weight: 30px;font-size: larger;}";
      html += ".tab-content { padding: 30px; border-radius: 10px;width: 45%;margin: auto;margin-bottom: 10%;}";
      html += "th, td { border: 2px solid rgb(115, 162, 251);padding: 15px;}}";
      html += "</style>";
      html += "<script>";
      html += "function openTab(event, tabName) {";
      html += "  var i, tabContent, tabLinks;";
      html += "  tabContent = document.getElementsByClassName('tab-content');";
      html += "  for (i = 0; i < tabContent.length; i++) {";
      html += "    tabContent[i].style.display = 'none';";
      html += "  }";
      html += "  tabLinks = document.getElementsByClassName('tab');";
      html += "  for (i = 0; i < tabLinks.length; i++) {";
      html += "    tabLinks[i].className = tabLinks[i].className.replace(' active', '');";
      html += "  }";
      html += "  document.getElementById(tabName).style.display = 'block';";
      html += "  event.currentTarget.className += ' active';";
      html += "}";
      html += "</script></head>";
      html += "<body>";
      html += "<div class='background-circle circle1'></div><div class='background-circle circle2'></div><div class='background-circle circle3'></div>";
      html += "<div class='tab-position'>";
      html += "<a href='#' class='tab' onclick='openTab(event, \"tab1\")'>Temperature</a>";
      html += "<a href='#' class='tab' onclick='openTab(event, \"tab2\")'>Distance</a>";
      html += "<a href='#' class='tab' onclick='openTab(event, \"tab3\")'>MQ 135</a>";
      html += "<a href='#' class='tab' onclick='openTab(event, \"tab4\")'>PIR</a>";
      html += "</div>";

      // Handle checkbox selections and generate tab content
      if (request->hasArg("temp")) {
        // html += "<div id='tab1' class='tab-content'>";
        // ... temperature data ...

        html += "<div id='tab1' class='tab-content'><h2>Temperature</h2><table><tr>";
        html += "<th>Time</th><th>Temperature</th></tr>";
        temp_file = SPIFFS.open(temp_fileName, "r");
        // time_file = SPIFFS.open(time_fileName, "r");
        if (temp_file) {
          while (temp_file.available()) {
            String line = temp_file.readStringUntil(',');
            String line_time = temp_file.readStringUntil('\n');
            html += "<tr><td>" + line_time + "</td><td>" + line + "</td></tr>";
          }
          temp_file.close();
          // time_file.close();

        } else {
          Serial.println("Failed to open temperature file");
        }

        html += "</table>";
        html += "</div>";
      }

      if (request->hasArg("distance")) {
        html += "<div id='tab2' class='tab-content'><h2>Distance</h2><table><tr><th>Time</th><th>Distance</th></tr>";
        // time_file = SPIFFS.open(time_fileName, "r");
        hc_file = SPIFFS.open(hc_fileName, "r");
        if (hc_file) {
          while (hc_file.available()) {
            String line = hc_file.readStringUntil(',');
            String line_time = hc_file.readStringUntil('\n');
            html += "<tr><td>" + line_time + "</td><td>" + line + "</td></tr>";
          }
          hc_file.close();
          // time_file.close();
        } else {
          Serial.println("Failed to open HC-SR04 file");
        }

        html += "</table>";
        html += "</div>";
      }

      if (request->hasArg("airquality")) {
        html += "<div id='tab4' class='tab-content'><h2>PIR (Motion)</h2><table><tr><th>Time</th><th>Motion</th></tr>";
        mq_file = SPIFFS.open(mq_fileName, "r");
        if (mq_file) {
          while (mq_file.available()) {
            String line = mq_file.readStringUntil(',');
            String line_time = mq_file.readStringUntil('\n');
            html += "<tr><td>" + line_time + "</td><td>" + line + "</td></tr>";
          }
          mq_file.close();
          // time_file.close();
        } else {
          Serial.println("Failed to open MQ-135 file");
        }
        html += "</table>";
        html += "</div>";
      }

      if (request->hasArg("motion")) {
        html += "<div id='tab3' class='tab-content'><h2>MQ 135</h2><table><tr><th>Time</th><th>MQ 135</th></tr>";
        pir_file = SPIFFS.open(pir_fileName, "r");
        if (pir_file) {
          while (pir_file.available()) {
            String line = pir_file.readStringUntil(',');
            String line_time = pir_file.readStringUntil('\n');
            html += "<tr><td>" + line_time + "</td><td>" + line + "</td></tr>";
          }
          pir_file.close();
          // time_file.close();
        } else {
          Serial.println("Failed to open PIR file");
        }

        html += "</table>";
        html += "</div>";
      } else {
        html += "<div id='tab5' class='tab-content'></div>";
      }

      html += "</body></html>";

      request->send(200, "text/html", html);
    });


    server.begin();

    //tEMP
    write_temp(temperatureC);
    read_temp();

    //MQ 135
    write_mq(sensorValue);
    read_mq();

    //Ultrasonic
    write_hc(distanceCm);
    read_hc();

    //PIR MOTION
    write_pir(pir_state);
    read_pir();

    // write_time();
    // read_time();
  }

  Serial.println("---------------------------------------------------");
  delay(5000);

}
