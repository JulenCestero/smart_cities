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

# Control de Contikis (Gateway)
- Script de Python
- Recibe los datos por serial desde el Máster y los envía al servidor a mysql, directamente a la tabla sensors mediante la cuenta que le permite acceder desde fuera de localhost
- Envía el modo y número de leds almacenado en la base de datos, en la tabla sensor_status al máster para que cambien el modo de operación
- *Falta limpiar un poco el script y hacerlo más seguro, con exceptions más robustas*