# 🔐 Sistema de Autenticación - Documentación Técnica

## 📋 Resumen de Archivos Creados

| Archivo | Tipo | Descripción |
|---------|------|-------------|
| **login.php** | 🔴 Principal | Página de entrada y procesamiento de login |
| **perfil.php** | 🟡 Protegido | Página de perfil del usuario logueado |
| **verificar_sesion.php** | 🔵 Utilidad | Verifica sesión activa (incluir en páginas protegidas) |
| **cerrar_sesion.php** | 🟡 Utilitario | Destruye sesión y redirecciona a login |
| **config.php** | ⚙️ Config | Configuración de base de datos (Ya existía) |
| **base_datos.sql** | 📊 SQL | Script para crear BD y datos de prueba |
| **GUIA_LOGIN.md** | 📖 Docs | Guía detallada de uso |
| **INSTRUCCIONES.txt** | 📋 Docs | Instrucciones rápidas |

---

## 🔄 Flujo de Autenticación

```
┌─────────────────────────────────────────────────────────┐
│ Usuario abre: http://localhost/proyecto-2daw/login.php │
└──────────────────┬──────────────────────────────────────┘
                   │
        ┌──────────▼──────────┐
        │ ¿Ya está logueado?  │
        └──────────┬──────────┘
         SI         │         NO
         │          │          │
    Redirect  Formula HTML  Espera POST
    perfil.php
                   │
        ┌──────────▼─────────────┐
        │ Usuario completa form  │
        │ - Correo               │
        │ - Contraseña           │
        └──────────┬─────────────┘
                   │
        ┌──────────▼──────────────┐
        │ Envío POST a login.php  │
        └──────────┬──────────────┘
                   │
        ┌──────────▼──────────────────────┐
        │ Buscar en BD por correo         │
        │ SELECT * FROM USUARIO           │
        │ WHERE correo = ?                │
        └──────────┬──────────────────────┘
                   │
        ┌──────────▼──────────┐
        │ ¿Usuario existe?    │
        └──────────┬──────────┘
         SI        │        NO
         │         │         │
      Verificar  ERROR     ERROR
      Contraseña  │         │
         │        └─────┬───┘
    ┌────▼─────┐        │
    │ ¿Válida? │    Mostrar
    └────┬─────┘    mensaje
    SI   │   NO     de error
    │    │   │      volver a
    │    │   │      form
    │    └───┴──►────┘
    │
    └──► $_SESSION['logueado'] = true
         $_SESSION['id_usuario'] = ...
         $_SESSION['nombre'] = ...
         $_SESSION['correo'] = ...
         $_SESSION['region'] = ...
         
         Redirigir a perfil.php
         
         ¿Acceso a perfil.php?
         
         ├─ Incluye verificar_sesion.php
         ├─ Verifica $_SESSION['logueado']
         ├─ SI → Muestra perfil
         └─ NO → Redirige a login.php
```

---

## 💻 Código de login.php - Explicado

### Parte 1: Incluir Configuración y Verificación

```php
<?php
require_once 'config.php';  // Incluye conexión a BD y sesión

// Si ya está logueado, redirige a perfil
if (isset($_SESSION['logueado']) && $_SESSION['logueado'] === true) {
    header("Location: perfil.php");
    exit();
}
```

### Parte 2: Procesar POST

```php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
    $contrasena = isset($_POST['contrasena']) ? trim($_POST['contrasena']) : '';
    
    // Validar no vacíos
    if (empty($correo) || empty($contrasena)) {
        $error = "Por favor completa todos los campos";
    } else {
        // Prepared statement para evitar SQL injection
        $consulta = "SELECT ID_usuario, nombre, correo, contrasena, region 
                    FROM USUARIO 
                    WHERE correo = ?";
        $stmt = $conn->prepare($consulta);
        $stmt->bind_param("s", $correo);  // "s" = string
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        // Verificar si existe el usuario
        if ($resultado->num_rows == 1) {
            $usuario = $resultado->fetch_assoc();
            
            // Comparar contraseña
            if ($usuario['contrasena'] === $contrasena) {
                // ÉXITO: Crear sesión
                $_SESSION['id_usuario'] = $usuario['ID_usuario'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['correo'] = $usuario['correo'];
                $_SESSION['region'] = $usuario['region'];
                $_SESSION['logueado'] = true;
                
                // Redirigir
                header("Location: perfil.php");
                exit();
            } else {
                $error = "Contraseña incorrecta";
            }
        } else {
            $error = "El correo no existe en el sistema";
        }
    }
}
?>
```

### Parte 3: Formulario HTML

