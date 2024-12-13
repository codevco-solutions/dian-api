# Registro de Archivos del Proyecto DIAN API

## Estructura y Convenciones

### Convenciones de Nomenclatura
- Controladores: Sufijo `Controller` (ej: `CompanyController`)
- Modelos: Singular (ej: `Company`)
- Requests: Prefijo acción + Sufijo `Request` (ej: `StoreCompanyRequest`)
- Resources: Sufijo `Resource` (ej: `CompanyResource`)

### Estándares API
- Prefijo: `/api`
- Respuestas en JSON
- Autenticación: Laravel Sanctum
- Multitenancy: Basado en subdominios

## Registro de Archivos Existentes

### Controladores
```
app/Http/Controllers/API/
├── Auth/
│   └── AuthController.php           # Autenticación y gestión de usuarios
├── Company/
│   └── CompanyController.php        # CRUD de empresas
├── Branch/
│   └── BranchController.php         # CRUD de sucursales
├── User/
│   └── UserController.php           # CRUD de usuarios
└── MasterTable/                     # Controladores de tablas maestras
    ├── LocationController.php       # Países, Estados, Ciudades
    ├── CurrencyController.php       # Monedas
    ├── IdentificationTypeController.php
    ├── OrganizationTypeController.php
    ├── TaxRegimeController.php
    ├── TaxResponsibilityController.php
    ├── OperationTypeController.php
    ├── DocumentTypeController.php
    ├── PaymentMeansController.php
    ├── PaymentMethodController.php
    ├── MeasurementUnitController.php
    ├── TaxController.php
    ├── ReferencePriceController.php
    ├── DiscountTypeController.php
    ├── ChargeTypeController.php
    └── EventTypeController.php
```

### Modelos
```
app/Models/
├── User.php
├── Company.php
├── Branch.php
└── MasterTable/
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

### Servicios
```
app/Services/
├── Auth/
│   └── AuthService.php
├── Company/
│   └── CompanyService.php
├── Branch/
│   └── BranchService.php
├── User/
│   └── UserService.php
└── MasterTable/
    └── MasterTableService.php
```

### Repositorios
```
app/Repositories/
├── Contracts/                       # Interfaces
│   ├── Auth/
│   ├── Company/
│   ├── Branch/
│   ├── User/
│   └── MasterTable/
└── Eloquent/                        # Implementaciones
    ├── Auth/
    ├── Company/
    ├── Branch/
    ├── User/
    └── MasterTable/
