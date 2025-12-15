#ifndef HC_SENSOR_H
#define HC_SENSOR_H

#include "config.h"



//HC-SR04
void write_hc(float distance) {
  hc_file = SPIFFS.open(hc_fileName, "r+");
  if (hc_file.size() > 338660) {
    count_hc = 0;
  }
  if (hc_file) {
    // Serial.println(hc_file.position());
    // if (distance < 10)
    //   hc_file.seek(6 * count_hc, SeekCur);
    // else if (distance > 9 && distance < 100)
    //   hc_file.seek(7 * count_hc, SeekCur);
    // else hc_file.seek(8 * count_hc, SeekCur);
    // hc_file.println(distance);

    hc_file.seek(28 * count_hc, SeekCur);
    // hc_file.seek(0, SeekEnd);
    hc_file.print(distance);
    hc_file.print(",");
    hc_file.println(getTime());
    hc_file.close();
  } else {
    Serial.println("Failed to open file");
  }
  count_hc += 1;
}
void read_hc() {
  hc_file = SPIFFS.open(hc_fileName, "r");
  if (hc_file) {
    String hc_data = hc_file.readString();
    hc_file.close();
    Serial.println("HC-SR04 data:");
    Serial.println(hc_data);
    // return hc_data;
  } else {
    Serial.println("Failed to open file");
  }
}