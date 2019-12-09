<?php
/*
	Addon iobroker.paw http for app_player
*/
class iobroker_tts extends tts_addon {
	
    function __construct($terminal) {
	$this->title="ioBroker.paw";  
	$this->description = '<b>Поддерживаемые возможности:</b>say(),sayTo()<br>';
	$this->description .= '<b>Описание:</b>&nbsp;Для работы использует &nbsp;<a href="https://play.google.com/store/apps/details?id=ru.codedevice.iobrokerpawii">ioBroker.paw</a>';
	
	$this->terminal = $terminal;
        $this->setting = json_decode($this->terminal['TTS_SETING'], true);  
        $this->port = empty($this->setting['TTS_PORT']) ? 8080 : $this->setting['TTS_PORT'];
	$this->curl = curl_init();
	$this->address = 'http://' . $this->terminal['HOST'] . ':' . $this->port;
        $this->turnOnDisplay = $this->setting['TTS_USE_DISPLAY'];
	$this->brightnes = $this->setting['TTS_BRIGHTNESS_DISPLAY'];
    }
    // Say
    function say_message($message, $terminal) //SETTINGS_SITE_LANGUAGE_CODE=код языка
    {
	getURLBackground($this->address . "/api/set.json?play=true",0);
	sleep(1);
	getURLBackground($this->address . "/api/set.json?ringtone=false",0);
	usleep(500000);
	$url = $this->address . "/api/set.json?tts=" . urlencode($message['MESSAGE']);
	getURLBackground($url,0);
	debMes($url);
    	sleep($message['MESSAGE_DURATION']);
        return true;
    }
	
    function turn_on_display($terminal) 
    {
	if ($this->turnOnDisplay) {
	    // включаем дисплей
		getURLBackground($this->address . "/api/set.json?toWake=true",0);
		debMes($this->address . "/api/set.json?toWake=true");
		usleep(500000);
	}
        return true;
    }
    function turn_off_display($terminal) 
    {
	if ($this->turnOnDisplay) {
	    // выключаем дисплей
	    getURLBackground($this->address . "/api/set.json?toWake=false",0);
	    usleep(100000);
	}  
        return true;
    }
	
    function set_brightness_display($terminal) 
    {
    	if ($this->turnOnDisplay) {
             // установим яркость дисплея
	     $url = $this->address . "/api/set.json?brightness=" . $this->brightnes;
	     getURLBackground($url,0);
	     usleep(500000);
	}
        return true;
    }
	
    // Get player status
    function status()
    {
        // Defaults
        $track_id = -1;
        $length   = 0;
        $time     = 0;
        $state    = 'unknown';
        $volume   = 0;
        $muted    = FALSE;
        $random   = FALSE;
        $loop     = FALSE;
        $repeat   = FALSE;
        
        $result = json_decode(getURL($this->address . "/api/get.json",0), true); 
        if ($result) {
            $this->data    = array(
                'track_id' => (int) $track_id, //ID of currently playing track (in playlist). Integer. If unknown (playback stopped or playlist is empty) = -1.
                'length' => (int) $length, //Track length in seconds. Integer. If unknown = 0. 
                'time' => (int)  $time, //Current playback progress (in seconds). If unknown = 0. 
                'state' => (string) strtolower($state), //Playback status. String: stopped/playing/paused/unknown 
                'volume' => intval($result['volume']['music-max']/$result['volume']['music']*100), // Volume level in percent. Integer. Some players may have values greater than 100.
                'muted' => (boolean)  $muted, // Muted mode. Boolean.
                'random' => (boolean) $random, // Random mode. Boolean. 
                'loop' => (boolean) $loop, // Loop mode. Boolean.
                'repeat' => (string) $repeat //Repeat mode. Boolean.
            );
        }
        return $this->data;
    }
}
?>
