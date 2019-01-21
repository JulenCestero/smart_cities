# MySQL (Servidor)
- **Usuarios**
  - *Phpmyadmin* podrá acceder a todas las tablas de admin y user (quitar este acceso en el futuro)
  - *Admin* Pass: Admin_Smart-cities4. Será usado para los servicios del usuario administrador. Además, se usará para todas las comprobaciones de login en la base de datos
  - *Wizard* Pass: Wizard_Smart-cities4. Será usado para los servicios del usuario normal. Se usará sobre todo para introducir sugerencias en la BBDD
  - *Contiki* Pass: Contiki_Smart-cities4. Se usa para introducir los datos remotamente de los contikis
- **Privilegios**
  - Estos usuarios únicamente tienen privilegios para la base de datos de Hogwarts
  - Los privilegios se cambian de la siguiente manera:
    ```sql
    CREATE USER 'newuser'@'localhost' IDENTIFIED BY 'password'; -- Create new users
    GRANT ALL PRIVILEGES ON *.* TO 'newuser'@'localhost'; -- Change privileges for a given user. * is for Database and table respectively
    FLUSH PRIVILEGES; -- Reload privileges
    ```
- **Tablas**
  - Users: Información para el login y autentificación de cada usuario, también incluye datos como su nick
    - User Dumbeldore, pass: Dumbeldore, tiene privilegios
    - User Harry, pass: Potter, es usuario no tiene priv
    - User Ron, pass: Weastley, es usuario no tiene priv
  - sensors: Tabla donde se almacenan los datos de los contikis. Tienen datos de luz, temperatura, modo, n_leds e ID del dato
  - suggestions: Tabla donde se almacenan las suggestions de los usuarios. Guardan datos de la suggestion en sí, el timestamp y el usuario que la ha realizado
  - sensor_status: Sirve de control del estado de los contikis. Es la tabla que se altera para modificar el modo automático, manual, n_leds..., y cuyos datos son constantemente enviados a los contikis

# Webpage (Servidor)
## Login
- login.html
  - Front end del login. Envía los datos de acceso en texto plano, protegido por SSL (?)
- login.php
  - Back end del login. Encripta los datos con sha256 y los compara con las tablas almacenadas en la base de datos. 
  - Si es correcto, redirige al hub correspondiente (welcomeDumbeldore.php si tiene privilegios, welcomeWizard.php si no)
  - *Falta sanetización*
- logout.php
  - Destruye la sesión de php para volver a enviar al usuario al login y restringir acceso a la página de esta manera
- admin_control.php
  - Cabecera que se añade a las páginas que únicamente podría acceder el administrador, que necesitan privilegios.
  - Comprueba si el usuario logeado tiene privilegios. Si no los tiene, rompe su sesión y le redirige al login
## Admin
### Header
- Mismo header que usuario, pero switchea ciertos parámetros al comprobar que se tienen privilegios
- Permite navegar por el área interna del admin: Hub principal, Suggestion reader, Motion, Sensor control
- Comprueba constantemente si el usuario está loggeado y restringe acceso a personas sin login
- Incluye CSS global
### Sugestion reader
- Llama a la base de datos y recupera todas las suggestions almacenadas. Por cada suggestion escupirá un bloque de HTML con los datos de la suggestion en cuestión
- *Falta cambiar fotos*
### Motion
- Página simple con un iframe que tiene dentro la emisión de motion redirigida por el gateway
- Cuenta con un botón para acceder a la página de donde sale la emisión directamente, por si hay problema de lag
### Sensores
- Control de contikis
  - Permite cambiar cada contiki individualmente a modo automático o manual, permitiendo seleccionar el número de leds a encender en cada contiki
  - Esto lo hace mediante PHP cambiando los valores almacenados en sensor_status, que son leidos por el script de control de los contikis para enviar el estado que se desea que tengan.
