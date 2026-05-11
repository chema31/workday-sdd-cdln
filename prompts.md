# prompts.md — Development Session Log

This file is automatically updated by the `UserPromptSubmit` Claude Code hook
(configured in `.claude/settings.local.json`). Every slash-command invocation
is appended here with its timestamp and arguments.

**Format:**
```
## YYYY-MM-DD HH:MM — /command-name
**Args:**
> [first 300 chars of arguments]
```

---

<!-- RETROACTIVE ENTRIES — 2026-05-11 session -->

## 2026-05-11 — /meta-prompt
**Args:**
> Crear una historia de usuario para implementar la base de esta aplicación. Se tratará de una aplicación monolítica Laravel, que permitirá fichar la entrada y salida de los empleados.
> En esta primera historia de usuario nos encargaremos de crear toda la gestión de usuarios de la aplicación.
> Un CRUD de empleados, gestionado desde un panel administrador con Filament y un acceso de administrador al panel para el email desarrollo@we-accom.com (que se almacenará en .env).
> En la parte frontal, accesible con email y password, cada usuario podrá acceder a la interfaz en la que aparecerá el registro de horas fichadas el último mes y un botón en el que podrá fichar la entrada al comienzo de jornada y la salida al finalizar dicha jornada.

## 2026-05-11 — /enrich-us
**Args:**
> [Output from /meta-prompt above — US-001 User Management & Application Bootstrap]
> Saved to: openspec/features/20260511-user-management-and-application-bootstrap.md

## 2026-05-11 — /meta-prompt
**Args:**
> los prompts que mantengamos de ahora en adelante, así como estos anteriores deben quedar registrados en un archivo prompts.md que almacene todos mis prompts y ejecuciones de comandos durante este desarrollo. Crea una skill o la funcionalidad que sea necesaria para ello o aconséjame la mejor solución.

## 2026-05-11 18:36 — /enrich-us
**Args:**
> US-001
