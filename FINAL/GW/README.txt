
######################################################
#                   Videovigilancia                  #
######################################################

# raspberry_conexion.sh (videocamara.sh ya está dentro de #initSoftAP_videocamara.sh)

- Sistema operativo: Raspbian (Instalado en la tarjeta SD)
- Instalar motion y configurar sus parámetros:
		* Framerate = 1000
		* Stream_port = 8081
		* Stream_localhost = OFF
		* Webconrol_localhost = OFF
		* start_motion_daemon = yes
- Asegurarse que no está guardando las imágenes.
- Configurar la raspberry con una IP estática (ip gateway = 10.0.0.1). (Para luego poder acceder desde SSH).
- En este punto, se puede ver el video desde la raspberry y también en el GW. 
- Para que se pueda ver el video desde el servidor, hay que configurar el GW como router.
- Para ello se utilizan los comandos iptables:

			* Habilitar SNAT (de privado a público) (La raspberry se puede conectar a internet)
						-> El cambio de las IPs se hace justo antes de ser enviado, es decir al final (POSTROUTING).
						-> -o (interfaz salida, internet): viene desde la raspberry y sale a internet, por lo que la interfaz de salida es el de internet
						-> -j MASQUERADE: Se utiliza para transformar la IP privada a una dirección IP pública cuando la source ip está asignada dinámicamente. 
									(en nuestro caso podríamos poner -j SNAT --to-source 10.0.0.2 (porque es estática, pero luego con la Wi-Fi las IPs serán dinámicas, por lo que nos conviene poner MASQUERADE ))
		1) iptables -t nat -A POSTROUTING -o (interfaz salida, internet) -j MASQUERADE 
		
			* Habilitar Forwarding, se encarga de la retransmisión de los paquetes que se reciben por una interfaz física
			  y de retransmitirlos por otra interfaz hacia otro nodo.
		2) sudo sysctl net.ipv4.ip_forward=1
		
			* Habilitar DNAT (de público a privado) (Los otros ordenadores se pueden conectar a la raspberry)
						->PREROUTING:El cambio de las IPs se hace justo al recibir el paquete, es decir al comienzo.
						-> -i (interfaz de entrada): Los paquetes vienen desde internet y van hacia la raspberry, asique el interfaz de entrada es el de internet
						-> -j DNAT --to-destination 10.0.0.2 -> Queremos que pase desde la ip 192.168.4.119 a 10.0.0.2 (IP del raspberry)
						-> -p tcp : Ya que el video trabaja con el protocolo tcp
		3)sudo iptables -t nat -A PREROUTING -i enx00606e439261 -p tcp -j DNAT --to-destination 10.0.0.2
- En este punto los otros ordenadores (por ejemplo el servidor) puede acceder al video utilizando la IP del Gateway y el puerto de salida del video 8081.

	
######################################################	
#                        Wi-Fi                       #
######################################################

#initSoftAP_videocamara.sh

- Asignar IP estática a la interface del Wi-Fi.
- El DNS utilizado tiene instalado el servidor, cambiar cada vez que se enciende la máquina virtual (resolv.conf).

- Instalar -> Hostapd y DNSMASQ:
		- Hostapd : software para crear puntos de acceso Wi-Fi
		- DNSMSQ: Es un servidor de:
				- DNS (pero nosotros no utilizamos esto, el servidor que utilizamos es BIND9 y está instalado en el servidor)
				- DHCP (para repartir las IPs)
				- TFTP 
- Configurar hostapd: -> hostapd.conf 
		-nterface=wlan0 : nombre de la interfaz que se utiliza como punto de acceso
		- driver=nl80211 : tipo de driver de la interfaz
		- ssid=wifi_nuevo : nombre de nuestro punto de acceso
		- channel=1 : número de canal en el que emitimos
- Configurar dnsmasq -> dnsmasq.conf
		- interface del wifi
		- dhcp_range = 10.5.5.3, 10.5.5.20 -> para habilitar el servidor dhcp y que reparta los IP en este rango.

- Matar procesos antes de activar dnsmasq.
- Para no tener problemas con en el driver y se pueda arrancar la interfaz se debe parar el network manager
(por lo tanto las interfaces estan definidas en /etc/network/interfaces, es decir, al iniciar Ubuntu no se tienen que meter manualmente las redes). 

-> Configurar iptables para que redirija paquetes, es decir configurarlo como router (necesario, si no, no se dispondrá de internet):
		-> Para que los paquetes puedan salir desde el interfaz del Wi-Fi hacia ethernet:
				- enviar los paquetes que entren por la interfaz wifi a la interfaz ethernet
			iptables -A FORWARD -i (interfaz wifi) -o (interfaz ethernet a internet) -j ACCEPT
		-> El SNAT está aplicado, es decir de privada a pública ya lo hemos activado para la videovigilancia al igual que el forwarding.  

- Activar dnsmasq: --> Para que reparta IPs	
	if [ -z "$(ps -e | grep dnsmasq)" ]  --> si la "$(ps -e | grep dnsmasq)" está vacio entonces ejecuta dnsmasq
										 --> "$(ps -e | grep dnsmasq)" == 
												* ps -e-> * ps muestra los procesos que se están corriendo
														  * -e : los que existen	
												* con grep se filtran los resultados, aquí filtramos los procesos de dnsmasq
	then
	 dnsmasq
	fi	
