#ifndef CONFIG_H
#define CONFIG_H

#include <SPIFFS.h>
#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <HTTPClient.h>
#include <AsyncTCP.h>
#include <ESPAsyncWebServer.h>
#include <time.h>
#include <Arduino.h>
#include <SoftwareSerial.h>

// Pins
#define Rled 2
#define Gled 4

// WiFi
const char* ssidAP = "ESP32_AP";
const char* passwordAP = "12345678";
IPAddress local_ip(192,168,4,1);
IPAddress gateway(192,168,4,1);
IPAddress subnet(255,255,255,0);
String wifi_state;

// SIM800
#define PHONE "+989xxxxxxxxx"
SoftwareSerial sim800(16,17);

// HTTP
String serverName = "https://yourserver.com";
String apiKeyValue = "YOUR_API_KEY";

// Web Auth
String www_username = "admin";
String www_password = "1234";

// SPIFFS filenames
String temp_fileName = "/temp.txt";
String hc_fileName = "/hc.txt";
String mq_fileName = "/mq.txt";
String pir_fileName = "/pir.txt";

// Counters
int count_temp = 0;
int count_hc = 0;
int count_mq = 0;
int count_pir = 0;

// Sensor variables
float temperatureC;
float distanceCm;
int sensorValue;
String pir_state;
int flag;
String sendMessage;

// Timing
unsigned long previousMillis = 0;
unsigned long interval = 1000;

#endif
