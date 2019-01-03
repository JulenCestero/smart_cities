# MySQL
- Creados usuarios *phpmyadmin*, *admin* y *wizard* para acceder a las tablas. Dependiendo del tipo de usuario, tendrán ciertos privilegios
  - *Phpmyadmin* podrá acceder a todas las tablas de admin y user (quitar este acceso en el futuro)
  - *Admin* será usado para los servicios del usuario administrador. Pass: Admin_Smart-cities4
  - *Wizard* será usado para los servicios del usuario normal. Pass: Wizard_Smart-cities4

    ```sql
    CREATE USER 'newuser'@'localhost' IDENTIFIED BY 'password'; -- Create new users
    GRANT ALL PRIVILEGES ON *.* TO 'newuser'@'localhost'; -- Change privileges for a given user. * is for Database and table respectively
    FLUSH PRIVILEGES; -- Reload privileges
    ```
# Suggestion box
- Creadas tablas *users* y *suggestions*
- Creado usuario **root** con password **toor**. 
- [ ] Queda a la espera crear usuario admin **Dumbeldore** con su password (Grindelward?)