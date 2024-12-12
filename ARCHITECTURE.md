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
│   │       ├── Auth/                    # Controladores de autenticación
│   │       │   └── AuthController.php
│   │       ├── Company/                 # Controladores de compañías
│   │       │   └── CompanyController.php
│   │       ├── Branch/                  # Controladores de sucursales
│   │       │   └── BranchController.php
│   │       ├── User/                    # Controladores de usuarios
│   │       │   └── UserController.php
│   │       └── MasterTable/             # Controladores de tablas maestras
│   │           ├── LocationController.php
│   │           ├── CurrencyController.php
│   │           ├── IdentificationTypeController.php
│   │           ├── OrganizationTypeController.php
│   │           ├── TaxRegimeController.php
│   │           ├── TaxResponsibilityController.php
│   │           ├── OperationTypeController.php
│   │           ├── DocumentTypeController.php
│   │           ├── PaymentMeansController.php
│   │           ├── PaymentMethodController.php
│   │           ├── MeasurementUnitController.php
│   │           ├── TaxController.php
│   │           ├── ReferencePriceController.php
│   │           ├── DiscountTypeController.php
│   │           ├── ChargeTypeController.php
│   │           └── EventTypeController.php
│   │
│   ├── Requests/                        # Validación de solicitudes
│   │   ├── Auth/
│   │   ├── Company/
│   │   ├── Branch/
│   │   └── User/
│   │
│   └── Resources/                       # Transformación de respuestas
│       ├── Auth/
│       ├── Company/
│       ├── Branch/
│       ├── User/
│       └── MasterTable/
│
├── Services/                            # Lógica de negocio
│   ├── Auth/
│   ├── Company/
│   ├── Branch/
│   ├── User/
│   └── MasterTable/
│
├── Repositories/                        # Capa de acceso a datos
│   ├── Contracts/                       # Interfaces
│   │   ├── Auth/
│   │   ├── Company/
│   │   ├── Branch/
│   │   ├── User/
│   │   └── MasterTable/
│   │
│   └── Eloquent/                        # Implementaciones
│       ├── Auth/
│       ├── Company/
│       ├── Branch/
│       ├── User/
│       └── MasterTable/
│
└── Models/                              # Modelos Eloquent
    ├── User.php
    ├── Company.php
    ├── Branch.php
    └── MasterTable/                     # Modelos de tablas maestras
        ├── Currency.php
        ├── IdentificationType.php
        ├── OrganizationType.php
        ├── TaxRegime.php
        ├── TaxResponsibility.php
        ├── OperationType.php
        ├── DocumentType.php
        ├── PaymentMeans.php
        ├── PaymentMethod.php
        ├── MeasurementUnit.php
        ├── Tax.php
        ├── ReferencePrice.php
        ├── DiscountType.php
        ├── ChargeType.php
        └── EventType.php
```

## Módulos Principales

### Autenticación (Auth)
- Registro de usuarios
- Login/Logout
- Gestión de perfiles

### Compañías (Companies)
- CRUD de compañías
- Búsqueda por subdominio y NIT
- Gestión de sucursales asociadas

### Sucursales (Branches)
- CRUD de sucursales
- Asociación con compañías
- Gestión de usuarios por sucursal

### Usuarios (Users)
- CRUD de usuarios
- Asociación con compañías y sucursales
- Gestión de roles y permisos

### Tablas Maestras (MasterTable)
1. **Ubicaciones**
   - Países
   - Estados/Departamentos
   - Ciudades/Municipios

2. **Identificación y Organización**
   - Tipos de identificación
   - Tipos de organización
   - Regímenes tributarios
   - Responsabilidades tributarias

3. **Operaciones y Documentos**
   - Tipos de operación
   - Tipos de documento
   - Medios de pago
   - Métodos de pago

4. **Medidas y Tributos**
   - Unidades de medida
   - Monedas
   - Tributos

5. **Comercial**
   - Tipos de referencia de precios
   - Tipos de descuento
   - Tipos de cargo
   - Tipos de evento

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

## Convenciones de Nombres

- **Controladores**: `{Módulo}Controller`
- **Servicios**: `{Módulo}Service`
- **Repositorios**: `{Módulo}Repository`
- **Modelos**: `{Módulo}` (singular)
- **Requests**: `Store{Módulo}Request`, `Update{Módulo}Request`
- **Resources**: `{Módulo}Resource`

## Estándares de API

### Endpoints
- Base URL: `/api`
- Versión: No versionado (implícito v1)
- Formato: JSON
- Autenticación: Bearer Token (Sanctum)

### Respuestas
- 200: Éxito
- 201: Creado
- 400: Error de validación
- 401: No autorizado
- 403: Prohibido
- 404: No encontrado
- 500: Error del servidor

### Paginación
```json
{
    "data": [],
    "links": {
        "first": "http://...",
        "last": "http://...",
        "prev": null,
        "next": "http://..."
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 5,
        "path": "http://...",
        "per_page": 10,
        "to": 10,
        "total": 50
    }
}
```

### Filtros Comunes
- `?search=término`
- `?is_active=1`
- `?sort=campo&order=desc`
- `?page=1&per_page=10`
