# MySQL
- Creados usuarios *phpmyadmin*, *admin* y *wizard* para acceder a las tablas. Dependiendo del tipo de usuario, tendrán ciertos privilegios
  - *Phpmyadmin* podrá acceder a todas las tablas de admin y user (quitar este acceso en el futuro)
  - *Admin* Pass: Admin_Smart-cities4. Será usado para los servicios del usuario administrador. Además, se usará para todas las comprobaciones de login en la base de datos
  - *Wizard* Pass: Wizard_Smart-cities4. Será usado para los servicios del usuario normal. Se usará sobre todo para introducir sugerencias en la BBDD

    ```sql
    CREATE USER 'newuser'@'localhost' IDENTIFIED BY 'password'; -- Create new users
    GRANT ALL PRIVILEGES ON *.* TO 'newuser'@'localhost'; -- Change privileges for a given user. * is for Database and table respectively
    FLUSH PRIVILEGES; -- Reload privileges
    ```
# Suggestion box
- Creadas tablas *users* y *suggestions*
- Creado usuario **root** con password **toor**. 
- [ ] Queda a la espera crear usuario admin **Dumbeldore** con su password (Grindelward?)

# Grafana
- Se ha instalado Grafana para visualizar los datos de los sensores. Acceso: admin + hacking
- API key para Dumbeldore: curl -H "Authorization: Bearer eyJrIjoia0U3YlBXOWdxNU5JcGU4NTNvb0FGVDRRUmZONFVtYU8iLCJuIjoiRHVtYmVsZG9yZSIsImlkIjoxfQ==" http://localhost:3000/api/dashboards/home
- https://stackoverflow.com/questions/42303800/how-to-display-grafana-graphs-in-my-websites-admin-panel-securely
- API key para Admin: curl -H "Authorization: Bearer eyJrIjoibWN5NnZ3bTRIYXhrcldsbjV6U2x3eXdjRzFvc1BwVTAiLCJuIjoiQWRtaW4iLCJpZCI6MX0=" http://localhost:3000/api/dashboards/home