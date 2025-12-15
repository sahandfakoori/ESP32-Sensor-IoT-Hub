#ifndef SMS_MANAGER_H
#define SMS_MANAGER_H

#include "config.h"

//recieve SMS process
void recieveSMS() {
  if (sim800.available()) {
    unsigned int len, index;
    String sms = sim800.readString();
    sms.trim();
    if (sms != "OK") {
      index = sms.indexOf(":");
      String code = sms.substring(0, index);
      Serial.println(code);
      if (code == "+CMT") {
        sms.remove(0, index + 3);
        String number = sms.substring(0, 13);
        sms.remove(0, 19);
        String time = sms.substring(0, 17);
        sms.remove(0, 21);
        sms.trim();
        String message = sms.substring(0, sms.length());
        message.toLowerCase();
        Serial.println(number + "\n" + time + "\n" + message);
        if (number == PHONE) {
          if ((message == "temp") || (message == "temerature")) {
            sendMessage = "ESP32\nTemperature: " + String(temperatureC);
            sendSMS(sendMessage);
          } else if ((message == "dis") || (message == "distance")) {
            sendMessage = "ESP32\nDistance: " + String(distanceCm);
            sendSMS(sendMessage);
          } else if ((message == "mq") || (message == "mq135") || (message == "air")) {
            sendMessage = "ESP32\nAir Quality: " + String(sensorValue);
            sendSMS(sendMessage);
          } else if (message == "all") {
            sendMessage = "ESP32\nTemperature: " + String(temperatureC) + "\nDistance: " + String(distanceCm) + "\nAir Quality: " + String(sensorValue) + "\nMotion: " + String(pir_state) + "\nWiFi Mode: " + String(wifi_state);
            sendSMS(sendMessage);
          }
        }
      }
    }
  }
}
//send SMS
void sendSMS(String text) {
  sim800.println("AT+CMGF=1");
  delay(1000);
  sim800.println("AT+CMGS=\"" + PHONE + "\"");
  delay(1000);
  sim800.print(text);
  delay(100);
  sim800.write(26);
  delay(1000);
  Serial.println("SMS Sent Successfully.");
}

#endif
