########################
# VIDEOCAMARA
########################
#El raspberry tiene que tener puesto la ip de la GW
# interface pública --> enx00606e439261 
############################################
# GW como router (SNAT y DNAT)
############################################
#SNAT
sudo iptables -t nat -A POSTROUTING -o enx00606e439261 -j MASQUERADE
sudo sysctl net.ipv4.ip_forward=1

#En este punto, se debe de ver el video desde el GW, introduciendo: IP_raspberry : 8081
#DNAT
sudo iptables -t nat -A PREROUTING -i enx00606e439261 -p ICMP -j DNAT --to-destination 10.0.0.2 
#UNICAMENTE ITRODUCIR ESTE COMANDO
sudo iptables -t nat -A PREROUTING -i enx00606e439261 -p tcp -j DNAT --to-destination 10.0.0.2
