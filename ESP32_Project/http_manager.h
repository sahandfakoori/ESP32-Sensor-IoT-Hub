#ifndef HTTP_MANAGER_H
#define HTTP_MANAGER_H

#include "config.h"

// ارسال داده ها به سرور
void sendDataToServer() {
    configTime(gmtOffset_sec, daylightOffset_sec, ntpServer);

    WiFiClientSecure* client = new WiFiClientSecure;
    client->setInsecure();  // بدون SSL certificate
    HTTPClient https;

    https.begin(*client, serverName);
    https.addHeader("Content-Type", "application/x-www-form-urlencoded");

    // آماده سازی داده ها
    flag = (temperatureC >= 33) ? 1 : 0;

    String httpRequestData = "api_key=" + apiKeyValue + "&DS18B20=" + temperatureC
                             + "&HC_SR04=" + distanceCm + "&MQ_135=" + sensorValue
                             + "&PIR_Motion=" + pir_state + "&WiFi_Mode=" + wifi_state
                             + "&flag=" + flag + "&Time=" + getTime();

    Serial.print("httpRequestData: ");
    Serial.println(httpRequestData);

    int httpResponseCode = https.POST(httpRequestData);

    if (httpResponseCode > 0) {
        Serial.print("HTTP Response code: ");
        Serial.println(httpResponseCode);
    } else {
        Serial.print("Error code: ");
        Serial.println(httpResponseCode);
        ESP.restart();
    }

    // ارسال داده های فایل ها به سرور
    temp_file = SPIFFS.open(temp_fileName, "r");
    hc_file = SPIFFS.open(hc_fileName, "r");
    mq_file = SPIFFS.open(mq_fileName, "r");
    pir_file = SPIFFS.open(pir_fileName, "r");

    if (temp_file && hc_file && mq_file && pir_file) {
        digitalWrite(Rled, HIGH);

        while ((temp_file.available()) && (hc_file.available()) && (mq_file.available()) && (pir_file.available())) {
            recieveSMS();

            String temp_line = temp_file.readStringUntil('\n');
            String hc_line = hc_file.readStringUntil('\n');
            String mq_line = mq_file.readStringUntil('\n');
            String pir_line = pir_file.readStringUntil('\n');

            int temp_commaIndex = temp_line.indexOf(',');
            int hc_commaIndex = hc_line.indexOf(',');
            int mq_commaIndex = mq_line.indexOf(',');
            int pir_commaIndex = pir_line.indexOf(',');

            if ((temp_commaIndex != -1) && (hc_commaIndex != -1) && (mq_commaIndex != -1) && (pir_commaIndex != -1)) {
                https.begin(*client, serverName);
                https.addHeader("Content-Type", "application/x-www-form-urlencoded");

                float temp_send = temp_line.substring(0, temp_commaIndex).toFloat();
                String temp_timeStr = temp_line.substring(temp_commaIndex + 1);

                float hc_send = hc_line.substring(0, hc_commaIndex).toFloat();
                int mq_send = mq_line.substring(0, mq_commaIndex).toInt();
                String pir_send = pir_line.substring(0, pir_commaIndex);

                String httpRequestData2 = "api_key=" + apiKeyValue + "&DS18B20=" + temp_send
                                          + "&HC_SR04=" + hc_send + "&MQ_135=" + mq_send
                                          + "&PIR_Motion=" + pir_send + "&WiFi_Mode=Access Point"
                                          + "&Time=" + temp_timeStr;

                Serial.print("httpRequestData2: ");
                Serial.println(httpRequestData2);

                int httpResponseCode2 = https.POST(httpRequestData2);

                if (httpResponseCode2 > 0) {
                    Serial.print("HTTP Response code: ");
                    Serial.println(httpResponseCode2);
                } else {
                    Serial.print("Error code: ");
                    Serial.println(httpResponseCode2);
                }

                https.end();
            }
        }
        digitalWrite(Rled, LOW);

        temp_file.close();
        hc_file.close();
        mq_file.close();
        pir_file.close();
        SPIFFS.remove(temp_fileName);
        SPIFFS.remove(hc_fileName);
        SPIFFS.remove(mq_fileName);
        SPIFFS.remove(pir_fileName);
    } else {
        Serial.println("Failed to open file");
    }

    https.end();
}

#endif
