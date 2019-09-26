<?php

/*
Addon Chromecast for app_player
*/

class chromecast_tts extends tts_addon
{
    
    // Constructor
    function __construct($terminal)
    {
        $this->title       = 'Google Chromecast';
        $this->description = 'Описание: Цифровой медиаплеер от компании Google.';
        //$this->terminal['PLAYER_PORT'] = (empty($this->terminal['PLAYER_PORT']) ? 8009 : $this->terminal['PLAYER_PORT']);
        $this->terminal = $terminal;
        $this->setting = json_decode($this->terminal['TTS_SETING'], true);       
        // Chromecast
        include_once(DIR_MODULES . 'app_player/libs/castv2/Chromecast.php');
    }

    // Say
    function say_media_message($message, $terminal) //SETTINGS_SITE_LANGUAGE_CODE=код языка
    {
        $outlink = $message['CACHED_FILENAME'];
        // берем ссылку http
        if (preg_match('/\/cms\/cached.+/', $outlink, $m)) {
            $server_ip = getLocalIp();
            if (!$server_ip) {
                DebMes("Server IP not found", 'terminals');
                return false;
            } else {
                $message_link = 'http://' . $server_ip . $m[0];
            }
        }

        //DebMes("Url to file " . $message_link);
        // конец блока получения ссылки на файл 
		DebMes($message_link);
		$cc = new GChromecast($this->terminal['HOST'], empty($this->setting['TTS_PORT']) ? 8009 : $this->setting['TTS_PORT']);
        $cc->requestId = time();
        $cc->load($message_link, 0);
        $response = $cc->play();
		//DebMes($response);
        $this->success = TRUE;
        $this->message = 'Play files';

        sleep($message['TIME_MESSAGE']);
        return $this->success;
    }
}

?>
