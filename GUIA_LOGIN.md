# Guía de Uso - Sistema de Autenticación

## Archivos Principales

### 1. **config.php**
Archivo de configuración de la base de datos. Contiene:
- Credenciales de conexión a MySQL
- Inicialización de sesión

### 2. **login.php**
Página de inicio de sesión. Características:
- Formulario con campos: correo y contraseña
- Validación de credenciales en la BD
- Creación de sesión si la autenticación es exitosa
- Redirección automática si ya está logueado

### 3. **perfil.php**
Página del perfil del usuario. Características:
- Requiere estar logueado
- Muestra datos del usuario (ID, nombre, correo, región)
- Enlace para cerrar sesión

### 4. **verificar_sesion.php**
Archivo de verificación. Úsalo en cualquier página que requiera autenticación:
```php
<?php
require_once 'verificar_sesion.php';
// El resto de tu código aquí
?>
```

### 5. **cerrar_sesion.php**
Destruye la sesión del usuario y redirige al login.

## Datos de Prueba

Puedes usar estas credenciales para probar:

| Correo | Contraseña | Nombre |
|--------|-----------|--------|
| alvaro@email.com | pass123 | Álvaro Martínez |
| lucia@email.com | lucia456 | Lucía Fernández |
| carlos@email.com | carlos789 | Carlos López |

## Flujo de Autenticación

1. Usuario ingresa a `login.php`
2. Completa formulario con correo y contraseña
3. Se valida en la BD tabla USUARIO
4. Si es correcto, se crea sesión con datos del usuario
5. Se redirige a `perfil.php`
6. Desde el perfil, puede cerrar sesión con `cerrar_sesion.php`

## Seguridad (Recomendaciones para Producción)

⚠️ **Importante**: Este código es para educación/desarrollo.

Para producción:
- Usar `password_hash()` para guardar contraseñas
- Usar `password_verify()` para verificar contraseñas
- Agregar HTTPS obligatorio
- Validar entrada más estrictamente
- Usar prepared statements (ya se usan en este código ✓)
- Agregar CSRF tokens
- Limitar intentos de login fallidos

## Estructura de Sesión

Variables disponibles tras login exitoso:
- `$_SESSION['id_usuario']` - ID del usuario
- `$_SESSION['nombre']` - Nombre completo
- `$_SESSION['correo']` - Correo electrónico
- `$_SESSION['region']` - Región del usuario
- `$_SESSION['logueado']` - Boolean (true si está logueado)