- Visualización de los datos
  - Muestra en un iframe el servidor de Grafana, alojado en el puerto 3000
  - El usuario deberá loggearse con user: admin, pass: Smart-cities4
  - *Falta mandar por ajax la autentificación de manera automática para evitar el login en grafana, problemas con CORS*
## Usuario
### Header
- Es el mismo header que el admin, pero switchea ciertos parámetros al ver que el usuario no tiene privilegios
- Únicamente permite acceso al hub princial del usuario, o a la suggestion box.
- Sirve para comprobar constantemente si el usuario está loggeado. Si se ha introducido una persona saltándose el paso del login, se le redirigirá constantemente al login.
- Incluye CSS global
### Suggestion box
- Sirve para insertar sugerencias en la base de datos
- Está hecho con una página web online el CSS y HTML, por lo que no es fácil replicarlo. Una página para hacer mails bonitos
- Tiene insertado código PHP para el header y para insertar las suggestions en la base de datos
- *Falta sanetización*
# DNS (Servidor)
- Nuestro servidor es el mismo DNS de nuestra red
- Instalado bind9 para estas tareas
- Forwardea a los DNS de google
- Traduce www.smarthogwarts.jan a la IP del servidor: 192.168.4.118
- No recuerdo exactamente cómo lo hice la verdad, pero dentro de la carpeta de bind (/etc/bind) tiene ciertos archivos de configuración que hemos ido cambiando  hasta conseguirlo. 
# Grafana (Servidor)
- Grafana se ha instalado mediante **Docker**. No es necesario usar docker, pero acabamos haciéndolo de esta manera porque queríamos meter el login mediante la API de HTTP (enviar la clave de acceso directamente desde la cabecera del HTML), y en stackoverflow te enseñaban a hacerlo mediante docker. A pesar de que no ha funcionado, se ha mantenido docker por no volver a borrarlo e instalar grafana manualmente. Además, facilita escalabilidad 
- Se ha instalado Grafana para visualizar los datos de los sensores. Acceso: admin + Smart-cities4
- Se ha creado un dashboard que muestra la evolución en el tiempo de los datos de luz y temperatura de cada uno de los sensores, además de una tabla con el estado actual de cada contiki
- API key para Dumbeldore: eyJrIjoiNEd0QVdXVFdNcTJRRDFJMk5yTXFSaVBGVzB2MmFyMDYiLCJuIjoiRHVtYmVsZG9yZSIsImlkIjoxfQ==
- https://stackoverflow.com/questions/42303800/how-to-display-grafana-graphs-in-my-websites-admin-panel-securely

---
---

# Control de Contikis (Gateway)
- Script de Python: *process_sensors.py*
- Recibe los datos por serial desde el Máster y los envía al servidor a mysql, directamente a la tabla sensors mediante la cuenta que le permite acceder desde fuera de localhost
- Envía el modo y número de leds almacenado en la base de datos, en la tabla sensor_status al máster para que cambien el modo de operación
- *Falta limpiar un poco el script y hacerlo más seguro, con exceptions más robustas*

# Videovigilancia
- Sistema operativo: Raspbian (Instalado en la tarjeta SD)
- Instalar motion y configurar sus parámetros:
    - Framerate = 1000
    - Stream_port = 8081
    - Stream_localhost = OFF
    - Webconrol_localhost = OFF
    - start_motion_daemon = yes
