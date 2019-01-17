#Cambiar el DNS
#sudo nano /etc/network/interfaces
#192.168.4.118
sudo nano /etc/resolv.conf
#Comprobar internet

#sudo ifconfig wlxe894f60bc38a up

#Matar procesos
#sudo systemctl stop systemd-resolved

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

####Start isc-dhcp-server ####
sudo systemctl restart isc-dhcp-server

########### Enable NAT ############
iptables -t nat -A POSTROUTING -o $arg2 -j MASQUERADE
iptables -A FORWARD -m conntrack --ctstate RELATED,ESTABLISHED -j ACCEPT
iptables -A FORWARD -i $arg1 -o $arg2 -j ACCEPT

sysctl -w net.ipv4.ip_forward=1
###########
########## Start hostapd ###########
hostapd hostapd.conf


## Videocamara
sudo iptables -t nat -A PREROUTING -i enx00606e439261 -p tcp -j DNAT --to-destination 10.0.0.2


##QoS
# Restricting the BW
# Download, solo el de Wi-Fi
#sudo tc qdisc add dev enx00606e43913f root handle 1: htb default 10
#sudo tc class add dev enx00606e43913f parent 1: classid 1:1 htb rate 5000kbit ceil 5000kbit
#sudo tc class add dev enx00606e43913f parent 1:1 classid 1:10 htb rate 5000kbit ceil 5000kbit prio 1
#sudo tc filter add dev enx00606e43913f parent 1:0 protocol ip handle 10 fw flowid 1:10
#sudo iptables -t mangle -A OUTPUT -d 10.0.0.2 -j MARK --set-mark 10
# Upload 
#sudo tc qdisc add dev enx00606e439261 root handle 1: htb default 10
#sudo tc class add dev enx00606e439261 parent 1: classid 1:1 htb rate 1000kbit ceil 1000kbit
#sudo tc class add dev enx00606e439261 parent 1:1 classid 1:10 htb rate 1000kbit ceil 1000kbit prio 1
#sudo tc filter add dev enx00606e439261 parent 1:0 protocol ip handle 10 fw flowid 1:10
#sudo iptables -t mangle -A FORWARD -s 10.0.0.2 -j MARK --set-mark 10



