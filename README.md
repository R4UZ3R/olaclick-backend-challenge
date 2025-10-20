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

### 2. Construir e iniciar los contenedores
```bash
docker-compose up -d --build
```

**Nota:** El Dockerfile automáticamente:
- Instala todas las dependencias de Composer
- Copia el archivo `.env.example` a `.env`
- Genera la clave de la aplicación (`APP_KEY`)
- Configura permisos necesarios


### 3. Ejecutar las migraciones
```bash
docker-compose exec app php artisan migrate
```

### 4. (Opcional) Ejecutar seeders para datos de prueba
```bash
docker-compose exec app php artisan db:seed
```

### 5. (Opcional) Generar documentación Swagger
```bash
docker-compose exec app php artisan l5-swagger:generate
```

## 🧪 Ejecutar Tests
```bash
docker-compose exec app php artisan test
```

**Resultado esperado:** 11 tests pasando

## 📡 Endpoints de la API

La API estará disponible en: `http://localhost:8000`

**Nota:** Los endpoints que usan método POST deben probarse con Postman, Insomnia o herramientas similares.

### Listar Órdenes Activas
```
GET http://localhost:8000/api/orders
```

Retorna todas las órdenes con status != 'delivered' (cacheado por 30 segundos)

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

Retorna información completa de la orden incluyendo items y logs de cambio de estado.

### Avanzar Estado de la Orden
```
POST http://localhost:8000/api/orders/{id}/advance
```

Transición: `initiated` → `sent` → `delivered` (eliminada)

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
initiated → sent → delivered (y eliminada del sistema)
```

Cuando una orden alcanza el estado `delivered`, es automáticamente eliminada de la base de datos y del caché.

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
├── Services/
│   ├── OrderServiceInterface.php
│   └── OrderService.php
└── Providers/
    └── AppServiceProvider.php

database/
├── migrations/
│   ├── create_orders_table.php
│   ├── create_order_items_table.php
│   └── create_order_logs_table.php
├── seeders/
│   ├── DatabaseSeeder.php
│   └── OrderSeeder.php
└── factories/
    ├── OrderFactory.php
    └── OrderItemFactory.php

tests/
├── Feature/
│   └── OrderTest.php (8 tests)
└── Unit/
    └── OrderServiceTest.php (3 tests)
```

## 🎯 Funcionalidades Implementadas

- ✅ **CRUD completo** de órdenes con Eloquent ORM
- ✅ **Caché con Redis** (TTL: 30s) para listado de órdenes activas
- ✅ **Validaciones robustas** con Form Requests
- ✅ **Repository Pattern** con interfaces
- ✅ **Service Layer** con interfaces
- ✅ **Inyección de Dependencias** en toda la aplicación
- ✅ **Logs de cambio de estado** con timestamps en tabla dedicada
- ✅ **Tests automatizados** (11 tests: 8 feature + 3 unit)
- ✅ **Seeders y Factories** para datos de prueba
- ✅ **Docker Compose** totalmente automatizado
- ✅ **Documentación Swagger/OpenAPI** interactiva
- ✅ **Principios SOLID** aplicados (SRP, OCP, LSP, ISP, DIP)
- ✅ **Cálculo automático** de totales basado en items
- ✅ **Invalidación automática** de caché al crear/modificar órdenes

## 🏗️ Arquitectura y Principios SOLID

### Single Responsibility Principle (SRP)
- Cada clase tiene una única responsabilidad
- Controllers solo manejan HTTP
- Services contienen lógica de negocio
- Repositories manejan persistencia

### Open/Closed Principle (OCP)
- Uso de interfaces permite extensión sin modificación
- Nuevas implementaciones de repositorios o services pueden ser agregadas fácilmente

### Liskov Substitution Principle (LSP)
- Las implementaciones de interfaces son intercambiables
- OrderRepository puede ser reemplazado por otra implementación

### Interface Segregation Principle (ISP)
- Interfaces específicas y cohesivas
- OrderRepositoryInterface y OrderServiceInterface tienen métodos específicos