- Asegurarse que no está guardando las imágenes.
- Configurar la raspberry con una IP estática (ip gateway = 10.0.0.1). (Para luego poder acceder desde SSH).
- En este punto, se puede ver el video desde la raspberry y también en el GW. 
- Para que se pueda ver el video desde el servidor, hay que configurar el GW como router.
- Para ello se utilizan los comandos iptables:
	- Habilitar SNAT (de privado a público) (La raspberry se puede conectar a internet)
        - El cambio de las IPs se hace justo antes de ser enviado, es decir al final (POSTROUTING).
		- -o (interfaz salida, internet): viene desde la raspberry y sale a internet, por lo que la interfaz de salida es el de internet
		- -j MASQUERADE: Se utiliza para transformar la IP privada a una dirección IP pública cuando la source ip está asignada dinámicamente. 
									(en nuestro caso podríamos poner -j SNAT --to-source 10.0.0.2 (porque es estática, pero luego con la Wi-Fi las IPs serán dinámicas, por lo que nos conviene poner MASQUERADE ))
		1) iptables -t nat -A POSTROUTING -o (interfaz salida, internet) -j MASQUERADE 
		
			* Habilitar Forwarding, se encarga de la retransmisión de los paquetes que se reciben por una interfaz física
			  y de retransmitirlos por otra interfaz hacia otro nodo.
		2) sudo sysctl net.ipv4.ip_forward=1
		
			- Habilitar DNAT (de público a privado) (Los otros ordenadores se pueden conectar a la raspberry)
                -PREROUTING:El cambio de las IPs se hace justo al recibir el paquete, es decir al comienzo.
				- -i (interfaz de entrada): Los paquetes vienen desde internet y van hacia la raspberry, asique el interfaz de entrada es el de internet
				- -j DNAT --to-destination 10.0.0.2 -> Queremos que pase desde la ip 192.168.4.119 a 10.0.0.2 (IP del raspberry)
				- -p tcp : Ya que el video trabaja con el protocolo tcp
		3)sudo iptables -t nat -A PREROUTING -i enx00606e439261 -p tcp -j DNAT --to-destination 10.0.0.2
- En este punto los otros ordenadores (por ejemplo el servidor) puede acceder al video utilizando la IP del Gateway y el puerto de salida del video 8081.

# Wifi
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

- Configurar iptables para que redirija paquetes, es decir configurarlo como router (necesario, si no, no se dispondrá de internet):
	- Para que los paquetes puedan salir desde el interfaz del Wi-Fi hacia ethernet:
      - enviar los paquetes que entren por la interfaz wifi a la interfaz ethernet
```bash
iptables -A FORWARD -i (interfaz wifi) -o (interfaz ethernet a internet) -j ACCEPT
```

- El SNAT está aplicado, es decir de privada a pública ya lo hemos activado para la videovigilancia al igual que el forwarding.  

- Activar dnsmasq: --> Para que reparta IPs	
```bash
	if [ -z "\$(ps -e | grep dnsmasq)" ]  # si la "$(ps -e | grep dnsmasq)" está vacio entonces ejecuta dnsmasq
# "$(ps -e | grep dnsmasq)" == 
# * ps -e-> * ps muestra los procesos que se están corriendo
# -e : los que existen	
# con grep se filtran los resultados, aquí filtramos los procesos de dnsmasq
	then
	 dnsmasq
	fi	
```
- Activar el punto de acceso: hostapd hostapd.conf 
- killall dnsmasq

- ¡¡¡Ya tenemos internet!!!

# QoS
qdisc -> Algoritmo que gestiona la cola de un dispositivo 

root qdisc -> Qdisc que se adjunta a un dispositivo (interfaz)
  * UP: Interfaz pública
  * DOWN: Interfaz de Wi-Fi


Hay diferentes maneras de gestionar una cola, por ejemplo FIFO es una manera. 
En nuestro caso utilizaremos HTB, el cual gestiona las colas garantizando a cada hijito su ancho de banda correspondiente.

Se utilizan comandos tc para añadir o quitar qdisc a interfaces incluso para filtrar. 

- UPLOAD: (1Mbps) 

1. Aplicar qdisc, de tipo htb a la interfaz de internet.
```bash
		sudo tc qdisc add dev enx00606e439261 root handle 1: htb default 10
```
2. Hemos utilizado classful qdisc, ya que a partir de 1: hemos creado una clase 1:1 (su padre es 1:), y desde está clase salen las otras tres clases:
	- 1:10 -> wi-fi (su padre es la clase 1:1)
	- 1:20 -> raspberry
	- 1:30 -> MySQL
	Por lo tanto, a la clase 1:1 se le asigna un ancho de banda (1Mbps).
