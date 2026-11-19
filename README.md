# Práctica 10 · CRUD de estudiantes UNS

Proyecto Laravel 12 para Aplicaciones Distribuidas I, VI ciclo. Implementa las siete acciones de un controlador de recursos, persistencia con Eloquent, vistas Blade, Bootstrap local y las validaciones de la guía. Los ocho estudiantes del seeder son ficticios.

## Instalación en Windows con XAMPP

1. Activa Apache y MySQL en XAMPP. En `http://localhost/phpmyadmin` ejecuta el contenido de `database/crear_base.sql`. El nombre `db_practica09_uns` reproduce el nombre exigido en la guía de la práctica 10.
2. Descomprime `proyecto-estudiantes` en una carpeta nueva, por ejemplo `C:\xampp\htdocs\proyecto-estudiantes`. No lo copies encima de tu proyecto anterior.
3. Abre una terminal en esa carpeta. Comprueba `php -v` y `composer --version`. Se requiere PHP 8.2 o superior; si PHP no se reconoce, usa `C:\xampp\php\php.exe` o añade `C:\xampp\php` al PATH.
4. En `C:\xampp\php\php.ini` habilita las extensiones requeridas por Composer, en particular `extension=zip` y `extension=intl` si están comentadas. XAMPP normalmente incluye mbstring, openssl, fileinfo y pdo_mysql. La terminal utiliza el PHP indicado por `where php`, no necesariamente el de Apache.
5. Ejecuta, una línea cada vez:

```bat
composer install
copy .env.example .env
php artisan key:generate
php artisan config:clear
php artisan migrate --seed
php artisan serve
```

No repitas `copy .env.example .env` si ya configuraste tu `.env`. Las dependencias necesitan internet durante `composer install`; las pantallas no requieren internet después de instalar, porque Bootstrap y el logo se incluyen localmente. No es necesario ejecutar npm.

6. Abre **http://localhost:8000/estudiantes**. Mantén abierta la terminal del servidor.

La configuración inicial de `.env.example` usa `mysql`, `127.0.0.1`, puerto `3306`, base `db_practica09_uns`, usuario `root` y contraseña vacía. Ajusta solo si tu XAMPP usa otros valores. `php artisan serve` atiende Laravel en el puerto 8000; Apache se utiliza para phpMyAdmin.

## Qué demostrar

1. Enseña la tabla `estudiantes` y el resultado de `php artisan migrate:status`.
2. Abre el listado y pasa de la página 1 a la 2 (cinco y tres registros de ejemplo).
3. Crea un estudiante con código `2026100099`, correo propio de prueba, ciclo VI y promedio 16.50.
4. Abre su ficha. Edita el ciclo a VII y el promedio a 18.25. Verifica el cambio también en phpMyAdmin.
5. Intenta repetir código o correo. Para probar una nota 25 en el servidor, el control HTML bloquea primero: en DevTools, pestaña Elements, agrega `novalidate` al `<form>` y envía el formulario. Laravel debe rechazar los datos y conservar el resto. Esta modificación se usa solo durante la prueba.
6. Cancela una eliminación y luego confírmala. Comprueba la desaparición del registro en la base.
7. Compara `created_at` con `updated_at` después de editar, dejando al menos un segundo entre ambas operaciones.

## Pruebas automatizadas

```bat
php artisan test
```

Ejecuta 19 pruebas y 76 aserciones con SQLite en memoria; requiere `pdo_sqlite` y `sqlite3` en el PHP de terminal. No borra ni usa los datos de MySQL configurados en `.env`. La verificación adicional con MariaDB y las pruebas HTTP reales se documentan en `../evidencias` y en el informe. PHPUnit omite CSRF por diseño; los estados 419 se comprobaron aparte mediante peticiones reales.

## Estructura importante

- `app/Models/Estudiante.php`: tabla, campos permitidos y conversión decimal.
- `app/Http/Controllers/EstudianteController.php`: siete acciones y reglas compartidas para crear y editar.
- `database/migrations/2026_11_18_000000_create_estudiantes_table.php`: esquema de estudiantes.
- `routes/web.php`: rutas de recurso; parámetro singular explícito `estudiante` para el binding.
- `resources/views/estudiantes`: listado, formularios, ficha y parcial compartido.
- `resources/views/layouts/app.blade.php`: plantilla principal.
- `tests/Feature/EstudianteCrudTest.php`: pruebas de comportamiento.

Los archivos de migración de usuarios, caché y trabajos son los originales del esqueleto Laravel; la entidad de esta práctica es `estudiantes`. No se implementa autenticación porque no se pide en la guía. El proyecto es una práctica local de laboratorio.

## Explicación breve para la exposición

El navegador envía una petición a una ruta de recurso. Laravel resuelve el estudiante si la URL contiene un ID. El controlador valida la entrada y usa el modelo Eloquent para consultar o modificar MySQL. Devuelve una vista Blade o una redirección con un mensaje. Los errores de validación regresan al formulario y `old()` recupera lo escrito. `@csrf` genera el token de sesión; `@method('PUT')` y `@method('DELETE')` indican a Laravel el método que representa el formulario POST.

## Si ocurre un error

- **could not find driver**: revisa `pdo_mysql` y `php --ini`; reinicia la terminal después de modificar PHP.
- **Access denied / Connection refused**: activa MySQL y revisa usuario, contraseña y puerto en `.env`; ejecuta `php artisan config:clear`.
- **Unknown database**: ejecuta `database/crear_base.sql` en phpMyAdmin.
- **No application encryption key**: ejecuta `php artisan key:generate`.
- **Port 8000 in use**: ejecuta `php artisan serve --port=8001` y abre el puerto correspondiente.
- **Composer usa otra versión de PHP**: comprueba `where php` y ejecuta Composer con PHP de XAMPP. `composer.lock` se resolvió para PHP 8.2.12.

No uses `migrate:fresh` ni `migrate:refresh` sobre tus datos de trabajo. Repetir `php artisan db:seed --class=EstudianteSeeder` solo incorpora los ejemplos que falten según su código.

## Materiales y referencias

Guía de práctica dirigida 10 y diapositivas de sesión 10 proporcionadas por el docente. Documentación oficial: https://laravel.com/docs/12.x. Bootstrap 5.3.3 se distribuye bajo licencia MIT (avisos incluidos en sus archivos). Logo institucional obtenido de https://www.uns.edu.pe/img/logo_rojo.png para esta práctica académica.

## Repetir las pruebas sobre MySQL de XAMPP

Crea una base separada y vacía para pruebas con `CREATE DATABASE db_practica09_uns_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`. Luego ejecuta `php vendor/bin/phpunit -c phpunit.mysql.xml`. El archivo fija la base de pruebas, host 127.0.0.1, puerto 3306 y root sin contraseña. Ajusta sus credenciales si tu instalación lo requiere. No cambies DB_DATABASE por tu base de trabajo: RefreshDatabase reconstruye las tablas de la base destinada a pruebas.
