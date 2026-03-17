#include <HTTPClient.h>
#include <Arduino_JSON.h> 

///// smooting audio: if the audio level of x% from the rexent x seconds were above the threshold audio level, then is_screaming is 1
#define BUFFER_SIZE_SMOOTH 25                    // check for audio volume every 100ms -> 25 Werte for 2.5s
int heul_history[BUFFER_SIZE_SMOOTH];
int history_index = 0;
unsigned long last_history_update = 0;
int is_screaming = 0;   
int device_id = 1;                   // wie eine Seriennummer fest eincodiert, sollte bei jedem Gerät anders sein.


void init_audio_history_array(){
    for(int i = 0; i < BUFFER_SIZE_SMOOTH; i++) {
        heul_history[i] = 0;
    }
}


// 70% LOGIC: is_screaming = 1 if the audio volume > the threshold during 60% of the last x seconds --> bridging breaks
int isMostlyLoud(int current_noise_detected){
    if (millis() - last_history_update >= 100) { // update every 100ms
        last_history_update = millis();
        heul_history[history_index] = current_noise_detected; // store instantaneous value
        history_index = (history_index + 1) % BUFFER_SIZE_SMOOTH;

        int count_ones = 0;
        for (int i = 0; i < BUFFER_SIZE_SMOOTH; i++) {
            if (heul_history[i] == 1) count_ones++;
        }

        // noise during 60% of the time (18 of 25 values):
        // Only update is_screaming here to prevent flickering
        if (count_ones >= (BUFFER_SIZE_SMOOTH * 0.6)) {
            return 1;
        } else {
            return 0;
        }
    }
}



// if the value  in the JSON array received from the database is a string (eg. "19"), we need to convert it into an int
int cast_int(JSONVar idValue) {
    if (JSON.typeof(idValue) == "string") {
        String temp = (const char*)idValue;
        return temp.toInt();
    } 
    
    else if (JSON.typeof(idValue) == "number") {      // parsing not necessary if the value is already an int
        return (int)idValue;
    }
    return 0;                                         // Fallback if neither string nor int
}


// int scream_id = 0;                                // HTTP POST request: entry id from database table will be stored here


// void write_sensordata_into_db(int is_screaming){
//     // Serial.println("entering save_into_db()");
//     JSONVar dataObject;                                      // construct JSON
//     dataObject["is_screaming"] = is_screaming;
//     dataObject["scream_id"] = scream_id;                // scream_id befindet sich in helper_functions.h
//     dataObject["device_id"] = device_id;                // device_id befindet sich in helper_functions.h

//     String jsonString = JSON.stringify(dataObject);

//             // upload session start and stop timestamp into DB
//     ////////////////////////////////////////////////////////////// start HTTP connection and perform a POST query
//     HTTPClient http;
//     http.begin("https://heulradar.dorfkneipe.ch/api/sensordata/mc_write_sensordata.php");
//     http.addHeader("Content-Type", "application/json");
//     int httpResponseCode = http.POST(jsonString);                  // httpResponseCode == 200 wenn alles klappt
//     // Serial.printf("HTTP Response code from server: %d\n", httpResponseCode);       // 200 wenn alles klappt

//     ////////////////////////////////////////////////////////////// process HTTP response
//     if (httpResponseCode > 0) {                                       // 200 wenn alles klappt
//         String response = http.getString();
//         // Serial.println("Response: " + response);                   // z.B. Response: {"status":"success","message":"inserted into db: started screaming","scream_id":"116"}

//         // parse JSON response - nur zur Info
//         JSONVar myObject = JSON.parse(response);
//         if (JSON.typeof(myObject) != "undefined") {
//             if (myObject.hasOwnProperty("scream_id")) {
//                 int received_scream_id = cast_int(myObject["scream_id"]);     // function cast_int() is in helper_functions.h: (eg. "19" -> 19)
//                 // received_scream_id != scream_id?Serial.printf("heulsession started: %d\n", received_scream_id):Serial.println("heulsession ended: %d\n", received_scream_id);
//                 if(received_scream_id != scream_id){
//                     scream_id = received_scream_id;
//                     Serial.printf("db: new scream_id: %d\n", received_scream_id);
//                 }
//                 else if(received_scream_id == scream_id){                    // wenn wenn der Server keine neue ID liefert, war die aktuelle Schreiperiode bisher noch nicht abgeschlossen. Jetzt aber.
//                     Serial.printf("db: scream ended (scream_id: %d) \n", received_scream_id);
//                     Serial.println("------------------------------------");
//                 }
//             }
//         } else {
//             Serial.println("Response parsing (transmitting sensordata to server) failed");
//         }
//     } else {
//         Serial.printf("Error on sending POST: %d\n", httpResponseCode);
//     }
//     http.end();
// }






// // called on setup() function, once at start: select the songs that should be played (GET Request)
// int selected_tracks_ids[15];         // es können auch weniger als 15 Tracks ausgewählt sein. 15 ist eben die maximale Grösse
// String selected_tracks_titles[15];
// int num_selected_tracks = 0;
// int randomTrackIndex;

// void update_selected_tracks(){
//     HTTPClient http;
//     http.begin("https://heulradar.dorfkneipe.ch/api/tracks/mc_get_selected_tracks.php");   // dort wird eine Datenbankabfrage gemacht: SELECT t.id, t.title FROM tracks t JOIN device_tracks dt ON t.id = dt.track_id WHERE dt.device_id = :device_id;";
//     JSONVar requestObj;
//     requestObj["device_id"] = device_id;
//     String jsonString = JSON.stringify(requestObj);
//     http.addHeader("Content-Type", "application/json");         // Header setzen: Wir sagen dem Server, dass wir JSON senden
//     int httpResponseCode = http.POST(jsonString);               // sollte 200 (OK) sein

//     if (httpResponseCode > 0) {
//         String response = http.getString();                     // Get the response payload as a string
//         // Serial.println("Response from api/tracks/mc_get_selected_tracks.php: " + response);             // z.B. [{"id":12,"title":"Sympathy for the devil"},{"id":13,"title":"Under the bridge"}]

//         JSONVar myObject = JSON.parse(response);
//         if (JSON.typeof(myObject) == "undefined") {
//             Serial.println("Response parsing (fetching track selection from mc_get_selected_tracks.php) failed");
//         } else {
//             for (int i = 0; i < 15 && i < myObject.length(); i++) {       // Access the "selected" field of each object
//                 selected_tracks_ids[i] = (int)myObject[i]["id"];
//                 selected_tracks_titles[i] = (String)myObject[i]["title"];
//                 num_selected_tracks++;
                
//                 // Serial.print("Track ");
//                 // Serial.print((int)myObject[i]["id"]);
//                 // Serial.println(myObject[i]["title"]);
//             }
//         }
//     } else {
//         Serial.print("Error on sending GET: ");
//         Serial.println(httpResponseCode);
//     }
//     http.end();
// }




// // Pick a random track id (1-15) to play it
// int getRandomTrackId() {
//     randomTrackIndex = random(0, num_selected_tracks); 
//     return selected_tracks_ids[randomTrackIndex];
// }

// String getRandomTrackName() {
//     String randomTrackName = selected_tracks_titles[randomTrackIndex]; 
//     Serial.print("randomTrackName: ");
//     Serial.println(randomTrackName);
//     return randomTrackName;
// }

