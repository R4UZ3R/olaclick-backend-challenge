# 🧪 OlaClick Backend Challenge - Laravel Edition

API RESTful para gestión de órdenes de restaurante desarrollada con Laravel, PostgreSQL y Redis.

## 🚀 Tecnologías

- Laravel 11
- PostgreSQL 15
- Redis 7
- Docker & Docker Compose
- PHPUnit (Tests)
- Swagger/OpenAPI

## 📋 Prerrequisitos

- Docker y Docker Compose instalados
- Git

## ⚙️ Instalación y Configuración

### 1. Clonar el repositorio
```bash
git clone https://github.com/R4UZ3R/olaclick-backend-challenge.git
cd olaclick-backend-challenge
```

### 2. Crear el archivo .env
```bash
cp .env.example .env
```

**Importante:** Verificar que en el archivo `.env` estén configuradas estas variables:
```env
CACHE_DRIVER=redis
CACHE_STORE=redis
SESSION_DRIVER=redis
REDIS_HOST=redis
```

### 3. Construir e iniciar los contenedores
```bash
docker-compose up -d --build
```

**Nota:** El Dockerfile ya instala las dependencias de Composer automáticamente.

### 4. Generar la clave de la aplicación
```bash
docker-compose exec app php artisan key:generate
```

### 5. Limpiar configuraciones de caché
```bash
docker-compose exec app php artisan config:clear
```

### 6. Ejecutar las migraciones
```bash
docker-compose exec app php artisan migrate
```

### 7. (Opcional) Ejecutar seeders para datos de prueba
```bash
docker-compose exec app php artisan db:seed
```

### 8. (Opcional) Generar documentación Swagger
```bash
docker-compose exec app php artisan l5-swagger:generate
```

## 🧪 Ejecutar Tests
```bash
docker-compose exec app php artisan test
```

## 📡 Endpoints de la API

La API estará disponible en: `http://localhost:8000`

**Nota:** Los endpoints que usan método POST deben probarse con Postman, Insomnia o herramientas similares.

### Listar Órdenes Activas
```
GET http://localhost:8000/api/orders
```

### Crear Nueva Orden
```
POST http://localhost:8000/api/orders
Content-Type: application/json

{
  "client_name": "Carlos Gómez",
  "items": [
    { "description": "Lomo saltado", "quantity": 1, "unit_price": 60 },
    { "description": "Inka Kola", "quantity": 2, "unit_price": 10 }
  ]
}
```

### Ver Detalles de una Orden
```
GET http://localhost:8000/api/orders/{id}
```

### Avanzar Estado de la Orden
```
POST http://localhost:8000/api/orders/{id}/advance
```

## 📚 Documentación Swagger

La documentación interactiva de la API está disponible en:
```
http://localhost:8000/api/documentation
```

Para regenerar la documentación después de cambios:
```bash
docker-compose exec app php artisan l5-swagger:generate
```

## 🔄 Flujo de Estados
```
initiated → sent → delivered (y eliminada)
```

## 📦 Estructura del Proyecto
```
app/
├── Http/
│   ├── Controllers/
│   │   └── OrderController.php
│   └── Requests/
│       └── CreateOrderRequest.php
├── Models/
│   ├── Order.php
│   ├── OrderItem.php
│   └── OrderLog.php
├── Repositories/
│   ├── OrderRepositoryInterface.php
│   └── OrderRepository.php
└── Services/
    └── OrderService.php

database/
├── migrations/
├── seeders/
└── factories/

tests/
├── Feature/
│   └── OrderTest.php
└── Unit/
    └── OrderServiceTest.php
```

## 🎯 Funcionalidades Implementadas

- ✅ CRUD de órdenes con Eloquent ORM
- ✅ Caché con Redis (TTL: 30s)
- ✅ Validaciones con Form Requests
- ✅ Repository Pattern
- ✅ Service Layer
- ✅ Inyección de Dependencias
- ✅ Logs de cambio de estado
- ✅ Tests automatizados (11 tests pasando)
- ✅ Seeders y Factories
- ✅ Docker Compose
- ✅ Documentación Swagger/OpenAPI
- ✅ Principios SOLID aplicados

## 🛑 Detener los Contenedores
```bash
docker-compose down
```

## 🗑️ Limpiar Todo (incluyendo volúmenes)
```bash
docker-compose down -v
```

## 📝 Notas Técnicas

- La API usa caché Redis con TTL de 30 segundos para el listado de órdenes activas
- Las órdenes en estado "delivered" son automáticamente eliminadas de la base de datos
- Todos los cambios de estado se registran en la tabla `order_logs` con timestamps
- El total de la orden se calcula automáticamente basado en los items
- Se invalida el caché automáticamente al crear o modificar órdenes
- Las dependencias de Composer se instalan automáticamente durante el build del contenedor

## 🔧 Comandos Útiles
```bash
# Acceder al contenedor de la aplicación
docker-compose exec app bash

# Ver logs en tiempo real
docker-compose logs -f app

# Limpiar configuraciones
docker-compose exec app php artisan config:clear

# Resetear base de datos con datos de prueba
docker-compose exec app php artisan migrate:fresh --seed

# Ejecutar tests
docker-compose exec app php artisan test

# Verificar rutas disponibles
docker-compose exec app php artisan route:list

# Reinstalar dependencias de Composer
docker-compose exec app composer install
```

## 📮 Colección Postman

Se incluye el archivo `postman_collection.json` con todos los endpoints configurados para testing rápido. Importar en Postman para facilitar las pruebas.

## 🐛 Troubleshooting

### Error de tabla "cache" no existe

Si aparece el error `relation "cache" does not exist`, ejecutar:
```bash
docker-compose exec app php artisan config:clear
docker-compose restart app
```

### Problemas con dependencias de Composer

Si hay problemas con vendor o dependencias:
```bash
docker-compose exec app composer install
docker-compose exec app composer dump-autoload
```

### Rebuild completo

Si algo no funciona, hacer rebuild completo:
```bash
docker-compose down -v
docker-compose up -d --build
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --seed
```