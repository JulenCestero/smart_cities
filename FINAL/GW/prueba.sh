# Más de una rama
# Videovigilancia
sudo tc class add dev enx00606e439261 parent 1:1 classid 1:30 htb rate 700kbit ceil 700kbit prio 1
sudo tc filter add dev enx00606e439261 parent 1:0 protocol ip handle 30 fw flowid 1:30
sudo iptables -t mangle -A FORWARD -s 10.0.0.2 -p TCP --dport=8081 -j MARK --set-mark 30

# Bulk
sudo tc class add dev enx00606e439261 parent 1:1 classid 1:50 htb rate 300kbit ceil 300kbit prio 1
sudo tc filter add dev enx00606e439261 parent 1:0 protocol ip handle 50 fw flowid 1:50
