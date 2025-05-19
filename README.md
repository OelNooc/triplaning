## Requisitos Previos
XAMPP instalado (Windows/macOS/Linux)

Git instalado

Cuenta en OpenWeatherMap (para API Key opcional)

## 1. Configuración Inicial de XAMPP
Descarga XAMPP desde https://www.apachefriends.org/

Instala con configuración predeterminada

Inicia el panel de control de XAMPP

Inicia los módulos:

Apache

MySQL

## 2. Clonar el Repositorio
```bash
git clone [git@github.com:OelNooc/triplaning.git]
cd triplaning
```

## 3. Configuración del Entorno
Copiar el archivo de configuración ejemplo:

```bash
cp .env.example .env

Editar .env con tus credenciales locales:

ini
DB_HOST=localhost
DB_NAME=triplaning_db
DB_USER=root
DB_PASS=

OPEN_WEATHER_API_KEY=tu_api_key
```

## 4. Configuración de la Base de Datos
Abre phpMyAdmin en http://localhost/phpmyadmin

Crea una nueva base de datos llamada triplaning_db

Importa la estructura:

Selecciona la base de datos

Ve a la pestaña "Importar"

Sube el archivo sql/01_schema.sql

Importa datos de prueba:

Repite el proceso para sql/02_test_data.sql

## 5. Configurar el Proyecto en XAMPP
Mueve la carpeta del proyecto a:

Windows: C:\xampp\htdocs\triplaning

macOS: /Applications/XAMPP/htdocs/triplaning

Linux: /opt/lampp/htdocs/triplaning

Configura el virtual host (opcional pero recomendado):

Edita C:\xampp\apache\conf\extra\httpd-vhosts.conf (Windows)

Añade:

```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/triplaning"
    ServerName triplaning.local
</VirtualHost>
Añade 127.0.0.1 triplaning.local a tu archivo hosts
```

## 6. Acceder al Proyecto
Inicia XAMPP si no está en ejecución

Abre tu navegador en:

http://localhost/triplaning (sin virtual host)

http://triplaning.local (con virtual host)

## 7. Credenciales de Prueba
Puedes iniciar sesión con estos usuarios de prueba:

Email: ana@example.com / Contraseña: password123

Email: carlos@example.com / Contraseña: password123

## 8. Configuración adicional - API del Clima
Regístrate en OpenWeatherMap

Obtén tu API Key gratuita

Actualiza .env:

```ini
OPEN_WEATHER_API_KEY=tu_api_key_real
```

## Solución de Problemas Comunes
Problema: No se conecta a la base de datos

Verifica que MySQL esté corriendo en XAMPP

Revisa las credenciales en .env

Problema: Páginas no cargan correctamente

Verifica que los archivos estén en htdocs

Prueba recargando sin cache (Ctrl+F5)

Problema: API del clima no funciona

Verifica que hayas configurado tu API Key

Comprueba que tengas conexión a internet
