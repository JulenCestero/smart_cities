#Cambiar el DNS
#sudo nano /etc/network/interfaces
#192.168.4.118
sudo nano /etc/resolv.conf
#Comprobar internet

sudo ifconfig wlxe894f60bc38a up

#Matar procesos
sudo systemctl stop systemd-resolved

#Configurar Wi-Fi
systemctl stop NetworkManager.service
#!/bin/bash
# Usage: ./initSoftAP
########### Initial wifi interface configuration #############
arg1=wlxe894f60bc38a
arg2=enx00606e439261
ip link set $arg1 down
ip addr flush dev $arg1
ip link set $arg1 up
ip addr add 10.5.5.1/24 dev $arg1

# If you still use ifconfig for some reason, replace the above lines with the following
# ifconfig $1 up 10.0.0.1 netmask 255.255.255.0
sleep 2
###########

########### Start dnsmasq ##########
if [ -z "$(ps -e | grep dnsmasq)" ]
then
 dnsmasq
fi
###########
########### Enable NAT ############
iptables -t nat -A POSTROUTING -o $arg2 -j MASQUERADE
iptables -A FORWARD -m conntrack --ctstate RELATED,ESTABLISHED -j ACCEPT
iptables -A FORWARD -i $arg1 -o $arg2 -j ACCEPT

#Thanks to lorenzo
#Uncomment the line below if facing problems while sharing PPPoE, see lorenzo's comment for more details
#iptables -I FORWARD -p tcp --tcp-flags SYN,RST SYN -j TCPMSS --clamp-mss-to-pmtu

sysctl -w net.ipv4.ip_forward=1
###########
########## Start hostapd ###########
hostapd hostapd.conf
killall dnsmasq


## Videocamara
sudo iptables -t nat -A PREROUTING -i enx00606e439261 -p tcp -j DNAT --to-destination 10.0.0.2




