# SOLI-ISS — Sistema de Gestión de Solicitudes

Sistema de gestión de solicitudes internas (tipo ITSM) desarrollado para el **Instituto de Seguridad Social de La Pampa (ISS La Pampa)**. Permite a los empleados registrar, seguir y resolver solicitudes de soporte, cambios y requerimientos a través de un flujo de trabajo de 9 estados.

---

## Stack tecnológico

| Componente | Tecnología |
|---|---|
| Backend | Laravel 13 / PHP 8.3 |
| Base de datos | IBM DB2 iSeries Power9 via ODBC |
| Driver DB | `cooperl/laravel-db2` (con parches de compatibilidad Laravel 13) |
| Frontend | Blade + CSS inline (sin framework CSS externo) |
| Autenticación | Sesiones Laravel nativas |

---

## Estructura del proyecto

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/LoginController.php      # Login, logout, rate limiting
│   │   ├── DashboardController.php       # Dashboard con métricas por rol
│   │   ├── SolicitudController.php       # CRUD + 9 transiciones de estado
│   │   └── UserController.php            # ABM de usuarios (solo Admin)
│   └── Middleware/
│       └── RoleMiddleware.php            # Protección de rutas por rol
├── Models/
│   ├── User.php                          # Tabla SOLIISS.USUARIOS
│   ├── Rol.php                           # Tabla SOLIISS.ROLES
│   ├── Solicitud.php                     # Tabla SOLIISS.SOLICITUDES
│   ├── TipoSol.php                       # Tabla SOLIISS.TIPOS_SOL
│   ├── EstadoSol.php                     # Tabla SOLIISS.ESTADOS_SOL
│   ├── AreaDest.php                      # Tabla SOLIISS.AREAS_DEST
│   ├── HistorialEstado.php               # Tabla SOLIISS.HISTORIAL_ESTADOS
│   └── Comentario.php                    # Tabla SOLIISS.COMENTARIOS
resources/views/
├── layouts/app.blade.php                 # Layout principal con navbar
├── auth/login.blade.php                  # Pantalla de login
├── dashboard.blade.php                   # Dashboard
├── solicitudes/
│   ├── index.blade.php                   # Listado con filtros
│   ├── create.blade.php                  # Formulario de creación
│   ├── edit.blade.php                    # Formulario de edición
│   └── show.blade.php                    # Detalle + acciones + historial
└── admin/usuarios/
    ├── index.blade.php                   # Listado de usuarios
    ├── create.blade.php                  # Formulario nuevo usuario
    └── edit.blade.php                    # Formulario editar usuario
```

---

## Configuración de la base de datos

### Conexión DB2 (`.env`)

```env
DB_CONNECTION=db2
DB_HOST=10.108.102.42
DB_PORT=446
DB_DATABASE=*LOCAL
DB_USERNAME=<usuario>
DB_PASSWORD=<password>
DB_SCHEMA=SOLIISS
DB_DRIVER={IBM i Access ODBC Driver}
DB_DATE_FORMAT=Y-m-d
DB_ODBC_KEYWORDS=TRANSLATE=1
```

### Tablas en esquema SOLIISS

| Tabla | Descripción |
|---|---|
| `USUARIOS` | Usuarios del sistema |
| `ROLES` | Roles disponibles (4 roles fijos) |
| `TIPOS_SOL` | Tipos de solicitud (Incidencia, Cambio, Requerimiento, etc.) |
| `ESTADOS_SOL` | Estados del flujo (9 estados con color y orden) |
| `AREAS_DEST` | Áreas destinatarias de solicitudes |
| `SOLICITUDES` | Solicitudes (tabla principal) |
| `HISTORIAL_ESTADOS` | Log de cada cambio de estado |
| `COMENTARIOS` | Comentarios por solicitud (públicos e internos) |
| `ADJUNTOS` | Archivos adjuntos (tabla disponible, funcionalidad pendiente) |

### Consideración importante: PDO::CASE_LOWER

El driver DB2 devuelve todos los nombres de columna en **minúsculas** por configuración. Todos los modelos usan nombres de columna en minúsculas (`usr_nombre`, `sol_titulo`, etc.) aunque en DB2 estén almacenados en mayúsculas.

---

## Roles de usuario

| ID | Nombre | Permisos principales |
|---|---|---|
| 1 | Solicitante | Crear y ver sus propias solicitudes |
| 2 | Supervisor | Revisar, aprobar/rechazar y asignar solicitudes |
| 3 | Operador | Tomar solicitudes asignadas y marcarlas como resueltas |
| 4 | Admin | Acceso total + ABM de usuarios |

---

## Flujo de estados (ITSM 9 estados)

```
                    ┌─────────────┐
                    │   BORRADOR  │ (1) — guardado sin enviar
                    └──────┬──────┘
                           │ enviar()   [Solicitante / Admin]
                    ┌──────▼──────┐
                    │   ENVIADA   │ (2)
                    └──────┬──────┘
                           │ tomarRevision()   [Supervisor / Admin]
                    ┌──────▼──────┐
                    │ EN REVISION │ (3)
                    └──┬──────┬───┘
           aprobar()   │      │  rechazar()
    [Supervisor/Admin] │      │  [Supervisor/Admin]
                    ┌──▼──┐ ┌─▼────────┐
                    │APRO-│ │RECHAZADA │ (5) — estado final
                    │BADA │ └──────────┘
                    │ (4) │
                    └──┬──┘
                       │ asignar()   [Supervisor / Admin]
                ┌──────▼──────┐
                │  ASIGNADA   │ (6)
                └──────┬──────┘
                       │ iniciar()   [Operador / Admin]
                ┌──────▼──────┐
                │ EN PROGRESO │ (7)
                └──────┬──────┘
                       │ resolver()   [Operador / Admin]
                ┌──────▼──────┐
                │  RESUELTA   │ (8)
                └──────┬──────┘
                       │ cerrar()   [Supervisor / Admin]
                ┌──────▼──────┐
                │   CERRADA   │ (9) — estado final
                └─────────────┘
