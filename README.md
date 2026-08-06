# workday-sdd-cdln — Control de Presencia

Aplicación web monolítica para la gestión del fichaje de empleados: registro de entradas y salidas, administración del personal y consulta del historial de asistencia mensual.

---

## Framework de desarrollo: dln-spec-kit

Este proyecto está desarrollado siguiendo el framework propio de **Spec-Driven Development (SDD)** [`dln-spec-kit`](dln-spec-kit/README.md).

El framework define un ciclo de vida completo de desarrollo asistido por IA: desde la creación de historias de usuario hasta el cierre y archivado de specs, garantizando que todo el código esté cubierto por tests y alineado con los estándares del proyecto.

**Para entender cómo se ha construido esta aplicación, continúa la lectura en [`dln-spec-kit/README.md`](dln-spec-kit/README.md).**

---

## Descripción funcional

**workday-sdd-cdln** es un sistema centralizado de control horario desarrollado por Chema de la Nieta. Los responsables de administración gestionan el alta, modificación y baja de empleados desde un panel de administración privado. Cada empleado accede a un portal propio donde consulta sus registros de asistencia del mes en curso y ficha su entrada o salida de turno. El sistema impide el acceso cruzado entre empleados: cada usuario ve únicamente sus propios datos.

La aplicación expone dos contextos de acceso diferenciados que comparten la misma base de datos de usuarios:

- **Panel de administración** (`/admin`) — gestionado con Filament, exclusivo para el rol `admin`.
- **Portal del empleado** (`/login`, `/dashboard`) — accesible para el rol `employee`.

---

## Roles y capacidades

### Administrador

Accede a `/admin` con sus credenciales. Desde el panel puede:

- Consultar el listado de empleados con búsqueda por nombre y correo, y filtro por estado activo/inactivo.
- Crear nuevos empleados indicando nombre completo, correo electrónico, contraseña y estado.
- Editar los datos de un empleado existente (la contraseña es opcional al editar: dejarla en blanco conserva la actual).
- Eliminar empleados tras confirmación. La cuenta del administrador principal (definida en `.env`) no puede eliminarse.

### Empleado

Accede a `/login` con sus credenciales. Desde su dashboard puede:

- Ver un saludo personalizado según la hora del día junto a su avatar de iniciales.
- Consultar la tabla de registros del mes actual: fecha, hora de entrada, hora de salida y total de horas trabajadas.
- Usar el botón de fichaje: **"Fichar entrada"** si no hay turno abierto hoy, **"Fichar salida"** si hay uno en curso. *(La lógica de escritura se implementa en US-002.)*

Las cuentas inactivas quedan bloqueadas en el login con el mensaje `"Your account has been deactivated. Contact the administrator."`.

---

## Funcionalidades incluidas (US-001)

- Control de acceso basado en roles: enum `admin` / `employee` con casteo nativo en el modelo.
- Panel de administración con CRUD completo de empleados (Filament 3.x), excluye la cuenta admin del listado.
- Bloqueo de cuentas inactivas en el proceso de autenticación del portal de empleado.
- Redirección por rol tras el login: administradores van a `/admin`, empleados a `/dashboard`.
- Dashboard del empleado con tabla de registros del mes en curso paginada a 50 registros.
- Botón de fichaje condicional en tarjeta visual inspirada en el widget de Factorial *(placeholder — sin lógica de escritura)*.
- Política de acceso `ClockRecordPolicy` que impide la consulta de registros ajenos.
- Semilla de cuenta administrador idempotente (`firstOrCreate`) desde variables de entorno.
- Identidad visual propia (teal `#2BBFB3`, pink `#E8195A`) registrada como tokens Tailwind.

## Funcionalidades planificadas (US-002 en adelante)

US-002 implementará la lógica completa de fichaje: el manejador POST que crea o cierra un `ClockRecord`, el temporizador visual de turno en curso y el contador de tiempo transcurrido. Incrementos posteriores contemplarán el flujo de restablecimiento de contraseña, notificaciones por correo al crear cuentas y configuración de zona horaria por empleado.

---

## Development Plan

See [Development Plan](openspec/development-plan.md) for the full list of planned user stories.

---

## Stack tecnológico

| Área | Tecnología |
|---|---|
| Framework | Laravel 11.x, PHP 8.2+ |
| Panel de administración | Filament 3.x (`/admin`) |
| Portal del empleado | Blade + Tailwind CSS + Alpine.js |
| Scaffold de autenticación | Laravel Breeze (pila Blade, rutas no usadas desactivadas) |
| Base de datos (desarrollo/producción) | MySQL |
| Base de datos (tests) | SQLite en memoria |
| Testing | PHPUnit (`php artisan test`) |
| Build | Vite + npm |

---

## Arquitectura

La aplicación sigue el patrón **MVC + capa de servicio**. Toda la lógica de negocio relacionada con usuarios vive en `App\Services\User\UserService` (`createEmployee()`, `updateEmployee()`, `isProtectedAdmin()`); los recursos de Filament y los controladores únicamente delegan en el servicio.

