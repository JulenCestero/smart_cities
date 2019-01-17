# Restricting the BW
# Download
sudo tc qdisc add dev enx00606e43913f root handle 1: htb default 10
sudo tc class add dev enx00606e43913f parent 1: classid 1:1 htb rate 5000kbit ceil 5000kbit
sudo tc class add dev enx00606e43913f parent 1:1 classid 1:10 htb rate 5000kbit ceil 5000kbit prio 1
sudo tc filter add dev enx00606e43913f parent 1:0 protocol ip handle 10 fw flowid 1:10
sudo iptables -t mangle -A OUTPUT -d 10.0.0.2 -j MARK --set-mark 10
# Upload 
sudo tc qdisc add dev enx00606e439261 root handle 1: htb default 10
sudo tc class add dev enx00606e439261 parent 1: classid 1:1 htb rate 1000kbit ceil 1000kbit
sudo tc class add dev enx00606e439261 parent 1:1 classid 1:10 htb rate 1000kbit ceil 1000kbit prio 1
sudo tc filter add dev enx00606e439261 parent 1:0 protocol ip handle 10 fw flowid 1:10
sudo iptables -t mangle -A FORWARD -s 10.0.0.2 -j MARK --set-mark 10

# Más de una rama
# Videovigilancia
sudo tc class add dev enx00606e439261 parent 1:1 classid 1:30 htb rate 450kbit ceil 450kbit prio 1
sudo tc filter add dev enx00606e439261 parent 1:0 protocol ip handle 30 fw flowid 1:30
sudo iptables -t mangle -A FORWARD -s 10.0.0.2 -p TCP --dport=8081 -j MARK --set-mark 30

#MySQL (puerto 3306)
sudo tc class add dev enx00606e439261 parent 1:1 classid 1:40 htb rate 250kbit ceil 250kbit prio 2
sudo tc filter add dev enx00606e439261 parent 1:0 protocol ip handle 40 fw flowid 1:40
sudo iptables -t mangle -A FORWARD -p TCP -s 192.168.4.119 --dport 3306 -j MARK --set-mark 40

# Bulk, Wi-Fi
sudo tc class add dev enx00606e439261 parent 1:1 classid 1:50 htb rate 300kbit ceil 300kbit prio 3
sudo tc filter add dev enx00606e439261 parent 1:0 protocol ip handle 50 fw flowid 1:50
