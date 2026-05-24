# Práctica 4 - Laravel Passport API

Práctica de la asignatura **Desarrollo Web en Entorno Servidor** de 2º DAW.

El objetivo es crear una API REST con autenticación mediante tokens usando Laravel Passport y el protocolo OAuth 2.0.

---

## Tecnologías usadas

- PHP 8.2
- Laravel 12
- Laravel Passport 13
- MySQL

---

## Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/Omar-andres-prog/laravel-passport-api.git
cd laravel-passport-api
```

### 2. Instalar dependencias

```bash
composer install
```

### 3. Configurar el entorno

Copiar el archivo de ejemplo y editarlo con tus datos:

```bash
cp .env.example .env
```

Cambiar en `.env` los datos de la base de datos:

```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=passport_api
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Crear la base de datos

Crear en MySQL una base de datos llamada `passport_api`.

### 5. Ejecutar migraciones e instalar Passport

```bash
php artisan migrate
php artisan passport:install
```

### 6. Arrancar el servidor

```bash
php artisan serve
```

---

## Endpoints

### POST /api/register
Registra un nuevo usuario.

**Body (JSON):**
```json
{
    "name": "nombreusuario",
    "email": "correo@ejemplo.com",
    "password": "contraseña"
}
```

**Respuesta:**
```json
{
    "message": "Usuario registrado correctamente.",
    "user": { ... }
}
```

---

### POST /api/login
Autentica al usuario con email **o** nombre y contraseña. Devuelve un token de acceso.

**Body (JSON):**
```json
{
    "identifier": "correo@ejemplo.com",
    "password": "contraseña"
}
```

**Respuesta:**
```json
{
    "message": "Sesión iniciada correctamente.",
    "token_type": "Bearer",
    "access_token": "eyJ0eXAiOiJKV1Qi...",
    "expires_in": 31536000
}
```

---

### GET /api/me
Devuelve los datos del usuario autenticado. Requiere token.

**Header:**
```
Authorization: Bearer {token}
```

**Respuesta:**
```json
{
    "user": { ... }
}
```

---

### POST /api/logout
Cierra la sesión eliminando todos los tokens del usuario. Requiere token.

**Header:**
```
Authorization: Bearer {token}
```

**Respuesta:**
```json
{
    "message": "Sesión cerrada correctamente. Todos los tokens han sido eliminados."
}
```

---

## Notas

- Las rutas `/me` y `/logout` están protegidas por el middleware `auth:api`. Si no se envía token o es inválido, se devuelve un error 401.
- El login acepta tanto el email como el nombre de usuario en el campo `identifier`.
- Al hacer logout se eliminan **todos** los tokens del usuario, no solo el actual.
