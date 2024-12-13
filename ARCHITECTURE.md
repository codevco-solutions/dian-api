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
│   │       ├── Auth/                    # Autenticación y autorización
│   │       ├── Common/                  # Componentes comunes (direcciones, contactos)
│   │       ├── Company/                 # Gestión de empresas
│   │       ├── Customer/                # Gestión de clientes
│   │       ├── Document/                # Documentos electrónicos
│   │       │   ├── Commercial/          # Facturas, notas crédito/débito
│   │       │   └── Payroll/            # Nómina electrónica
│   │       ├── Location/                # Países, estados, ciudades
│   │       ├── MasterTable/             # Tablas maestras y configuración
│   │       ├── Product/                 # Productos y listas de precios
│   │       ├── Supplier/                # Gestión de proveedores
│   │       └── User/                    # Gestión de usuarios
│   │
│   ├── Requests/                        # Validación de solicitudes
│   │   ├── Auth/
│   │   ├── Common/
│   │   ├── Company/
│   │   ├── Customer/
│   │   ├── Document/
│   │   ├── Product/
│   │   ├── Supplier/
│   │   └── User/
│   │
│   └── Resources/                       # Transformación de respuestas
│       ├── Auth/
│       ├── Common/
│       ├── Company/
│       ├── Customer/
│       ├── Document/
│       │   ├── Commercial/
│       │   └── Payroll/
│       ├── MasterTable/
│       ├── Product/
│       ├── Supplier/
│       └── User/
│
├── Models/                              # Modelos por dominio
│   ├── Auth/                           # Autenticación y roles
│   ├── Branch/                         # Sucursales
│   ├── Common/                         # Modelos comunes
│   ├── Company/                        # Empresas
│   ├── Customer/                       # Clientes
│   ├── Document/                       # Documentos electrónicos
│   │   ├── Commercial/                 # Documentos comerciales
│   │   └── Payroll/                   # Documentos de nómina
│   ├── Location/                       # Ubicaciones
│   ├── MasterTable/                    # Tablas maestras
│   ├── Product/                        # Productos
│   └── Supplier/                       # Proveedores
│
├── Repositories/
│   ├── Contracts/                      # Interfaces de repositorio
│   │   ├── Auth/
│   │   ├── Common/
│   │   ├── Company/
│   │   ├── Customer/
│   │   ├── Document/
│   │   ├── Location/
│   │   ├── MasterTable/
│   │   ├── Product/
│   │   ├── Supplier/
│   │   └── User/
│   │
│   └── Eloquent/                       # Implementaciones
│       ├── Auth/
│       ├── Common/
│       ├── Company/
│       ├── Customer/
│       ├── Document/
│       ├── Location/
│       ├── MasterTable/
│       ├── Product/
│       ├── Supplier/
│       └── User/
│
└── Services/                           # Servicios por dominio
    ├── Auth/
    ├── Common/
    ├── Company/
    ├── Customer/
    ├── Document/
    │   ├── Commercial/
    │   └── Payroll/
    ├── Location/
    ├── MasterTable/
    ├── Product/
    ├── Supplier/
    └── User/
```

## Dominios Principales

### Autenticación (Auth)
- Gestión de usuarios y roles
- Autenticación con Sanctum
- Control de acceso basado en roles

### Empresas (Company)
- Gestión de empresas y sucursales
- Configuración por empresa
- Multi-tenancy por subdominio

### Documentos Electrónicos (Document)
#### Comerciales
- Facturas electrónicas
- Notas crédito/débito
- Cotizaciones y órdenes
- Recibos de pago

#### Nómina
- Documentos de nómina
- Ajustes y deducciones
- Períodos de nómina

### Productos (Product)
- Catálogo de productos
- Categorías
- Listas de precios

### Clientes y Proveedores
- Gestión de clientes
- Gestión de proveedores
- Contactos y direcciones

### Tablas Maestras (MasterTable)
- Tipos de identificación
- Tipos de organización
- Regímenes tributarios
- Responsabilidades tributarias
- Tipos de operación
- Medios de pago
- Unidades de medida
- Monedas y tributos

## Patrones y Principios

### Patrones de Diseño
- Repository Pattern
- Service Layer
- Domain-Driven Design (DDD)
- Factory Pattern
- Observer Pattern (Eventos)

### Principios SOLID
- Single Responsibility
- Open/Closed
- Liskov Substitution
- Interface Segregation
- Dependency Inversion

## Convenciones

### Nomenclatura
- Controllers: `{Domain}Controller`
- Services: `{Domain}Service`
- Repositories: `{Domain}Repository`
- Models: Singular `{Domain}`
- Interfaces: `{Domain}RepositoryInterface`
- Resources: `{Domain}Resource`

### Estándares API
- RESTful endpoints
- JSON responses
- Autenticación con Bearer token
- Validación de requests
- Resources para transformación
- API Resources para relaciones
- Paginación estándar
- Filtros dinámicos
- Ordenamiento
- Caché cuando sea apropiado

## Seguridad
- Autenticación con Sanctum
- Roles y permisos (Spatie)
- Validación de requests
- Sanitización de datos
- Rate limiting
- CORS configurado
- Headers de seguridad
- Logs de auditoría