```html
<form method="POST" action="login.php">
    <div class="form-group">
        <label for="correo">Correo Electrónico</label>
        <input type="email" id="correo" name="correo" required>
    </div>
    <div class="form-group">
        <label for="contrasena">Contraseña</label>
        <input type="password" id="contrasena" name="contrasena" required>
    </div>
    <button type="submit" class="btn-login">Iniciar Sesión</button>
</form>
```

---

## 🛡️ verificar_sesion.php - Protección

Usar este archivo en cualquier página que requiera login:

```php
<?php
require_once 'config.php';

// Si no está logueado, redirige al login
if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
    header("Location: login.php");
    exit();
}
// Continúa con el resto del código solo si está logueado
?>
```

**Ejemplo de uso en forum.html → forum.php:**

```php
<?php
require_once 'verificar_sesion.php';
?>
<!-- Aquí viene el HTML del foro -->
<!-- Solo usuarios logueados llegarán aquí -->
```

---

## 🔄 cerrar_sesion.php

```php
<?php
require_once 'config.php';

// Destruir sesión
session_destroy();
$_SESSION = array();

// Redirigir al login
header("Location: login.php");
exit();
?>
```

---

## 📊 Estructura de Datos

### Tabla USUARIO
```
ID_usuario (CHAR 9)  | nombre (VARCHAR)    | correo (VARCHAR UNIQUE) | contrasena | region
─────────────────────────────────────────────────────────────────────────────────────
U00000001           | Álvaro Martínez     | alvaro@email.com        | pass123    | Andalucía
U00000002           | Lucía Fernández     | lucia@email.com         | lucia456   | Cataluña
U00000003           | Carlos López        | carlos@email.com        | carlos789  | Madrid
```

---

## 🔒 Variables de Sesión Disponibles

Después de login exitoso, disponibles en `$_SESSION`:

```php
$_SESSION['id_usuario']    // "U00000001"
$_SESSION['nombre']        // "Álvaro Martínez"
$_SESSION['correo']        // "alvaro@email.com"
$_SESSION['region']        // "Andalucía"
$_SESSION['logueado']      // true
```

---

## ✅ Características de Seguridad Implementadas

| Feature | Estado | Detalles |
|---------|--------|----------|
| **Prepared Statements** | ✅ | Previene SQL Injection |
| **Trim en inputs** | ✅ | Elimina espacios en blanco |
| **htmlspecialchars()** | ✅ | Previene XSS en mensajes |
| **Session handling** | ✅ | Variables de sesión seguras |
| **Validación inputs** | ✅ | Campos requeridos |
| **Redireccionamiento** | ✅ | Automático según estado |

---

## ⚠️ Mejoras para Producción

```php
// ❌ ACTUAL (Desarrollo/Educación):
if ($usuario['contrasena'] === $contrasena) {

// ✅ PRODUCCIÓN - Agregar:
if (password_verify($contrasena, $usuario['contrasena'])) {
```

Al guardar usuarios, usar:
```php
$hash = password_hash($contrasena_nueva, PASSWORD_BCRYPT);
// Guardar $hash en BD, no la contraseña plana
```

---

## 🧪 Datos de Prueba

```
alvaro@email.com  → pass123
lucia@email.com   → lucia456
carlos@email.com  → carlos789
```

---

## 📁 Estructura de Carpetas (Recomendada)

```
proyecto-2daw/
├── config.php                  (Configuración)
├── login.php                   (Página principal)
├── perfil.php                  (Página protegida)
├── verificar_sesion.php        (Incluir en otras)
├── cerrar_sesion.php           (Logout)
├── base_datos.sql              (BD)
├── estilos.css
├── index.html
├── forum.html (convertir a PHP)
├── img/
└── README.md
```

---

## 🔗 URLs Importantes

| URL | Descripción |
|-----|-------------|
| `login.php` | Página de login |
| `perfil.php` | Perfil del usuario (protegido) |
| `cerrar_sesion.php` | Cerrar sesión |

---

## 🐛 Solución de Problemas

### "Error de conexión a BD"
- Verifica que MySQL esté corriendo
- Verifica credenciales en config.php
- Base de datos se llama "proyecto"

### "Contraseña incorrecta"
- Verifica que escribiste bien la contraseña
- Las contraseñas son sensibles a mayúsculas
- En BD no están hasheadas (solo para desarrollo)

### "No redirige a perfil.php"
- Verifica que no hay espacios en blanco antes de `<?php`
- No debe haber salida antes de `header()`

### "Session no persiste"
- Verifica que cookies estén habilitadas
- Comprueba que `session_start()` está en config.php

---

## 📝 Notas

- Los nombres de variables están **100% en español**
- Los comentarios están en **español**
- Todo es compatible con **PHP 7+**
- Usa **UTF-8** para caracteres españoles

---

**Última actualización:** 2025-04-23
