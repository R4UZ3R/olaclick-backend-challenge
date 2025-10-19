# 🎯 Respuestas a Preguntas Técnicas

## 1. ¿Cómo asegurarías que esta API escale ante alta concurrencia?

### Caché y Optimización
- **Caché distribuido con Redis** (implementado): Cachear queries frecuentes con TTL apropiado
- **Eager Loading**: Prevenir problema N+1 con carga anticipada de relaciones
- **Índices en base de datos**: Crear índices en columnas consultadas frecuentemente
- **Paginación**: Limitar registros por request para reducir carga

### Procesamiento Asíncrono
- **Laravel Queues**: Procesar tareas pesadas (emails, reportes, notificaciones) en background
- **Supervisord/Horizon**: Gestionar workers de cola eficientemente
- **Eventos**: Desacoplar lógica pesada mediante sistema de eventos

### Infraestructura
- **Load Balancing**: Múltiples instancias detrás de balanceador (Nginx, HAProxy, AWS ELB)
- **Database Read Replicas**: Separar lecturas y escrituras con réplicas de PostgreSQL
- **Connection Pooling**: Usar PgBouncer para gestionar conexiones eficientemente
- **Auto-scaling**: Escalado horizontal con Kubernetes o Docker Swarm

### Monitoreo y Seguridad
- **Rate Limiting**: Throttling por IP/usuario para prevenir abuso
- **APM**: Monitoreo con New Relic, Datadog o Laravel Telescope
- **CDN**: Para assets estáticos y respuestas cacheables

---

## 2. ¿Qué estrategia seguirías para desacoplar la lógica del dominio de Laravel/Eloquent?

### Arquitectura Hexagonal + DDD (Domain-Driven Design)

**Estructura de capas:**

**Domain (Núcleo - Sin dependencias del framework):**
- **Entities**: Objetos de negocio puros (POPOs - Plain Old PHP Objects)
- **Value Objects**: Objetos inmutables (Money, OrderStatus, ClientName)
- **Domain Services**: Lógica de negocio compleja
- **Repository Interfaces**: Contratos (ports) sin implementación

**Application (Casos de uso):**
- **Use Cases**: Orquestación de lógica de dominio
- **DTOs**: Transferencia de datos entre capas
- **Application Services**: Coordinación de casos de uso

**Infrastructure (Implementaciones concretas):**
- **Eloquent/Doctrine Repositories**: Implementación de interfaces de dominio (adapters)
- **HTTP Controllers**: Punto de entrada de requests
- **Mappers**: Conversión entre modelos Eloquent y entidades de dominio

### Beneficios

- Lógica de negocio independiente del framework
- Testeable sin base de datos
- Fácil migración a otro ORM o framework
- Cumple con Dependency Inversion Principle (SOLID)
- Reglas de negocio centralizadas y protegidas

### Ejemplo de flujo

1. Controller recibe request
2. DTO construye objeto de transferencia
3. Use Case ejecuta lógica de dominio
4. Repository Interface define contrato
5. Eloquent Repository implementa persistencia
6. Mapper convierte Eloquent Model ↔ Domain Entity
7. Response retorna al cliente

---

## 3. ¿Cómo manejarías versiones de la API en producción?

### Estrategia de Versionado

**Opción 1: Versionado en URL (Recomendado)**
- `/api/v1/orders`
- `/api/v2/orders`
- Ventajas: Explícito, fácil de cachear, compatible con CDN

**Opción 2: Versionado por Header**
- `Accept: application/vnd.olaclick.v2+json`
- `Accept-Version: v2`
- Ventajas: URLs limpias, más RESTful

### Política de Deprecación

**Ciclo de vida:**
1. **Stable** (6-12 meses): Versión actual en producción
2. **Deprecated** (6 meses): Soporte paralelo, avisos en headers
3. **Discontinued** (3 meses): Solo parches de seguridad críticos
4. **Removed**: Versión eliminada

**Headers de deprecación:**
- `X-API-Deprecated: true`
- `X-API-Sunset: 2025-12-31`
- `X-API-Migration-Guide: https://docs.api.com/migration`

### Gestión de Cambios

**Breaking Changes:**
- Requieren nueva versión MAJOR (v1 → v2)
- Ejemplos: Cambiar tipos de datos, eliminar campos, cambiar estructura de response

**Non-Breaking Changes:**
- Se pueden agregar en versión actual
- Ejemplos: Nuevos endpoints, campos opcionales, nuevos parámetros opcionales

**Documentación:**
- Changelog detallado con diferencias entre versiones
- Guías de migración paso a paso
- Ejemplos de requests/responses por versión
- Deprecated warnings con alternativas

### Implementación Técnica

**Estructura de código:**
- Controladores separados por versión (V1, V2, V3)
- Transformers/Resources diferentes por versión
- Tests de compatibilidad por versión
- Middleware de versionado

**API Gateway:**
- Usar Kong, Tyk o AWS API Gateway
- Routing automático por versión
- Rate limiting diferenciado
- Canary releases (v2 al 10% de usuarios inicialmente)

### Monitoreo

**Métricas clave:**
- % de requests por versión
- Usuarios activos en versiones deprecated
- Tiempo de respuesta por versión
- Errores por versión

**Comunicación:**
- Emails a clientes usando versiones deprecated
- Dashboard público con estado de versiones
- Notificaciones in-app sobre deprecación
- Blog posts anunciando cambios

### Semantic Versioning

Seguir [SemVer](https://semver.org/):
- **MAJOR**: Breaking changes (v1 → v2)
- **MINOR**: Nuevas features compatibles (v1.0 → v1.1)
- **PATCH**: Bug fixes (v1.1.0 → v1.1.1)