El rol de usuario se modela como enum PHP 8.1 (`App\Enums\UserRole`) y se castea automáticamente en el modelo `User`. El contrato `canAccessPanel()` de Filament restringe el panel de administración a usuarios con `role = admin`. La autenticación del portal de empleado es independiente de la de Filament y usa la sesión nativa de Laravel.

El acceso a los registros de asistencia está protegido a nivel de fila por `App\Policies\ClockRecordPolicy`, que garantiza que cada empleado solo pueda consultar sus propios registros, con doble refuerzo: scope de Eloquent en el controlador y autorización mediante política.

---

## Esquema de base de datos

### Tabla `users`

Extiende la migración por defecto de Laravel con dos columnas adicionales:

| Columna | Tipo | Valor por defecto | Descripción |
|---|---|---|---|
| `role` | enum(`admin`, `employee`) | `employee` | Casteado a `UserRole` |
| `is_active` | boolean | `true` | `false` bloquea el acceso al login |

### Tabla `clock_records`

| Columna | Tipo | Descripción |
|---|---|---|
| `id` | bigint PK | Autoincremental |
| `user_id` | bigint FK → `users.id` | CASCADE on delete |
| `clocked_in_at` | timestamp | Requerido |
| `clocked_out_at` | timestamp nullable | `null` indica turno abierto |
| `created_at` / `updated_at` | timestamps | Gestionados por Eloquent |

---

## Instalación y puesta en marcha

1. `composer install`
2. `npm install && npm run build`
3. `cp .env.example .env && php artisan key:generate`
4. Configura `.env` con las credenciales de base de datos y las variables `ADMIN_*` (ver tabla a continuación).
5. `php artisan migrate --seed`
6. `composer dev` para arrancar todos los servicios a la vez, o `php artisan serve` solo el servidor.

### Variables de entorno requeridas

| Variable | Descripción |
|---|---|
| `ADMIN_EMAIL` | Correo de la cuenta administradora sembrada |
| `ADMIN_PASSWORD` | Contraseña de la cuenta administradora |
| `ADMIN_NAME` | Nombre visible del administrador |

---

## Comandos de desarrollo

| Comando | Propósito |
|---|---|
| `composer dev` | Arranca servidor, cola, logs y Vite en paralelo |
| `php artisan serve` | Servidor de desarrollo únicamente |
| `npm run dev` | Vite en modo watch |
| `php artisan test` | Ejecuta la suite de tests |
| `php artisan migrate:fresh --seed` | Reinicia la base de datos y siembra de nuevo |

---

## Sistema de diseño

Tokens de marca registrados en `tailwind.config.js`:

| Token | Hex | Uso |
|---|---|---|
| `accom-teal` | `#2BBFB3` | Botones primarios, estado activo, fondo del avatar, navbar |
| `accom-teal-light` | `#E8F9F8` | Fondos de tarjeta, hover, etiquetas |
| `accom-pink` | `#E8195A` | Botón de salida, mensajes de error, acciones destructivas |

El logo se renderiza como SVG inline con los valores hexadecimales de marca. No se usan archivos de imagen externos ni CSS personalizado: solo clases de utilidad de Tailwind.

---

## Estado del proyecto (US-001)

| Spec | Descripción | Estado |
|---|---|---|
| Spec 0 | Tokens de marca Tailwind | ✅ Completada |
| Spec 1 | Bootstrap Laravel 11 + Filament + Breeze | ✅ Completada |
| Spec 2 | Migraciones de base de datos | ✅ Completada |
| Spec 3 | Enum `UserRole` + modelo `User` + estados de factory | ✅ Completada |
| Spec 4 | Modelo `ClockRecord` + factory | ⏳ Pendiente |
| Spec 5 | `AdminSeeder` idempotente desde `.env` | ⏳ Pendiente |
| Spec 6 | `UserService` (crear, editar, guardia admin) | ⏳ Pendiente |
| Spec 7 | `EmployeeResource` de Filament (CRUD completo) | ⏳ Pendiente |
| Spec 8 | Autenticación del empleado + rutas limpias | ⏳ Pendiente |
| Spec 9 | Dashboard del empleado + tabla de registros | ⏳ Pendiente |
| Spec 10 | `ClockRecordPolicy` | ⏳ Pendiente |
| Spec 11 | Suite de 11 feature tests en verde | ⏳ Pendiente |
| Spec 12 | Componentes Blade (`<x-navbar>`, `<x-clock-widget>`) + `TimeGreetingHelper` | ⏳ Pendiente |
| Playwright | Tests funcionales de la historia de usuario | ⏳ Pendiente |

---

## Nota sobre la inclusión de dln-spec-kit en el repositorio

En una instalación habitual de `dln-spec-kit`, tanto el directorio del framework como los symlinks generados por su instalador se añadirían a `.gitignore` para mantener el repositorio de la aplicación limpio e independiente del kit.

En este repositorio se han **incluido expresamente** para que el evaluador pueda revisar el framework, su estructura, las reglas de codificación y los artefactos de especificación (`openspec/`) como parte de la entrega del trabajo práctico.