```

### Transiciones por rol

| Acción | Ruta | Roles habilitados |
|---|---|---|
| Enviar borrador | `POST /solicitudes/{id}/enviar` | Solicitante, Admin |
| Tomar para revisión | `POST /solicitudes/{id}/tomar-revision` | Supervisor, Admin |
| Aprobar | `POST /solicitudes/{id}/aprobar` | Supervisor, Admin |
| Rechazar | `POST /solicitudes/{id}/rechazar` | Supervisor, Admin |
| Asignar a operador | `POST /solicitudes/{id}/asignar` | Supervisor, Admin |
| Iniciar trabajo | `POST /solicitudes/{id}/iniciar` | Operador, Admin |
| Resolver | `POST /solicitudes/{id}/resolver` | Operador, Admin |
| Cerrar | `POST /solicitudes/{id}/cerrar` | Supervisor, Admin |
| Comentar | `POST /solicitudes/{id}/comentar` | Todos los roles |

---

## Rutas completas

### Autenticación
```
GET  /              → redirige a /login
GET  /login         → formulario de login
POST /login         → procesa login (rate limit: 5 intentos/minuto)
POST /logout        → cierra sesión
GET  /dashboard     → dashboard con métricas según rol
```

### Solicitudes (requiere auth)
```
GET    /solicitudes                    → listado con filtros por estado
GET    /solicitudes/create             → formulario de creación
POST   /solicitudes                    → guardar nueva solicitud
GET    /solicitudes/{id}               → detalle de solicitud
GET    /solicitudes/{id}/edit          → formulario de edición
PUT    /solicitudes/{id}               → actualizar solicitud
DELETE /solicitudes/{id}               → eliminar (solo borradores propios)

POST   /solicitudes/{id}/enviar
POST   /solicitudes/{id}/tomar-revision
POST   /solicitudes/{id}/aprobar
POST   /solicitudes/{id}/rechazar
POST   /solicitudes/{id}/asignar
POST   /solicitudes/{id}/iniciar
POST   /solicitudes/{id}/resolver
POST   /solicitudes/{id}/cerrar
POST   /solicitudes/{id}/comentar
```

### Administración (requiere rol Admin)
```
GET    /admin/usuarios                 → listado de usuarios
GET    /admin/usuarios/create          → formulario nuevo usuario
POST   /admin/usuarios                 → crear usuario
GET    /admin/usuarios/{id}/edit       → formulario editar usuario
PUT    /admin/usuarios/{id}            → actualizar usuario
POST   /admin/usuarios/{id}/toggle     → activar/desactivar usuario
```

---

## Instalación

### Requisitos previos
- PHP 8.3+
- Composer
- Driver ODBC de IBM i Access instalado en el servidor
- Acceso de red al host DB2 (por defecto puerto 446)

### Pasos

```bash
# 1. Clonar el repositorio
git clone <repo> soliiss && cd soliiss

# 2. Instalar dependencias
composer install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate
# Completar variables DB_* en .env

# 4. Verificar conexión DB2
php artisan tinker
>>> DB::select('select 1 from sysibm.sysdummy1')

# 5. Ejecutar migraciones
php artisan migrate

# 6. Levantar servidor
php artisan serve
```

### Parches al vendor de DB2

El paquete `cooperl/laravel-db2` requiere parches de compatibilidad con Laravel 13. Los archivos modificados son:

- `vendor/cooperl/laravel-db2/src/Schema/Grammars/DB2Grammar.php`
- `vendor/cooperl/laravel-db2/src/DB2Connection.php`
- `vendor/cooperl/laravel-db2/src/Schema/Blueprint.php`
- `vendor/cooperl/laravel-db2/src/Schema/Builder.php`

El diff completo está en `patches/cooperl-laravel-db2-laravel13-compat.patch`.

> **Importante:** Si se ejecuta `composer install` o `composer update`, estos parches deben reaplicarse manualmente antes de usar la aplicación.

---

## Numeración de solicitudes

Formato: `SOL-YYYY-NNNNN`

Ejemplos: `SOL-2026-00001`, `SOL-2026-00042`

El número se genera automáticamente luego del INSERT usando el ID retornado por DB2.

---

## Seguridad

- Contraseñas hasheadas con **bcrypt**
- Rate limiting en login: **5 intentos por minuto** por IP
- CSRF protection en todos los formularios
- Autorización por rol verificada en cada método del controlador
- Solicitantes solo ven sus propias solicitudes
- Middleware `role:4` protege todas las rutas `/admin/*`

---

## Roadmap

- [ ] Notificaciones por email al cambiar estado
- [ ] Carga y descarga de adjuntos (tabla `ADJUNTOS` ya creada en DB)
- [ ] Filtros avanzados por fecha, área y solicitante
- [ ] Dashboard con gráficos históricos
- [ ] Exportación a PDF / Excel
