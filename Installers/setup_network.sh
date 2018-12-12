sudo iptables -t nat -A POSTROUTING -o enx00606e439261 -j MASQUERADE
sudo sysctl net.ipv4.ip_forward=1
sudo iptables -t nat -A PREROUTING -i enx00606e43913f -p ICMP -j DNAT --to-destination 192.168.104.2

# Restricting the BW
# Download
sudo tc qdisc add dev enx00606e43913f root handle 1: htb default 10
sudo tc class add dev enx00606e43913f parent 1: classid 1:1 htb rate 1000kbit ceil 1000kbit
sudo tc class add dev enx00606e43913f parent 1:1 classid 1:10 htb rate 1000kbit ceil 1000kbit prio 1
sudo tc filter add dev enx00606e43913f parent 1:0 protocol ip handle 10 fw flowid 1:10
sudo iptables -t mangle -A OUTPUT -d 192.168.104.2 -j MARK --set-mark 10
# Upload 
