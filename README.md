# Gestor de Proyectos con Laravel y Vue

Este es un proyecto de portafolio que consiste en una aplicación web completa para la gestión de proyectos y tareas, construida con un stack tecnológico moderno. La aplicación permite a los usuarios registrarse, iniciar sesión y gestionar sus propios proyectos y tareas de forma privada y segura.

## Características Implementadas

La aplicación cuenta con un ciclo de vida completo para la gestión de entidades y una experiencia de usuario mejorada.

### Funcionalidades del Backend:
- **Autenticación Completa:** Sistema de registro, inicio de sesión y gestión de perfiles de usuario implementado con **Laravel Breeze**.
- **Gestión de Proyectos (CRUD):**
    - Crear, leer, editar y eliminar proyectos.
    - Cada proyecto está asociado a un único usuario.
    - Subida y almacenamiento de imágenes de portada para cada proyecto.
- **Gestión de Tareas (CRUD):**
    - Añadir tareas a un proyecto específico.
    - Editar el título de las tareas.
    - Marcar tareas como completadas o pendientes.
    - Eliminar tareas.
- **Seguridad y Autorización:** Uso de **Policies** de Laravel para asegurar que un usuario solo pueda ver, editar o eliminar sus propios proyectos y tareas.
- **Relaciones de Base de Datos:** Arquitectura de datos relacional (Usuarios -> Proyectos -> Tareas) implementada con el ORM Eloquent.
- **Atributos Calculados:** El progreso de un proyecto se calcula automáticamente en el backend como un "accessor" basado en el porcentaje de tareas completadas.

### Funcionalidades del Frontend:
- **Interfaz Reactiva (SPA-like):** Construido con **Vue.js** e **Inertia.js** para una experiencia de usuario rápida y fluida sin recargas de página.
- **Notificaciones Flash:** Mensajes de éxito que aparecen después de realizar acciones (crear, actualizar, eliminar) para dar feedback al usuario.
- **Modales de Confirmación:** Ventanas emergentes personalizadas para acciones destructivas (eliminar un proyecto o una tarea), mejorando la UX y previniendo errores.
- **Organización Visual:**
    - Las tareas completadas se separan visualmente y se mueven al final de la lista.
    - Indicadores visuales para la prioridad de las tareas.
    - Barra de progreso automática en cada proyecto que se actualiza en tiempo real.

## Stack Tecnológico

- **Backend:** PHP 8.2+, Laravel 12
- **Frontend:** Vue.js 3, Inertia.js, Tailwind CSS, Vite
- **Base de Datos:** PostgreSQL
- **Entorno de Desarrollo:** Docker, Laravel Sail (corriendo en una VM de Ubuntu)

## Cómo Iniciar el Entorno de Desarrollo Local

Este proyecto está configurado para correr en un entorno de desarrollo basado en Docker con Laravel Sail.

1.  **Clonar el repositorio:**
    ```bash
    git clone [https://github.com/NousGR/gestor-proyectos-laravel.git](https://github.com/NousGR/gestor-proyectos-laravel.git)
    cd gestor-proyectos-laravel
    ```
2.  **Instalar dependencias:**
    ```bash
    # Instalar dependencias de PHP
    composer install

    # Instalar dependencias de JavaScript
    npm install
    ```
3.  **Configurar el entorno:**
    * Copiar el archivo de entorno: `cp .env.example .env`
    * Generar la clave de la aplicación: `php artisan key:generate`
    * Asegurarse de que el archivo `.env` tenga la configuración correcta para Sail y PostgreSQL:
        ```env
        DB_CONNECTION=pgsql
        DB_HOST=pgsql
        DB_PORT=5432
        DB_DATABASE=laravel
        DB_USERNAME=sail
        DB_PASSWORD=password
        ```

4.  **Iniciar los servicios con Sail:**
    * Crear un alias para comodidad (opcional, por sesión): `alias sail='bash vendor/bin/sail'`
    * Levantar los contenedores en segundo plano: `sail up -d`

5.  **Preparar la base de datos:**
    * Ejecutar las migraciones para crear todas las tablas: `sail artisan migrate`

6.  **Iniciar el servidor de frontend:**
    * En una segunda terminal, compilar los assets y mantenerlo observando cambios: `npm run dev`

7.  **Acceder a la aplicación:**
    * La aplicación estará disponible en `http://localhost`.

## Ideas para el Futuro (Roadmap)
- Implementar un sistema de comentarios para las tareas.
- Añadir lógica para que las tareas se marquen como "vencidas" si su fecha límite ha pasado (usando el Task Scheduler de Laravel).
- Mejorar el formulario de edición de proyectos para permitir la actualización o eliminación de la imagen de portada.
- Escribir pruebas automatizadas con Pest para asegurar la robustez de la aplicación.