```
sudo tc class add dev enx00606e439261 parent 1: classid 1:1 htb rate 1000kbit ceil 1000kbit
```
3.  Ahora se gestionan los hijos por separado, desde la clase 1:1 se reparte el ancho de banda entre los 3 hijos.

	Si alguno de los hijos no está utilizando el ancho de banda asignado, entonces los otros hijos lo pueden utilizar 
	dependiendo de la prioiridad que tenga cada uno.:

    -  Wi-Fi
       - Al hijo Wi-Fi se le asigna un ancho de banda de 300kbit de los 1000kbit que tiene 1:1, y la prioridad que tiene es 3.
			Es decir, si alguno de los otros hijos no utiliza el ancho de banda, este tiene prioridad 3 de poder utilizar el ancho de banda restante.
        ```
        sudo tc class add dev enx00606e439261 parent 1:1 classid 1:10 htb rate 300kbit ceil 1000kbit prio 3
        ```		

       - El filtro se utiliza para clasificar. En este caso, los paquetes que están marcados con el numero 10 se filtrar con el ancho de banda 
			       correspondiente al hijo 1:10, es decir con 300kbit. El filtro se aplica en el 1: es decir al comienzo, en el root qdisc. 
        ```
        sudo tc filter add dev enx00606e439261 parent 1:0 protocol ip handle 10 fw flowid 1:10
        ```
		- Todos los paquetes que vienen de la interfaz del wi-fi y salen por la interfaz de internet se marcan con el número 10.
        ```
        sudo iptables -t mangle -A FORWARD -i wlxe894f60bc38a -o enx00606e439261 -j MARK --set-mark 10
        ```			
	Con el resto de los hijos se hace lo mismo, pero con diferentes prioridades y ancho de banda. Lo que cambia sobre todo son los iptables:
	- Videovigilancia:
		- BW: 450kbit
		- prio 1
		- sudo iptables -t mangle -A FORWARD -s 10.0.0.2 -j MARK --set-mark 20 # Todos los paquetes que vienen de la ip 10.0.0.2 se marcan con el numero 20
	- MySQL:
      -  BW: 250kbit
      -  prio 2
    ```bash
    sudo iptables -t mangle -A OUTPUT -p tcp --dport 3306 -j MARK --set-mark 30  # Todos los paquetes que salgan por el puerto 3306(el puerto de MySQL) se marcan con el numero 30
    ```
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

# Contiki
## Master

**Estructuras**:
- msg_brod:contiene los campos de 
	tipo de comunicación 
	número de sequencia
	variable para saber si lo envia un master o un slave.
- msg_uni: contiene los campos de:
	número de sequencia
	luz (tanto en int como en char).
	temperatura (tanto en int como en char).
	modo (tanto en int como en char).
	número de leds (tanto en int como en char).
	ack
- msg_uni_send: 
	número de sequencia
	modo (tanto en int como en char).
	número de leds (tanto en int como en char).
- VecinosList: 
	direciones de los vecinos.

**Funciones**:
- get_light: devuelve la luz detectada por el sensor de luz.
- get_temp: devuelve la temperatura detectada por el sensor de luz.
- str_split: divide un string en diferentes partes.
- positionList: devuelve la posición de un vecino con una direción (-1 si no está)
- receive_brd_function: mira si se conocia a ese vecino, si no se conocia sse añade a la lista de vecinos
- receive_uni_function: mira si se conocia a ese vecino, si no se conocia sse añade a la lista de vecinos, si se conoce recoge los valores enviados y los transmite por puerto serial.
- uart_rx_callback: detecta los caracteres recibido por serial y llama al proceso SerialLine_process al recibir un salto de linea.

