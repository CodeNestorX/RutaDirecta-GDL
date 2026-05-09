# RUTADirecta GDL


## 📋 Requisitos

* [Docker Desktop](https://www.docker.com/products/docker-desktop/)


## 🚀 Instalación

1. **Clonar el repositorio**

   ```bash
   git clone https://github.com/CodeNestorX/RutaDirecta-GDL.git
   cd rutadirecta-gdl
   ```


2. **Copiar el archivo .env.example**

   ```bash
   cp .env.example .env
   ```

3. **Iniciar el entorno Docker**

   ```bash
   docker compose up -d --build
   ```

   **Nota:** La primera vez puede tardar algunos minutos en completarse.

4. **Configurar Laravel**

   ```bash
   docker compose exec app composer install
   docker compose exec app php artisan key:generate
   ```
5. **Crear Base de Datos**

   ```bash
   docker compose exec app php artisan migrate:fresh
   ```

6. **Activar Servidor Web**
   ```bash
   docker compose exec -d app php artisan serve --host=0.0.0.0 --port=8000
   ```

7. **Rutas de acceso**
   ```bash
   http://localhost:8005
   ```
   ```bash
   http://localhost:8081
   ```

**Nota: De preferencia todos los comandos de docker deben de ejecutarse en una terminal como administrador**

### Bitacora de Errores y Soluciones

1. **Errores de Puertos**

   - Problema: Windows bloquea los puertos 8005 u 8081
   - Solución: Reinicar el servidor de red ejecutando en una terminal como administrador:

   1. net stop winnat
   2. docker compose up -d
   3. net start winnat




