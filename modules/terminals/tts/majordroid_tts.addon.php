﻿<?php

class majordroid_tts extends tts_addon {

    function __construct($terminal) {
		$this->terminal = $terminal;
        $this->title="MajorDroid";
		$this->description = 'Используется на устройствах которые имеют в себе MajorDroid API.';
        parent::__construct($terminal);
    }

    function say_message($message, $terminal) //SETTINGS_SITE_LANGUAGE_CODE=код языка
    {
        return $this->sendMajorDroidCommand('tts:'.$message['MESSAGE']);
    }

    function sendMajorDroidCommand($cmd) {
        if ($this->terminal['HOST']) {
            DebMes("Sending $cmd to ".$this->terminal['HOST'],'majordroid');
            $service_port = '7999';
            $in = $cmd;
            $address = $this->terminal['HOST'];
            if (!preg_match('/^\d/', $address)) return 0;
            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            if ($socket === false) {
                return 0;
            }
            $result = socket_connect($socket, $address, $service_port);
            if ($result === false) {
                return 0;
            }
            $result = socket_write($socket, $in, strlen($in));
            usleep(500000);
            socket_close($socket);
            return $result;
        }
    }
}