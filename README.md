## EntregaLigaPersistente

Proyecto PHP simple para gestionar una liga de fútbol con persistencia en MySQL.

Este README explica qué se usa en el proyecto y cómo puede ponerlo en funcionamiento un desarrollador nuevo en su máquina usando XAMPP y HeidiSQL.

## Resumen

- Lenguaje: PHP (requiere PHP 8.0+ por uso de tipado y uniones).
- Servidor local: XAMPP (Apache + MySQL/MariaDB).
- Gestor de base de datos recomendado: HeidiSQL (o phpMyAdmin si prefiere interfaz web).
- Estilo: Tailwind CSS (configurado en `assets/css`).

## Estructura principal del proyecto

- `index.php` — punto de entrada.
- `app/` — páginas de la aplicación (equipos, partidos, etc.).
- `assets/` — CSS y otros recursos estáticos.
- `model/` — clases del dominio (`Equipo.php`, `Partido.php`).
- `persistence/conf/database.php` — configuración de conexión a la base de datos.
- `persistence/DAO/` — acceso a datos (DAO para entidades).
- `persistence/script/bd.sql` — script SQL para crear la base de datos y tablas.
- `templates/` — cabecera y pie de página reutilizables.
- `utils/` — utilidades (gestión de sesión y validaciones).

## Requisitos previos

1. Instalar XAMPP (https://www.apachefriends.org/es/index.html).
2. Instalar HeidiSQL (https://www.heidisql.com/) o usar phpMyAdmin incluido en XAMPP.
3. PHP 8.0 o superior (XAMPP moderno contiene PHP 8+ — confirmar versión en XAMPP Control Panel).
4. Habilitar la extensión `mysqli` (está habilitada por defecto en XAMPP).

## Configuración detallada (XAMPP + HeidiSQL)

1. Copie/clona este repositorio en el directorio de htdocs de XAMPP. Ejemplo en Windows:

```
C:\xampp\htdocs\FutbolPersistencia
```

2. Abra el Panel de Control de XAMPP y arranque **Apache** y **MySQL** (los botones "Start").

3. Verifique que Apache esté escuchando en el puerto 80 (o 8080 si 80 está en uso). Si hay conflictos, ajuste el puerto en XAMPP o cierre la aplicación que lo usa.

4. Importar la base de datos usando HeidiSQL (recomendado):

   - Abra HeidiSQL y cree una nueva sesión con estos datos:

     - Network type: MySQL (TCP/IP)
     - Hostname / IP: localhost
     - User: root
     - Password: (dejar vacío si no configuró contraseña)
     - Port: 3306

   - Conéctese, haga click derecho y cree la base de datos `futbol_persistencia` (si no existe).
   - Seleccione la base de datos recién creada y vaya a "File -> Run SQL file...".
   - Seleccione `persistence/script/bd.sql` y ejecútelo para crear las tablas y datos iniciales.

   > Nota: el proyecto ya viene configurado para conectar a:
   > - Host: `localhost`
   > - Usuario: `root`
   > - Contraseña: (vacía por defecto)
   > - Base de datos: `futbol_persistencia`

   Estos valores están en `persistence/conf/database.php`. Si quiere usar otras credenciales o nombre de BD, edite ese archivo.

5. Alternativa con phpMyAdmin (incluido en XAMPP):

   - Abra http://localhost/phpmyadmin
   - Cree la base de datos `futbol_persistencia` y use la interfaz "Importar" para subir `persistence/script/bd.sql`.

## Ejecutar la aplicación

- Asegúrese de que XAMPP tenga Apache y MySQL arrancados.
- Abra un navegador y acceda a:

```
http://localhost/FutbolPersistencia/index.php
```

Si ha cambiado el nombre de la carpeta o el puerto de Apache, adapte la URL (ej. `http://localhost:8080/FutbolPersistencia/`).

Opcional — servidor PHP embebido (solo para pruebas):

```bash
php -S localhost:8000 -t .
```

Luego abra http://localhost:8000/index.php (no recomendado para producción).

## Archivo de configuración de BD

El archivo `persistence/conf/database.php` contiene la conexión. Valores por defecto encontrados en este proyecto:

- DB_HOST: `localhost`
- DB_USER: `root`
- DB_PASSWORD: `` (vacío)
- DB_NAME: `futbol_persistencia`

Si su entorno usa contraseña para root o usuario distinto, edite ese archivo.

## Desarrollo y buenas prácticas

- Use un IDE/Editor con soporte PHP (VS Code + extensión de PHP).
- Mantenga seguro el acceso a la base de datos (no subir credenciales en repositorios públicos).
- Si va a trabajar en colaboración, considere añadir un archivo `config.example.php` y excluir el real con `.gitignore`.

## Problemas comunes y soluciones

- MySQL no arranca: asegúrese de que el puerto 3306 no esté ocupado y revise los logs de XAMPP.
- Apache no arranca: conflicto en el puerto 80/443 (cierre IIS, Skype u otras apps que usen esos puertos o cambie el puerto en XAMPP).
- Error de conexión a BD: verifique `persistence/conf/database.php` y que la BD `futbol_persistencia` exista.
- Permisos: en Windows normalmente no hay problema, pero en Linux asegúrese de que Apache tenga permisos sobre la carpeta del proyecto.

## Contribuir

- Cree una rama por feature/bugfix y abra un pull request con una descripción clara.
- Añada tests básicos cuando extienda la lógica de negocio.

## Próximos pasos sugeridos

- Añadir un `README` traducido a inglés si habrá colaboradores internacionales.
- Añadir Docker/Docker Compose para facilitar el entorno en nuevos equipos.
- Añadir pruebas automatizadas (PHPUnit) y CI.

---

Archivo creado automáticamente: `README.md` — contiene instrucciones para poner en marcha el proyecto localmente (XAMPP + HeidiSQL) y detalles de la estructura.