```

### Migraciones
```
database/migrations/
├── 0001_01_01_000000_create_users_table.php
├── 0001_01_01_000001_create_cache_table.php
├── 0001_01_01_000002_create_jobs_table.php
├── 2014_10_11_000000_create_master_tables.php    # Tablas maestras DIAN
├── 2024_12_10_180504_create_personal_access_tokens_table.php
├── 2024_12_10_181004_create_roles_table.php
├── 2024_12_10_181028_create_companies_table.php
├── 2024_12_10_181043_create_branches_table.php
├── 2024_12_10_182442_modify_users_table.php
└── 2024_12_11_030456_add_prefix_and_group_to_operation_types.php
```

## Módulos Implementados

### Auth
- Registro de usuarios
- Login/Logout con Sanctum
- Gestión de perfiles
- Middleware de autenticación

### Companies
- CRUD completo de empresas
- Búsqueda por subdominio y NIT
- Gestión de sucursales asociadas
- Validación de datos fiscales

### Branches
- CRUD completo de sucursales
- Vinculación con empresas
- Gestión de usuarios por sucursal
- Validación de ubicaciones

### Users
- CRUD completo de usuarios
- Asociación con compañías y sucursales
- Gestión de roles y permisos
- Validación de permisos

### Master Tables
1. **Ubicaciones**
   - Países (countries)
   - Estados/Departamentos (states)
   - Ciudades/Municipios (cities)

2. **Identificación y Organización**
   - Tipos de identificación (identification_types)
   - Tipos de organización (organization_types)
   - Regímenes tributarios (tax_regimes)
   - Responsabilidades tributarias (tax_responsibilities)

3. **Operaciones y Documentos**
   - Tipos de operación (operation_types)
   - Tipos de documento (document_types)
   - Medios de pago (payment_means)
   - Métodos de pago (payment_methods)

4. **Medidas y Tributos**
   - Unidades de medida (measurement_units)
   - Monedas (currencies)
   - Tributos (taxes)

5. **Comercial**
   - Tipos de referencia de precios (reference_prices)
   - Tipos de descuento (discount_types)
   - Tipos de cargo (charge_types)
   - Tipos de evento (event_types)

## Registro de Cambios

### 2024-12-11

#### Implementación de Tablas Maestras

##### Nuevos Modelos Creados
- Creados modelos específicos para cada tabla maestra en `app/Models/MasterTable/`
- Implementado scope `active()` en todos los modelos
- Agregados campos fillable y casts apropiados
- Implementadas relaciones necesarias (ej: PaymentMeans con PaymentMethods)

##### Nuevos Controladores
- Creados controladores para cada tabla maestra en `app/Http/Controllers/API/MasterTable/`
- Todos heredan de `MasterTableController` para funcionalidad común
- Implementado CRUD completo (index, show, store, update, delete, active)

##### Rutas Actualizadas
- Agregadas rutas para todas las tablas maestras
- Implementado patrón consistente para endpoints
- Soporte para operaciones CRUD y listado de registros activos

##### Mejoras Generales
- Implementada paginación estándar
- Agregados filtros comunes (search, is_active, sort)
- Validación consistente en todas las operaciones
- Respuestas estandarizadas mediante Resources

### 2024-12-10

#### Correcciones en Sistema de Autenticación y Autorización

##### Cambios en Modelo de Usuario
- **app/Models/User.php**
  - Actualizado los métodos de verificación de roles para usar slugs en lugar de IDs
  - Mejorado la relación con el modelo Role
  - Implementado métodos `isSuperAdmin()`, `isCompanyAdmin()`, e `isUser()` usando slugs

##### Cambios en Controladores
- **app/Http/Controllers/API/Company/CompanyController.php**
  - Corregido el método de acceso al usuario autenticado
  - Simplificado la lógica de verificación de roles usando slugs
  - Mejorado el manejo de permisos y respuestas HTTP
  - Optimizado el método index para manejar diferentes roles de usuario
  - Agregado validación apropiada en métodos de actualización
  - Implementado manejo consistente de errores

- **app/Http/Controllers/API/Branch/BranchController.php**
  - Actualizado el sistema de verificación de roles
  - Mejorado la lógica de autorización usando slugs
  - Implementado manejo de errores usando ModelNotFoundException
  - Estandarizado los mensajes de respuesta
  - Optimizado el método index para diferentes roles
  - Agregado validación robusta en métodos de actualización

#### Mejoras Generales
- Implementado un sistema consistente de mensajes de error
- Estandarizado los códigos de respuesta HTTP
- Mejorado la seguridad en la verificación de roles
- Optimizado las consultas a la base de datos
- Implementado validación más robusta en las operaciones CRUD

#### Correcciones de Bugs
- Solucionado el error "Undefined method 'isSuperAdmin'"
- Corregido el error "Undefined method 'load'"
- Solucionado el error "Undefined method 'getByCompanyId'"
- Mejorado el manejo de relaciones entre modelos

#### Seguridad
- Implementado verificaciones de autorización más robustas
- Mejorado la validación de permisos de usuario
- Asegurado que los usuarios solo puedan acceder a sus recursos autorizados

### Notas Adicionales
- Los cambios mantienen la compatibilidad con la estructura existente
- Se ha priorizado la seguridad y la robustez del sistema
- Se mantiene el patrón de diseño Repository
- Se han agregado validaciones adicionales para mejorar la integridad de los datos

## Historial de Cambios

### 12 de Diciembre 2023 - Implementación de Arquitectura de Repositorios y Servicios

#### Cambios Realizados
1. **Reorganización de Modelos por Dominio**
   - Modelos movidos a carpetas específicas por dominio (Auth, Company, Branch, etc.)
   - Implementación de relaciones y propiedades en cada modelo
   - Mejora en la organización y mantenibilidad del código

2. **Implementación del Patrón Repositorio**
   - Creación de interfaces en `app/Repositories/Contracts`
   - Implementación de repositorios en `app/Repositories/Eloquent`
   - Separación de la lógica de acceso a datos

3. **Capa de Servicios**
   - Implementación de servicios por dominio
   - Encapsulamiento de la lógica de negocio
   - Mejora en la reutilización de código

4. **Migraciones y Seeders**
   - Reorganización de migraciones por dominio
   - Nuevos seeders para datos iniciales
   - Mejora en la estructura de la base de datos

5. **Recursos API**
   - Nuevos recursos para transformación de datos
   - Estandarización de respuestas API
   - Mejora en la consistencia de datos

6. **Documentación**
   - Actualización de la colección Postman
   - Documentación de la arquitectura
   - Registro de cambios en FILE_REGISTRY.md

#### Estructura Actual del Proyecto
```
app/
├── Http/
│   ├── Controllers/API/            # Controladores por dominio
│   ├── Resources/                  # Recursos API
│   └── Requests/                   # Validación de solicitudes
├── Models/                         # Modelos organizados por dominio
├── Repositories/
│   ├── Contracts/                 # Interfaces de repositorios
│   └── Eloquent/                  # Implementaciones de repositorios
├── Services/                       # Servicios por dominio
└── Providers/
    └── RepositoryServiceProvider.php
```

#### Próximos Pasos
1. Implementación de pruebas unitarias
2. Documentación detallada de la API
3. Implementación de caché
4. Optimización de consultas
5. Implementación de eventos y listeners

## Notas de Implementación
- Se sigue el patrón Repository en todos los módulos
- Implementación consistente de filtros y paginación
- Validación robusta en todas las operaciones
- Manejo estandarizado de errores y respuestas

## Próximos Pasos
1. Implementar seeders para todas las tablas maestras
2. Agregar validaciones específicas por tipo de documento
3. Implementar caché para tablas maestras
4. Agregar pruebas automatizadas
5. Documentar API con Swagger/OpenAPI