**Procesos**:
- LED_process: Cada segundo comprueba el modo que tiene que estar y el número de leds a encender en el caso de modo manual (1). Los valores de los intervalos son los dados por ellos.
- Broadcast_process: envia los campos de msg_brod como:
    - tipo de comunicación: 1
    - número de sequencia : contador que sube cada vez que envia, ya sea un broadcast o un unicast
    - variable para saber si lo envia un master a 1.
- Unicast_process: envia los campos de msg_uni_send como:
    - número de sequencia contador que sube cada vez que envia, ya sea un broadcast o un unicast
    - modo (tanto en int como en char): modo en el que se tiene que poner el contiki al que se le envia
    - número de leds (tanto en int como en char) número de leds a encender en modo manual que poner el - contiki al que se le envia
- SerialLine_process: LLama a la función para particionar un string, analiza el primer digito (9 para master y el resto para slaves), en el casso del master escribe las variables de modo y de número de ledss, en el caso de sslave rellena las varialess para el mensaje unicast y llama al proceso 
- Main_process: inicializa todo lo necesario y manda ssus datos cada 5 segundos y además un broadcast cada 25.


## Slave


**Estructuras**:
- msg_brod:contiene los campos de 
    - tipo de comunicación 
    - número de sequencia
    - variable para saber si lo envia un master o un slave.
- msg_uni_send: 
    - número de sequencia
    - luz
    - temperatura
    - modo 
    - número de leds 
- msg_uni_receive: 
    - número de sequencia
    - modo (tanto en int como en char).
    - número de leds (tanto en int como en char).
- VecinosList: 

    direciones de los vecinos.
    variable para saber si el vecino es master

**Funciones**:
- get_light: devuelve la luz detectada por el sensor de luz.
- get_temp: devuelve la temperatura detectada por el sensor de luz.
- positionList: devuelve la posición de un vecino con una direción (-1 si no está)
- receive_brd_function: mira si se conocia a ese vecino, si no se conocia se añade a la lista de vecinos, junto con si es vecino o no.
- receive_uni_function: mira si el que lo envia es un master y si lo es cambia sus variables de estado.

**Procesos**:
- LED_process: Cada segundo comprueba el modo que tiene que estar y el número de leds a encender en el caso de modo manual (1). Los valores de los intervalos son los dados por ellos.
- Broadcast_process: envia los campos de msg_brod como:
    - tipo de comunicación: 1
    - número de sequencia : contador que sube cada vez que envia, ya sea un broadcast o un unicast
    - variable para saber si lo envia un master a 0.
- Unicast_process: envia los campos de msg_uni_send como:
    - número de sequencia contador que sube cada vez que envia, ya sea un broadcast o un unicast
    - luz
    - temperatura
    - modo (tanto en int como en char): modo en el que se tiene que poner el contiki al que se le envia
    - número de leds (tanto en int como en char) número de leds a encender en modo manual que poner el contiki al que se le envia
- Main_process: inicializa todo lo necesario y manda sus datos por un unicast cada 5 segundos y además un broadcast cada 25.


-------------------------------------------------------------------

## DNS server

Instalación de cliente bind9 y configurar el paquete de named.conf.options a un dnss publico como el de google (8.8.8.8), en el fichero de resolución meter como nameserver el localhost. Crear un archivo de zona (db.local) en el que se especifique la pagina web y la ip donde está. en el archivo de zona db.172 se hace lo mismo junto con otros parametros como el tiempo que dura en el servidor. POr ultimo en name.cong.local sse añaden las zonas anteriores para entrada de datos y salidas de datos, permitiendo que pueda haber comunicación bidireccional con la web.

-------------------------------------------------------------------

## HTTPS

Habilitar que apache pueda trabajar con ssl con el a2enmod, creamos un certificado autofirmado con su correspondiente key, habilitamos el virtual host para HTTPS,(no quitamos el host de http, para que este siga funcionando). Para que la pagina pueda acceder a PHPMyAdmin hay que especificarloo en su archivo de configuración.




