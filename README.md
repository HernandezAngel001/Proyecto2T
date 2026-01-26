# STEMFounding - Diario de Desarrollo

Este es el registro de cómo va avanzando el proyecto *STEMFounding*, la plataforma de crowdfunding para ciencia. Aquí voy apuntando lo que voy haciendo cada día.

---

## 📅 Registro de Actividad

### 30/01/2026 - Inicio del Proyecto y Configuración
- *Setup Inicial*:
    - Creación del proyecto en Laravel.
    - Configuración de la base de datos MySQL (`stemfounding`).
    - Creación del repositorio Git con ramas `main`, `development` y `feature/setup`.
- *Base de Datos*: Me puse con el diseño de las tablas principales.
    - *Usuarios*: Añadí campos para roles (`admin`, `entrepreneur`, `investor`), billetera (`wallet`) y baneo.
    - *Proyectos*: Tabla completa con estados, financiación y fechas límite.
    - *Inversiones*: Tabla pivot para registrar quién pone dinero en qué.
    - *Actualizaciones*: Sistema para que los emprendedores suban noticias.
- *Modelos y Relaciones*:
    - Definí las relaciones `hasMany` y `belongsTo` entre Usuarios, Proyectos e Inversiones.
- *Datos de Prueba (Seeding)*:
    - Creé un `DatabaseSeeder` potente que genera un usuario de cada tipo (Admin, Elon Musk como emprendedor, Warren Buffett como inversor) y proyectos de ejemplo para no empezar con la web vacía.
- *Docs*:
    - Creé el `task.md` para llevar el control.
    - Creé el `implementation_plan.md` con la hoja de ruta técnica.
    - Generé el `secret.md` (ignorado en git) para explicar el código al profesor.

---

### 30/01/2026 - Sprint de Implementación Completa
- *Autenticación y Seguridad*:
    - Implementé el sistema de Login/Registro de Laravel.
    - Creé el middleware `EnsureUserRole` para proteger rutas según el rol (Admin, Emprendedor, Inversor).
    - Añadí una regla extra en el middleware para *bloquear automáticamente a los usuarios baneados* (los expulsa si intentan navegar).

- *Módulo de Proyectos*:
    - Creé el `ProjectController` con toda la lógica CRUD para emprendedores.
    - Implementé la regla de negocio: "Máximo 2 proyectos activos por emprendedor".
    - *Vistas*: Diseñé el feed público con filtros y buscador, y la vista de detalles con barra de progreso y carrusel de novedades.

- *Sistema de Inversiones (Core)*:
    - *Billetera*: Creé una gestión básica de saldo (simulada) para que los inversores recarguen fondos.
    - *Invertir*: Lógica transaccional para restar saldo y sumar financiación de forma segura (`DB::transaction`).
    - *Reglas*: Control de tope máximo (nadie puede invertir más de lo que falta) y *derecho de desistimiento* (retirar inversión en las primeras 24h).

- *Panel de Administración*:
    - Dashboard con KPIs en tiempo real (Proyectos activos, dinero total recaudado, etc.).
    - Gestión de Usuarios: Botón para Banear/Desbanear usuarios conflictivos.
    - Moderación: Lista de proyectos pendientes para Aprobar o Rechazar.

- *Automatización (Cron Jobs)*:
    - Implementé el comando `projects:check-status` que corre a diario.
        - *Reembolsos*: Si un proyecto expira sin llegar al mínimo, devuelve el dinero a los inversores.
        - *Éxito*: Si llega al mínimo o al tope, marca el proyecto como completado y transfiere los fondos al emprendedor.

- *Extras*:
    - *Blog de Actualizaciones*: Los emprendedores ya pueden postear noticias en sus proyectos.

---

## 📊 Estado del Proyecto

### ✅ Cosas Terminadas

Aquí un resumen de lo que he ido cerrando.

#### 1. Base y Setup
- [x] Montar el Laravel y configurar entorno.
- [x] Diseñar la base de datos (tablas, claves foráneas).
- [x] Crear los Seeders para tener datos de prueba.
- [x] Configurar Git Flow (`main` -> `development` -> `features`).

#### 2. Seguridad y Roles
- [x] Login y Registro.
- [x] Middlewares de roles (Admin, Emprendedor, Inversor).
- [x] Seguridad contra usuarios baneados.

#### 3. Backend Core
- [x] Controladores (Project, Investment, Admin, Update).
- [x] Lógica de negocio (Límites, 24h reembolso, Transacciones).
- [x] Scheduler para revisión automática de proyectos.

#### 4. Frontend (Blade)
- [x] Layout principal con navegación por roles.
- [x] Vistas de Proyectos (Listado, Creación, Edición, Detalles).
- [x] Vistas de Admin (Dashboard, Tablas de gestión).
- [x] Implementación de Carrusel y Modales (Bootstrap).

#### 5. Próximos Pasos (Fase API y React)
- [ ] Implementar la API REST completa.
- [ ] Desarrollar el cliente React para emprendedores.

---
*Este documento se actualizará automáticamente con cada avance significativo.*
