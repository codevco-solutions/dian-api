# Registro de Archivos del Proyecto DIAN API

## Estructura y Convenciones

### Convenciones de Nomenclatura
- Controladores: Sufijo `Controller` (ej: `CustomerController`)
- Modelos: Singular (ej: `Customer`)
- Requests: Prefijo acción + Sufijo `Request` (ej: `StoreCustomerRequest`)

### Estándares API
- Prefijo: `/api/v1/`
- Respuestas en JSON
- Autenticación: Laravel Sanctum
- Multitenancy: Basado en subdominios

## Registro de Archivos Existentes

### Controladores
```
app/Http/Controllers/
├── API/
│   ├── AuthController.php       # Autenticación y gestión de usuarios
│   ├── CompanyController.php    # CRUD de empresas
│   └── BranchController.php     # CRUD de sucursales
```

### Modelos
```
app/Models/
├── User.php
├── Company.php
└── Branch.php
```

### Migraciones
```
database/migrations/
├── 2014_10_11_000000_create_master_tables.php    # Tablas maestras DIAN
├── 2024_12_10_180504_create_personal_access_tokens_table.php
├── 2024_12_10_181004_create_roles_table.php
├── 2024_12_10_181028_create_companies_table.php
└── 2024_12_10_181043_create_branches_table.php
```

### Seeders
```
database/seeders/
├── DatabaseSeeder.php
├── RoleSeeder.php
├── MasterTablesSeeder.php
├── CountriesTableSeeder.php
└── DianMasterTablesSeeder.php
```

## Módulos Implementados

### Auth
- Autenticación con Sanctum
- Gestión de usuarios y roles
- Middleware de autenticación

### Companies
- CRUD completo de empresas
- Validación de datos fiscales
- Integración con tablas maestras

### Branches
- CRUD completo de sucursales
- Vinculación con empresas
- Validación de ubicaciones

## Próximos Módulos

### Customers (En desarrollo)
- CRUD de clientes
- Validación de documentos
- Información fiscal
- Direcciones de entrega

### Documents (Pendiente)
- Gestión de documentos electrónicos
- Integración con DIAN
- Eventos y notificaciones

## Notas de Implementación
- Cada módulo nuevo seguirá el patrón Repository
- Los módulos existentes se mantendrán con su estructura actual
- Migración gradual a nueva arquitectura según necesidad
