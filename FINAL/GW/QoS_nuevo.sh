# Restricting the BW
# Download, unicamente en el Wi-Fi
sudo tc qdisc add dev wlxe894f60bc38a root handle 1: htb default 10
sudo tc class add dev wlxe894f60bc38a parent 1: classid 1:1 htb rate 5000kbit ceil 5000kbit
sudo tc class add dev wlxe894f60bc38a parent 1:1 classid 1:10 htb rate 5000kbit ceil 5000kbit prio 1
sudo tc filter add dev wlxe894f60bc38a parent 1:0 protocol ip handle 10 fw flowid 1:10
sudo iptables -t mangle -A OUTPUT -d 10.5.5.1 -j MARK --set-mark 10

# Upload 
sudo tc qdisc add dev enx00606e439261 root handle 1: htb default 10
sudo tc class add dev enx00606e439261 parent 1: classid 1:1 htb rate 1000kbit ceil 1000kbit


# Upload, Wi-Fi  
sudo tc class add dev enx00606e439261 parent 1:1 classid 1:10 htb rate 300kbit ceil 1000kbit prio 3
sudo tc filter add dev enx00606e439261 parent 1:0 protocol ip handle 10 fw flowid 1:10
sudo iptables -t mangle -A FORWARD -i wlxe894f60bc38a -o enx00606e439261 -j MARK --set-mark 10
#sudo iptables -t mangle -A FORWARD -s 10.5.5.1 -j MARK --set-mark 10 #-i Wi-Fi -o SalidaEthernet


# Upload, Videovigilancia
sudo tc class add dev enx00606e439261 parent 1:1 classid 1:20 htb rate 450kbit ceil 1000kbit prio 1
sudo tc filter add dev enx00606e439261 parent 1:0 protocol ip handle 20 fw flowid 1:20
sudo iptables -t mangle -A FORWARD -s 10.0.0.2 -j MARK --set-mark 20
#sudo iptables -t mangle -A FORWARD -s 10.0.0.2 -j RETURN

# Upload, MySQL
sudo tc class add dev enx00606e439261 parent 1:1 classid 1:30 htb rate 250kbit ceil 1000kbit prio 2
sudo tc filter add dev enx00606e439261 parent 1:0 protocol ip handle 30 fw flowid 1:30
sudo iptables -t mangle -A OUTPUT -p tcp --dport 3306 -j MARK --set-mark 30 #puerto por donde sale el database de MySQL

#sudo iptables -L -nvx (-t mangle)
#sudo iptables -F

