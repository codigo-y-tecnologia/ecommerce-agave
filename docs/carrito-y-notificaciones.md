## 🔔 Sistema de Notificaciones de Carrito Activo

### Objetivo
Recordar al usuario (o invitado) que tiene productos pendientes, sin ser invasivos.

### Etapas de notificación

| Etapa | Tiempo de inactividad | Tipo |
|----|----------------------|------|
| 24h | 24 horas | Email suave |
| 72h | 72 horas | Email con incentivo |
| 7d  | 7 días | Último recordatorio |

Cada etapa:
- Se envía **una sola vez por carrito**
- Se registra en `tbl_cart_notifications` para evitar duplicados

---

## ⚙️ Comando Principal

```bash
php artisan carts:notify-active

## Requisito
El entorno debe ejecutar:
php artisan schedule:run

## Linux / macOS
Configurar cron:

* * * * * php /ruta/proyecto/artisan schedule:run

## Windows
Configurar Task Scheduler para ejecutar el mismo comando cada minuto.

## Docker / Cloud
Usar cron, supervisor o scheduler del proveedor.

## Las notificaciones se envían mediante jobs, por lo que se requiere:
php artisan queue:work

## Desarrollo local
Para pruebas locales se puede ejecutar manualmente:
php artisan queue:work
php artisan carts:notify-active