- Activar el punto de acceso: hostapd hostapd.conf 
- killall dnsmasq

- ¡¡¡Ya tenemos internet!!!
		

######################################################
#                         QoS                        # 
######################################################

#QoS_nuevo.sh

qdisc -> Algoritmo que gestiona la cola de un dispositivo 
root qdisc -> Qdisc que se adjunta a un dispositivo (interfaz)
			* UP: Interfaz pública
			* DOWN: Interfaz de Wi-Fi
Hay diferentes maneras de gestionar una cola, por ejemplo FIFO es una manera. 
En nuestro caso utilizaremos HTB, el cual gestiona las colas garantizando a cada hijito su ancho de banda correspondiente.

Se utilizan comandos tc para añadir o quitar qdisc a interfaces incluso para filtrar. 

- UPLOAD: (1Mbps) 

   1) Aplicar qdisc, de tipo htb a la interfaz de internet.
		1*)sudo tc qdisc add dev enx00606e439261 root handle 1: htb default 10

	2)Hemos utilizado classful qdisc, ya que a partir de 1: hemos creado una clase 1:1 (su padre es 1:), y desde está clase salen las otras tres clases:
	1:10 -> wi-fi (su padre es la clase 1:1)
	1:20 -> raspberry
	1:30 -> MySQL
	Por lo tanto, a la clase 1:1 se le asigna un ancho de banda (1Mbps).
		2*)sudo tc class add dev enx00606e439261 parent 1: classid 1:1 htb rate 1000kbit ceil 1000kbit

	3) Ahora se gestionan los hijos por separado, desde la clase 1:1 se reparte el ancho de banda entre los 3 hijos.
	Si alguno de los hijos no está utilizando el ancho de banda asignado, entonces los otros hijos lo pueden utilizar 
	dependiendo de la prioiridad que tenga cada uno.:
		3.1) Wi-Fi
		
			3.1.1) Al hijo Wi-Fi se le asigna un ancho de banda de 300kbit de los 1000kbit que tiene 1:1, y la prioridad que tiene es 3.
			Es decir, si alguno de los otros hijos no utiliza el ancho de banda, este tiene prioridad 3 de poder utilizar el ancho de banda restante.
			3.1.1*) sudo tc class add dev enx00606e439261 parent 1:1 classid 1:10 htb rate 300kbit ceil 1000kbit prio 3
			
			3.1.2) El filtro se utiliza para clasificar. En este caso, los paquetes que están marcados con el numero 10 se filtrar con el ancho de banda 
			       correspondiente al hijo 1:10, es decir con 300kbit. El filtro se aplica en el 1: es decir al comienzo, en el root qdisc. 
			3.1.2*)sudo tc filter add dev enx00606e439261 parent 1:0 protocol ip handle 10 fw flowid 1:10

			3.1.3) Todos los paquetes que vienen de la interfaz del wi-fi y salen por la interfaz de internet se marcan con el número 10.
			3.1.3*) sudo iptables -t mangle -A FORWARD -i wlxe894f60bc38a -o enx00606e439261 -j MARK --set-mark 10
			
	Con el resto de los hijos se hace lo mismo, pero con diferentes prioridades y ancho de banda. Lo que cambia sobre todo son los iptables:
		3.2) Videovigilancia:
			 -> BW: 450kbit
			 -> prio 1
			 -> sudo iptables -t mangle -A FORWARD -s 10.0.0.2 -j MARK --set-mark 20 # Todos los paquetes que vienen de la ip 10.0.0.2 se marcan con el numero 20
		3.3) MySQL:
			-> BW: 250kbit
			-> prio 2
			-> sudo iptables -t mangle -A OUTPUT -p tcp --dport 3306 -j MARK --set-mark 30  # Todos los paquetes que salgan por el puerto 3306(el puerto de MySQL) se marcan con el numero 30

- DOWNLOAD	
	1) sudo tc qdisc add dev wlxe894f60bc38a root handle 1: htb default 10
		-> Se le define como se va controlar el trafico
	2) sudo tc class add dev wlxe894f60bc38a parent 1: classid 1:1 htb rate 5000kbit ceil 5000kbit
		-> Se le asigna un ancho de banda
	3) sudo tc class add dev wlxe894f60bc38a parent 1:1 classid 1:10 htb rate 5000kbit ceil 5000kbit prio 1
		-> Se le dice cuanto ancho de banda tiene que coger el hijo que sale de 1:1, 
		   en este caso como solo hemos restringido el del Wi-Fi, le hemos puesto los 5000kbit enteros.
	4)sudo tc filter add dev wlxe894f60bc38a parent 1:0 protocol ip handle 10 fw flowid 1:10
		-> Filtramos todos los paquetes con número 10 con el ancho de banda de 1:10
	5)sudo iptables -t mangle -A OUTPUT -d 10.5.5.1 -j MARK --set-mark 10
		-> Se marcan los paquetes que van a la IP 10.5.5.1 con el número 10. 

		
--> Para ver que se marcan los paquetes por lo que QoS está bien definido: sudo iptables -L -nvx -t mangle
