#ifndef MQ_SENSOR_H
#define MQ_SENSOR_H

#include "config.h"



//MQ-135
void write_mq(int mq_135) {
  mq_file = SPIFFS.open(mq_fileName, "r+");
  if (mq_file.size() > 338660) {
    count_mq = 0;
  }
  if (mq_file) {
    // Serial.println(mq_file.position());
    mq_file.seek(26 * count_mq, SeekCur);
    // mq_file.println(mq_135);
    // mq_file.seek(0, SeekEnd);
    mq_file.print(mq_135);
    mq_file.print(",");
    mq_file.println(getTime());
    mq_file.close();
  } else {
    Serial.println("Failed to open file");
  }
  count_mq += 1;
}
void read_mq() {
  mq_file = SPIFFS.open(mq_fileName, "r");
  if (mq_file) {
    String mq_data = mq_file.readString();
    mq_file.close();
    Serial.println("MQ-135 data:");
    Serial.println(mq_data);
    // return mq_data;
  } else {
    Serial.println("Failed to open file");
  }
}