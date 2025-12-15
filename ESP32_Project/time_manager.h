#ifndef TIME_MANAGER_H
#define TIME_MANAGER_H

#include "config.h"

//Time
String getTime() {
  struct tm timeinfo;
  if (!getLocalTime(&timeinfo)) {
    String error = "Failed to obtain time";
    return error;
  } else {
    char timeString[30];
    // strftime(timeString, sizeof(timeString), "%A, %B %d %Y %H:%M:%S", &timeinfo); //ذخیره با نام روز مثل friday , ...
    strftime(timeString, sizeof(timeString), "%Y-%m-%d %H:%M:%S", &timeinfo);
    return timeString;
  }
}

#endif
