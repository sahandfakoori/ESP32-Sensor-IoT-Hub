#ifndef TEMP_SENSOR_H
#define TEMP_SENSOR_H

#include "config.h"

//DS18B20
void write_temp(float temp) {
  temp_file = SPIFFS.open(temp_fileName, "r+");
  if (temp_file.size() > 338660) {
    count_temp = 0;
  }
  // Serial.print("temp size:");
  // Serial.println(temp_file.size());
  Serial.print("spiffs size:");
  Serial.println(SPIFFS.totalBytes());
  Serial.print("Used: ");
  Serial.println(SPIFFS.usedBytes());
  if (temp_file) {
    temp_file.seek(28 * count_temp, SeekCur);
    temp_file.print(temp);
    temp_file.print(",");
    temp_file.println(getTime());
    Serial.println(temp_file.position());
    temp_file.close();
  } else {
    Serial.println("Failed to open file");
  }
  count_temp += 1;
}
void read_temp() {
  temp_file = SPIFFS.open(temp_fileName, "r");
  if (temp_file) {
    String data = temp_file.readString();
    temp_file.close();
    Serial.println("Temperature data:");
    Serial.println(data);
    // return data;
  } else {
    Serial.println("Failed to open file");
  }
}

#endif
