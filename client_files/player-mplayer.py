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
infile = "http://videoserver/player.php?mac=" + mac

on = True

while(on):
    try:
        a = subprocess.call(["mplayer","-fs","-playlist",infile])
    except KeyboardInterrupt:
        on = False
        break
