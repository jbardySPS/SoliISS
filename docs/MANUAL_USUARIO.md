# Manual de Usuario — SOLI-ISS

**Sistema de Gestión de Solicitudes — ISS La Pampa**

---

## Índice

1. [Acceso al sistema](#1-acceso-al-sistema)
2. [Dashboard](#2-dashboard)
3. [Roles y permisos](#3-roles-y-permisos)
4. [Gestión de solicitudes](#4-gestión-de-solicitudes)
   - [Crear una solicitud](#41-crear-una-solicitud)
   - [Ver el detalle](#42-ver-el-detalle)
   - [Editar una solicitud](#43-editar-una-solicitud)
5. [Flujo de trabajo por rol](#5-flujo-de-trabajo-por-rol)
   - [Solicitante](#51-solicitante)
   - [Supervisor](#52-supervisor)
   - [Operador](#53-operador)
   - [Administrador](#54-administrador)
6. [Comentarios](#6-comentarios)
7. [Historial de estados](#7-historial-de-estados)
8. [Administración de usuarios](#8-administración-de-usuarios)

---

## 1. Acceso al sistema

Ingresar a la URL del sistema y completar:

- **Email:** dirección de correo institucional
- **Contraseña:** contraseña asignada por el administrador

> Si olvidaste tu contraseña, contactá al administrador del sistema para que la restablezca.

**Límite de intentos:** después de 5 intentos fallidos consecutivos, el acceso se bloquea por 1 minuto automáticamente.

---

## 2. Dashboard

Al ingresar se muestra el **Dashboard**, que varía según tu rol:

| Rol | Información mostrada |
|---|---|
| Solicitante | Mis solicitudes: total, pendientes, resueltas |
| Supervisor | Solicitudes enviadas pendientes de revisión |
| Operador | Solicitudes asignadas y en progreso |
| Admin | Totales globales del sistema |

Desde el navbar superior podés navegar entre **Solicitudes** y (si sos Admin) **Usuarios**.

---

## 3. Roles y permisos

El sistema tiene 4 roles:

### Solicitante
- Crear solicitudes propias
- Ver solo sus propias solicitudes
- Guardar como borrador o enviar directamente
- Editar solicitudes en estado Borrador

### Supervisor
- Ver todas las solicitudes enviadas y en revisión
- Tomar una solicitud para revisarla
- Aprobar o rechazar solicitudes
- Asignar solicitudes aprobadas a un operador
- Cerrar solicitudes resueltas
- Comentar en cualquier solicitud

### Operador
- Ver solicitudes asignadas a él
- Iniciar el trabajo (pasar a "En progreso")
- Resolver solicitudes terminadas
- Comentar en sus solicitudes

### Administrador
- Acceso completo a todas las solicitudes
- Puede ejecutar cualquier acción del flujo
- Gestionar usuarios (crear, editar, activar/desactivar)

---

## 4. Gestión de solicitudes

### 4.1 Crear una solicitud

1. Ir a **Solicitudes** en el menú superior
2. Hacer clic en **+ Nueva solicitud**
3. Completar el formulario:

| Campo | Descripción | Obligatorio |
|---|---|---|
| Tipo de solicitud | Reparación/Incidencia, Solicitud de Cambio, Nuevo Requerimiento, etc. | Sí |
| Área destino | Área que atenderá la solicitud (Soporte Técnico, Desarrollo, etc.) | Sí |
| Título | Resumen breve (máx. 200 caracteres) | Sí |
| Descripción | Detalle completo del problema o requerimiento (mín. 10 caracteres) | Sí |
| Prioridad | Baja / Media / Alta | Sí |
| Supervisor asignado | Supervisor que revisará la solicitud | No |
| Sistema / Aplicación | Sistema afectado (opcional) | No |

4. Al terminar, elegir una de las acciones:
   - **Enviar solicitud** → queda en estado *Enviada* y pasa al flujo de revisión
   - **Guardar borrador** → queda guardada pero solo visible para vos, podés editarla después

### 4.2 Ver el detalle

Desde el listado, hacer clic en **Ver** en cualquier solicitud. La pantalla de detalle muestra:

- **Encabezado:** número, título, estado actual (badge de color), fecha y autor
- **Detalle:** tipo, área, prioridad, descripción, sistema
- **Asignación:** supervisor y operador asignados
- **Panel de acciones:** botones disponibles según el estado actual y tu rol
- **Fechas clave:** creación, envío, aprobación (si aplica)
- **Historial de estados:** línea de tiempo de todos los cambios con fecha y usuario
- **Comentarios:** sección de intercambio entre los participantes

### 4.3 Editar una solicitud

Solo se pueden editar solicitudes en estado **Borrador** (y solo el propio solicitante o el Admin).

1. Desde el listado, entrar al detalle
2. Hacer clic en **Editar** (si aparece el botón)
3. Modificar los campos necesarios
4. Guardar cambios

---

## 5. Flujo de trabajo por rol

### 5.1 Solicitante

**Proceso típico:**

```
1. Crear solicitud → "Enviar solicitud"
2. La solicitud pasa a estado "Enviada"
3. Esperar que el Supervisor la tome para revisión
4. Seguir el progreso desde la pantalla de detalle
```

**Estados visibles:** Borrador, Enviada, En revision, Aprobada, Rechazada, Asignada, En progreso, Resuelta, Cerrada (solo las propias).

**Acciones disponibles:**
- Crear nueva solicitud
- Enviar un borrador guardado
- Agregar comentarios a sus solicitudes

---

### 5.2 Supervisor

**Proceso típico:**

```
1. Ver solicitudes en estado "Enviada" (filtrar por ese estado)
2. Entrar al detalle → "Tomar para revisión"
   → pasa a "En revision"
3. Evaluar la solicitud:
   - Si corresponde → "Aprobar" (con observación opcional)
   - Si no corresponde → "Rechazar" (motivo obligatorio)
4. Si fue aprobada → "Asignar a operador"
   → seleccionar operador y agregar instrucciones opcionales
5. Cuando el operador la resuelva → "Cerrar"
```

**Acciones disponibles en cada estado:**

| Estado actual | Acciones disponibles |
|---|---|
| Enviada | Tomar para revisión, Aprobar directamente |
| En revision | Aprobar, Rechazar |
| Aprobada | Asignar a operador |
| Resuelta | Cerrar |

---

### 5.3 Operador

**Proceso típico:**

```
1. Ver solicitudes asignadas (filtrar por "Asignada")
2. Entrar al detalle → "Iniciar trabajo"
   → pasa a "En progreso"
3. Trabajar en la resolución
4. Una vez solucionado → "Resolver"
   → pasa a "Resuelta" y queda pendiente de cierre por el Supervisor
```

**Acciones disponibles en cada estado:**

| Estado actual | Acciones disponibles |
|---|---|
| Asignada | Iniciar trabajo |
| En progreso | Resolver |

---

### 5.4 Administrador

El Admin puede ejecutar **cualquier acción** del flujo independientemente del estado. Además tiene acceso exclusivo al **ABM de Usuarios** (ver sección 8).

---

## 6. Comentarios

En la pantalla de detalle de cualquier solicitud hay una sección de **Comentarios** al pie.

- **Comentario público:** visible para todos los participantes
- **Comentario interno:** marcado con "Solo para uso interno", visible solo para Supervisores, Operadores y Admin (no para el Solicitante)

Para agregar un comentario:
1. Escribir en el campo de texto
2. Opcionalmente marcar "Uso interno"
3. Hacer clic en **Comentar**

---

## 7. Historial de estados

Cada cambio de estado queda registrado automáticamente en la línea de tiempo de la solicitud, mostrando:

- Estado anterior y nuevo estado
- Fecha y hora exacta del cambio
- Usuario que realizó el cambio
- Observación o nota (si se ingresó una al hacer la transición)

Este historial es **inmutable** — no puede modificarse ni eliminarse.

---

## 8. Administración de usuarios

> Solo disponible para el rol **Administrador**.

Acceder desde el menú superior → **Usuarios**.

### Listado de usuarios

- Muestra todos los usuarios con nombre, email, área, rol y estado
- Filtros disponibles: búsqueda por nombre/email, filtro por rol, filtro por estado (activo/inactivo)

### Crear usuario

1. Hacer clic en **+ Nuevo usuario**
2. Completar:

| Campo | Descripción |
|---|---|
| Nombre completo | Nombre y apellido |
| Email | Correo institucional (debe ser único) |
| Contraseña | Mínimo 8 caracteres, al menos 1 mayúscula y 1 minúscula |
| Rol | Solicitante / Supervisor / Operador / Admin |
| Área / Sector | Sector al que pertenece (opcional) |
| Usuario activo | Si está marcado, puede ingresar al sistema |

3. Hacer clic en **Crear usuario**

### Editar usuario

1. Hacer clic en **Editar** en la fila del usuario
2. Modificar los campos necesarios
3. Para cambiar la contraseña: ingresar la nueva en el campo correspondiente (si se deja vacío, no se modifica)
4. Guardar cambios

### Activar / Desactivar usuario

Desde el listado, el botón **Desactivar** / **Activar** en la fila del usuario permite bloquear o habilitar el acceso sin eliminar el registro ni su historial.

> Un usuario desactivado no puede iniciar sesión aunque ingrese sus credenciales correctas.

---

## Estados y colores de referencia

| Estado | Color |
|---|---|
| Borrador | Gris |
| Enviada | Azul |
| En revision | Naranja |
| Aprobada | Verde |
| Rechazada | Rojo |
| Asignada | Violeta |
| En progreso | Azul claro |
| Resuelta | Verde oscuro |
| Cerrada | Gris oscuro |

## Prioridades

| Prioridad | Indicación |
|---|---|
| Alta | Problema crítico que afecta la operación |
| Media | Problema importante, tiene solución alternativa |
| Baja | Mejora o requerimiento no urgente |
