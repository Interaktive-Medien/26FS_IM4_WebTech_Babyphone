int scream_id = 0;                                // HTTP POST request: entry id from database table will be stored here

void write_sensordata_into_db(int is_screaming){
    // Serial.println("entering save_into_db()");
    JSONVar dataObject;                                      // construct JSON
    dataObject["is_screaming"] = is_screaming;
    dataObject["scream_id"] = scream_id;                // scream_id befindet sich in helper_functions.h
    dataObject["device_id"] = device_id;                // device_id befindet sich in helper_functions.h

    String jsonString = JSON.stringify(dataObject);

            // upload session start and stop timestamp into DB
    ////////////////////////////////////////////////////////////// start HTTP connection and perform a POST query
    HTTPClient http;
    http.begin("https://heulradar.dorfkneipe.ch/api/sensordata/mc_write_sensordata.php");
    http.addHeader("Content-Type", "application/json");
    int httpResponseCode = http.POST(jsonString);                  // httpResponseCode == 200 wenn alles klappt
    // Serial.printf("HTTP Response code from server: %d\n", httpResponseCode);       // 200 wenn alles klappt

    ////////////////////////////////////////////////////////////// process HTTP response
    if (httpResponseCode > 0) {                                       // 200 wenn alles klappt
        String response = http.getString();
        // Serial.println("Response: " + response);                   // z.B. Response: {"status":"success","message":"inserted into db: started screaming","scream_id":"116"}

        // parse JSON response - nur zur Info
        JSONVar myObject = JSON.parse(response);
        if (JSON.typeof(myObject) != "undefined") {
            if (myObject.hasOwnProperty("scream_id")) {
                int received_scream_id = cast_int(myObject["scream_id"]);     // function cast_int() is in helper_functions.h: (eg. "19" -> 19)
                // received_scream_id != scream_id?Serial.printf("heulsession started: %d\n", received_scream_id):Serial.println("heulsession ended: %d\n", received_scream_id);
                if(received_scream_id != scream_id){
                    scream_id = received_scream_id;
                    Serial.printf("db: new scream_id: %d\n", received_scream_id);
                }
                else if(received_scream_id == scream_id){                    // wenn wenn der Server keine neue ID liefert, war die aktuelle Schreiperiode bisher noch nicht abgeschlossen. Jetzt aber.
                    Serial.printf("db: scream ended (scream_id: %d) \n", received_scream_id);
                    Serial.println("------------------------------------");
                }
            }
        } else {
            Serial.println("Response parsing (transmitting sensordata to server) failed");
        }
    } else {
        Serial.printf("Error on sending POST: %d\n", httpResponseCode);
    }
    http.end();
}

