# Arquitectura DIAN API

## Estructura de Capas

```
Solicitud HTTP → Controller → Service → Repository → Model → Base de Datos
Respuesta HTTP ← Resource ← Service ← Repository ← Model ← Base de Datos
```

## Estructura de Directorios

```
app/
├── Http/
│   ├── Controllers/
│   │   └── API/
│   │       ├── Controller.php           # Controlador base con respuestas estándar
│   │       └── {Módulo}/               # Controladores por módulo
│   │           └── {Módulo}Controller.php
│   │
│   ├── Requests/                        # Validación de solicitudes
│   │   └── {Módulo}/
│   │       ├── Store{Módulo}Request.php
│   │       └── Update{Módulo}Request.php
│   │
│   └── Resources/                       # Transformación de respuestas
│       └── {Módulo}/
│           └── {Módulo}Resource.php
│
├── Services/                            # Lógica de negocio
│   └── {Módulo}/
│       └── {Módulo}Service.php
│
├── Repositories/                        # Capa de acceso a datos
│   ├── Contracts/                       # Interfaces
│   │   └── {Módulo}/
│   │       └── {Módulo}RepositoryInterface.php
│   │
│   └── Eloquent/                        # Implementaciones
│       └── {Módulo}/
│           └── {Módulo}Repository.php
│
└── Models/                              # Modelos Eloquent
    └── {Módulo}.php
```

## Responsabilidades por Capa

### Controllers
- Recibir solicitudes HTTP
- Validar datos de entrada usando Requests
- Delegar lógica al Service
- Devolver respuestas formateadas

### Requests
- Definir reglas de validación
- Personalizar mensajes de error
- Autorización a nivel de solicitud

### Services
- Implementar lógica de negocio
- Coordinar operaciones complejas
- Manejar transacciones
- Lanzar eventos

### Repositories
- Abstraer acceso a datos
- Implementar consultas complejas
- Centralizar lógica de persistencia
- Permitir cambio de fuente de datos

### Models
- Definir estructura de datos
- Establecer relaciones
- Implementar scopes y mutators
- Definir atributos y casting

### Resources
- Transformar datos para respuestas
- Formatear relaciones
- Controlar exposición de datos

## Flujo de Datos

1. **Entrada**
   ```
   HTTP Request → Controller
   → Request (validación)
   → Service (lógica)
   → Repository (datos)
   → Model (persistencia)
   ```

2. **Salida**
   ```
   Model → Repository
   → Service (transformación)
   → Resource (formato)
   → Controller
   → HTTP Response
   ```

## Ventajas

1. **Mantenibilidad**
   - Separación clara de responsabilidades
   - Código organizado y predecible
   - Fácil de testear

2. **Escalabilidad**
   - Módulos independientes
   - Fácil agregar funcionalidades
   - Cambios aislados

3. **Reutilización**
   - Lógica centralizada
   - Componentes modulares
   - DRY (Don't Repeat Yourself)

## Convenciones de Nombres

- **Controladores**: `{Módulo}Controller`
- **Servicios**: `{Módulo}Service`
- **Repositorios**: `{Módulo}Repository`
- **Modelos**: `{Módulo}` (singular)
- **Requests**: `Store{Módulo}Request`, `Update{Módulo}Request`
- **Resources**: `{Módulo}Resource`

Donde `{Módulo}` es el nombre del módulo (ej: Customer, Company, Branch)
