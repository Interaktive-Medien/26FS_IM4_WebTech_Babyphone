/******************************************************************************************************
 * helper_functions.h
 * this code is included into mc.ino to keep mc.ino clean 
 * - outsourced functions to make mc.ino more readable
******************************************************************************************************/

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



///// smooting audio: if the audio level of x% from the rexent x seconds were above the threshold audio level, then is_screaming is 1
#define BUFFER_SIZE_SMOOTH 40                    // check for audio volume every 100ms -> 40 Werte for 4s
int heul_history[BUFFER_SIZE_SMOOTH];
int history_index = 0;
unsigned long last_history_update = 0;
int is_screaming = 0;   
int device_id = 1;                   // wie eine Seriennummer fest eincodiert, sollte bei jedem Gerät anders sein.


void init_audio_history_array(){     // called in setup() of mc.ino
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

        // noise during 50% of the time (18 of 25 values):
        // Only update is_screaming here to prevent flickering
        if (count_ones >= (BUFFER_SIZE_SMOOTH * 0.5)) {
            return 1;
        } else {
            return 0;
        }
    }
}