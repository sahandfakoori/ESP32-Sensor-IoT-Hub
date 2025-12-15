#ifndef PIR_SENSOR_H
#define PIR_SENSOR_H

#include "config.h"



//PIR
void write_pir(String pir_motion) {
  pir_file = SPIFFS.open(pir_fileName, "r+");
  if (pir_file.size() > 338660) {
    count_pir = 0;
  }
  if (pir_file) {
    // Serial.println(pir_file.position());
    // if (pir_motion == "No Motion")
    //   pir_file.seek(11 * count_pir, SeekCur);
    // else
    //   pir_file.seek(17 * count_pir, SeekCur);
    pir_file.seek(37 * count_pir, SeekCur);
    // pir_file.println(pir_motion);
    // pir_file.seek(0, SeekEnd);
    pir_file.print(pir_motion);
    pir_file.print(",");
    pir_file.println(getTime());
    pir_file.close();
  } else {
    Serial.println("Failed to open file");
  }
  count_pir += 1;
}
void read_pir() {
  pir_file = SPIFFS.open(pir_fileName, "r");
  if (pir_file) {
    String pir_data = pir_file.readString();
    pir_file.close();
    Serial.println("PIR (Motion):");
    Serial.println(pir_data);
    // return pir_data;
  } else {
    Serial.println("Failed to open file");
  }
}