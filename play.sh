#!/bin/sh

mac="$(ifconfig eth0|grep -E -o '[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}')"
mplayer -loop 0 -fs "http://videoserver/player.php?mac=$mac"
