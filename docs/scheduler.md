# Scheduler de Laravel – Limpieza de Carritos Abandonados

Este proyecto utiliza el **Scheduler de Laravel** para realizar tareas automáticas de mantenimiento.

Una de estas tareas es la **limpieza automática de carritos abandonados**.

---

## 🧹 Limpieza de carritos abandonados

Existe un comando Artisan encargado de eliminar carritos con estado `abandonado` después de un periodo de tiempo configurable.

## Requisito
El entorno debe ejecutar:

php artisan schedule:run

cada minuto.

## Linux / macOS
Configurar cron:

* * * * * php /ruta/proyecto/artisan schedule:run

## Windows
Configurar Task Scheduler para ejecutar el mismo comando cada minuto.

## Docker / Cloud
Usar cron, supervisor o scheduler del proveedor.
