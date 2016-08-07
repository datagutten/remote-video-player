#!/usr/bin/python
import sys
import subprocess
import os
import glob
import re
import urllib2

ifconfig = subprocess.check_output(["ifconfig", "eth0"])
m = re.search("[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}", ifconfig)
mac = m.group()

playlist=urllib2.urlopen("http://videoserver/player.php?mac=" + mac).read()


on = True

while(on):
    try:
        for video in playlist.rstrip().split("\n"):
            a = subprocess.call(["omxplayer", video])
    except KeyboardInterrupt:
        on = False
        break
