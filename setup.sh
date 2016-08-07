apt-get install mplayer python lxde-core lightdm
wget -O /home/player.py "http://videoserver/client_files/player-mplayer.py"
chmod +x /home/player.py
mkdir /root/.ssh
wget -O /root/.ssh/authorized_keys "http://videoserver/client_files/authorized_keys"
mv /etc/lightdm/lightdm.conf /etc/lightdm/lightdm.conf.bckp
wget -O /etc/lightdm/lightdm.conf "http://videoserver/client_files/lightdm.conf"