### Dependency Inversion Principle (DIP)
- Dependencia de abstracciones (interfaces), no de implementaciones concretas
- Controllers dependen de OrderServiceInterface
- Services dependen de OrderRepositoryInterface

## 🛑 Detener los Contenedores
```bash
docker-compose down
```

## 🗑️ Limpiar Todo (incluyendo volúmenes)
```bash
docker-compose down -v
```

## 📝 Notas Técnicas

- La API usa **Redis** como driver de caché con TTL de 30 segundos para el listado de órdenes activas
- Las órdenes en estado `delivered` son **automáticamente eliminadas** de la base de datos y del caché
- Todos los cambios de estado se registran en la tabla `order_logs` con timestamps precisos
- El **total de la orden** se calcula automáticamente basado en la suma de subtotales de los items
- Se **invalida el caché** automáticamente al crear, modificar o eliminar órdenes
- La arquitectura sigue **principios SOLID** con interfaces para Services y Repositories
- El **Dockerfile** instala automáticamente todas las dependencias, genera la clave de la aplicación y configura permisos
- **PostgreSQL 15** con índices optimizados en columnas frecuentemente consultadas
- **Eager loading** implementado para prevenir problema N+1

## 🔧 Comandos Útiles
```bash
# Acceder al contenedor de la aplicación
docker-compose exec app bash

# Ver logs en tiempo real
docker-compose logs -f app

# Ver logs de un contenedor específico
docker-compose logs db
docker-compose logs redis

# Verificar estado de los contenedores
docker-compose ps

# Limpiar configuraciones de Laravel
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear

# Resetear base de datos con datos de prueba
docker-compose exec app php artisan migrate:fresh --seed

# Ejecutar tests con cobertura
docker-compose exec app php artisan test --coverage

# Verificar rutas disponibles
docker-compose exec app php artisan route:list

# Acceder a PostgreSQL
docker-compose exec db psql -U olaclick -d olaclick

# Acceder a Redis CLI
docker-compose exec redis redis-cli

# Verificar chaves en Redis
docker-compose exec redis redis-cli KEYS "*"
```

## 📮 Colección Postman

Se incluye el archivo `postman_collection.json` con todos los endpoints configurados para testing rápido. 

**Para usar:**
1. Abrir Postman
2. Click en "Import"
3. Seleccionar el archivo `postman_collection.json`
4. Todos los endpoints estarán listos para probar

## 🐛 Troubleshooting

### Error de tabla "cache" no existe

Si aparece el error `relation "cache" does not exist`:
```bash
docker-compose exec app php artisan config:clear
docker-compose restart app
```

### Container reiniciando continuamente

Ver logs para identificar el problema:
```bash
docker-compose logs app
```

Los errores más comunes:
- Falta la carpeta `vendor` (el Dockerfile la instala automáticamente)
- Falta `APP_KEY` (el Dockerfile la genera automáticamente)
- Puerto 8000 ya en uso (cambiar puerto en `docker-compose.yml`)

### Puerto 8000 ya en uso

Si el puerto 8000 está ocupado, cambiar en `docker-compose.yml`:
```yaml
ports:
  - "8080:8000"  # Usar puerto 8080 en lugar de 8000
```

Luego acceder en `http://localhost:8080`

### Rebuild completo

Si algo no funciona, hacer rebuild desde cero:
```bash
docker-compose down -v
docker-compose up -d --build
```

Aguardar 30-40 segundos y verificar:
```bash
docker-compose ps
docker-compose logs app
```

### Permisos en Linux/Mac

Si hay problemas de permisos en Linux o Mac:
```bash
sudo chown -R $USER:$USER .
docker-compose down
docker-compose up -d --build
```

## 📄 Respuestas a Preguntas Técnicas

Ver archivo `RESPUESTAS.md` para respuestas detalladas sobre:
- Escalabilidad ante alta concurrencia
- Desacoplamiento de lógica de dominio
- Versionado de API en producción